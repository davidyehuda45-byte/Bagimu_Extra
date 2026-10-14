<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->index(['periode', 'eskul_utama', 'status']);
        });

        Schema::table('izin', function (Blueprint $table) {
            $table->index(['eskul_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropIndex(['periode', 'eskul_utama', 'status']);
        });

        Schema::table('izin', function (Blueprint $table) {
            $table->dropIndex(['eskul_id', 'status']);
        });
    }
};

