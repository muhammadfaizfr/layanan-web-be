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
        Schema::create('tbl_kontak', function (Blueprint $table) {
            $table->id('id_kontak'); // Primary Key & Auto Increment
            $table->string('nama', 100);
            $table->string('email', 100);
            $table->text('pesan');
            
            // Kolom datetime dengan default current_timestamp() dan bisa bernilai NULL (nullable)
            $table->dateTime('tanggal_kirim')->useCurrent()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_kontak');
    }
};
