<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->string('admin_id', 191);
            $table->string('admin_email', 191);
            $table->string('action', 50);
            $table->string('target_type', 50)->nullable();
            $table->string('target_id', 191)->nullable();
            $table->text('detail')->nullable();
            $table->timestamps();

            $table->index('admin_id');
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
    }
};
