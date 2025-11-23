<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Security Guard',
            'Supervisor',
            'Receptionist',
            'Maintenance',
            'Manager'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
