<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesertaPreRegistered extends Model
{
    use HasFactory;

    protected $table = 'peserta_pre_registereds';

    protected $fillable = [
        'nama',
        'nomor_bib',
        'email',
        'nomor_telepon',
        'kategori_lari',
        'is_registered'
    ];

    protected $casts = [
        'is_registered' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get formatted phone number
     */
    public function getFormattedPhoneAttribute()
    {
        $phone = preg_replace('/[^0-9]/', '', $this->nomor_telepon);
        
        if (substr($phone, 0, 2) === '62') {
            return $phone;
        } elseif (substr($phone, 0, 1) === '0') {
            return '62' . substr($phone, 1);
        } else {
            return '62' . $phone;
        }
    }

    /**
     * Mark as registered
     */
    public function markAsRegistered()
    {
        $this->update(['is_registered' => true]);
    }

    /**
     * Scope to get unregistered participants
     */
    public function scopeUnregistered($query)
    {
        return $query->where('is_registered', false);
    }

    /**
     * Scope to get registered participants
     */
    public function scopeRegistered($query)
    {
        return $query->where('is_registered', true);
    }

    /**
     * Find by BIB number
     */
    public static function findByBib($bibNumber)
    {
        return static::where('nomor_bib', $bibNumber)->first();
    }
}