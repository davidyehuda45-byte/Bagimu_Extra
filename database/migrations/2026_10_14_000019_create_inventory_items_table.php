<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('eskul_id')->nullable()->constrained('eskuls')->nullOnDelete();
            $table->string('nama', 100);
            $table->unsignedInteger('jumlah')->default(1);
            $table->string('kondisi', 20)->default('BAIK');
            $table->string('keterangan', 500)->nullable();
            $table->timestamps();

            $table->index('eskul_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
