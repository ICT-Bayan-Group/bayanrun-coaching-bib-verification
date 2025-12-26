<?php

namespace App\Services;

use App\Models\PesertaLari;
use Illuminate\Support\Facades\{Http, Log};
use Illuminate\Http\JsonResponse;

class WhatsAppService
{
    private const MAX_RETRIES = 3;
    private const RETRY_DELAY_MULTIPLIER = 2;
    private const TIMEOUT_SECONDS = 45;
    private const BULK_DELAY_SECONDS = 2;

    private array $config;

    public function __construct()
    {
        $this->config = $this->loadConfig();
    }

    public function sendRegistrationConfirmation(PesertaLari $peserta): array
    {
        if (!$this->isConfigValid()) {
            return $this->failureResponse('Konfigurasi WhatsApp tidak ditemukan');
        }

        $phoneNumber = $this->formatPhoneNumber($peserta->telepon);
        $registrationNumber = $this->getRegistrationNumber($peserta);

        Log::info('Sending WhatsApp confirmation', [
            'peserta_id' => $peserta->id,
            'phone' => $phoneNumber
        ]);

        if (!empty($this->config['template_id'])) {
            return $this->sendWithTemplate($phoneNumber, $peserta, $registrationNumber);
        }

        return $this->sendDirectMessage($phoneNumber, $peserta, $registrationNumber);
    }

