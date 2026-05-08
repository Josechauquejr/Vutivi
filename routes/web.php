<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DigitalResourceController;
use App\Http\Controllers\PhysicalResourceController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ResourceController::class, 'library'])->name('library');
Route::get('/mine', [ResourceController::class, 'mine'])->name('mine');
Route::get('/favorites', [ResourceController::class, 'favorites'])->name('favorites');
Route::get('/categories', [ResourceController::class, 'categories'])->name('categories');

Route::middleware('guest')->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'create')->name('login');
        Route::post('/login', 'store')->name('login.store');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/register', 'create')->name('users.create');
        Route::post('/register', 'store')->name('users.store');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [ResourceController::class, 'library'])->name('home');

    Route::prefix('users')->name('users.')->controller(UserController::class)->group(function () {
        Route::get('{user}/edit', 'edit')->name('edit');
        Route::put('{user}', 'update')->name('update');
        Route::delete('{user}', 'destroy')->name('destroy');
    });

    Route::resource('resources', ResourceController::class)->only(['index', 'show', 'destroy']);

    Route::controller(ReservationController::class)->prefix('reservations')->name('reservations.')->group(function () {
        Route::patch('{id}/return', 'return')->name('return');
    });

    Route::resources([
        'physical-resources' => PhysicalResourceController::class,
        'digital-resources' => DigitalResourceController::class,
        'reservations' => ReservationController::class,
    ]);
});
