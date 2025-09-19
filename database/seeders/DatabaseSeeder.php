<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Primero roles/permisos y demás catálogos
        $this->call([
            RolesAndPermissionsSeeder::class,
            StatusSeeder::class,
        ]);

        // 2) Crear/actualizar usuario admin (SIN .env)
        $adminEmail    = 'admin@corworth.com';
        $adminName     = 'Admin';
        $adminPassword = 'admin@corworth.com'; // cámbialo si quieres

        $admin = User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name'     => $adminName,
                'password' => Hash::make($adminPassword),
            ]
        );

        // 3) Asignar rol admin (que ya existe por el seeder anterior)
        $admin->syncRoles(['admin']);
    }
}
