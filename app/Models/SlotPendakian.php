<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlotPendakian extends Model
{
    use HasFactory;

    protected $table = 'tbl_slot_pendakian';
    protected $primaryKey = 'id_slot';

    protected $fillable = [
        'tanggal_pendakian',
        'kuota_maksimal',
        'kuota_tersedia',
        'id_admin',
    ];

    /**
     * Relasi ke Admin yang mengelola slot ini.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    /**
     * Relasi One-to-Many ke Booking.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'id_slot', 'id_slot');
    }
}
