<?php

use Illuminate\Support\Facades\Route;
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
use App\Livewire\Projects\ProjectsShow;  // página de creación

 


Route::get('/', function () {
    return view('welcome');
})->name('home');

//FOR DRAFTER
Route::get('/drafters', Drafters::class)->name('drafters.index');
Route::get('/drafters/create', DraftersFormulario::class)->name('drafters.create');
// END DRAFTER

//FOR SELLER
Route::get('/sellers', Sellers::class)->name('sellers.index');
Route::get('/sellers/create', SellersFormulario::class)->name('sellers.create');
// END SELLER

//FOR BUILDING
Route::get('/buildings', Buildings::class)->name('buildings.index');
Route::get('/buildings/create', BuildingFormulario::class)->name('buildings.create');
// END BUILDING

//FOR PROJECT
Route::get('/projects', Projects::class)->name('projects.index');
Route::get('/projects/create', ProjectFormulario::class)->name('projects.create');
Route::get('/projects/{project}', ProjectsShow::class)->name('projects.show');          // <- detalle
Route::get('/projects/{project}/edit', ProjectFormulario::class)->name('projects.edit');

// END PROJECT


Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
