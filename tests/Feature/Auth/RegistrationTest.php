<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('public registration routes are unavailable', function () {
    $this->get('/register')->assertStatus(404);
    $this->post('/register')->assertStatus(404);
});

test('non-admin cannot access admin user creation', function () {
    $user = User::factory()->create();
    $user->markEmailAsVerified(); // si proteges admin con 'verified'

    $this->actingAs($user)
        ->get('/admin/users/create')
        ->assertStatus(403);
});

test('admin can access admin user creation form', function () {
    $admin = User::factory()->create();
  

    // Asegura que exista el rol y asígnalo
    Role::firstOrCreate(['name' => 'Admin']);
    $admin->assignRole('Admin');

    $this->actingAs($admin)
        ->get('/admin/users/create')
        ->assertOk();
});
