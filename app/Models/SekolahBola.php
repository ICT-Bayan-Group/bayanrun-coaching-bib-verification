<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SekolahBola extends Model
{
    use HasFactory;

    protected $table = 'sekolah_bolas';

    protected $fillable = [
        'nama',
        'pic',
        'email',
        'telepon',
        'user_token',
    ];

    /**
     * Boot method untuk generate user_token otomatis
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->user_token)) {
                $model->user_token = self::generateUserToken();
            }
        });
    }

    /**
     * Generate unique user token menggunakan UUID untuk keamanan maksimal
     */
    public static function generateUserToken()
    {
        do {
            $token = (string) Str::uuid();
        } while (self::where('user_token', $token)->exists());

        return $token;
    }

    public function pemainBola()
    {
        return $this->hasMany(PemainBola::class, 'sekolah_bola_id');
    }

    public function pemainBolas()
    {
        return $this->hasMany(PemainBola::class, 'sekolah_bola_id');
    }

    /**
     * Get user URL
     */
    public function getUserUrlAttribute()
    {
        return url('/user/' . $this->user_token);
    }
    public function kuotaSekolah(): HasOne
        {
            return $this->hasOne(KuotaSekolah::class);
        }

        // Tambahkan method helper ini
        public function getKuotaDataAttribute(): array
        {
            $kuota = $this->kuotaSekolah;
            
            if (!$kuota) {
                return [
                    'total' => 0,
                    '7-8' => 0,
                    '9-10' => 0,
                    '11-12' => 0,
                    'has_quota' => false
                ];
            }

            return [
                'total' => $kuota->kuota_7_8 + $kuota->kuota_9_10 + $kuota->kuota_11_12,
                '7-8' => $kuota->kuota_7_8,
                '9-10' => $kuota->kuota_9_10,
                '11-12' => $kuota->kuota_11_12,
                'has_quota' => true
            ];
        }

        public function getCurrentPlayerCounts(): array
        {
            $counts = $this->pemainBolas()
                ->selectRaw('umur_kategori, COUNT(*) as count')
                ->groupBy('umur_kategori')
                ->pluck('count', 'umur_kategori')
                ->toArray();

            return [
                '7-8' => $counts['7-8'] ?? 0,
                '9-10' => $counts['9-10'] ?? 0,
                '11-12' => $counts['11-12'] ?? 0,
                'total' => array_sum($counts)
            ];
        }

    /**
     * Scope untuk mencari berdasarkan user_token
     */
    public function scopeByUserToken($query, $token)
    {
        return $query->where('user_token', $token);
    }
}