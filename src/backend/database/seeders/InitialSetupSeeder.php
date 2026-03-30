<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitialSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gespt.local'],
            [
                'name' => 'Admin',
                'email' => 'admin@gespt.local',
                'password' => bcrypt('admin123'),
            ]
        );

    }
}
