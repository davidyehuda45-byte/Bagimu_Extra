<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_classes', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50)->unique();
            $table->unsignedTinyInteger('tingkat');
            $table->string('jurusan', 20);
            $table->string('tahun_ajaran', 20)->nullable();
            $table->foreignId('wali_kelas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('wali_kelas_nama', 100)->nullable();
            $table->timestamps();

            $table->index(['tingkat', 'jurusan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_classes');
    }
};
