<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateAdministratorSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@euproxy.com'],
            [
                'name'             => 'Administrator',
                'password'         => Hash::make('Admin@123456'),
                'is_administrator' => 1,
            ]
        );

        $this->command->info('Admin created:');
        $this->command->info('  Email:    admin@euproxy.com');
        $this->command->info('  Password: Admin@123456');
    }
}
