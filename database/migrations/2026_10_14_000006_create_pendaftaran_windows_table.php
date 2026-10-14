<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran_windows', function (Blueprint $table) {
            $table->string('periode', 9)->primary();
            $table->foreign('periode')->references('periode')->on('periodes')->cascadeOnDelete();
            $table->boolean('buka')->default(true);
            $table->dateTime('tgl_buka')->nullable();
            $table->dateTime('tgl_tutup')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_windows');
    }
};
