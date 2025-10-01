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
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = 'web';

        // ---- Dominios de permisos ----
        $project = [
            'project.viewAny','project.view','project.create','project.update','project.delete',
            'project.assignDrafter','project.changePhaseStatus','project.changeGeneralStatus',
            'project.comment.create','project.comment.delete',
        ];

        // Vista Activity (timeline / auditoría del proyecto)
        $activity = [
            'activity.view',
        ];

        $building = [
            'building.viewAny','building.view','building.create','building.update','building.delete',
        ];

        $seller = [
            'seller.viewAny','seller.view','seller.create','seller.update','seller.delete',
        ];

        $drafter = [
            'drafter.viewAny','drafter.view','drafter.create','drafter.update','drafter.delete',
        ];

        // Usuarios / Admin (granulares) + compat opcional
        $users = [
            'user.viewAny','user.view','user.create','user.update','user.delete','roles.manage',
            // 'user.manage', // si lo usas en rutas antiguas, déjalo y asignaló solo a Admin
        ];

        // Crear/asegurar todos los permisos
        $allPerms = array_merge($project, $activity, $building, $seller, $drafter, $users);
        foreach ($allPerms as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => $guard]);
        }

        // ---- Roles ----
        $admin      = Role::firstOrCreate(['name' => 'Admin',      'guard_name' => $guard]);
        $manager    = Role::firstOrCreate(['name' => 'Manager',    'guard_name' => $guard]);
        $operations = Role::firstOrCreate(['name' => 'Operations', 'guard_name' => $guard]);
        $viewer     = Role::firstOrCreate(['name' => 'Viewer',     'guard_name' => $guard]);

        // ---- Matriz por rol ----
        // Admin: TODO
        $admin->syncPermissions(Permission::all());

        // Manager: TODO menos crear usuarios
        $manager->syncPermissions(array_values(array_diff($allPerms, ['user.create'])));

        // Operations: SOLO Project (+ Activity). NO building/seller/drafter ni usuarios.
        $operations->syncPermissions(array_merge($project, $activity));

        // Viewer: solo lectura (Project + Activity + catálogos)
        $viewer->syncPermissions([
            'project.viewAny','project.view',
            'activity.view',
            'building.viewAny','building.view',
            'seller.viewAny','seller.view',
            'drafter.viewAny','drafter.view',
        ]);

        // Promover Admin por .env o si hay 1 usuario
        if ($adminEmail = env('ADMIN_EMAIL')) {
            if ($u = User::where('email', $adminEmail)->first()) $u->syncRoles([$admin]);
        } elseif (User::count() === 1) {
            User::first()->syncRoles([$admin]);
        }

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
