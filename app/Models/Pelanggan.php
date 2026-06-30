<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'tbl_pelanggan';
    protected $primaryKey = 'id_pelanggan';

    protected $fillable = [
        'nama_lengkap',
        'no_identitas',
        'no_hp',
        'email',
    ];

    /**
     * Relasi One-to-Many ke Booking.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'id_pelanggan', 'id_pelanggan');
    }
}
