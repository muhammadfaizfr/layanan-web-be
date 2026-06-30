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
        Schema::create('tbl_slot_pendakian', function (Blueprint $table) {
            $table->id('id_slot'); // Primary Key & Auto Increment
            $table->date('tanggal_pendakian');
            $table->integer('kuota_maksimal');
            $table->integer('kuota_tersedia');
            $table->integer('id_admin'); // Kolom Foreign Key (Index) menuju tbl_admin
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_slot_pendakian');
    }
};
