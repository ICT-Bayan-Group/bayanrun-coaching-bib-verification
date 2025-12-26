<?php

namespace App\Services;

use App\Models\PesertaLari;
use Illuminate\Support\Facades\{Http, Log, Storage};

class QRCodeService
{
    private const QR_API_URL = 'https://api.qrserver.com/v1/create-qr-code/';
    private const QR_SIZE = '300x300';
    private const QR_FORMAT = 'png';
    private const QR_MARGIN = 20;
    private const MAX_RETRIES = 3;

    public function generateAndSave(PesertaLari $peserta): string
    {
        $qrData = $this->prepareQRData($peserta);
        $qrCodeApiUrl = $this->generateQRCodeURL($qrData);
        
        // Download dan simpan QR Code
        $qrCodePath = $this->downloadAndSave($qrCodeApiUrl, $peserta->id);
        
        if ($qrCodePath) {
            $peserta->update([
                'qr_code_path' => $qrCodePath,
                'qr_url' => $qrData
            ]);
            
            return url('storage/' . $qrCodePath);
        }
        
        // Fallback ke API URL
        $peserta->update(['qr_url' => $qrData]);
        return $qrCodeApiUrl;
    }

    public function regenerate(PesertaLari $peserta): string
    {
        // Delete old QR code if exists
        if ($peserta->qr_code_path && Storage::disk('public')->exists($peserta->qr_code_path)) {
            Storage::disk('public')->delete($peserta->qr_code_path);
        }

        return $this->generateAndSave($peserta);
    }

    private function prepareQRData(PesertaLari $peserta): string
    {
        $namaFormatted = str_replace(' ', '_', $peserta->nama_lengkap);
        return "https://coaching.bayanevent.com/verify?email=" . urlencode($peserta->email) . "#" . $namaFormatted;
    }

    private function generateQRCodeURL(string $data): string
    {
        return self::QR_API_URL . "?" . http_build_query([
            'size' => self::QR_SIZE,
            'format' => self::QR_FORMAT,
            'margin' => self::QR_MARGIN,
            'data' => $data
        ]);
    }

    private function downloadAndSave(string $qrCodeApiUrl, int $pesertaId): ?string
    {
        for ($attempt = 1; $attempt <= self::MAX_RETRIES; $attempt++) {
            try {
                $response = Http::timeout(15)->retry(2, 1000)->get($qrCodeApiUrl);
                
                if ($response->successful()) {
                    $qrCodePath = $this->saveFile($response->body(), $pesertaId);
                    
                    if ($qrCodePath) {
                        Log::info('QR Code saved successfully', [
                            'peserta_id' => $pesertaId,
                            'path' => $qrCodePath,
                            'attempt' => $attempt
                        ]);
                        return $qrCodePath;
                    }
                }
                
                if ($attempt < self::MAX_RETRIES) {
                    sleep(1);
                }
            } catch (\Exception $e) {
                Log::error("QR download attempt {$attempt} failed", [
                    'peserta_id' => $pesertaId,
                    'error' => $e->getMessage()
                ]);
                
                if ($attempt < self::MAX_RETRIES) {
                    sleep(1);
                }
            }
        }
        
        Log::warning('QR Code download failed after all retries', ['peserta_id' => $pesertaId]);
        return null;
    }

    private function saveFile(string $content, int $pesertaId): ?string
    {
        try {
            $directory = 'qr-codes';
            $filename = "{$pesertaId}.png";
            $path = "{$directory}/{$filename}";
            
            // Pastikan directory exists
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }
            
            // Save file
            Storage::disk('public')->put($path, $content);
            
            // Verify file exists and has content
            if (Storage::disk('public')->exists($path) && Storage::disk('public')->size($path) > 0) {
                return $path;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Failed to save QR file', [
                'peserta_id' => $pesertaId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}