<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PlatformRoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'super_admin',
            'admin',
            'moderator',
            'customer',
            'individual_provider',
            'organization_owner',
        ] as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }
    }
}
