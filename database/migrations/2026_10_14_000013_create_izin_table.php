<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('izin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registrations')->cascadeOnDelete();
            $table->foreignId('eskul_id')->constrained('eskuls')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('jenis', 20);
            $table->string('alasan', 500);
            $table->string('status', 20)->default('PENDING');
            $table->string('decided_by', 191)->nullable();
            $table->dateTime('decided_at')->nullable();
            $table->timestamps();

            $table->index(['eskul_id', 'tanggal']);
            $table->index('registration_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('izin');
    }
};
