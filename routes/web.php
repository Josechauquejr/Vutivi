<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DigitalResourceController;
use App\Http\Controllers\PhysicalResourceController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReservationTermsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ResourceController::class, 'index'])->name('index');
Route::get('/library', [ResourceController::class, 'library'])->name('library');
Route::get('/sobre', [ResourceController::class, 'about'])->name('about');
Route::get('/apresentacao', function () {
    return view('vutivi-apresentacao');
})->name('apresentacao');
Route::get('/recurso/{resource:slug}', [ResourceController::class, 'showPublic'])->name('resources.public.show');
Route::get('/mine', [ResourceController::class, 'mine'])->name('mine');
Route::get('/favorites', [ResourceController::class, 'favorites'])->name('favorites');
Route::get('/categories', [ResourceController::class, 'categories'])->name('categories');

Route::middleware('guest')->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'create')->name('login');
        Route::post('/login', 'store')->name('login.store');
        Route::get('/forgot-password', function () {
            return view('users.forgot-password');
        })->name('password.request');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/register', 'create')->name('users.create');
        Route::post('/register', 'store')->name('users.store');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [ResourceController::class, 'index'])->name('home');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/borrowed', [ResourceController::class, 'borrowed'])->name('borrowed');
    Route::get('/lent', [ResourceController::class, 'lent'])->name('lent');
    Route::get('/loan-alerts', [ResourceController::class, 'loanAlerts'])->name('loan-alerts');
    Route::get('/loan-history', [ReservationController::class, 'history'])->name('loan-history');
    Route::get('/account', [ResourceController::class, 'account'])->name('account');
    Route::get('/resources/create', [ResourceController::class, 'create'])->name('resources.create');
    Route::post('/resources/{resource}/favorite', [ResourceController::class, 'toggleFavorite'])->name('resources.favorite');
    Route::get('/resources/{resource}/digital/view', [ResourceController::class, 'viewDigital'])->name('resources.digital.view');
    Route::get('/resources/{resource}/digital/download', [ResourceController::class, 'downloadDigital'])->name('resources.digital.download');

    Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
        Route::get('{user}/edit', 'edit')->name('edit');
        Route::put('{user}', 'update')->name('update');
        Route::delete('{user}', 'destroy')->name('destroy');
    });

    Route::resource('resources', ResourceController::class)->only(['index', 'show', 'destroy']);

    Route::controller(ReservationController::class)->prefix('reservations')->name('reservations.')->group(function () {
        Route::patch('{id}/approve', 'approve')->name('approve');
        Route::patch('{id}/deny', 'deny')->name('deny');
        Route::patch('{id}/return', 'return')->name('return');
        Route::patch('{id}/request-extension', 'requestExtension')->name('extension.request');
        Route::patch('{id}/approve-extension', 'approveExtension')->name('extension.approve');
        Route::patch('{id}/deny-extension', 'denyExtension')->name('extension.deny');
    });

    Route::get('/resources/{resource}/terms', [ReservationTermsController::class, 'showTerms'])->name('reservations.terms.show');
    Route::post('/resources/terms', [ReservationTermsController::class, 'store'])->name('reservations.terms.store');

    Route::resources([
        'physical-resources' => PhysicalResourceController::class,
        'digital-resources' => DigitalResourceController::class,
        'reservations' => ReservationController::class,
    ]);
});
