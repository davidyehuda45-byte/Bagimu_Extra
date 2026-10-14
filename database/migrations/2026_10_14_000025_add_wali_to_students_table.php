<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('wali_nama', 100)->nullable()->after('no_absen');
            $table->string('wali_kontak', 20)->nullable()->after('wali_nama');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['wali_nama', 'wali_kontak']);
        });
    }
};
