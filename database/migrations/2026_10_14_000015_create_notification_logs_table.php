<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('channel', 20);
            $table->string('to', 191);
            $table->string('template', 100);
            $table->text('payload')->nullable();
            $table->string('status', 20)->default('MENUNGGU');
            $table->unsignedInteger('attempts')->default(0);
            $table->string('provider_resp', 500)->nullable();
            $table->string('dedupe_key', 128)->nullable()->unique();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
