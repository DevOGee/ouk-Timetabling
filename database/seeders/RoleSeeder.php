<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'System administrator with full access'
            ],
            [
                'name' => 'dean',
                'description' => 'School dean with management privileges'
            ],
            [
                'name' => 'timetabler',
                'description' => 'School timetabler responsible for scheduling'
            ],
            [
                'name' => 'instructor',
                'description' => 'Teaching staff with view-only access'
            ]
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
