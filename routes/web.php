<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReadlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Single resourceful route for readlist
Route::resource('readlist', ReadlistController::class)
    ->only(['index', 'store', 'edit', 'update', 'destroy'])
    ->middleware(['auth', 'verified']);

// Correct the typo in the explicit PATCH route (if intended)
Route::patch('/readlist/{readlist}', [ReadlistController::class, 'update'])->name('readlist.update');

require __DIR__ . '/auth.php';