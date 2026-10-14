<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registrations')->cascadeOnDelete();
            $table->unsignedInteger('nilai');
            $table->string('predikat', 2);
            $table->text('catatan')->nullable();
            $table->string('actor_id', 191)->nullable();
            $table->string('actor_email', 191)->nullable();
            $table->timestamps();

            $table->index(['registration_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_history');
    }
};
