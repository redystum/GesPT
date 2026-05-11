<?php

namespace Database\Seeders;

use App\Models\Configuration;
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
                'id' => 1,
                'name' => 'Admin',
                'email' => 'admin@gespt.local',
                'type' => 'local',
                'password' => bcrypt('admin123'),
            ]
        );

        Configuration::set('login_regex', [
            "regex"=>"^[a-zA-Z0-9._%+-]+@ipleiria.pt$",
            "test_subjects" => ["2231034@my.ipleiria.pt", "2231034@ipleiria.pt", "ruben.alves@ipleiria.pt"]
        ]);

        $this->call([
            PermissionsSeeder::class,
        ]);

        UserRole::firstOrCreate(
            ['id_user' => $user->id, 'id_role' => 1],
            ['id_user' => $user->id, 'id_role' => 1]
        );

    }
}
