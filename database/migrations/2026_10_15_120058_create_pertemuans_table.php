<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pertemuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eskul_id')->constrained('eskuls')->cascadeOnDelete();
            $table->unsignedInteger('nomor');
            $table->date('tanggal');
            $table->string('keterangan', 200)->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->string('locked_by')->nullable();
            $table->timestamps();

            $table->unique(['eskul_id', 'nomor']);
            $table->unique(['eskul_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pertemuans');
    }
};
