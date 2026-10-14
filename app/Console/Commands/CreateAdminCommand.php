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

        $validator = Validator::make(
            compact('name', 'email', 'username', 'password'),
            [
                'name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'email', 'max:100', 'unique:users,email'],
                'username' => ['required', 'string', 'max:50', 'unique:users,username'],
                'password' => ['required', 'string', 'min:8'],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($password),
            'role' => Role::ADMIN,
        ]);

        $this->info("Admin '{$user->name}' ({$user->email}) berhasil dibuat.");

        return self::SUCCESS;
    }
}
