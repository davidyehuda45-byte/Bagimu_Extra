<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('inventory_loans');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('achievements');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Fitur telah dibersihkan secara permanen
    }
};
