<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;

use App\Livewire\Salesperson;
use App\Livewire\Drafters\Drafters;
use App\Livewire\Drafters\DraftersFormulario;
use App\Livewire\Sellers\Sellers;
use App\Livewire\Sellers\SellersFormulario;
use App\Livewire\Buildings\Buildings;
use App\Livewire\Buildings\BuildingFormulario;
use App\Livewire\Projects\Projects;
use App\Livewire\Projects\ProjectFormulario;
use App\Livewire\Projects\ProjectsShow;
use App\Livewire\Dashboard\Main as DashboardMain;
use App\Models\ProjectCommentAttachment;
use App\Livewire\Activity\Index as ActivityIndex;
use App\Livewire\Projects\PublicList;
use App\Livewire\Admin\Users\Home  as UsersHome;
use App\Livewire\Admin\Users\Index as UsersIndex;
use App\Livewire\Admin\Users\UsersFormulario;
use App\Livewire\GlobalSearch;
use App\Livewire\Drafters\DraftersFilamentTable;
use App\Livewire\Settings\RolesPermissions;
use App\Livewire\Settings\RolesIndex;      // CRUD de roles
use App\Livewire\Settings\AssignRoles;     // Asignar roles a usuarios
use App\Livewire\Settings\RbacContainer;

Route::get('/', fn () => view('welcome'))->name('home');

Route::get('/projects/public', PublicList::class)->name('projects.public');

/* ========= DRAFTERS ========= */
Route::middleware(['auth'])->group(function () {
    Route::get('/drafters', Drafters::class)
        ->name('drafters.index')
        ->middleware('permission:drafter.view');

    Route::get('/drafters/create', DraftersFormulario::class)
        ->name('drafters.create')
        ->middleware('permission:drafter.create');
});

/* ========= SELLERS ========= */
Route::middleware(['auth'])->group(function () {
    Route::get('/sellers', Sellers::class)
        ->name('sellers.index')
        ->middleware('permission:seller.view');

    Route::get('/sellers/create', SellersFormulario::class)
        ->name('sellers.create')
        ->middleware('permission:seller.create');
});

/* ========= BUILDINGS ========= */
Route::middleware(['auth'])->group(function () {
    Route::get('/buildings', Buildings::class)
        ->name('buildings.index')
        ->middleware('permission:building.view');

    Route::get('/buildings/create', BuildingFormulario::class)
        ->name('buildings.create')
        ->middleware('permission:building.create');
});

/* ========= PROJECTS ========= */
Route::middleware(['auth'])->group(function () {
    Route::get('/projects', Projects::class)
        ->name('projects.index')
        ->middleware('permission:project.view');

    Route::get('/projects/create', ProjectFormulario::class)
        ->name('projects.create')
        ->middleware('permission:project.create');

    Route::get('/projects/{project}', ProjectsShow::class)
        ->whereNumber('project')
        ->name('projects.show')
        ->middleware('permission:project.view');

    Route::get('/projects/{project}/edit', ProjectFormulario::class)
        ->whereNumber('project')
        ->name('projects.edit')
        ->middleware('permission:project.update');
});

/* ========= DASHBOARD / ACTIVITY ========= */
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardMain::class)->name('dashboard');

    // Activity (timeline/auditoría). Protegido con permiso.
    Route::get('/activity', ActivityIndex::class)
        ->name('activity.index')
        ->middleware('permission:activity.view');
});

Route::get('/ping', fn() => 'OK');

/* ========= ÁREA AUTENTICADA ========= */
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Adjuntos (ver/descargar). Restringimos a quien pueda ver proyectos.
    Route::get('/attachments/{att}/view', function (ProjectCommentAttachment $att) {
        $stream = Storage::disk($att->disk)->readStream($att->path);
        abort_unless($stream, 404);

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type'        => $att->mime ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.addslashes($att->original_name).'"',
            'Cache-Control'       => 'private, max-age=0, no-cache',
        ]);
    })->name('attachments.view')->middleware('permission:project.view');

    Route::get('/attachments/{att}/download', function (ProjectCommentAttachment $att) {
        abort_unless(Storage::disk($att->disk)->exists($att->path), 404);
        return Storage::disk($att->disk)->download($att->path, $att->original_name);
    })->name('attachments.download')->middleware('permission:project.view');
});

Route::middleware('auth')->get('/whoami', function () {
    return [
        'user'      => auth()->user()->only('id','email'),
        'roles'     => auth()->user()->getRoleNames(),   // ← lo que Spatie ve
        'verified'  => (bool) auth()->user()->hasVerifiedEmail(),
    ];
});

Route::middleware(['auth','permission:roles.manage'])->group(function () {

   Route::get('/settings/rbac', RbacContainer::class)->name('settings.rbac');
    // Permisos por rol (ya la tienes)
    Route::get('/settings/roles-permissions', RolesPermissions::class)
        ->name('settings.roles-permissions');

    // NUEVAS:
    Route::get('/settings/roles', RolesIndex::class)
        ->name('settings.roles');                 // crear/renombrar/eliminar roles

    Route::get('/settings/assign-roles', AssignRoles::class)
        ->name('settings.assign-roles');          // asignar roles a usuarios
});

/* ========= ADMIN ========= */
Route::middleware(['auth','verified','role:Admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::redirect('/', '/admin/users')->name('home');
        Route::get('/ping', fn() => 'OK')->name('ping');

        // Crear y editar con el mismo componente
        Route::get('/users/create', UsersFormulario::class)->name('users.create');
        Route::get('/users/{userId}/edit', UsersFormulario::class)->name('users.edit');

        // Página que envuelve la tabla (wrapper)
        Route::get('/users', UsersHome::class)->name('users.index');
    });

require __DIR__.'/auth.php';
