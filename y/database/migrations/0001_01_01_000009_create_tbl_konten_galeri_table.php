<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_konten_galeri', function (Blueprint $table) {
            $table->id('id_konten'); // Primary Key & Auto Increment
            $table->string('judul_konten', 150);
            $table->string('file', 255);
            $table->integer('id_admin'); // Kolom Foreign Key (Index) menuju tbl_admin
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_konten_galeri');
    }
};
