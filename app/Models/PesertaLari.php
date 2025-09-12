<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PesertaLari extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'peserta_laris';

    protected $fillable = [
        'nomor_bib',
        'nama_lengkap',
        'kategori_lari',
        'email',
        'telepon',
        'qr_token',
        'qr_code_path',
        'status'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Status constants
    const STATUS_TERDAFTAR = 'terdaftar';
    const STATUS_KONFIRMASI = 'konfirmasi';
    const STATUS_HADIR = 'hadir';
    const STATUS_TIDAK_HADIR = 'tidak_hadir';

    /**
     * Get QR Code URL
     */
    public function getQrCodeUrlAttribute()
    {
        if ($this->qr_code_path) {
            return asset('storage/' . $this->qr_code_path);
        }
        return null;
    }

    /**
     * Get formatted phone number
     */
    public function getFormattedPhoneAttribute()
    {
        $phone = preg_replace('/[^0-9]/', '', $this->telepon);
        
        if (substr($phone, 0, 2) === '62') {
            return $phone;
        } elseif (substr($phone, 0, 1) === '0') {
            return '62' . substr($phone, 1);
        } else {
            return '62' . $phone;
        }
    }

    /**
     * Scope untuk filter berdasarkan kategori
     */
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori_lari', $kategori);
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get peserta terdaftar hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Find by BIB number
     */
    public static function findByBib($bibNumber)
    {
        return static::where('nomor_bib', $bibNumber)->first();
    }
}