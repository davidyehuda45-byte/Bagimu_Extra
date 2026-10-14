<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eskul_jadwals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eskul_id')->constrained('eskuls')->cascadeOnDelete();
            $table->string('hari', 10);
            $table->string('jam_mulai', 5);
            $table->string('jam_selesai', 5);
            $table->string('tempat', 100);
            $table->timestamps();

            $table->index('eskul_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eskul_jadwals');
    }
};
