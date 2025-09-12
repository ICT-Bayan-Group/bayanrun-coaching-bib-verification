<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KuotaSekolah extends Model
{
    use HasFactory;

    protected $table = 'kuota_sekolahs';

    protected $fillable = [
        'sekolah_bola_id',
        'kuota_7_8',
        'kuota_9_10',
        'kuota_11_12',
        'updated_by',
        'notes',
    ];

    protected $casts = [
        'kuota_7_8' => 'integer',
        'kuota_9_10' => 'integer',
        'kuota_11_12' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Relationship dengan SekolahBola
     */
    public function sekolahBola(): BelongsTo
    {
        return $this->belongsTo(SekolahBola::class);
    }

    /**
     * Relationship dengan User (admin yang mengupdate)
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get total kuota
     */
    public function getTotalKuotaAttribute(): int
    {
        return $this->kuota_7_8 + $this->kuota_9_10 + $this->kuota_11_12;
    }

    /**
     * Check if kuota is available for specific category
     */
    public function isKuotaAvailable(string $kategori, int $currentCount = 0): bool
    {
        $kuotaField = 'kuota_' . str_replace('-', '_', $kategori);
        
        if (!property_exists($this, $kuotaField)) {
            return false;
        }
        
        return $this->$kuotaField > $currentCount;
    }

    /**
     * Get remaining quota for specific category
     */
    public function getRemainingKuota(string $kategori, int $currentCount = 0): int
    {
        $kuotaField = 'kuota_' . str_replace('-', '_', $kategori);
        
        if (!property_exists($this, $kuotaField)) {
            return 0;
        }
        
        return max(0, $this->$kuotaField - $currentCount);
    }

    /**
     * Get kuota status
     */
    public function getKuotaStatus(): array
    {
        $sekolah = $this->sekolahBola;
        
        if (!$sekolah) {
            return [
                'status' => 'unknown',
                'details' => []
            ];
        }

        // Count pemain per kategori
        $pemainCounts = $sekolah->pemainBolas()
            ->selectRaw('umur_kategori, COUNT(*) as count')
            ->groupBy('umur_kategori')
            ->pluck('count', 'umur_kategori')
            ->toArray();

        $details = [
            '7-8' => [
                'kuota' => $this->kuota_7_8,
                'terisi' => $pemainCounts['7-8'] ?? 0,
                'sisa' => max(0, $this->kuota_7_8 - ($pemainCounts['7-8'] ?? 0)),
                'percentage' => $this->kuota_7_8 > 0 ? round((($pemainCounts['7-8'] ?? 0) / $this->kuota_7_8) * 100, 1) : 0
            ],
            '9-10' => [
                'kuota' => $this->kuota_9_10,
                'terisi' => $pemainCounts['9-10'] ?? 0,
                'sisa' => max(0, $this->kuota_9_10 - ($pemainCounts['9-10'] ?? 0)),
                'percentage' => $this->kuota_9_10 > 0 ? round((($pemainCounts['9-10'] ?? 0) / $this->kuota_9_10) * 100, 1) : 0
            ],
            '11-12' => [
                'kuota' => $this->kuota_11_12,
                'terisi' => $pemainCounts['11-12'] ?? 0,
                'sisa' => max(0, $this->kuota_11_12 - ($pemainCounts['11-12'] ?? 0)),
                'percentage' => $this->kuota_11_12 > 0 ? round((($pemainCounts['11-12'] ?? 0) / $this->kuota_11_12) * 100, 1) : 0
            ]
        ];

        $totalKuota = $this->total_kuota;
        $totalTerisi = array_sum(array_column($details, 'terisi'));
        $overallPercentage = $totalKuota > 0 ? round(($totalTerisi / $totalKuota) * 100, 1) : 0;

        // Determine overall status
        if ($overallPercentage >= 100) {
            $status = 'penuh';
        } elseif ($overallPercentage >= 80) {
            $status = 'hampir_penuh';
        } elseif ($overallPercentage >= 50) {
            $status = 'sedang';
        } else {
            $status = 'tersedia';
        }

        return [
            'status' => $status,
            'overall_percentage' => $overallPercentage,
            'total_kuota' => $totalKuota,
            'total_terisi' => $totalTerisi,
            'total_sisa' => max(0, $totalKuota - $totalTerisi),
            'details' => $details
        ];
    }
}