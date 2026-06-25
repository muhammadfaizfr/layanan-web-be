<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'tbl_booking';
    protected $primaryKey = 'id_booking';

    protected $fillable = [
        'id_pelanggan',
        'id_slot',
        'jenis_tiket',
        'jml_tiket',
        'total_payar', // Sesuai kolom di migrasi: total_payar
        'status_booking',
    ];

    /**
     * Relasi ke Pelanggan yang memesan.
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    /**
     * Relasi ke Slot Pendakian yang dipilih.
     */
    public function slotPendakian()
    {
        return $this->belongsTo(SlotPendakian::class, 'id_slot', 'id_slot');
    }

    /**
     * Relasi ke Pembayaran yang terkait.
     */
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_booking', 'id_booking');
    }
}
