<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email', 191)->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->string('method', 20);
            $table->boolean('success')->default(false);
            $table->string('reason', 200)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['email', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_history');
    }
};
