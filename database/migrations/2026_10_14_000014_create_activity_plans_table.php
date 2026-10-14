<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eskul_id')->constrained('eskuls')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('jenis', 20)->default('LATIHAN');
            $table->string('agenda', 200);
            $table->text('tujuan')->nullable();
            $table->text('materi')->nullable();
            $table->string('lokasi', 100)->nullable();
            $table->text('dokumentasi')->nullable();
            $table->text('hasil')->nullable();
            $table->text('kendala')->nullable();
            $table->string('created_by', 191)->nullable();
            $table->timestamps();

            $table->index(['eskul_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_plans');
    }
};
