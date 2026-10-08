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
        Route::resource('/courses', \App\Http\Controllers\Admin\CourseController::class)->names('courses');
        Route::get('/children', [\App\Http\Controllers\Admin\ChildController::class, 'index'])->name('children.index');
        Route::get('/billing', [\App\Http\Controllers\Admin\BillingController::class, 'index'])->name('billing.index');
        Route::post('/billing/{item}/mark-paid', [\App\Http\Controllers\Admin\BillingController::class, 'markPaid'])->name('billing.mark-paid');
        Route::post('/billing/{item}/mark-unpaid', [\App\Http\Controllers\Admin\BillingController::class, 'markUnpaid'])->name('billing.mark-unpaid');
    });

////////////////////////////////////// Teacher / Nauczyciel
Route::middleware(['auth', 'verified', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::get('/courses', [\App\Http\Controllers\Teacher\CourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{course}', [\App\Http\Controllers\Teacher\CourseController::class, 'show'])->name('courses.show');
        Route::get('/courses/{course}/lessons/create', [\App\Http\Controllers\Teacher\LessonController::class, 'create'])->name('lessons.create');
        Route::post('/courses/{course}/lessons', [\App\Http\Controllers\Teacher\LessonController::class, 'store'])->name('lessons.store');
        Route::get('/lessons/{lesson}', [\App\Http\Controllers\Teacher\LessonController::class, 'show'])->name('lessons.show');
    });

////////////////////////////////////// User / Rodzic
Route::middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/pulpit', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

Route::middleware(['auth', 'verified', 'role:user'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::resource('/children', \App\Http\Controllers\User\ChildController::class)->names('children');
        Route::post('/children/enroll', [\App\Http\Controllers\User\ChildController::class, 'enroll'])->name('children.enroll');
        Route::delete('/children/{child}/unenroll/{course}', [\App\Http\Controllers\User\ChildController::class, 'unenroll'])->name('children.unenroll');
        Route::get('/courses', [\App\Http\Controllers\User\ChildController::class, 'coursesCatalog'])->name('courses.index');
        Route::get('/platnosci', [\App\Http\Controllers\User\PaymentController::class, 'index'])->name('payments.index');
        Route::post('/platnosci/pay-all', [\App\Http\Controllers\User\PaymentController::class, 'payAll'])->name('payments.pay-all');
        Route::post('/platnosci/{item}/pay', [\App\Http\Controllers\User\PaymentController::class, 'payItem'])->name('payments.pay-item');
    });

require __DIR__ . '/auth.php';

// School Landing Page & Rejestracja przypisana do szkoły
Route::get('/{school:slug}', [SchoolController::class, 'show'])->name('schools.show');
Route::get('/{school:slug}/register', [RegisteredUserController::class, 'createForSchool'])->name('schools.register');
