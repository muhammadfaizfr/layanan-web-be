<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom baru untuk sistem tiket:
     * - id_tiket: ID tiket otomatis (BOOK-0001, BOOK-0002, dst)
     * - harga_tiket: harga satuan per tiket
     * - jumlah_tiket: jumlah tiket (alias dari jml_tiket yang sudah ada)
     * - total_harga: total harga otomatis (jumlah_tiket x harga_tiket)
     *
     * Kolom lama (jml_tiket, total_payar) TIDAK dihapus agar data lama tetap aman.
     */
    public function up(): void
    {
        Schema::table('tbl_booking', function (Blueprint $table) {
            $table->string('id_tiket', 20)->nullable()->unique()->after('id_booking');
            $table->decimal('harga_tiket', 12, 2)->nullable()->after('jenis_tiket');
            $table->integer('jumlah_tiket')->nullable()->after('harga_tiket');
            $table->decimal('total_harga', 12, 2)->nullable()->after('jumlah_tiket');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_booking', function (Blueprint $table) {
            $table->dropColumn(['id_tiket', 'harga_tiket', 'jumlah_tiket', 'total_harga']);
        });
    }
};
