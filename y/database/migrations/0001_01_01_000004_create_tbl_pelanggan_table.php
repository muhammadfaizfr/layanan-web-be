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
        Schema::create('tbl_pelanggan', function (Blueprint $table) {
            $table->id('id_pelanggan'); // Primary Key & Auto Increment
            $table->string('nama_lengkap', 100);
            $table->string('no_hp', 20);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_pelanggan');
    }
};
