<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;                 // 👈 para servir archivos
use Livewire\Volt\Volt;
use App\Livewire\Salesperson;
use App\Livewire\Drafters\Drafters;
use App\Livewire\Drafters\DraftersFormulario;
use App\Livewire\Sellers\Sellers;
use App\Livewire\Sellers\SellersFormulario;
use App\Livewire\Buildings\Buildings;
use App\Livewire\Buildings\BuildingFormulario;
use App\Livewire\Projects\Projects;           // índice + modal
use App\Livewire\Projects\ProjectFormulario;  // página de creación
use App\Livewire\Projects\ProjectsShow;       // detalle

use App\Models\ProjectCommentAttachment;       // 👈 modelo de adjuntos

Route::get('/', function () {
    return view('welcome');
})->name('home');

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
Route::get('/projects/{project}', ProjectsShow::class)->name('projects.show');
Route::get('/projects/{project}/edit', ProjectFormulario::class)->name('projects.edit');

// ===== DASHBOARD =====
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ===== ÁREA AUTENTICADA =====
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // === Adjuntos de comentarios (ver/descargar SIN storage:link) ===
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

        return Storage::disk($att->disk)->download(
            $att->path,
            $att->original_name
        );
    })->name('attachments.download');
});

require __DIR__.'/auth.php';
