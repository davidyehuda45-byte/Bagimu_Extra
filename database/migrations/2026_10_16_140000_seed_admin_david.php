<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        User::updateOrCreate(
            ['username' => 'davidyehudasurbakti'],
            [
                'name' => 'David Yehuda Surbakti',
                'email' => 'david.yehuda45@smk.belajar.id',
                'password' => 'Yehuda4545!',
                'role' => Role::ADMIN,
                'email_verified_at' => now(),
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        User::where('username', 'davidyehudasurbakti')->delete();
    }
};

