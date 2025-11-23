<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'Admin',
            'password' => Hash::make('password'),
            'weekly_hours_limit' => 50,
        ]);

        // Security Guards
        $securityRole = Role::where('name', 'Security Guard')->first();
        for ($i = 1; $i <= 3; $i++) {
            User::firstOrCreate([
                'email' => "guard$i@example.com",
            ], [
                'name' => "Security Guard $i",
                'password' => Hash::make('password'),
                'role_id' => $securityRole->id,
                'weekly_hours_limit' => 40,
            ]);
        }

        // Receptionists
        $receptionRole = Role::where('name', 'Receptionist')->first();
        for ($i = 1; $i <= 2; $i++) {
            User::firstOrCreate([
                'email' => "reception$i@example.com",
            ], [
                'name' => "Receptionist $i",
                'password' => Hash::make('password'),
                'role_id' => $receptionRole->id,
                'weekly_hours_limit' => 40,
            ]);
        }

        // Maintenance
        $maintenanceRole = Role::where('name', 'Maintenance')->first();
        User::firstOrCreate([
            'email' => "maintenance@example.com",
        ], [
            'name' => "Maintenance Staff",
            'password' => Hash::make('password'),
            'role_id' => $maintenanceRole->id,
            'weekly_hours_limit' => 40,
        ]);

        // Supervisor
        $supervisorRole = Role::where('name', 'Supervisor')->first();
        User::firstOrCreate([
            'email' => "supervisor@example.com",
        ], [
            'name' => "Supervisor",
            'password' => Hash::make('password'),
            'role_id' => $supervisorRole->id,
            'weekly_hours_limit' => 45,
        ]);
    }
}
