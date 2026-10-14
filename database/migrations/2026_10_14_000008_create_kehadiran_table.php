<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kehadiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eskul_id')->constrained('eskuls')->cascadeOnDelete();
            $table->foreignId('registration_id')->constrained('registrations')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('status', 20)->default('HADIR');
            $table->string('keterangan', 200)->nullable();
            // Presensi terkunci — tidak dapat diubah tanpa unlock admin + jejak.
            $table->dateTime('locked_at')->nullable();
            $table->string('locked_by', 191)->nullable();
            $table->timestamps();

            $table->unique(['eskul_id', 'registration_id', 'tanggal']);
            $table->index(['eskul_id', 'tanggal']);
            $table->index('registration_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kehadiran');
    }
};
