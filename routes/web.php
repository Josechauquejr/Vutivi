<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DigitalResourceController;
use App\Http\Controllers\FineController;
use App\Http\Controllers\LibrarianController;
use App\Http\Controllers\ModerationController;
use App\Http\Controllers\PhysicalResourceController;
use App\Http\Controllers\ReadingListController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ResourceReviewController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReservationTermsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/font/golos-text.ttf', fn () => response()->file(resource_path('font/GolosText-VariableFont_wght.ttf')))->name('font.golos-text');
Route::get('/font/space-grotesk.ttf', fn () => response()->file(resource_path('font/SpaceGrotesk-VariableFont_wght.ttf')))->name('font.space-grotesk');
Route::get('/', [ResourceController::class, 'index'])->name('index');
Route::get('/logout',[LoginController::class, 'destroy'])->name('logout');
Route::get('/library', [ResourceController::class, 'library'])->middleware('not_admin')->name('library');
Route::get('/library/suggestions', [ResourceController::class, 'suggestions'])->middleware('not_admin')->name('library.suggestions');
Route::get('/sobre', [ResourceController::class, 'about'])->name('about');
Route::get('/apresentacao', function () {
    return view('pages.presentation');
})->name('apresentacao');
Route::get('/recurso/{resource:slug}', [ResourceController::class, 'showPublic'])->middleware('not_admin')->name('resources.public.show');
Route::get('/mine', [ResourceController::class, 'mine'])->middleware('not_admin')->name('mine');
Route::get('/favorites', [ResourceController::class, 'favorites'])->middleware('not_admin')->name('favorites');
Route::get('/categories', [ResourceController::class, 'categories'])->middleware('not_admin')->name('categories');
Route::get('/moderation', [ModerationController::class, 'index'])->name('moderation.index');
Route::get('/api/moderation/state', [ModerationController::class, 'state'])->name('moderation.api.state');
Route::post('/api/moderation/users', [ModerationController::class, 'users'])->name('moderation.api.users');
Route::patch('/api/moderation/users/{user}', [ModerationController::class, 'updateUser'])->name('moderation.api.users.update');
Route::delete('/api/moderation/users/{user}', [ModerationController::class, 'destroyUser'])->name('moderation.api.users.destroy');
Route::patch('/api/moderation/users/{user}/ban', [ModerationController::class, 'banUser'])->name('moderation.api.users.ban');
Route::post('/api/moderation/me', [ModerationController::class, 'updateMe'])->name('moderation.api.me.update');
Route::post('/api/moderation/blacklist/words', [ModerationController::class, 'storeBlacklistWord'])->name('moderation.api.blacklist.words.store');
Route::post('/api/moderation/blacklist/hashes', [ModerationController::class, 'storeBlacklistHash'])->name('moderation.api.blacklist.hashes.store');
Route::get('/api/moderation/blacklist/words', [ModerationController::class, 'blacklistWords'])->name('moderation.api.blacklist.words');
Route::get('/api/moderation/blacklist/hashes', [ModerationController::class, 'blacklistHashes'])->name('moderation.api.blacklist.hashes');
Route::patch('/api/moderation/blacklist/words/{blacklistWord}', [ModerationController::class, 'updateBlacklistWord'])->name('moderation.api.blacklist.words.update');
Route::patch('/api/moderation/blacklist/hashes/{blacklistHash}', [ModerationController::class, 'updateBlacklistHash'])->name('moderation.api.blacklist.hashes.update');
Route::delete('/api/moderation/blacklist/words/{blacklistWord}', [ModerationController::class, 'destroyBlacklistWord'])->name('moderation.api.blacklist.words.destroy');
Route::delete('/api/moderation/blacklist/hashes/{blacklistHash}', [ModerationController::class, 'destroyBlacklistHash'])->name('moderation.api.blacklist.hashes.destroy');
Route::patch('/api/moderation/resources/{resource}', [ModerationController::class, 'moderate'])->name('moderation.api.resources.moderate');
Route::get('/moderation/resources/{resource}/preview', [ModerationController::class, 'preview'])->name('moderation.resources.preview');

Route::middleware('guest')->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'create')->name('login');
        Route::post('/login', 'store')->name('login.store');
    });

    Route::controller(PasswordResetController::class)->group(function () {
        Route::get('/forgot-password', 'create')->name('password.request');
        Route::post('/forgot-password', 'store')->name('password.email');
        Route::get('/reset-password/{token}', 'edit')->name('password.reset');
        Route::post('/reset-password', 'update')->name('password.update');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/register', 'create')->name('users.create');
        Route::post('/register', 'store')->name('users.store');
    });
});

