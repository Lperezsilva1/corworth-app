<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Limpia caché de permisos/roles (Spatie)
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = 'web';

        // --- Permisos base de tu app ---
        $permissions = [
            // Projects
            'project.viewAny',
            'project.view',
            'project.create',
            'project.update',
            'project.delete',
            'project.assignDrafter',
            'project.changePhaseStatus',
            'project.changeGeneralStatus',

            // Users / Admin
            'user.manage', // acceso a /admin, CRUD de usuarios, etc.
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => $guard]);
        }

        // --- Roles ---
        $admin   = Role::firstOrCreate(['name' => 'Admin',   'guard_name' => $guard]);
        $manager = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => $guard]);
        $drafter = Role::firstOrCreate(['name' => 'Drafter', 'guard_name' => $guard]);
        $seller  = Role::firstOrCreate(['name' => 'Seller',  'guard_name' => $guard]);

        // --- Asignación de permisos por rol ---
        $admin->syncPermissions($permissions);

        $manager->syncPermissions([
            'project.viewAny',
            'project.view',
            'project.create',
            'project.update',
            'project.assignDrafter',
            'project.changeGeneralStatus',
            // (no 'user.manage' aquí si quieres que solo Admin administre usuarios)
        ]);

        $drafter->syncPermissions([
            'project.viewAny',
            'project.view',
            'project.changePhaseStatus',
        ]);

        $seller->syncPermissions([
            'project.viewAny',
            'project.view',
        ]);

        // --- (Opcional) Promover a Admin a un usuario por email ---
        // Define ADMIN_EMAIL en tu .env, p. ej. ADMIN_EMAIL=admin@miapp.com
        if ($adminEmail = env('ADMIN_EMAIL')) {
            if ($u = User::where('email', $adminEmail)->first()) {
                $u->syncRoles([$admin]);
            }
        }
        // Alternativa: si solo hay 1 usuario en la BD, hacerlo Admin
        elseif (User::count() === 1) {
            User::first()->syncRoles([$admin]);
        }

        // Refresca caché otra vez por si acaso
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
