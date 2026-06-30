<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    // Menentukan nama tabel sesuai ketentuan
    protected $table = 'tbl_laporan';

    // Primary key disesuaikan dengan pola tabel database lainnya (id_laporan)
    protected $primaryKey = 'id_laporan';

    // Kolom-kolom fillable sesuai perkiraan kolom migrasi
    protected $fillable = [
        'id_booking',
        'total_pendapatan',
        'jumlah_tiket',
        'jumlah_pengunjung',
        'tanggal_laporan',
    ];

    /**
     * Relasi Eloquent: Laporan belongsTo Booking.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class, 'id_booking', 'id_booking');
    }
}
