<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaxiController;
use App\Http\Controllers\DailyRecordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VidangeController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to taxis index (dashboard)
Route::get('/', function () {
    return redirect()->route('taxis.index');
});

// Redirect /dashboard to taxis index
Route::get('/dashboard', function () {
    return redirect()->route('taxis.index');
})->middleware(['auth'])->name('dashboard');

// Authenticated routes group
Route::middleware(['auth'])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Taxis dashboard and filtering (index with AJAX)
    Route::get('/taxis', [TaxiController::class, 'index'])->name('taxis.index');

    // Manage page for taxis (list + edit/delete)
    Route::get('/taxis/manage', [TaxiController::class, 'manage'])->name('taxis.manage');

    // Monthly data AJAX endpoint for a single taxi
    Route::get('/taxis/{taxi}/monthly-data', [TaxiController::class, 'monthlyData'])->name('taxis.monthlyData');

    // Resource routes for taxis excluding index and show (already defined)
    Route::resource('taxis', TaxiController::class)->except(['index', 'show']);

    // Daily records routes (adjusted for clarity)
    Route::get('daily-records', [DailyRecordController::class, 'index'])->name('daily-records.index'); // List daily records (optional)
    Route::get('daily-records/create', [DailyRecordController::class, 'create'])->name('daily-records.create');
    Route::post('daily-records', [DailyRecordController::class, 'store'])->name('daily-records.store');
    Route::post('/vidange/store', [VidangeController::class, 'store'])->name('vidange.store');
    Route::get('/taxis/{taxi}/vidanges', [VidangeController::class, 'history'])->name('vidange.history');
});

// Include auth routes (login, register, password resets, etc.)
require __DIR__.'/auth.php';
