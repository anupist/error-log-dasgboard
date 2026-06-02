<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Create the default admin user.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@errorlog.com'],
            [
                'name'     => 'Admin',
                'email'    => 'admin@errorlog.com',
                'password' => Hash::make('admin123'),
            ]
        );

        $this->command->info('Admin user created: admin@errorlog.com / admin123');
    }
}
