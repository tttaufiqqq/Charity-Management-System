<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        Role::create(['name' => 'donor']);
        Role::create(['name' => 'public']);
        Role::create(['name' => 'organizer']);
        Role::create(['name' => 'volunteer']);
    }
}
