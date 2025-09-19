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

Route::get('/', fn () => view('welcome'))->name('home');

Route::get('/projects/public', PublicList::class)->name('projects.public');

// ===== DRAFTERS =====
Route::get('/drafters', Drafters::class)->name('drafters.index');
Route::get('/drafters/create', DraftersFormulario::class)->name('drafters.create');

// ===== SELLERS =====
Route::get('/sellers', Sellers::class)->name('sellers.index');
Route::get('/sellers/create', SellersFormulario::class)->name('sellers.create');

// ===== BUILDINGS =====
Route::get('/buildings', Buildings::class)->name('buildings.index');
Route::get('/buildings/create', BuildingFormulario::class)->name('buildings.create');

// ===== PROJECTS =====
Route::get('/projects', Projects::class)->name('projects.index');
Route::get('/projects/create', ProjectFormulario::class)->name('projects.create');
Route::get('/projects/{project}', ProjectsShow::class)->whereNumber('project')->name('projects.show');
Route::get('/projects/{project}/edit', ProjectFormulario::class)->whereNumber('project')->name('projects.edit');

// ===== DASHBOARD =====
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardMain::class)->name('dashboard');
    Route::get('/activity', ActivityIndex::class)->name('activity.index');
});

// ===== ÁREA AUTENTICADA =====
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Adjuntos (ver/descargar sin storage:link)
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
    })->name('attachments.view');

    Route::get('/attachments/{att}/download', function (ProjectCommentAttachment $att) {
        abort_unless(Storage::disk($att->disk)->exists($att->path), 404);
        return Storage::disk($att->disk)->download($att->path, $att->original_name);
    })->name('attachments.download');
});

// ===== ADMIN =====
Route::middleware(['auth','verified','role:Admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::redirect('/', '/admin/users')->name('home'); // 👈 atajo
        Route::get('/ping', fn() => 'OK')->name('ping'); // <— SOLO prueba
        Route::get('/users/create', UsersFormulario::class)->name('users.create');
        Route::get('/users/{userId}/edit', UsersFormulario::class)->name('users.edit'); // ← usa {userId}
        
        
        // Página que envuelve la tabla (wrapper)
        Route::get('/users', UsersHome::class)->name('users.index');
         // Crear y Editar usando el mismo componente de formulario
       
    });

require __DIR__.'/auth.php';
