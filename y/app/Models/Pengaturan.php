<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'tbl_pengaturan';

    protected $fillable = [
        'harga_lokal',
        'harga_mancanegara',
        'harga_camping',
        'jam_buka',
        'jam_tutup',
        'kuota_harian',
        'kebijakan_pembatalan_aktif',
        'teks_kebijakan',
    ];
}
