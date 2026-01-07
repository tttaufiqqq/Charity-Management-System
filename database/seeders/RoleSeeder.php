<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Database: izzhilmy (PostgreSQL) - Users & Roles
     */
    public function run(): void
    {
        // Temporarily set default database to izzhilmy for Spatie Permission
        $originalConnection = config('database.default');
        config(['database.default' => 'izzhilmy']);

        try {
            // Set connection for Spatie Permission models
            // Roles and permissions are stored in izzhilmy database
            DB::connection('izzhilmy')->transaction(function () {
                // Create roles (stored in izzhilmy)
                $roles = ['admin', 'donor', 'public', 'organizer', 'volunteer'];

                foreach ($roles as $roleName) {
                    Role::firstOrCreate(['name' => $roleName]);
                }

                // Create admin user (stored in izzhilmy)
                $admin = User::firstOrCreate(
                    ['email' => 'admin@gmail.com'],
                    [
                        'name' => 'Admin',
                        'password' => Hash::make('password'),
                    ]
                );

                // Assign admin role
                if (! $admin->hasRole('admin')) {
                    $admin->assignRole('admin');
                }

                $this->command->info('✓ Created roles and admin user in izzhilmy database');
            });
        } finally {
            // Restore original default connection
            config(['database.default' => $originalConnection]);
        }
    }
}
