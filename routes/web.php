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

// Ogólnodostępne pobieranie / podgląd dokumentów placówki (Regulamin, RODO itp.)
Route::get('/documents/{document}/download', \App\Http\Controllers\DocumentDownloadController::class)->name('documents.download');

////////////////////////////////////// Admin szkoły
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('/users', UserController::class)->names('user');
        Route::resource('/seasons', \App\Http\Controllers\Admin\SeasonController::class)->names('seasons');
        Route::post('/seasons/{season}/activate', [\App\Http\Controllers\Admin\SeasonController::class, 'activate'])->name('seasons.activate');
        Route::resource('/courses', \App\Http\Controllers\Admin\CourseController::class)->names('courses');
        Route::post('/courses/{course}/reorder-children', [\App\Http\Controllers\Admin\CourseController::class, 'reorderChildren'])->name('courses.reorder-children');
        Route::post('/courses/{course}/enroll-children', [\App\Http\Controllers\Admin\CourseController::class, 'enrollChildren'])->name('courses.enroll-children');
        Route::delete('/courses/{course}/unenroll-child/{child}', [\App\Http\Controllers\Admin\CourseController::class, 'unenrollChild'])->name('courses.unenroll-child');
        Route::get('/children', [\App\Http\Controllers\Admin\ChildController::class, 'index'])->name('children.index');
        Route::get('/billing', [\App\Http\Controllers\Admin\BillingController::class, 'index'])->name('billing.index');
        Route::post('/billing/{item}/mark-paid', [\App\Http\Controllers\Admin\BillingController::class, 'markPaid'])->name('billing.mark-paid');
        Route::post('/billing/{item}/mark-unpaid', [\App\Http\Controllers\Admin\BillingController::class, 'markUnpaid'])->name('billing.mark-unpaid');
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
        Route::resource('/documents', \App\Http\Controllers\Admin\DocumentController::class)->names('documents');
        Route::get('/documents/{document}/download', \App\Http\Controllers\DocumentDownloadController::class)->name('documents.download');
        Route::get('/notifications/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('notifications.create');
        Route::post('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('notifications.store');
        Route::post('/notifications/billing-summary', [\App\Http\Controllers\Admin\NotificationController::class, 'sendBillingSummary'])->name('notifications.billing-summary');
    });

////////////////////////////////////// Teacher / Nauczyciel
Route::middleware(['auth', 'verified', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::get('/courses', [\App\Http\Controllers\Teacher\CourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{course}', [\App\Http\Controllers\Teacher\CourseController::class, 'show'])->name('courses.show');
        Route::post('/courses/{course}/reorder-children', [\App\Http\Controllers\Teacher\CourseController::class, 'reorderChildren'])->name('courses.reorder-children');
        Route::post('/courses/{course}/enroll-children', [\App\Http\Controllers\Teacher\CourseController::class, 'enrollChildren'])->name('courses.enroll-children');
        Route::delete('/courses/{course}/unenroll-child/{child}', [\App\Http\Controllers\Teacher\CourseController::class, 'unenrollChild'])->name('courses.unenroll-child');
        Route::get('/children', [\App\Http\Controllers\Teacher\CourseController::class, 'childrenIndex'])->name('children.index');
        Route::get('/lessons', [\App\Http\Controllers\Teacher\LessonController::class, 'index'])->name('lessons.index');
        Route::get('/courses/{course}/lessons/create', [\App\Http\Controllers\Teacher\LessonController::class, 'create'])->name('lessons.create');
        Route::post('/courses/{course}/lessons', [\App\Http\Controllers\Teacher\LessonController::class, 'store'])->name('lessons.store');
        Route::get('/lessons/{lesson}', [\App\Http\Controllers\Teacher\LessonController::class, 'show'])->name('lessons.show');
        Route::get('/notifications/create', [\App\Http\Controllers\Teacher\NotificationController::class, 'create'])->name('notifications.create');
        Route::post('/notifications', [\App\Http\Controllers\Teacher\NotificationController::class, 'store'])->name('notifications.store');
    });

////////////////////////////////////// User / Rodzic & Wspólne dla zalogowanych
Route::middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/pulpit', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
        Route::post('/switch-role', \App\Http\Controllers\SwitchRoleController::class)->name('role.switch');
        Route::post('/switch-school', \App\Http\Controllers\SwitchSchoolController::class)->name('school.switch');
        Route::post('/school-terms/accept', \App\Http\Controllers\AcceptSchoolTermsController::class)->name('school.terms.accept');
        Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Powiadomienia użytkownika
        Route::get('/powiadomienia', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/powiadomienia/{id}/read', [\App\Http\Controllers\NotificationController::class, 'read'])->name('notifications.read');
        Route::get('/powiadomienia/{id}/go', [\App\Http\Controllers\NotificationController::class, 'read'])->name('notifications.go');
        Route::post('/powiadomienia/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::get('/powiadomienia/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
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
        Route::get('/platnosci/sukces', [\App\Http\Controllers\User\PaymentController::class, 'success'])->name('payments.success');
        Route::get('/platnosci/blad', [\App\Http\Controllers\User\PaymentController::class, 'error'])->name('payments.error');
        Route::get('/dokumenty', [\App\Http\Controllers\User\DocumentController::class, 'index'])->name('documents.index');
        Route::get('/dokumenty/{document}/download', \App\Http\Controllers\DocumentDownloadController::class)->name('documents.download');
        Route::post('/dokumenty/zgody', [\App\Http\Controllers\User\DocumentController::class, 'updateConsents'])->name('documents.consents');
    });

// Tpay Webhook
Route::post('/tpay/webhook', \App\Http\Controllers\TpayWebhookController::class)->name('tpay.webhook');

////////////////////////////////////// SuperAdmin (SU)
Route::middleware(['auth', 'verified', 'su'])
    ->prefix('su')
    ->name('su.')
    ->group(function () {
        Route::get('/', [\App\Http\Controllers\Su\SchoolController::class, 'index'])->name('dashboard');
        Route::resource('/schools', \App\Http\Controllers\Su\SchoolController::class);
        Route::post('/schools/{school}/switch', [\App\Http\Controllers\Su\SchoolController::class, 'switch'])->name('schools.switch');
        Route::post('/schools/{school}/tpay-whitelist', [\App\Http\Controllers\Su\SchoolController::class, 'tpayWhitelist'])->name('schools.tpay-whitelist');
        Route::post('/schools/{school}/tpay-manual-verify', [\App\Http\Controllers\Su\SchoolController::class, 'tpayManualVerify'])->name('schools.tpay-manual-verify');
        Route::get('/settings', [\App\Http\Controllers\Su\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [\App\Http\Controllers\Su\SettingController::class, 'store'])->name('settings.store');
        Route::put('/settings', [\App\Http\Controllers\Su\SettingController::class, 'updateAll'])->name('settings.update-all');
        Route::delete('/settings/{setting}', [\App\Http\Controllers\Su\SettingController::class, 'destroy'])->name('settings.destroy');
    });

require __DIR__ . '/auth.php';

// School Landing Page & Rejestracja przypisana do szkoły
Route::get('/{school:slug}', [SchoolController::class, 'show'])->name('schools.show');
Route::get('/{school:slug}/register', [RegisteredUserController::class, 'createForSchool'])->name('schools.register');
