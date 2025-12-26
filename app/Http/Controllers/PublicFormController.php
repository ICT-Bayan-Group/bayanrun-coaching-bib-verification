<?php

namespace App\Http\Controllers;

use App\Models\PesertaLari;
use App\Models\PesertaPreRegistered;
use App\Services\QRCodeService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log, DB};
use Illuminate\Http\JsonResponse;

class PublicFormController extends Controller
{
    private const MAX_REGISTRATIONS = 580;
    private const QR_DOWNLOAD_RETRIES = 3;
    private const WHATSAPP_MAX_RETRIES = 3;

    public function __construct(
        private QRCodeService $qrCodeService,
        private WhatsAppService $whatsAppService
    ) {}

    public function index()
    {
        $stats = $this->getRegistrationStats();
        return view('public-form', $stats);
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        $validated = $request->validate(
            ['email' => 'required|email'],
            ['email.required' => 'Email harus diisi', 'email.email' => 'Format email tidak valid']
        );

        if (!$this->isRegistrationOpen()) {
            return $this->registrationClosedResponse();
        }

        try {
            $peserta = PesertaPreRegistered::where('email', $validated['email'])->first();

            if (!$peserta) {
                return $this->errorResponse('Email tidak ditemukan. Pastikan email yang Anda masukkan benar.', 404);
            }

            if (PesertaLari::where('email', $validated['email'])->exists()) {
                return $this->errorResponse('Email ini sudah terdaftar sebelumnya. Setiap email hanya dapat mendaftar satu kali.', 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Email berhasil diverifikasi',
                'data' => [
                    'email' => $peserta->email,
                    'nama' => $peserta->nama,
                    'kategori_lari' => $peserta->kategori_lari,
                    'nomor_telepon' => $peserta->nomor_telepon,
                    'remaining_slots' => $this->getRemainingSlots()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Email verification error', ['email' => $validated['email'], 'error' => $e->getMessage()]);
            return $this->errorResponse('Terjadi kesalahan saat memverifikasi Email. Silakan coba lagi.', 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateRegistrationData($request);

        try {
            DB::beginTransaction();

            if (!$this->isRegistrationOpen()) {
                DB::rollBack();
                return $this->registrationClosedResponse();
            }

            $preRegistered = PesertaPreRegistered::where('email', $validated['email'])->first();
            if (!$preRegistered) {
                DB::rollBack();
                return $this->errorResponse('Email tidak valid.', 404);
            }

            $peserta = $this->createPeserta($validated);
            $qrCodeUrl = $this->qrCodeService->generateAndSave($peserta);
            
            $this->markAsRegistered($preRegistered);
            
            DB::commit();

            // Send WhatsApp asynchronously (non-blocking)
            $whatsappResult = $this->whatsAppService->sendRegistrationConfirmation($peserta);

            return $this->successResponse($peserta, $qrCodeUrl, $whatsappResult);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return $this->errorResponse('Terjadi kesalahan saat menyimpan data.', 500);
        }
    }

    public function verifyQR(Request $request): JsonResponse
    {
        $email = $request->input('email');

        try {
            $peserta = PesertaLari::where('email', $email)->first();

            if (!$peserta) {
                return $this->errorResponse('Email tidak ditemukan', 404);
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
            return $this->errorResponse('Terjadi kesalahan', 500);
        }
    }

    public function showRegistrations()
    {
        $pesertaLaris = PesertaLari::latest()->get();
        $stats = $this->getRegistrationStats();

        return view('registrations.index', compact('pesertaLaris') + $stats);
    }

    public function showDetail($id)
    {
        $peserta = PesertaLari::findOrFail($id);
        return view('registrations.detail', compact('peserta'));
    }

    public function getStats(): JsonResponse
    {
        $totalPeserta = PesertaLari::count();
        $pesertaPerKategori = PesertaLari::select('kategori_lari', DB::raw('count(*) as total'))
            ->groupBy('kategori_lari')
            ->get();
        $pesertaTerbaru = PesertaLari::latest()->limit(10)->get();

        return response()->json([
            'total_peserta' => $totalPeserta,
            'max_registrations' => self::MAX_REGISTRATIONS,
            'remaining_slots' => $this->getRemainingSlots(),
            'registration_open' => $this->isRegistrationOpen(),
            'percentage_full' => $this->getPercentageFull($totalPeserta),
            'peserta_per_kategori' => $pesertaPerKategori,
            'peserta_terbaru' => $pesertaTerbaru
        ]);
    }

    public function checkRegistrationStatus(): JsonResponse
    {
        $totalRegistrations = PesertaLari::count();
        $registrationOpen = $this->isRegistrationOpen();

        return response()->json([
            'registration_open' => $registrationOpen,
            'total_registrations' => $totalRegistrations,
            'max_registrations' => self::MAX_REGISTRATIONS,
            'remaining_slots' => $this->getRemainingSlots(),
            'percentage_full' => $this->getPercentageFull($totalRegistrations),
            'message' => $registrationOpen ? 'Pendaftaran masih terbuka' : 'Pendaftaran ditutup'
        ]);
    }

    public function regenerateQR($id): JsonResponse
    {
        try {
            $peserta = PesertaLari::findOrFail($id);
            $qrCodeUrl = $this->qrCodeService->regenerate($peserta);

            return response()->json([
                'success' => true,
                'message' => 'QR Code regenerated',
                'data' => ['qr_code_url' => $qrCodeUrl]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed: ' . $e->getMessage(), 500);
        }
    }

    public function retryQRCode($pesertaId): JsonResponse
    {
        try {
            $peserta = PesertaLari::findOrFail($pesertaId);
            $result = $this->whatsAppService->sendRegistrationConfirmation($peserta);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Retry failed', ['peserta_id' => $pesertaId, 'error' => $e->getMessage()]);
            return $this->errorResponse('Failed: ' . $e->getMessage(), 500);
        }
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

        if (!file_exists($qrPath)) {
            abort(404, 'QR Code not found');
        }

        return response()->download($qrPath, "qr-code-{$peserta->id}.png");
    }

    public function bulkSendQR(Request $request): JsonResponse
    {
        $pesertaIds = $request->input('peserta_ids', []);

        if (empty($pesertaIds)) {
            return $this->errorResponse('Tidak ada peserta dipilih', 400);
        }

        $results = $this->whatsAppService->sendBulk($pesertaIds);

        return response()->json([
            'success' => $results['successful'] > 0,
            'message' => "Bulk send completed. {$results['successful']} berhasil, {$results['failed']} gagal.",
            'summary' => $results['summary'],
            'results' => $results['details']
        ]);
    }

    public function testWhatsApp(Request $request): JsonResponse
    {
        $phone = $request->input('phone', '6285377640809');
        return $this->whatsAppService->sendTestMessage($phone);
    }

    // ==================== PRIVATE HELPERS ====================

    private function validateRegistrationData(Request $request): array
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

    private function createPeserta(array $validated): PesertaLari
    {
        return PesertaLari::create([
            'email' => $validated['email'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'kategori_lari' => $validated['kategori_lari'],
            'telepon' => $validated['telepon'],
            'qr_token' => \Str::random(32),
            'status' => 'terdaftar'
        ]);
    }

    private function markAsRegistered($preRegistered): void
    {
        if (method_exists($preRegistered, 'markAsRegistered')) {
            $preRegistered->markAsRegistered();
        }
    }

    private function isRegistrationOpen(): bool
    {
        return PesertaLari::count() < self::MAX_REGISTRATIONS;
    }

    private function getRemainingSlots(): int
    {
        return max(0, self::MAX_REGISTRATIONS - PesertaLari::count());
    }

    private function getPercentageFull(int $totalRegistrations): float
    {
        return round(($totalRegistrations / self::MAX_REGISTRATIONS) * 100, 2);
    }

    private function getRegistrationStats(): array
    {
        $totalRegistrations = PesertaLari::count();
        
        return [
            'totalRegistrations' => $totalRegistrations,
            'registrationOpen' => $this->isRegistrationOpen(),
            'remainingSlots' => $this->getRemainingSlots(),
            'percentageFull' => $this->getPercentageFull($totalRegistrations)
        ];
    }

    private function registrationClosedResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Maaf, pendaftaran sudah ditutup. Batas maksimal peserta telah tercapai.'
        ], 422);
    }

    private function errorResponse(string $message, int $statusCode): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $statusCode);
    }

    private function successResponse($peserta, $qrCodeUrl, $whatsappResult): JsonResponse
    {
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
                'remaining_slots' => $this->getRemainingSlots(),
                'registration_number' => PesertaLari::count()
            ]
        ]);
    }
}