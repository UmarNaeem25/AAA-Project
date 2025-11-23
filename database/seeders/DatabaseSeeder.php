<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesTableSeeder::class,
            LocationsTableSeeder::class,
        ]);

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('12345678'),
            'role_id' => 1,
            'weekly_hours_limit' => 40,
        ]);

        User::create([
            'name' => 'John Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('12345678'),
            'role_id' => 2,
            'weekly_hours_limit' => 45,
        ]);

        User::create([
            'name' => 'Sarah Employee',
            'email' => 'employee@example.com',
            'password' => Hash::make('12345678'),
            'role_id' => 3,
            'weekly_hours_limit' => 35,
        ]);

        User::create([
            'name' => 'Mike Worker',
            'email' => 'mike@example.com',
            'password' => Hash::make('12345678'),
            'role_id' => 3,
            'weekly_hours_limit' => 38,
        ]);

        User::create([
            'name' => 'Lisa Assistant',
            'email' => 'lisa@example.com',
            'password' => Hash::make('12345678'),
            'role_id' => 3,
            'weekly_hours_limit' => 32,
        ]);
    }
}