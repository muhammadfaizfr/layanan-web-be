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
        Schema::table('tbl_pelanggan', function (Blueprint $table) {
            $table->string('no_identitas', 50)->nullable()->after('nama_lengkap');
            $table->string('email', 100)->nullable()->after('no_hp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_pelanggan', function (Blueprint $table) {
            $table->dropColumn(['no_identitas', 'email']);
        });
    }
};
