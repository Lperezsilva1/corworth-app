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
