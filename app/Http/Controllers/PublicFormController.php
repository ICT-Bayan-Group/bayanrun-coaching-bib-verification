<?php

namespace App\Http\Controllers;

use App\Models\PesertaLari;
use App\Models\PesertaPreRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PublicFormController extends Controller
{
    // ===============================
    // PUBLIC FORM METHODS
    // ===============================

    /**
     * Tampilkan form pendaftaran event lari
     */
    public function index()
    {
        return view('public-form');
    }

    /**
     * Verifikasi nomor BIB
     */
    public function verifyBib(Request $request)
    {
        $validated = $request->validate([
            'nomor_bib' => 'required|string'
        ], [
            'nomor_bib.required' => 'Nomor BIB harus diisi'
        ]);

        try {
            $peserta = PesertaPreRegistered::findByBib($validated['nomor_bib']);
            
            if (!$peserta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor BIB tidak ditemukan. Pastikan nomor BIB yang Anda masukkan benar.'
                ], 404);
            }

            $sudahTerdaftar = PesertaLari::where('nomor_bib', $validated['nomor_bib'])->exists();
            
            if ($sudahTerdaftar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor BIB ini sudah terdaftar sebelumnya. Setiap BIB hanya dapat mendaftar satu kali.'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'BIB berhasil diverifikasi',
                'data' => [
                    'nomor_bib' => $peserta->nomor_bib,
                    'nama' => $peserta->nama,
                    'kategori_lari' => $peserta->kategori_lari,
                    'email' => $peserta->email,
                    'nomor_telepon' => $peserta->nomor_telepon
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('BIB verification error', [
                'nomor_bib' => $validated['nomor_bib'],
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memverifikasi BIB. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Proses pendaftaran event lari
     */
    public function store(Request $request)
    {
        $validated = $this->validateRegistrationData($request);
        
        try {
            DB::beginTransaction();

            // Verifikasi BIB
            $preRegistered = PesertaPreRegistered::findByBib($validated['nomor_bib']);
            if (!$preRegistered) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor BIB tidak valid atau tidak ditemukan dalam database.'
                ], 404);
            }

            // Buat peserta baru
            $peserta = $this->createPeserta($validated);
            
            // Generate dan simpan QR Code
            $qrCodeUrl = $this->processQRCode($peserta);
            
            // Mark pre-registered as registered
            $preRegistered->markAsRegistered();

            DB::commit();

            // Kirim WhatsApp notification
            $whatsappResult = $this->sendWhatsAppNotification($peserta, $qrCodeUrl);

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran Anda berhasil! Detail pendaftaran sudah kami kirimkan melalui WhatsApp.',
                'data' => [
                    'id' => $peserta->id,
                    'nomor_bib' => $peserta->nomor_bib,
                    'nama' => $peserta->nama_lengkap,
                    'kategori' => $peserta->kategori_lari,
                    'qr_code_url' => $qrCodeUrl,
                    'whatsapp_sent' => $whatsappResult['success'] ?? false,
                    'whatsapp_message' => $whatsappResult['message'] ?? 'Unknown status'
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration error', [
                'error' => $e->getMessage(),
                'request_data' => $validated ?? []
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'
            ], 500);
        }
    }

    // ===============================
    // QR CODE METHODS
    // ===============================

    /**
     * Process QR Code generation and saving
     */
    private function processQRCode($peserta)
    {
        $qrData = json_encode([
            'id' => $peserta->id,
            'bib' => $peserta->nomor_bib,
            'nama' => $peserta->nama_lengkap,
            'kategori' => $peserta->kategori_lari,
            'token' => $peserta->qr_token
        ]);

        $qrCodeUrl = $this->generateQRCodeURL($qrData);
        $qrCodePath = $this->downloadAndSaveQRCode($qrCodeUrl, $peserta->id);
        
        if ($qrCodePath) {
            $peserta->update(['qr_code_path' => $qrCodePath]);
        }

        return $qrCodeUrl;
    }

    /**
     * Generate QR Code URL
     */
    private function generateQRCodeURL($data)
    {
        $encodedData = urlencode($data);
        return "https://api.qrserver.com/v1/create-qr-code/?size=300x300&format=png&data={$encodedData}";
    }

    /**
     * Download dan simpan QR Code dengan retry mechanism
     */
    private function downloadAndSaveQRCode($qrCodeUrl, $pesertaId, $maxRetries = 3)
    {
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                Log::info("QR Code download attempt {$attempt}", [
                    'peserta_id' => $pesertaId,
                    'url' => $qrCodeUrl
                ]);

                $response = Http::timeout(15)->retry(2, 1000)->get($qrCodeUrl);
                
                if ($response->successful()) {
                    $qrCodePath = $this->saveQRCodeFile($response->body(), $pesertaId);
                    if ($qrCodePath) {
                        Log::info('QR Code saved successfully', [
                            'peserta_id' => $pesertaId,
                            'path' => $qrCodePath,
                            'attempts' => $attempt
                        ]);
                        return $qrCodePath;
                    }
                }

                if ($attempt < $maxRetries) {
                    sleep(1);
                }

            } catch (\Exception $e) {
                Log::error("QR Code download attempt {$attempt} failed", [
                    'peserta_id' => $pesertaId,
                    'error' => $e->getMessage()
                ]);
                
                if ($attempt < $maxRetries) {
                    sleep(1);
                }
            }
        }

        Log::error('QR Code download failed after all attempts', [
            'peserta_id' => $pesertaId,
            'total_attempts' => $maxRetries
        ]);

        return null;
    }

    /**
     * Save QR Code file to storage
     */
    private function saveQRCodeFile($fileContent, $pesertaId)
    {
        try {
            $qrDir = storage_path('app/public/qr-codes');
            if (!file_exists($qrDir)) {
                mkdir($qrDir, 0755, true);
            }

            $qrCodePath = 'qr-codes/' . $pesertaId . '.png';
            $fullPath = storage_path('app/public/' . $qrCodePath);
            
            file_put_contents($fullPath, $fileContent);
            
            if (file_exists($fullPath) && filesize($fullPath) > 0) {
                return $qrCodePath;
            }
        } catch (\Exception $e) {
            Log::error('Failed to save QR Code file', [
                'peserta_id' => $pesertaId,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    // ===============================
    // WHATSAPP METHODS
    // ===============================

    /**
     * Send WhatsApp notification dengan retry mechanism
     */
    private function sendWhatsAppNotification($peserta, $qrCodeUrl)
    {
        try {
            $config = $this->getWhatsAppConfig();
            if (!$config['valid']) {
                return ['success' => false, 'message' => 'WhatsApp configuration not found'];
            }

            $phoneNumber = $this->formatPhoneNumber($peserta->telepon);
            
            Log::info('Starting WhatsApp notification', [
                'peserta_id' => $peserta->id,
                'phone' => $phoneNumber,
                'nama' => $peserta->nama_lengkap
            ]);

            // Validate QR Code URL
            if (!$this->validateQRCodeUrl($qrCodeUrl)) {
                Log::error('QR Code URL validation failed', [
                    'peserta_id' => $peserta->id,
                    'qr_url' => $qrCodeUrl
                ]);
                return ['success' => false, 'message' => 'QR Code URL tidak valid'];
            }

            // Send text message first
            $textResult = $this->sendWhatsAppText($phoneNumber, $peserta);
            if (!$textResult['success']) {
                return $textResult;
            }

            // Wait before sending image
            sleep(3);

            // Send QR Code image
            $imageResult = $this->sendWhatsAppImage($phoneNumber, $qrCodeUrl, $peserta);

            return [
                'success' => $textResult['success'] && $imageResult['success'],
                'message' => $imageResult['success'] 
                    ? 'WhatsApp message and QR Code sent successfully'
                    : 'Text sent but QR Code failed: ' . $imageResult['message'],
                'text_success' => $textResult['success'],
                'image_success' => $imageResult['success']
            ];

        } catch (\Exception $e) {
            Log::error('WhatsApp notification error', [
                'peserta_id' => $peserta->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send WhatsApp text message with retry
     */
    private function sendWhatsAppText($phoneNumber, $peserta, $maxRetries = 3)
    {
        $config = $this->getWhatsAppConfig();
        $message = $this->prepareWhatsAppMessage($peserta);
        
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                Log::info("WhatsApp text attempt {$attempt}", [
                    'peserta_id' => $peserta->id,
                    'phone' => $phoneNumber
                ]);

                $response = Http::timeout(30)
                    ->retry(2, 1000)
                    ->withHeaders([
                        'Authorization' => $config['token'],
                        'Content-Type' => 'application/json',
                    ])
                    ->post($config['url'], [
                        'phone' => $phoneNumber,
                        'message' => $message,
                        'isGroup' => false
                    ]);

                Log::info("WhatsApp text response attempt {$attempt}", [
                    'peserta_id' => $peserta->id,
                    'status_code' => $response->status(),
                    'successful' => $response->successful()
                ]);

                if ($response->successful()) {
                    return [
                        'success' => true,
                        'message' => 'Text message sent successfully',
                        'attempts' => $attempt
                    ];
                }

                if ($attempt < $maxRetries) {
                    sleep(2);
                }

            } catch (\Exception $e) {
                Log::error("WhatsApp text attempt {$attempt} failed", [
                    'peserta_id' => $peserta->id,
                    'error' => $e->getMessage()
                ]);
                
                if ($attempt < $maxRetries) {
                    sleep(2);
                }
            }
        }

        return [
            'success' => false,
            'message' => "Failed to send text message after {$maxRetries} attempts"
        ];
    }

    /**
     * Send WhatsApp image with retry and fallback
     */
    private function sendWhatsAppImage($phoneNumber, $qrCodeUrl, $peserta, $maxRetries = 5)
    {
        $config = $this->getWhatsAppConfig();
        
        if (empty($config['image_url'])) {
            Log::error('WhatsApp image URL not configured', ['peserta_id' => $peserta->id]);
            return ['success' => false, 'message' => 'Image URL not configured'];
        }

        $caption = $this->prepareQRCodeCaption($peserta);

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                Log::info("WhatsApp image attempt {$attempt}", [
                    'peserta_id' => $peserta->id,
                    'phone' => $phoneNumber
                ]);

                // Progressive delay
                if ($attempt > 1) {
                    $delay = $attempt * 2;
                    Log::info("Waiting {$delay} seconds before retry", [
                        'peserta_id' => $peserta->id,
                        'attempt' => $attempt
                    ]);
                    sleep($delay);
                }

                $response = Http::timeout(45)
                    ->withHeaders([
                        'Authorization' => $config['token'],
                        'Content-Type' => 'application/json',
                    ])
                    ->post($config['image_url'], [
                        'phone' => $phoneNumber,
                        'image' => $qrCodeUrl,
                        'caption' => $caption
                    ]);

                Log::info("WhatsApp image response attempt {$attempt}", [
                    'peserta_id' => $peserta->id,
                    'status_code' => $response->status(),
                    'successful' => $response->successful()
                ]);

                if ($response->successful()) {
                    $responseData = $response->json();
                    if (isset($responseData['status']) && $responseData['status'] === true) {
                        Log::info('WhatsApp image sent successfully', [
                            'peserta_id' => $peserta->id,
                            'attempts' => $attempt
                        ]);
                        
                        return [
                            'success' => true,
                            'message' => 'QR Code image sent successfully',
                            'attempts' => $attempt
                        ];
                    }
                }

            } catch (\Exception $e) {
                Log::error("WhatsApp image attempt {$attempt} failed", [
                    'peserta_id' => $peserta->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // All attempts failed, try fallback
        Log::error('All WhatsApp image attempts failed, trying fallback', [
            'peserta_id' => $peserta->id,
            'total_attempts' => $maxRetries
        ]);

        return $this->sendQRCodeFallback($phoneNumber, $peserta);
    }

    /**
     * Send QR Code data as text fallback
     */
    private function sendQRCodeFallback($phoneNumber, $peserta)
    {
        try {
            $config = $this->getWhatsAppConfig();
            $fallbackMessage = $this->prepareFallbackMessage($peserta);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => $config['token'],
                    'Content-Type' => 'application/json',
                ])
                ->post($config['url'], [
                    'phone' => $phoneNumber,
                    'message' => $fallbackMessage,
                    'isGroup' => false
                ]);

            Log::info('QR Code fallback message sent', [
                'peserta_id' => $peserta->id,
                'successful' => $response->successful()
            ]);

            return [
                'success' => $response->successful(),
                'message' => $response->successful() 
                    ? 'Fallback QR Code data sent as text'
                    : 'Both image and fallback failed',
                'fallback_used' => true
            ];

        } catch (\Exception $e) {
            Log::error('QR Code fallback failed', [
                'peserta_id' => $peserta->id,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Both QR Code image and fallback failed',
                'fallback_used' => true
            ];
        }
    }

    // ===============================
    // UTILITY METHODS
    // ===============================

    /**
     * Validate registration form data
     */
    private function validateRegistrationData(Request $request)
    {
        return $request->validate([
            'nomor_bib' => 'required|string|unique:peserta_laris,nomor_bib',
            'nama_lengkap' => 'required|string|max:255',
            'kategori_lari' => 'required|string|max:100',
            'email' => 'required|email|unique:peserta_laris,email',
            'telepon' => 'required|string|max:20',
        ], [
            'nomor_bib.required' => 'Nomor BIB harus diisi',
            'nomor_bib.unique' => 'Nomor BIB sudah terdaftar',
            'nama_lengkap.required' => 'Nama lengkap harus diisi',
            'kategori_lari.required' => 'Kategori lari harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar, gunakan email lain',
            'telepon.required' => 'Nomor telepon harus diisi'
        ]);
    }

    /**
     * Create new peserta record
     */
    private function createPeserta($validated)
    {
        return PesertaLari::create([
            'nomor_bib' => $validated['nomor_bib'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'kategori_lari' => $validated['kategori_lari'],
            'email' => $validated['email'],
            'telepon' => $validated['telepon'],
            'qr_token' => Str::random(32),
            'status' => 'terdaftar'
        ]);
    }

    /**
     * Get WhatsApp configuration
     */
    private function getWhatsAppConfig()
    {
        $config = [
            'url' => config('services.wablas.url'),
            'token' => config('services.wablas.token'),
            'image_url' => config('services.wablas.image_url'),
            'valid' => false
        ];

        $config['valid'] = !empty($config['token']) && !empty($config['url']);

        return $config;
    }

    /**
     * Validate QR Code URL
     */
    private function validateQRCodeUrl($qrCodeUrl)
    {
        try {
            $response = Http::timeout(10)->head($qrCodeUrl);
            
            Log::info('QR Code URL validation', [
                'url' => $qrCodeUrl,
                'status_code' => $response->status(),
                'content_type' => $response->header('Content-Type')
            ]);
            
            return $response->successful() && 
                   str_contains($response->header('Content-Type', ''), 'image');
        } catch (\Exception $e) {
            Log::error('QR Code URL validation failed', [
                'url' => $qrCodeUrl,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Format phone number for WhatsApp
     */
    private function formatPhoneNumber($phone)
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phone);
        
        if (substr($phoneNumber, 0, 2) === '62') {
            $formatted = $phoneNumber;
        } elseif (substr($phoneNumber, 0, 1) === '0') {
            $formatted = '62' . substr($phoneNumber, 1);
        } elseif (substr($phoneNumber, 0, 1) === '8') {
            $formatted = '62' . $phoneNumber;
        } else {
            $formatted = '62' . $phoneNumber;
        }
        
        Log::info('Phone formatted', [
            'original' => $phone,
            'formatted' => $formatted
        ]);
        
        return $formatted;
    }

    // ===============================
    // MESSAGE PREPARATION METHODS
    // ===============================

    /**
     * Prepare WhatsApp message
     */
    private function prepareWhatsAppMessage($peserta)
    {
        return "🎉 BAYAN RUN 2025 - COACHING CLINIC BERHASIL! 🎉\n\n" .
               "Halo {$peserta->nama_lengkap}! 👋\n\n" .
               "Terima kasih telah mendaftar di Coaching Clinic Bayan Run 2025! 🏃‍♂️✨\n\n" .
               "📋 DETAIL PENDAFTARAN:\n" .
               "━━━━━━━━━━━━━━━━━━━━━━━━━\n" .
               "🎫 BIB: {$peserta->nomor_bib}\n" .
               "👤 Nama: {$peserta->nama_lengkap}\n" .
               "🏆 Kategori: {$peserta->kategori_lari}\n" .
               "📧 Email: {$peserta->email}\n" .
               "📱 No. HP: {$peserta->telepon}\n" .
               "━━━━━━━━━━━━━━━━━━━━━━━━━\n\n" .
               "📱 QR Code Anda akan dikirim sebagai gambar berikutnya sebagai bukti registrasi.\n\n" .
               "⚠️ PENTING:\n" .
               "• Simpan QR Code ini dengan baik\n" .
               "• Tunjukkan QR Code saat check-in event\n" .
               "• Datang 30 menit sebelum acara dimulai\n\n" .
               "🎯 Info lebih lanjut akan kami kirimkan menjelang event.\n\n" .
               "Pesan ini bersifat automatis dan tidak menerima balasan.\n\n" .
               "Sampai jumpa di coaching clinic! 🏃‍♂️🏃‍♀️\n\n" .
               "Salam Olahraga,\n" .
               "Tim Bayan Run 2025 🏃‍♂️✨";
    }

    /**
     * Prepare QR Code caption
     */
    private function prepareQRCodeCaption($peserta)
    {
        return "🎫 QR Code Pendaftaran Coaching Clinic Bayan Run 2025\n" .
               "📝 BIB: {$peserta->nomor_bib}\n" .
               "👤 Nama: {$peserta->nama_lengkap}\n" .
               "🏃‍♂️ Kategori: {$peserta->kategori_lari}\n\n" .
               "⚠️ Simpan QR Code ini untuk check-in event!";
    }

    /**
     * Prepare fallback message
     */
    private function prepareFallbackMessage($peserta)
    {
        return "🚨 QR CODE FALLBACK 🚨\n\n" .
               "Maaf, terjadi kendala teknis dalam mengirim QR Code sebagai gambar.\n\n" .
               "📋 DATA QR CODE ANDA:\n" .
               "━━━━━━━━━━━━━━━━━━━━━━━━━\n" .
               "🎫 BIB: {$peserta->nomor_bib}\n" .
               "👤 Nama: {$peserta->nama_lengkap}\n" .
               "🏆 Kategori: {$peserta->kategori_lari}\n" .
               "🔐 Token: {$peserta->qr_token}\n" .
               "━━━━━━━━━━━━━━━━━━━━━━━━━\n\n" .
               "📱 Anda dapat menggunakan data di atas untuk verifikasi manual saat check-in.\n\n" .
               "🔄 QR Code gambar akan dikirim ulang secepatnya.";
    }

    // ===============================
    // ADMIN & API METHODS
    // ===============================

    /**
     * Test WhatsApp connection
     */
    public function testWhatsApp(Request $request)
    {
        $phone = $request->input('phone', '6285377640809');
        
        try {
            $config = $this->getWhatsAppConfig();
            
            if (!$config['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wablas configuration not found'
                ]);
            }

            $formattedPhone = $this->formatPhoneNumber($phone);
            $testMessage = "🧪 Test pesan dari sistem Bayan Run 2025\n\n" .
                          "Jika Anda menerima pesan ini, maka konfigurasi WhatsApp sudah benar! ✅\n\n" .
                          "Waktu test: " . now()->format('d/m/Y H:i:s');

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => $config['token'],
                    'Content-Type' => 'application/json',
                ])
                ->post($config['url'], [
                    'phone' => $formattedPhone,
                    'message' => $testMessage,
                    'isGroup' => false
                ]);

            return response()->json([
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'formatted_phone' => $formattedPhone,
                'message' => $response->successful() 
                    ? 'Test message sent successfully!' 
                    : 'Failed to send test message'
            ]);

        } catch (\Exception $e) {
            Log::error('WhatsApp test error', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retry QR Code sending manually
     */
    public function retryQRCode($pesertaId)
    {
        try {
            $peserta = PesertaLari::findOrFail($pesertaId);
            
            Log::info('Manual QR Code retry initiated', [
                'peserta_id' => $pesertaId,
                'nama' => $peserta->nama_lengkap
            ]);
            
            $qrData = json_encode([
                'id' => $peserta->id,
                'bib' => $peserta->nomor_bib,
                'nama' => $peserta->nama_lengkap,
                'kategori' => $peserta->kategori_lari,
                'token' => $peserta->qr_token
            ]);

            $qrCodeUrl = $this->generateQRCodeURL($qrData);
            $phoneNumber = $this->formatPhoneNumber($peserta->telepon);
            
            $imageResult = $this->sendWhatsAppImage($phoneNumber, $qrCodeUrl, $peserta);
            
            return response()->json([
                'success' => $imageResult['success'],
                'message' => $imageResult['message'],
                'data' => $imageResult
            ]);

        } catch (\Exception $e) {
            Log::error('Manual QR Code retry failed', [
                'peserta_id' => $pesertaId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retry QR Code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show registrations (admin)
     */
    public function showRegistrations()
    {
        $pesertaLaris = PesertaLari::orderBy('created_at', 'desc')->get();
        return view('registrations.index', compact('pesertaLaris'));
    }

    /**
     * Show registration detail (admin)
     */
    public function showDetail($id)
    {
        $peserta = PesertaLari::findOrFail($id);
        return view('registrations.detail', compact('peserta'));
    }

    /**
     * Verifikasi QR Code
     */
    public function verifyQR($token)
    {
        $peserta = PesertaLari::where('qr_token', $token)->first();
        
        if (!$peserta) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $peserta->id,
                'nama' => $peserta->nama_lengkap,
                'kategori' => $peserta->kategori_lari,
                'status' => $peserta->status,
                'waktu_daftar' => $peserta->created_at->format('d/m/Y H:i')
            ]
        ]);
    }

    /**
     * Export data ke Excel/CSV
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $pesertaLaris = PesertaLari::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'message' => 'Export functionality untuk peserta event lari',
            'total_data' => $pesertaLaris->count()
        ]);
    }

    /**
     * API untuk mendapatkan statistik pendaftaran
     */
    public function getStats()
    {
        $totalPeserta = PesertaLari::count();
        
        $pesertaPerKategori = PesertaLari::select('kategori_lari', DB::raw('count(*) as total'))
            ->groupBy('kategori_lari')
            ->get();

        $pesertaTerbaru = PesertaLari::latest()->take(10)->get();

        return response()->json([
            'total_peserta' => $totalPeserta,
            'peserta_per_kategori' => $pesertaPerKategori,
            'peserta_terbaru' => $pesertaTerbaru
        ]);
    }

    // Duplicate method removed: downloadAndSaveQRCode
    /**
     * Regenerate QR Code untuk peserta tertentu
     */
    public function regenerateQR($id)
    {
        try {
            $peserta = PesertaLari::findOrFail($id);
            
            $qrData = json_encode([
                'id' => $peserta->id,
                'nama' => $peserta->nama_lengkap,
                'kategori' => $peserta->kategori_lari,
                'token' => $peserta->qr_token
            ]);

            $qrCodeUrl = $this->generateQRCodeURL($qrData);
            $qrCodePath = $this->downloadAndSaveQRCode($qrCodeUrl, $peserta->id);
            
            $peserta->update(['qr_code_path' => $qrCodePath]);

            return response()->json([
                'success' => true,
                'message' => 'QR Code berhasil di-generate ulang',
                'qr_code_url' => $qrCodeUrl
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal me-regenerate QR Code: ' . $e->getMessage()
            ], 500);
        }
    }
}
