<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Front\SchoolController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('front.welcome');
});

// Front Schools
Route::get('/schools', [SchoolController::class, 'index'])->name('schools.index');

////////////////////////////////////// Admin szkoły
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('/users', UserController::class)->names('user');
    });

////////////////////////////////////// User / Rodzic
Route::middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/pulpit', function () {
            return view('user.dashboard');
        })->name('dashboard');
        Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

Route::middleware(['auth', 'verified', 'role:user'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        
    });

require __DIR__ . '/auth.php';

// School Landing Page & Rejestracja przypisana do szkoły
Route::get('/{school:slug}', [SchoolController::class, 'show'])->name('schools.show');
Route::get('/{school:slug}/register', [RegisteredUserController::class, 'createForSchool'])->name('schools.register');
