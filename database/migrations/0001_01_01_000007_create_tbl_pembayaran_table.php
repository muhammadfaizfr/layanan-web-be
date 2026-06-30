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
        Schema::create('tbl_pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran'); // Primary Key & Auto Increment
            $table->integer('id_booking'); // Kolom Foreign Key (Index) menuju tbl_booking
            $table->string('metode_pembayaran', 50);
            $table->string('bukti_transfer', 255)->nullable(); // Tak Ternilai: Ya, Bawaan: NULL
            $table->dateTime('waktu_pembayaran')->nullable(); // Tak Ternilai: Ya, Bawaan: NULL
            
            // Kolom ENUM dengan nilai bawaan (Default) dan bisa bernilai NULL
            $table->enum('status_pembayaran', ['Menunggu Verifikasi', 'Valid', 'DitolaK'])
                  ->default('Menunggu Verifikasi')
                  ->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_pembayaran');
    }
};
