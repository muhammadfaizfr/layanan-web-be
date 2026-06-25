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
        Schema::create('tbl_booking', function (Blueprint $table) {
            $table->id('id_booking'); // Primary Key & Auto Increment
            $table->integer('id_pelanggan'); // Kolom Foreign Key (Index)
            $table->integer('id_slot'); // Kolom Foreign Key (Index)
            $table->string('jenis_tiket', 50);
            $table->integer('jml_tiket');
            $table->decimal('total_payar', 12, 2); // Catatan: Sesuaikan typo 'total_payar' atau 'total_bayar' jika diperlukan
            
            // Kolom ENUM dengan nilai bawaan (Default)
            $table->enum('status_booking', ['Menunggu Pembayaran', 'Diproses', 'Dikonfirmasi', 'Selesai', 'Batal'])
                  ->default('Menunggu Pembayaran')
                  ->nullable();
                  $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_booking');
    }
};
