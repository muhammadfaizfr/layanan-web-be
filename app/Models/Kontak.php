<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kontak extends Model
{
    use HasFactory;

    protected $table = 'tbl_kontak';
    protected $primaryKey = 'id_kontak';

    // Tabel ini tidak menggunakan timestamps default Laravel (created_at/updated_at)
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'email',
        'subjek',
        'pesan',
        'tanggal_kirim',
    ];
}
