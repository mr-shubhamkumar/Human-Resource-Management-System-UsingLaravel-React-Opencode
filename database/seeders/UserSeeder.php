<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed admin and HR manager accounts.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'Admin')->firstOrFail();
        $hrRole = Role::where('name', 'HR Manager')->firstOrFail();

        $accounts = [
            ['name' => 'Alex Morgan', 'email' => 'admin@company.com', 'role_id' => $adminRole->id],
            ['name' => 'Maria Santos', 'email' => 'hr.manager@company.com', 'role_id' => $hrRole->id],
            ['name' => 'Juan Dela Cruz', 'email' => 'hr.assistant@company.com', 'role_id' => $hrRole->id],
        ];

        foreach ($accounts as $account) {
            User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'role_id' => $account['role_id'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
