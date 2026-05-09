<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitialSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@gespt.local'],
            [
                'name' => 'Admin',
                'email' => 'admin@gespt.local',
                'type' => 'local',
                'password' => bcrypt('admin123'),
            ]
        );

        $this->call([
            PermissionsSeeder::class,
        ]);

        UserRole::firstOrCreate(
            ['id_user' => $user->id, 'id_role' => 1],
            ['id_user' => $user->id, 'id_role' => 1]
        );

    }
}
