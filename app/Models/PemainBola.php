<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemainBola extends Model
{
    use HasFactory;

    protected $table = 'pemain_bolas';

    protected $fillable = [
        'sekolah_bola_id',
        'nama',
        'umur',
        'umur_kategori',
    ];

    protected $casts = [
        'umur' => 'integer',
    ];

    /**
     * Relasi: Pemain milik 1 Sekolah Bola
     */
    public function sekolahBola()
    {
        return $this->belongsTo(SekolahBola::class, 'sekolah_bola_id');
    }

    /**
     * Alias untuk relasi sekolahBola (untuk backward compatibility)
     */
    public function sekolah()
    {
        return $this->belongsTo(SekolahBola::class, 'sekolah_bola_id');
    }

    /**
     * Scope untuk filter berdasarkan kategori umur
     */
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('umur_kategori', $kategori);
    }

    /**
     * Scope untuk filter berdasarkan range umur
     */
    public function scopeByUmur($query, $minUmur, $maxUmur = null)
    {
        if ($maxUmur) {
            return $query->whereBetween('umur', [$minUmur, $maxUmur]);
        }
        return $query->where('umur', $minUmur);
    }

    /**
     * Accessor untuk mendapatkan nama lengkap dengan umur
     */
    public function getNamaLengkapAttribute()
    {
        return $this->nama . ' (' . $this->umur . ' tahun)';
    }

    /**
     * Method untuk validasi konsistensi umur dan kategori
     */
    public function isKategoriValid()
    {
        switch ($this->umur_kategori) {
            case '7-8':
                return $this->umur >= 7 && $this->umur <= 8;
            case '9-10':
                return $this->umur >= 9 && $this->umur <= 10;
            case '11-12':
                return $this->umur >= 11 && $this->umur <= 12;
            default:
                return false;
        }
    }
}