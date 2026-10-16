<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    protected $signature = 'app:create-admin
        {--name= : Nama admin}
        {--email= : Email admin}
        {--username= : Username login}
        {--password= : Password (disarankan diisi via prompt tersembunyi)}';

    protected $description = 'Buat akun admin baru (cara aman untuk production, pengganti seeder demo)';

    public function handle(): int
    {
        $name = $this->option('name') ?? $this->ask('Nama admin');
        $email = $this->option('email') ?? $this->ask('Email admin');
        $username = $this->option('username') ?? $this->ask('Username login');
        $password = $this->option('password') ?? $this->secret('Password (min. 8 karakter)');

        $existingUser = User::where('username', $username)->orWhere('email', $email)->first();

        $rules = [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8'],
        ];

        if ($existingUser) {
            $rules['email'][] = 'unique:users,email,' . $existingUser->id;
            $rules['username'][] = 'unique:users,username,' . $existingUser->id;
        } else {
            $rules['email'][] = 'unique:users,email';
            $rules['username'][] = 'unique:users,username';
        }

        $validator = Validator::make(
            compact('name', 'email', 'username', 'password'),
            $rules
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        if ($existingUser) {
            $existingUser->update([
                'name' => $name,
                'email' => $email,
                'username' => $username,
                'password' => $password,
                'role' => Role::ADMIN,
                'email_verified_at' => $existingUser->email_verified_at ?? now(),
            ]);

            $this->info("Admin '{$existingUser->name}' ({$existingUser->email}) berhasil diperbarui.");

            return self::SUCCESS;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'username' => $username,
            'password' => $password,
            'role' => Role::ADMIN,
            'email_verified_at' => now(),
        ]);

        $this->info("Admin '{$user->name}' ({$user->email}) berhasil dibuat.");

        return self::SUCCESS;
    }
}