Route::middleware(['auth', 'not_admin'])->group(function () {
    Route::get('/home', [ResourceController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/borrowed', [ResourceController::class, 'borrowed'])->name('borrowed');
    Route::get('/lent', [ResourceController::class, 'lent'])->name('lent');
    Route::get('/loan-alerts', [ResourceController::class, 'loanAlerts'])->name('loan-alerts');
    Route::get('/loan-history', [ReservationController::class, 'history'])->name('loan-history');
    Route::get('/account', [ResourceController::class, 'account'])->name('account');
    Route::get('/resources/create', [ResourceController::class, 'create'])->name('resources.create');
    Route::post('/resources/{resource}/favorite', [ResourceController::class, 'toggleFavorite'])->name('resources.favorite');
    Route::get('/resources/{resource}/digital/view', [ResourceController::class, 'viewDigital'])->name('resources.digital.view');
    Route::get('/resources/{resource}/digital/download', [ResourceController::class, 'downloadDigital'])->name('resources.digital.download');

    // Reviews
    Route::post('/resources/{resource}/reviews', [ResourceReviewController::class, 'store'])->name('resources.reviews.store');
    Route::delete('/resources/{resource}/reviews', [ResourceReviewController::class, 'destroy'])->name('resources.reviews.destroy');

    // Reading lists
    Route::get('/reading-lists', [ReadingListController::class, 'index'])->name('reading-lists.index');
    Route::post('/reading-lists', [ReadingListController::class, 'store'])->name('reading-lists.store');
    Route::delete('/reading-lists/{list}', [ReadingListController::class, 'destroy'])->name('reading-lists.destroy');
    Route::post('/reading-lists/{list}/items', [ReadingListController::class, 'addItem'])->name('reading-lists.items.add');
    Route::delete('/reading-lists/{list}/items/{resource}', [ReadingListController::class, 'removeItem'])->name('reading-lists.items.remove');

    // Fines
    Route::get('/fines', [FineController::class, 'index'])->name('fines.index');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/active-loans', [ReportController::class, 'activeLoans'])->name('reports.active-loans');
    Route::get('/reports/overdue-loans', [ReportController::class, 'overdueLoans'])->name('reports.overdue-loans');
    Route::get('/reports/loan-history', [ReportController::class, 'loanHistory'])->name('reports.loan-history');
    Route::get('/reports/fines', [ReportController::class, 'fines'])->name('reports.fines');

    Route::controller(UserController::class)->group(function () {
        Route::get('/account/edit', 'edit')->name('users.edit');
        Route::put('/account', 'update')->name('users.update');
        Route::delete('/account', 'destroy')->name('users.destroy');
    });

    Route::resource('resources', ResourceController::class)->only(['index', 'show', 'destroy']);

    Route::controller(ReservationController::class)->prefix('reservations')->name('reservations.')->group(function () {
        Route::patch('{id}/approve', 'approve')->name('approve');
        Route::patch('{id}/deny', 'deny')->name('deny');
        Route::patch('{id}/return', 'return')->name('return');
        Route::patch('{id}/auto-renew', 'autoRenew')->name('auto-renew');
        Route::patch('{id}/request-extension', 'requestExtension')->name('extension.request');
        Route::patch('{id}/approve-extension', 'approveExtension')->name('extension.approve');
        Route::patch('{id}/deny-extension', 'denyExtension')->name('extension.deny');
        Route::post('bulk', 'bulkAction')->name('bulk');
    });

    Route::get('/resources/{resource}/terms', [ReservationTermsController::class, 'showTerms'])->name('reservations.terms.show');
    Route::post('/resources/terms', [ReservationTermsController::class, 'store'])->name('reservations.terms.store');

    Route::resources([
        'physical-resources' => PhysicalResourceController::class,
        'digital-resources' => DigitalResourceController::class,
    ]);

    // Reservations with throttle on write operations
    Route::resource('reservations', ReservationController::class)
        ->only(['index', 'show', 'create', 'edit', 'destroy']);

    Route::middleware('throttle:reservations')->group(function () {
        Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
        Route::put('/reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
        Route::patch('/reservations/{reservation}', [ReservationController::class, 'update']);
    });
});

Route::middleware(['auth', 'librarian'])->prefix('librarian')->name('librarian.')->group(function () {
    Route::get('/', [LibrarianController::class, 'dashboard'])->name('dashboard');
    Route::patch('reservations/{id}/pickup', [LibrarianController::class, 'confirmPickup'])->name('reservations.pickup');
    Route::patch('reservations/{id}/return', [LibrarianController::class, 'confirmReturn'])->name('reservations.return');
    Route::patch('reservations/{id}/approve', [LibrarianController::class, 'approvePending'])->name('reservations.approve');
    Route::patch('reservations/{id}/deny', [LibrarianController::class, 'denyPending'])->name('reservations.deny');
    Route::patch('fines/{id}/pay', [LibrarianController::class, 'markFinePaid'])->name('fines.pay');
});

Route::middleware('auth')->post('/logout', [LoginController::class, 'destroy'])->name('logout');
