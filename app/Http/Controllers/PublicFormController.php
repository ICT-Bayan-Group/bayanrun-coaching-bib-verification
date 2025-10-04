<?php

namespace App\Http\Controllers;

use App\Models\PesertaLari;
use App\Models\PesertaPreRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // FIXED: Added missing import
use Illuminate\Support\Facades\Http; // FIXED: Added missing import
use Illuminate\Support\Str;

class PublicFormController extends Controller
{
    // Maximum allowed registrations
    private const MAX_REGISTRATIONS = 600; // FIXED: Changed from 3 to 600

    // ===============================
    // PUBLIC FORM METHODS
    // ===============================

    /**
     * Tampilkan form pendaftaran event lari
     */
    public function index()
    {
        // Check if registration is still open
        $totalRegistrations = PesertaLari::count();
        $registrationOpen = $totalRegistrations < self::MAX_REGISTRATIONS;
        
        return view('public-form', compact('registrationOpen', 'totalRegistrations'));
    }

    /**
     * Verifikasi Email
     */
    public function verifyEmail(Request $request)
    {
        // Check registration limit first
        $totalRegistrations = PesertaLari::count();
        if ($totalRegistrations >= self::MAX_REGISTRATIONS) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf, pendaftaran sudah ditutup. Batas maksimal 600 peserta telah tercapai.'
            ], 422);
        }

        $validated = $request->validate([
            'email' => 'required|string|email'
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid'
        ]);

        try {
            $peserta = PesertaPreRegistered::where('email', $validated['email'])->first(); // FIXED: Changed from findByEmail method

            if (!$peserta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email tidak ditemukan. Pastikan email yang Anda masukkan benar.'
                ], 404);
            }

            $sudahTerdaftar = PesertaLari::where('email', $validated['email'])->exists();

            if ($sudahTerdaftar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email ini sudah terdaftar sebelumnya. Setiap email hanya dapat mendaftar satu kali.'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Email berhasil diverifikasi',
                'data' => [
                    'email' => $peserta->email,
                    'nama' => $peserta->nama,
                    'kategori_lari' => $peserta->kategori_lari,
                    'nomor_telepon' => $peserta->nomor_telepon,
                    'remaining_slots' => self::MAX_REGISTRATIONS - $totalRegistrations
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Email verification error', [
                'email' => $validated['email'],
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memverifikasi Email. Silakan coba lagi.'
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

            // Double check registration limit in transaction
            $totalRegistrations = PesertaLari::count();
            if ($totalRegistrations >= self::MAX_REGISTRATIONS) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Maaf, pendaftaran sudah ditutup. Batas maksimal 600 peserta telah tercapai.'
                ], 422);
            }

            // Verifikasi Email
            $preRegistered = PesertaPreRegistered::where('email', $validated['email'])->first(); // FIXED: Changed from findByEmail method
            if (!$preRegistered) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Email tidak valid atau tidak ditemukan dalam database.'
                ], 404);
            }

            // Buat peserta baru
            $peserta = $this->createPeserta($validated, $preRegistered);
            
            // Generate dan simpan QR Code
            $qrCodeUrl = $this->processQRCode($peserta);
            
            // Mark pre-registered as registered (if method exists)
            if (method_exists($preRegistered, 'markAsRegistered')) {
                $preRegistered->markAsRegistered();
            }

            DB::commit();

            // Kirim WhatsApp notification dengan urutan: QR Code dahulu, kemudian message
            $whatsappResult = $this->sendWhatsAppNotificationNewOrder($peserta, $qrCodeUrl);

            // Calculate remaining slots
            $remainingSlots = self::MAX_REGISTRATIONS - PesertaLari::count();

            return response()->json([
                'success' => true,
                'message' => 'Pendaftaran Anda berhasil! QR Code dan detail pendaftaran sudah kami kirimkan melalui WhatsApp.',
                'data' => [
                    'id' => $peserta->id,
                    'nama' => $peserta->nama_lengkap,
                    'kategori' => $peserta->kategori_lari,
                    'email' => $peserta->email, // FIXED: Added missing email field
                    'qr_code_url' => $qrCodeUrl,
                    'whatsapp_sent' => $whatsappResult['success'] ?? false,
                    'whatsapp_message' => $whatsappResult['message'] ?? 'Unknown status',
                    'remaining_slots' => $remainingSlots,
                    'registration_number' => PesertaLari::count()
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
        // Format nama untuk URL (replace spasi dengan underscore)
        $namaFormatted = str_replace(' ', '_', $peserta->nama_lengkap);
        
        // Buat URL dengan format yang diinginkan - MENGGUNAKAN EMAIL
        $qrUrl = "https://coaching.bayanevent.com/verify?email=" . urlencode($peserta->email) 
               . "#" . $namaFormatted;

        $qrCodeUrl = $this->generateQRCodeURL($qrUrl);
        $qrCodePath = $this->downloadAndSaveQRCode($qrCodeUrl, $peserta->id);
        
        if ($qrCodePath) {
            $peserta->update([
                'qr_code_path' => $qrCodePath,
                'qr_url' => $qrUrl // Simpan URL yang di-encode di QR
            ]);
        }

        return $qrCodeUrl;
    }

    /**
     * Generate QR Code URL
     */
    private function generateQRCodeURL($data)
    {
        $encodedData = urlencode($data);
        return "https://api.qrserver.com/v1/create-qr-code/?size=300x300&format=png&margin=20&data={$encodedData}";
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
    // QR CODE VERIFICATION METHODS
    // ===============================

    /**
     * Verifikasi QR Code - MENGGUNAKAN EMAIL
     */
    public function verifyQR(Request $request)
    {
        $email = $request->input('email');

        try {
            $peserta = PesertaLari::where('email', $email)->first();
            
            if (!$peserta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email tidak ditemukan dalam database'
                ], 404);
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
            Log::error('QR Code verification error', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memverifikasi QR Code'
            ], 500);
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
            'nama_lengkap' => 'required|string|max:255',
            'kategori_lari' => 'required|string|max:100',
            'email' => 'required|email|unique:peserta_laris,email',
            'telepon' => 'required|string|max:20',
        ], [
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
    private function createPeserta($validated, $preRegistered)
    {
        return PesertaLari::create([
            'email' => $validated['email'], // FIXED: Use validated email instead of pre-registered
            'nama_lengkap' => $validated['nama_lengkap'],
            'kategori_lari' => $validated['kategori_lari'],
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
    // WHATSAPP METHODS - NEW ORDER
    // ===============================

    /**
     * Send WhatsApp notification dengan urutan baru: QR Code dahulu, kemudian message
     */
    private function sendWhatsAppNotificationNewOrder($peserta, $qrCodeUrl)
    {
        try {
            $config = $this->getWhatsAppConfig();
            if (!$config['valid']) {
                return ['success' => false, 'message' => 'WhatsApp configuration not found'];
            }

            $phoneNumber = $this->formatPhoneNumber($peserta->telepon);
            
            Log::info('Starting WhatsApp notification (QR first)', [
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

            // STEP 1: Send QR Code image first
            $imageResult = $this->sendWhatsAppImage($phoneNumber, $qrCodeUrl, $peserta);
            
            if (!$imageResult['success']) {
                Log::warning('QR Code image failed, trying fallback', [
                    'peserta_id' => $peserta->id
                ]);
                // Try fallback but continue with text message
            }

            // Wait before sending text message
            sleep(5);

            // STEP 2: Send text message after QR Code
            $textResult = $this->sendWhatsAppTextAfterQR($phoneNumber, $peserta);

            return [
                'success' => $imageResult['success'] && $textResult['success'],
                'message' => $this->formatWhatsAppResult($imageResult, $textResult),
                'image_success' => $imageResult['success'],
                'text_success' => $textResult['success'],
                'order' => 'qr_first_then_message'
            ];

        } catch (\Exception $e) {
            Log::error('WhatsApp notification error (new order)', [
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
     * Format WhatsApp result message
     */
    private function formatWhatsAppResult($imageResult, $textResult)
    {
        if ($imageResult['success'] && $textResult['success']) {
            return 'QR Code dan pesan coaching clinic berhasil dikirim';
        } elseif ($imageResult['success'] && !$textResult['success']) {
            return 'QR Code berhasil dikirim, pesan coaching clinic gagal: ' . $textResult['message'];
        } elseif (!$imageResult['success'] && $textResult['success']) {
            return 'Pesan coaching clinic berhasil dikirim, QR Code gagal: ' . $imageResult['message'];
        } else {
            return 'QR Code dan pesan coaching clinic gagal dikirim';
        }
    }

    /**
     * Send WhatsApp text message after QR Code
     */
    private function sendWhatsAppTextAfterQR($phoneNumber, $peserta, $maxRetries = 3)
    {
        $config = $this->getWhatsAppConfig();
        $message = $this->prepareCoachingClinicSuccessMessage($peserta);
        
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                Log::info("WhatsApp success message attempt {$attempt}", [
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

                Log::info("WhatsApp success message response attempt {$attempt}", [
                    'peserta_id' => $peserta->id,
                    'status_code' => $response->status(),
                    'successful' => $response->successful()
                ]);

                if ($response->successful()) {
                    return [
                        'success' => true,
                        'message' => 'Coaching clinic success message sent successfully',
                        'attempts' => $attempt
                    ];
                }

                if ($attempt < $maxRetries) {
                    sleep(2);
                }

            } catch (\Exception $e) {
                Log::error("WhatsApp success message attempt {$attempt} failed", [
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
            'message' => "Failed to send coaching clinic success message after {$maxRetries} attempts"
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
    // MESSAGE PREPARATION METHODS - UPDATED
    // ===============================

    /**
     * Prepare coaching clinic success message (sent after QR Code)
     */
    private function prepareCoachingClinicSuccessMessage($peserta)
    {
        $remainingSlots = self::MAX_REGISTRATIONS - PesertaLari::count();
        $registrationNumber = PesertaLari::count();
        
        return "🎉 COACHING CLINIC BAYAN RUN 2025 BERHASIL! 🎉\n\n" .
               "Selamat {$peserta->nama_lengkap}! 👋\n\n" .
               "Pendaftaran Anda di Coaching Clinic Bayan Run 2025 telah berhasil! 🏃‍♂️✨\n\n" .
               "📋 DETAIL PENDAFTARAN:\n" .
               "━━━━━━━━━━━━━━━━━━━━━━━━━\n" .
               "👤 Nama: {$peserta->nama_lengkap}\n" .
               "🏆 Kategori: {$peserta->kategori_lari}\n" .
               "📧 Email: {$peserta->email}\n" .
               "📱 No. HP: {$peserta->telepon}\n" .
               "🔢 Peserta ke: {$registrationNumber}/600\n" .
               "━━━━━━━━━━━━━━━━━━━━━━━━━\n\n" .
               "📱 QR Code telah dikirim sebelumnya sebagai bukti registrasi Anda.\n\n" .
               "⚠️ PENTING:\n" .
               "• Simpan QR Code dengan baik\n" .
               "• Tunjukkan QR Code saat check-in event\n" .
               "• QR ini adalah tiket masuk Anda\n\n" .
               "📅 Tanggal Coaching Clinic: 11 Oktober 2025\n" .
               "🕒 Waktu: 15:00 - 17:00 WITA\n" .
               "📍 Lokasi: Gedung Kesenian Balikpapan, Kalimantan Timur, Indonesia\n\n" .
               "Terima kasih telah bergabung! Sampai jumpa di coaching clinic! 🏃‍♂️🏃‍♀️\n\n" .
               "Salam Olahraga,\n" .
               "Tim Bayan Run 2025 🏃‍♂️✨";
    }

    /**
     * Prepare QR Code caption (sent first)
     */
    private function prepareQRCodeCaption($peserta)
    {
        return "🎫 QR CODE COACHING CLINIC BAYAN RUN 2025\n\n" .
               "Halo {$peserta->nama_lengkap}! 👋\n\n" .
               "Ini adalah QR Code pendaftaran Coaching Clinic Bayan Run 2025 Anda:\n" .
               "🏃‍♂️ Kategori: {$peserta->kategori_lari}\n\n" .
               "🔒 SIMPAN QR CODE INI DENGAN BAIK!\n" .
               "Ini adalah tiket masuk Anda ke coaching clinic.\n\n" .
               "Detail lengkap akan dikirim pada pesan berikutnya... 📩";
    }

    /**
     * Prepare fallback message - MENGGUNAKAN EMAIL
     */
    private function prepareFallbackMessage($peserta)
    {
        return "🚨 QR CODE FALLBACK - COACHING CLINIC BAYAN RUN 2025 🚨\n\n" .
               "Halo {$peserta->nama_lengkap}! 👋\n\n" .
               "Maaf, terjadi kendala teknis dalam mengirim QR Code sebagai gambar.\n\n" .
               "📋 DATA VERIFIKASI ANDA:\n" .
               "━━━━━━━━━━━━━━━━━━━━━━━━━\n" .
               "👤 Nama: {$peserta->nama_lengkap}\n" .
               "🏆 Kategori: {$peserta->kategori_lari}\n" .
               "📧 Email: {$peserta->email}\n" .
               "━━━━━━━━━━━━━━━━━━━━━━━━━\n\n" .
               "📱 Link verifikasi:\n" .
               "https://coaching.bayanevent.com/verify?email=" . urlencode($peserta->email) . "#" . str_replace(' ', '_', $peserta->nama_lengkap) . "\n\n" .
               "📱 Anda dapat menggunakan link di atas untuk verifikasi.\n\n" .
               "🔄 QR Code gambar akan dikirim ulang secepatnya.\n\n" .
               "Detail coaching clinic akan dikirim pada pesan berikutnya...";
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
            
            // Generate QR dengan format URL baru - MENGGUNAKAN EMAIL
            $namaFormatted = str_replace(' ', '_', $peserta->nama_lengkap);
            $qrUrl = "https://coaching.bayanevent.com/verify?email=" . urlencode($peserta->email) 
                   . "#" . $namaFormatted;

            $qrCodeUrl = $this->generateQRCodeURL($qrUrl);
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
        $totalRegistrations = $pesertaLaris->count();
        $remainingSlots = self::MAX_REGISTRATIONS - $totalRegistrations;
        $registrationOpen = $totalRegistrations < self::MAX_REGISTRATIONS;
        
        return view('registrations.index', compact('pesertaLaris', 'totalRegistrations', 'remainingSlots', 'registrationOpen'));
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
     * Export data ke Excel/CSV
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        $pesertaLaris = PesertaLari::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'message' => 'Export functionality untuk peserta event lari',
            'total_data' => $pesertaLaris->count(),
            'max_registrations' => self::MAX_REGISTRATIONS,
            'remaining_slots' => self::MAX_REGISTRATIONS - $pesertaLaris->count()
        ]);
    }

    /**
     * API untuk mendapatkan statistik pendaftaran
     */
    public function getStats()
    {
        $totalPeserta = PesertaLari::count();
        $remainingSlots = self::MAX_REGISTRATIONS - $totalPeserta;
        $registrationOpen = $totalPeserta < self::MAX_REGISTRATIONS;
        
        $pesertaPerKategori = PesertaLari::select('kategori_lari', DB::raw('count(*) as total'))
            ->groupBy('kategori_lari')
            ->get();

        $pesertaTerbaru = PesertaLari::latest()->take(10)->get();

        return response()->json([
            'total_peserta' => $totalPeserta,
            'max_registrations' => self::MAX_REGISTRATIONS,
            'remaining_slots' => $remainingSlots,
            'registration_open' => $registrationOpen,
            'percentage_full' => round(($totalPeserta / self::MAX_REGISTRATIONS) * 100, 2),
            'peserta_per_kategori' => $pesertaPerKategori,
            'peserta_terbaru' => $pesertaTerbaru
        ]);
    }

    /**
     * Check registration status
     */
    public function checkRegistrationStatus()
    {
        $totalRegistrations = PesertaLari::count();
        $remainingSlots = self::MAX_REGISTRATIONS - $totalRegistrations;
        $registrationOpen = $totalRegistrations < self::MAX_REGISTRATIONS;
        
        return response()->json([
            'registration_open' => $registrationOpen,
            'total_registrations' => $totalRegistrations,
            'max_registrations' => self::MAX_REGISTRATIONS,
            'remaining_slots' => $remainingSlots,
            'percentage_full' => round(($totalRegistrations / self::MAX_REGISTRATIONS) * 100, 2),
            'message' => $registrationOpen 
                ? "Pendaftaran masih terbuka. {$remainingSlots} slot tersisa."
                : "Pendaftaran telah ditutup. Batas maksimal 600 peserta telah tercapai."
        ]);
    }

    /**
     * Regenerate QR Code untuk peserta tertentu - MENGGUNAKAN EMAIL
     */
    public function regenerateQR($id)
    {
        try {
            $peserta = PesertaLari::findOrFail($id);
            
            // Format nama untuk URL
            $namaFormatted = str_replace(' ', '_', $peserta->nama_lengkap);
            
            // Buat URL dengan format baru - MENGGUNAKAN EMAIL
            $qrUrl = "https://coaching.bayanevent.com/verify?email=" . urlencode($peserta->email)
                   . "#" . $namaFormatted;

            $qrCodeUrl = $this->generateQRCodeURL($qrUrl);
            $qrCodePath = $this->downloadAndSaveQRCode($qrCodeUrl, $peserta->id);
            
            $peserta->update([
                'qr_code_path' => $qrCodePath,
                'qr_url' => $qrUrl
            ]);

            return response()->json([
                'success' => true,
                'message' => 'QR Code berhasil di-generate ulang',
                'data' => [
                    'qr_code_url' => $qrCodeUrl,
                    'qr_url' => $qrUrl
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal me-regenerate QR Code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get registration limit info
     */
    public function getRegistrationLimit()
    {
        return response()->json([
            'max_registrations' => self::MAX_REGISTRATIONS,
            'description' => 'Batas maksimal pendaftaran coaching clinic'
        ]);
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

    /**
     * Bulk send QR codes (admin function)
     */
    public function bulkSendQR(Request $request)
    {
        $pesertaIds = $request->input('peserta_ids', []);
        
        if (empty($pesertaIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada peserta yang dipilih'
            ], 400);
        }

        $results = [];
        $successful = 0;
        $failed = 0;

        foreach ($pesertaIds as $pesertaId) {
            try {
                $peserta = PesertaLari::findOrFail($pesertaId);
                
                $namaFormatted = str_replace(' ', '_', $peserta->nama_lengkap);
                $qrUrl = "https://coaching.bayanevent.com/verify?email=" . urlencode($peserta->email)
                       . "#" . $namaFormatted;

                $qrCodeUrl = $this->generateQRCodeURL($qrUrl);
                $phoneNumber = $this->formatPhoneNumber($peserta->telepon);
                
                $whatsappResult = $this->sendWhatsAppNotificationNewOrder($peserta, $qrCodeUrl);
                
                if ($whatsappResult['success']) {
                    $successful++;
                } else {
                    $failed++;
                }

                $results[] = [
                    'peserta_id' => $pesertaId,
                    'nama' => $peserta->nama_lengkap,
                    'success' => $whatsappResult['success'],
                    'message' => $whatsappResult['message']
                ];

                // Delay between sends to avoid rate limiting
                sleep(2);

            } catch (\Exception $e) {
                $failed++;
                $results[] = [
                    'peserta_id' => $pesertaId,
                    'nama' => 'Unknown',
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => $successful > 0,
            'message' => "Bulk send completed. {$successful} berhasil, {$failed} gagal.",
            'summary' => [
                'total' => count($pesertaIds),
                'successful' => $successful,
                'failed' => $failed
            ],
            'results' => $results
        ]);
    }
}