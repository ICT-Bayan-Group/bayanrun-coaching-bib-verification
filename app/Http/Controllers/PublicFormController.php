<?php

namespace App\Http\Controllers;

use App\Models\PesertaLari;
use App\Models\PesertaPreRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PublicFormController extends Controller
{
    private const MAX_REGISTRATIONS = 580;

    public function index()
    {
        $totalRegistrations = PesertaLari::count();
        $registrationOpen = $totalRegistrations < self::MAX_REGISTRATIONS;
        return view('public-form', compact('registrationOpen', 'totalRegistrations'));
    }

    public function verifyEmail(Request $request)
    {
        $totalRegistrations = PesertaLari::count();
        if ($totalRegistrations >= self::MAX_REGISTRATIONS) {
            return response()->json(['success' => false, 'message' => 'Maaf, pendaftaran sudah ditutup. Batas maksimal 600 peserta telah tercapai.'], 422);
        }

        $validated = $request->validate(['email' => 'required|string|email'], ['email.required' => 'Email harus diisi', 'email.email' => 'Format email tidak valid']);

        try {
            $peserta = PesertaPreRegistered::where('email', $validated['email'])->first();
            if (!$peserta) {
                return response()->json(['success' => false, 'message' => 'Email tidak ditemukan. Pastikan email yang Anda masukkan benar.'], 404);
            }

            if (PesertaLari::where('email', $validated['email'])->exists()) {
                return response()->json(['success' => false, 'message' => 'Email ini sudah terdaftar sebelumnya. Setiap email hanya dapat mendaftar satu kali.'], 422);
            }

            return response()->json(['success' => true, 'message' => 'Email berhasil diverifikasi', 'data' => ['email' => $peserta->email, 'nama' => $peserta->nama, 'kategori_lari' => $peserta->kategori_lari, 'nomor_telepon' => $peserta->nomor_telepon, 'remaining_slots' => self::MAX_REGISTRATIONS - $totalRegistrations]]);
        } catch (\Exception $e) {
            Log::error('Email verification error', ['email' => $validated['email'], 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memverifikasi Email. Silakan coba lagi.'], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $this->validateRegistrationData($request);
        
        try {
            DB::beginTransaction();

            if (PesertaLari::count() >= self::MAX_REGISTRATIONS) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Maaf, pendaftaran sudah ditutup.'], 422);
            }

            $preRegistered = PesertaPreRegistered::where('email', $validated['email'])->first();
            if (!$preRegistered) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Email tidak valid.'], 404);
            }

            $peserta = $this->createPeserta($validated, $preRegistered);
            $qrCodeUrl = $this->processQRCode($peserta);
            
            if (method_exists($preRegistered, 'markAsRegistered')) {
                $preRegistered->markAsRegistered();
            }

            DB::commit();

            $whatsappResult = $this->sendQontakWhatsApp($peserta, $qrCodeUrl);

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran Anda berhasil!',
                'data' => [
                    'id' => $peserta->id,
                    'nama' => $peserta->nama_lengkap,
                    'kategori' => $peserta->kategori_lari,
                    'email' => $peserta->email,
                    'qr_code_url' => $qrCodeUrl,
                    'whatsapp_sent' => $whatsappResult['success'] ?? false,
                    'whatsapp_message' => $whatsappResult['message'] ?? 'Unknown',
                    'remaining_slots' => self::MAX_REGISTRATIONS - PesertaLari::count(),
                    'registration_number' => PesertaLari::count()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
        }
    }

    private function processQRCode($peserta)
    {
        $namaFormatted = str_replace(' ', '_', $peserta->nama_lengkap);
        $qrUrl = "https://coaching.bayanevent.com/verify?email=" . urlencode($peserta->email) . "#" . $namaFormatted;
        $qrCodeUrl = $this->generateQRCodeURL($qrUrl);
        $qrCodePath = $this->downloadAndSaveQRCode($qrCodeUrl, $peserta->id);
        
        if ($qrCodePath) {
            $peserta->update(['qr_code_path' => $qrCodePath, 'qr_url' => $qrUrl]);
        }
        return $qrCodeUrl;
    }

    private function generateQRCodeURL($data)
    {
        return "https://api.qrserver.com/v1/create-qr-code/?size=300x300&format=png&margin=20&data=" . urlencode($data);
    }

    private function downloadAndSaveQRCode($qrCodeUrl, $pesertaId, $maxRetries = 3)
    {
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::timeout(15)->retry(2, 1000)->get($qrCodeUrl);
                if ($response->successful()) {
                    $qrCodePath = $this->saveQRCodeFile($response->body(), $pesertaId);
                    if ($qrCodePath) return $qrCodePath;
                }
                if ($attempt < $maxRetries) sleep(1);
            } catch (\Exception $e) {
                Log::error("QR download attempt {$attempt} failed", ['peserta_id' => $pesertaId, 'error' => $e->getMessage()]);
                if ($attempt < $maxRetries) sleep(1);
            }
        }
        return null;
    }

    private function saveQRCodeFile($fileContent, $pesertaId)
    {
        try {
            $qrDir = storage_path('app/public/qr-codes');
            if (!file_exists($qrDir)) mkdir($qrDir, 0755, true);

            $qrCodePath = 'qr-codes/' . $pesertaId . '.png';
            $fullPath = storage_path('app/public/' . $qrCodePath);
            file_put_contents($fullPath, $fileContent);
            
            return (file_exists($fullPath) && filesize($fullPath) > 0) ? $qrCodePath : null;
        } catch (\Exception $e) {
            Log::error('Failed to save QR file', ['peserta_id' => $pesertaId, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function verifyQR(Request $request)
    {
        $email = $request->input('email');
        try {
            $peserta = PesertaLari::where('email', $email)->first();
            if (!$peserta) {
                return response()->json(['success' => false, 'message' => 'Email tidak ditemukan'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data peserta berhasil ditemukan',
                'data' => [
                    'id' => $peserta->id,
                    'email' => $peserta->email,
                    'nama' => $peserta->nama_lengkap,
                    'kategori' => $peserta->kategori_lari,
                    'telepon' => $peserta->telepon,
                    'waktu_daftar' => $peserta->created_at->format('d/m/Y H:i:s'),
                    'status' => $peserta->status ?? 'terdaftar'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('QR verification error', ['email' => $email, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan'], 500);
        }
    }

    private function getQontakConfig()
    {
        $config = [
            'url' => config('services.qontak.url', 'https://service-chat.qontak.com/api/open/v1/broadcasts/whatsapp/direct'),
            'token' => config('services.qontak.token'),
            'channel_integration_id' => config('services.qontak.channel_integration_id'),
            'valid' => false
        ];
        $config['valid'] = !empty($config['token']) && !empty($config['channel_integration_id']);
        return $config;
    }

    private function sendQontakWhatsApp($peserta, $qrCodeUrl)
    {
        try {
            $config = $this->getQontakConfig();
            if (!$config['valid']) {
                return ['success' => false, 'message' => 'Qontak config not found'];
            }

            $phoneNumber = $this->formatPhoneNumber($peserta->telepon);
            Log::info('Starting Qontak WhatsApp', ['peserta_id' => $peserta->id, 'phone' => $phoneNumber]);

            if (!$this->validateQRCodeUrl($qrCodeUrl)) {
                return ['success' => false, 'message' => 'QR URL tidak valid'];
            }

            $result = $this->sendQontakMessage($phoneNumber, $qrCodeUrl, $peserta, $config);
            return ['success' => $result['success'], 'message' => $result['message'], 'qontak_response' => $result['response'] ?? null];

        } catch (\Exception $e) {
            Log::error('Qontak error', ['peserta_id' => $peserta->id, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }

 private function sendQontakMessage($phoneNumber, $qrCodeUrl, $peserta, $config, $maxRetries = 3)
{
    for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
        try {
            if ($attempt > 1) sleep($attempt * 2);

            // Payload minimal yang diperlukan Qontak
            $payload = [
                'to_number' => $phoneNumber,
                'to_name' => $peserta->nama_lengkap,
                'message_template_id' => '818a2923-cb00-4293-827f-00214fa4eac5',
                'channel_integration_id' => $config['channel_integration_id'],
                'language' => [
                    'code' => 'id'
                ],
                'parameters' => [
                    'body' => []  // Array kosong untuk template tanpa variable
                ]
            ];

            Log::info("Qontak payload attempt {$attempt}", [
                'peserta_id' => $peserta->id,
                'payload' => $payload
            ]);

            Log::info("Qontak payload attempt {$attempt}", [
                'peserta_id' => $peserta->id,
                'payload' => $payload
            ]);

            $response = Http::timeout(45)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['token'],
                    'Content-Type' => 'application/json'
                ])
                ->post($config['url'], $payload);

            Log::info("Qontak attempt {$attempt}", [
                'peserta_id' => $peserta->id,
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['status']) && 
                    in_array($responseData['status'], ['success', 'pending', true], true)) {
                    return [
                        'success' => true,
                        'message' => 'QR Code berhasil dikirim',
                        'attempts' => $attempt,
                        'response' => $responseData
                    ];
                }
            }

            // Log detail error untuk debugging
            Log::warning("Qontak attempt {$attempt} tidak berhasil", [
                'peserta_id' => $peserta->id,
                'status_code' => $response->status(),
                'error_body' => $response->body()
            ]);

        } catch (\Exception $e) {
            Log::error("Qontak attempt {$attempt} failed", [
                'peserta_id' => $peserta->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    return [
        'success' => false,
        'message' => "Gagal setelah {$maxRetries} percobaan"
    ];
}

    private function validateRegistrationData(Request $request)
    {
        return $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'kategori_lari' => 'required|string|max:100',
            'email' => 'required|email|unique:peserta_laris,email',
            'telepon' => 'required|string|max:20',
        ], [
            'nama_lengkap.required' => 'Nama lengkap harus diisi',
            'kategori_lari.required' => 'Kategori lari harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'telepon.required' => 'Nomor telepon harus diisi'
        ]);
    }

    private function createPeserta($validated, $preRegistered)
    {
        return PesertaLari::create([
            'email' => $validated['email'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'kategori_lari' => $validated['kategori_lari'],
            'telepon' => $validated['telepon'],
            'qr_token' => Str::random(32),
            'status' => 'terdaftar'
        ]);
    }

    private function validateQRCodeUrl($qrCodeUrl)
    {
        try {
            $response = Http::timeout(10)->head($qrCodeUrl);
            return $response->successful() && str_contains($response->header('Content-Type', ''), 'image');
        } catch (\Exception $e) {
            Log::error('QR URL validation failed', ['url' => $qrCodeUrl, 'error' => $e->getMessage()]);
            return false;
        }
    }

    private function formatPhoneNumber($phone)
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($phoneNumber, 0, 2) === '62') {
            $formatted = $phoneNumber;
        } elseif (substr($phoneNumber, 0, 1) === '0') {
            $formatted = '62' . substr($phoneNumber, 1);
        } else {
            $formatted = '62' . $phoneNumber;
        }
        
        Log::info('Phone formatted', ['original' => $phone, 'formatted' => $formatted]);
        return $formatted;
    }

    private function prepareQRCodeCaption($peserta)
    {
        $registrationNumber = PesertaLari::where('created_at', '<=', $peserta->created_at)->count();
        
        return "🎫 QR CODE COACHING CLINIC BAYAN RUN 2025\n\n" .
               "Halo {$peserta->nama_lengkap}! 👋\n\n" .
               "Pendaftaran Anda di Coaching Clinic Bayan Run 2025 telah berhasil! 🏃‍♂️✨\n\n" .
               "📋 DETAIL PENDAFTARAN:\n" .
               "━━━━━━━━━━━━━━━━━━━━━━━━━\n" .
               "👤 Nama: {$peserta->nama_lengkap}\n" .
               "🏃‍♂️ Kategori: {$peserta->kategori_lari}\n" .
               "📧 Email: {$peserta->email}\n" .
               "📱 No. HP: {$peserta->telepon}\n" .
               "🔢 Peserta ke: {$registrationNumber}/600\n" .
               "━━━━━━━━━━━━━━━━━━━━━━━━━\n\n" .
               "⚠️ PENTING:\n" .
               "• Simpan QR Code dengan baik\n" .
               "• Tunjukkan QR Code saat check-in\n" .
               "• QR ini adalah tiket masuk Anda\n\n" .
               "📅 Tanggal: 11 Oktober 2025\n" .
               "🕒 Waktu: 15:00 - 17:00 WITA\n" .
               "📍 Lokasi: Gedung Kesenian Balikpapan\n\n" .
               "Terima kasih! Sampai jumpa! 🏃‍♂️🏃‍♀️\n\n" .
               "Salam Olahraga,\nTim Bayan Run 2025 🏃‍♂️✨";
    }

    public function testWhatsApp(Request $request)
{
    $phone = $request->input('phone', '6285377640809');
    
    try {
        $config = $this->getQontakConfig();
        
        if (!$config['valid']) {
            return response()->json([
                'success' => false,
                'message' => 'Qontak config not found',
                'debug' => [
                    'token_exists' => !empty(config('services.qontak.token')),
                    'channel_exists' => !empty(config('services.qontak.channel_integration_id')),
                    'template_exists' => !empty(config('services.qontak.template_id'))
                ]
            ]);
        }

        $formattedPhone = $this->formatPhoneNumber($phone);
        $testMessage = "🧪 Test dari Bayan Run 2025\n\nKonfigurasi WhatsApp Qontak berhasil! ✅\n\nWaktu: " . now()->format('d/m/Y H:i:s');

        // Payload dengan parameters kosong untuk template tanpa variable
        $payload = [
            'to_number' => $formattedPhone,
            'to_name' => 'Test User',
            'message_template_id' => '818a2923-cb00-4293-827f-00214fa4eac5',
            'channel_integration_id' => $config['channel_integration_id'],
            'language' => [
                'code' => 'id'
            ],
            'parameters' => [
                'body' => []
            ]
        ];

        Log::info('Test WhatsApp Payload', ['payload' => $payload]);

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $config['token'],
                'Content-Type' => 'application/json'
            ])
            ->post($config['url'], $payload);

        return response()->json([
            'success' => $response->successful(),
            'status_code' => $response->status(),
            'formatted_phone' => $formattedPhone,
            'response' => $response->json(),
            'message' => $response->successful() ? 'Test berhasil!' : 'Test gagal',
            'config' => [
                'url' => $config['url'],
                'channel_id' => $config['channel_integration_id'],
                'template_id' => '818a2923-cb00-4293-827f-00214fa4eac5'
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Test error', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}

// Method baru untuk list semua templates
public function listTemplates()
{
    try {
        $config = $this->getQontakConfig();
        
        if (!$config['valid']) {
            return response()->json([
                'success' => false,
                'message' => 'Qontak config not found'
            ]);
        }

        // Endpoint untuk list templates (coba beberapa kemungkinan)
        $possibleEndpoints = [
            'https://service-chat.qontak.com/api/open/v1/templates',
            'https://service-chat.qontak.com/api/open/v1/broadcasts/whatsapp/templates',
            'https://service-chat.qontak.com/api/open/v1/message_templates'
        ];

        $results = [];
        
        foreach ($possibleEndpoints as $endpoint) {
            try {
                $response = Http::timeout(30)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $config['token'],
                        'Content-Type' => 'application/json'
                    ])
                    ->get($endpoint);

                $results[$endpoint] = [
                    'status_code' => $response->status(),
                    'success' => $response->successful(),
                    'data' => $response->json()
                ];

                if ($response->successful()) {
                    break; // Kalau berhasil, stop
                }
            } catch (\Exception $e) {
                $results[$endpoint] = [
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'message' => 'Template list attempt',
            'results' => $results,
            'instruction' => 'Cek dashboard Qontak di: https://chat.qontak.com → Message Templates'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}
    public function retryQRCode($pesertaId)
    {
        try {
            $peserta = PesertaLari::findOrFail($pesertaId);
            $namaFormatted = str_replace(' ', '_', $peserta->nama_lengkap);
            $qrUrl = "https://coaching.bayanevent.com/verify?email=" . urlencode($peserta->email) . "#" . $namaFormatted;
            $qrCodeUrl = $this->generateQRCodeURL($qrUrl);
            $result = $this->sendQontakWhatsApp($peserta, $qrCodeUrl);
            
            return response()->json(['success' => $result['success'], 'message' => $result['message'], 'data' => $result]);
        } catch (\Exception $e) {
            Log::error('Retry failed', ['peserta_id' => $pesertaId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    public function showRegistrations()
    {
        $pesertaLaris = PesertaLari::orderBy('created_at', 'desc')->get();
        $totalRegistrations = $pesertaLaris->count();
        $remainingSlots = self::MAX_REGISTRATIONS - $totalRegistrations;
        $registrationOpen = $totalRegistrations < self::MAX_REGISTRATIONS;
        return view('registrations.index', compact('pesertaLaris', 'totalRegistrations', 'remainingSlots', 'registrationOpen'));
    }

    public function showDetail($id)
    {
        $peserta = PesertaLari::findOrFail($id);
        return view('registrations.detail', compact('peserta'));
    }

    public function export(Request $request)
    {
        $pesertaLaris = PesertaLari::orderBy('created_at', 'desc')->get();
        return response()->json([
            'message' => 'Export functionality',
            'total_data' => $pesertaLaris->count(),
            'max_registrations' => self::MAX_REGISTRATIONS,
            'remaining_slots' => self::MAX_REGISTRATIONS - $pesertaLaris->count()
        ]);
    }

    public function getStats()
    {
        $totalPeserta = PesertaLari::count();
        $pesertaPerKategori = PesertaLari::select('kategori_lari', DB::raw('count(*) as total'))->groupBy('kategori_lari')->get();
        $pesertaTerbaru = PesertaLari::latest()->take(10)->get();

        return response()->json([
            'total_peserta' => $totalPeserta,
            'max_registrations' => self::MAX_REGISTRATIONS,
            'remaining_slots' => self::MAX_REGISTRATIONS - $totalPeserta,
            'registration_open' => $totalPeserta < self::MAX_REGISTRATIONS,
            'percentage_full' => round(($totalPeserta / self::MAX_REGISTRATIONS) * 100, 2),
            'peserta_per_kategori' => $pesertaPerKategori,
            'peserta_terbaru' => $pesertaTerbaru
        ]);
    }

    public function checkRegistrationStatus()
    {
        $totalRegistrations = PesertaLari::count();
        $registrationOpen = $totalRegistrations < self::MAX_REGISTRATIONS;
        
        return response()->json([
            'registration_open' => $registrationOpen,
            'total_registrations' => $totalRegistrations,
            'max_registrations' => self::MAX_REGISTRATIONS,
            'remaining_slots' => self::MAX_REGISTRATIONS - $totalRegistrations,
            'percentage_full' => round(($totalRegistrations / self::MAX_REGISTRATIONS) * 100, 2),
            'message' => $registrationOpen ? "Pendaftaran masih terbuka" : "Pendaftaran ditutup"
        ]);
    }

    public function regenerateQR($id)
    {
        try {
            $peserta = PesertaLari::findOrFail($id);
            $namaFormatted = str_replace(' ', '_', $peserta->nama_lengkap);
            $qrUrl = "https://coaching.bayanevent.com/verify?email=" . urlencode($peserta->email) . "#" . $namaFormatted;
            $qrCodeUrl = $this->generateQRCodeURL($qrUrl);
            $qrCodePath = $this->downloadAndSaveQRCode($qrCodeUrl, $peserta->id);
            
            $peserta->update(['qr_code_path' => $qrCodePath, 'qr_url' => $qrUrl]);

            return response()->json(['success' => true, 'message' => 'QR Code regenerated', 'data' => ['qr_code_url' => $qrCodeUrl, 'qr_url' => $qrUrl]]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    public function getRegistrationLimit()
    {
        return response()->json(['max_registrations' => self::MAX_REGISTRATIONS, 'description' => 'Batas maksimal pendaftaran']);
    }

    public function showQRCode($id)
    {
        $peserta = PesertaLari::findOrFail($id);
        return view('qr-code', compact('peserta'));
    }

    public function downloadQR($id)
    {
        $peserta = PesertaLari::findOrFail($id);
        $qrPath = storage_path('app/public/' . $peserta->qr_code_path);
        
        if (file_exists($qrPath)) {
            return response()->download($qrPath, "qr-code-{$peserta->id}.png");
        }
        
        return abort(404, 'QR Code not found');
    }

    public function bulkSendQR(Request $request)
    {
        $pesertaIds = $request->input('peserta_ids', []);
        
        if (empty($pesertaIds)) {
            return response()->json(['success' => false, 'message' => 'Tidak ada peserta dipilih'], 400);
        }

        $results = [];
        $successful = 0;
        $failed = 0;

        foreach ($pesertaIds as $pesertaId) {
            try {
                $peserta = PesertaLari::findOrFail($pesertaId);
                $namaFormatted = str_replace(' ', '_', $peserta->nama_lengkap);
                $qrUrl = "https://coaching.bayanevent.com/verify?email=" . urlencode($peserta->email) . "#" . $namaFormatted;
                $qrCodeUrl = $this->generateQRCodeURL($qrUrl);
                $result = $this->sendQontakWhatsApp($peserta, $qrCodeUrl);
                
                if ($result['success']) {
                    $successful++;
                } else {
                    $failed++;
                }

                $results[] = ['peserta_id' => $pesertaId, 'nama' => $peserta->nama_lengkap, 'success' => $result['success'], 'message' => $result['message']];
                sleep(2);

            } catch (\Exception $e) {
                $failed++;
                $results[] = ['peserta_id' => $pesertaId, 'nama' => 'Unknown', 'success' => false, 'message' => 'Error: ' . $e->getMessage()];
            }
        }

        return response()->json([
            'success' => $successful > 0,
            'message' => "Bulk send completed. {$successful} berhasil, {$failed} gagal.",
            'summary' => ['total' => count($pesertaIds), 'successful' => $successful, 'failed' => $failed],
            'results' => $results
        ]);
    }
}