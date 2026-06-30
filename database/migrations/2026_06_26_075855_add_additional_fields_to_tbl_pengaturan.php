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
        Schema::table('tbl_pengaturan', function (Blueprint $table) {
            $table->integer('harga_camping')->default(25000);
            $table->string('jam_buka')->default('07:00');
            $table->string('jam_tutup')->default('17:00');
            $table->integer('kuota_harian')->default(2500);
            $table->boolean('kebijakan_pembatalan_aktif')->default(true);
            $table->text('teks_kebijakan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_pengaturan', function (Blueprint $table) {
            $table->dropColumn([
                'harga_camping', 
                'jam_buka', 
                'jam_tutup', 
                'kuota_harian', 
                'kebijakan_pembatalan_aktif', 
                'teks_kebijakan'
            ]);
        });
    }
};
