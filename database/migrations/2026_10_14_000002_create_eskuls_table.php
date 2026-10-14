<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eskuls', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('kuota')->default(30);
            $table->boolean('kuota_aktif')->default(false);
            $table->boolean('aktif')->default(true);
            $table->foreignId('pembina_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('temp_pembina_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('temp_pembina_sampai')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eskuls');
    }
};