    public function sendTestMessage(string $phone): JsonResponse
    {
        if (!$this->isConfigValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi WhatsApp tidak valid',
                'debug' => [
                    'token_exists' => !empty($this->config['token']),
                    'channel_exists' => !empty($this->config['channel_integration_id'])
                ]
            ], 400);
        }

        $formattedPhone = $this->formatPhoneNumber($phone);
        $testMessage = $this->prepareTestMessage($formattedPhone);

        $payload = [
            'to_number' => $formattedPhone,
            'to_name' => 'Test User',
            'channel_integration_id' => $this->config['channel_integration_id'],
            'message_type' => 'text',
            'text' => $testMessage
        ];

        try {
            $response = $this->sendRequest($payload);
            
            return response()->json([
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'formatted_phone' => $formattedPhone,
                'response' => $response->json(),
                'message' => $response->successful() ? 'Test berhasil dikirim!' : 'Test gagal'
            ]);
        } catch (\Exception $e) {
            Log::error('Test WhatsApp error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendBulk(array $pesertaIds): array
    {
        $results = ['successful' => 0, 'failed' => 0, 'details' => []];

        foreach ($pesertaIds as $pesertaId) {
            try {
                $peserta = PesertaLari::findOrFail($pesertaId);
                $result = $this->sendRegistrationConfirmation($peserta);

                $results['details'][] = [
                    'peserta_id' => $pesertaId,
                    'nama' => $peserta->nama_lengkap,
                    'success' => $result['success'],
                    'message' => $result['message']
                ];

                $result['success'] ? $results['successful']++ : $results['failed']++;
                
                sleep(self::BULK_DELAY_SECONDS);
            } catch (\Exception $e) {
                $results['failed']++;
                $results['details'][] = [
                    'peserta_id' => $pesertaId,
                    'nama' => 'Unknown',
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ];
            }
        }

        $results['summary'] = [
            'total' => count($pesertaIds),
            'successful' => $results['successful'],
            'failed' => $results['failed']
        ];

        return $results;
    }

    // ==================== PRIVATE METHODS ====================

    private function loadConfig(): array
    {
        $config = [
            'url' => config('services.qontak.url', 'https://service-chat.qontak.com/api/open/v1/broadcasts/whatsapp/direct'),
            'token' => config('services.qontak.token'),
            'channel_integration_id' => config('services.qontak.channel_integration_id'),
            'template_id' => config('services.qontak.template_id'),
        ];

        $config['valid'] = !empty($config['token']) && !empty($config['channel_integration_id']);

        return $config;
    }

    private function isConfigValid(): bool
    {
        return $this->config['valid'] ?? false;
    }

    private function sendWithTemplate(string $phoneNumber, PesertaLari $peserta, int $registrationNumber): array
    {
        $payload = [
            'to_number' => $phoneNumber,
            'to_name' => $peserta->nama_lengkap,
            'message_template_id' => $this->config['template_id'],
            'channel_integration_id' => $this->config['channel_integration_id'],
            'language' => ['code' => 'id'],
            'parameters' => [
                'body' => $this->prepareTemplateParameters($peserta, $registrationNumber)
            ]
        ];

        try {
            Log::info('Sending template message', [
                'peserta_id' => $peserta->id,
                'template_id' => $this->config['template_id']
            ]);

            $response = $this->sendRequest($payload);

            if ($this->isSuccessfulResponse($response)) {
                return $this->successResponse('WhatsApp berhasil dikirim via template', $response->json());
            }

            Log::error('Template message failed', [
                'peserta_id' => $peserta->id,
                'response' => $response->json()
            ]);

            return $this->failureResponse('Template gagal: ' . ($response->json()['message'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            Log::error('Template exception', [
                'peserta_id' => $peserta->id,
                'error' => $e->getMessage()
            ]);
            return $this->failureResponse('Exception: ' . $e->getMessage());
        }
    }

    private function sendDirectMessage(string $phoneNumber, PesertaLari $peserta, int $registrationNumber): array
    {
        $messageText = $this->prepareDirectMessage($peserta, $registrationNumber);

        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            try {
                if ($attempt > 1) {
                    sleep($attempt * self::RETRY_DELAY_MULTIPLIER);
                }

                $payload = [
                    'to_number' => $phoneNumber,
                    'to_name' => $peserta->nama_lengkap,
                    'channel_integration_id' => $this->config['channel_integration_id'],
                    'message_type' => 'text',
                    'text' => $messageText
                ];

                Log::info("Direct message attempt {$attempt}", [
                    'peserta_id' => $peserta->id
                ]);

                $response = $this->sendRequest($payload);

                if ($this->isSuccessfulResponse($response)) {
                    return $this->successResponse(
                        'WhatsApp berhasil dikirim (direct message)',
                        $response->json(),
                        $attempt
                    );
                }

                Log::warning("Attempt {$attempt} failed", [
                    'peserta_id' => $peserta->id,
                    'status' => $response->status()
                ]);
            } catch (\Exception $e) {
                Log::error("Attempt {$attempt} exception", [
                    'peserta_id' => $peserta->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $this->failureResponse("Gagal mengirim WhatsApp setelah " . self::MAX_RETRIES . " percobaan");
    }

    private function sendRequest(array $payload)
    {
        return Http::timeout(self::TIMEOUT_SECONDS)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->config['token'],
                'Content-Type' => 'application/json'
            ])
            ->post($this->config['url'], $payload);
    }

    private function isSuccessfulResponse($response): bool
    {
        if (!$response->successful()) {
            return false;
        }

        $responseData = $response->json();

        if (isset($responseData['status']) && 
            in_array(strtolower($responseData['status']), ['success', 'pending', 'sent'], true)) {
            return true;
        }

        if (isset($responseData['success']) && $responseData['success'] === true) {
            return true;
        }

        return false;
    }

    private function formatPhoneNumber(string $phone): string
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phoneNumber, '62')) {
            return $phoneNumber;
        }

        if (str_starts_with($phoneNumber, '0')) {
            return '62' . substr($phoneNumber, 1);
        }

        return '62' . $phoneNumber;
    }

    private function getRegistrationNumber(PesertaLari $peserta): int
    {
        return PesertaLari::where('created_at', '<=', $peserta->created_at)->count();
    }

    private function prepareTemplateParameters(PesertaLari $peserta, int $registrationNumber): array
    {
        return [
            ['key' => '1', 'value' => 'nama_lengkap', 'value_text' => $peserta->nama_lengkap],
            ['key' => '2', 'value' => 'nama_lengkap', 'value_text' => $peserta->nama_lengkap],
            ['key' => '3', 'value' => 'kategori', 'value_text' => $peserta->kategori_lari],
            ['key' => '4', 'value' => 'email', 'value_text' => $peserta->email],
            ['key' => '5', 'value' => 'telepon', 'value_text' => $peserta->telepon],
            ['key' => '6', 'value' => 'nomor_urut', 'value_text' => (string)$registrationNumber]
        ];
    }

    private function prepareDirectMessage(PesertaLari $peserta, int $registrationNumber): string
    {
        return "🎫 *KONFIRMASI PENDAFTARAN COACHING CLINIC*\n" .
               "*Bayan Run 2025*\n\n" .
               "Halo *{$peserta->nama_lengkap}*! 👋\n\n" .
               "Pendaftaran Anda telah *BERHASIL*! 🎉\n\n" .
               "━━━━━━━━━━━━━━━━━━━━━━\n" .
               "📋 *DETAIL PENDAFTARAN:*\n" .
               "━━━━━━━━━━━━━━━━━━━━━━\n" .
               "👤 Nama: {$peserta->nama_lengkap}\n" .
               "🏃 Kategori: {$peserta->kategori_lari}\n" .
               "📧 Email: {$peserta->email}\n" .
               "📱 Telepon: {$peserta->telepon}\n" .
               "🔢 Peserta ke: *{$registrationNumber}/600*\n" .
               "━━━━━━━━━━━━━━━━━━━━━━\n\n" .
               "⚠️ *PENTING:*\n" .
               "• Simpan pesan ini sebagai bukti pendaftaran\n" .
               "• Screenshot QR Code dari website\n" .
               "• Tunjukkan saat check-in\n\n" .
               "📅 *JADWAL ACARA:*\n" .
               "📆 Tanggal: 11 Oktober 2025\n" .
               "🕒 Waktu: 15:00 - 17:00 WITA\n" .
               "📍 Lokasi: Gedung Kesenian Balikpapan\n\n" .
               "Terima kasih atas partisipasi Anda! 🏃‍♂️🏃‍♀️\n\n" .
               "Salam Olahraga,\n" .
               "*Tim Bayan Run 2025* 🏃‍♂️✨";
    }

    private function prepareTestMessage(string $phone): string
    {
        return "🧪 *TEST MESSAGE*\n\n" .
               "Halo! Ini adalah test message dari sistem Bayan Run 2025.\n\n" .
               "✅ Konfigurasi WhatsApp berhasil!\n" .
               "📱 Nomor: {$phone}\n" .
               "🕐 Waktu: " . now()->format('d/m/Y H:i:s') . "\n\n" .
               "Jika Anda menerima pesan ini, artinya integrasi WhatsApp sudah berfungsi dengan baik.";
    }

    private function successResponse(string $message, ?array $response = null, ?int $attempts = null): array
    {
        return [
            'success' => true,
            'message' => $message,
            'attempts' => $attempts,
            'response' => $response
        ];
    }

    private function failureResponse(string $message, ?array $response = null): array
    {
        return [
            'success' => false,
            'message' => $message,
            'response' => $response
        ];
    }
}