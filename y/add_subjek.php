<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasColumn('tbl_kontak', 'subjek')) {
    Schema::table('tbl_kontak', function (Blueprint $table) {
        $table->string('subjek', 100)->nullable()->after('email');
    });
    echo "Column subjek added.\n";
} else {
    echo "Column subjek already exists.\n";
}
