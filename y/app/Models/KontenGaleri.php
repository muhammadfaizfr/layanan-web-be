<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontenGaleri extends Model
{
    use HasFactory;

    protected $table = 'tbl_konten_galeri';
    protected $primaryKey = 'id_konten';

    protected $fillable = [
        'judul_konten',
        'file',
        'id_admin',
    ];

    /**
     * Relasi ke Admin yang mengunggah konten ini.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }
}
