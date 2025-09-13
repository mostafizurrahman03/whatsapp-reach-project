<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // ১. সব permission ensure আছে
        $permissions = [
            // User permissions
            'view_any_user', 'view_user', 'create_user', 'update_user', 'delete_user',
            // Role permissions
            'view_any_role', 'view_role', 'create_role', 'update_role', 'delete_role',
            // Permission permissions
            'view_any_permission', 'view_permission', 'create_permission', 'update_permission', 'delete_permission',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ২. Admin role create করে সব permission assign
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // ৩. Admin user খুঁজুন
        $admin = User::where('email', 'admin@example.com')->first();

        if ($admin) {
            // Admin user কে Admin role assign
            $admin->assignRole($adminRole);

            // Admin user কে সব permission directly assign (optional, Shield menu visibility ensure করতে)
            $admin->givePermissionTo(Permission::all());
        }
    }
}
