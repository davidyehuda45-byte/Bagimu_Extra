<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->string('peminjam', 100);
            $table->unsignedInteger('jumlah')->default(1);
            $table->date('tgl_pinjam');
            $table->date('tgl_kembali')->nullable();
            // DIPINJAM = sedang dipinjam, KEMBALI = sudah kembali,
            // HILANG/RUSAK = laporan.
            $table->string('status', 20)->default('DIPINJAM');
            $table->timestamps();

            $table->index('item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_loans');
    }
};
