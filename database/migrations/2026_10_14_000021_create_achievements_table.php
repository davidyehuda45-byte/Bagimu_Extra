<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eskul_id')->nullable()->constrained('eskuls')->nullOnDelete();
            $table->string('judul', 200);
            $table->string('tingkat', 50)->nullable();
            $table->date('tanggal')->nullable();
            $table->string('peserta', 500)->nullable();
            $table->timestamps();

            $table->index('eskul_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
