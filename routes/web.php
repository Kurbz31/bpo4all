<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:Super Admin,HR Manager')->group(function () {
        Route::resource('users', UserController::class);
    });

    Route::middleware('role:Super Admin,Team Leader,HR Manager')->group(function () {
        Route::resource('campaigns', CampaignController::class);
        Route::get('employees/terminated', [EmployeeController::class, 'terminated'])->name('employees.terminated');
        Route::resource('employees', EmployeeController::class);
    });
});

require __DIR__.'/auth.php';
