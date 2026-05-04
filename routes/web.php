<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\GuruDashboardController;
use App\Http\Controllers\PotionController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\RaporController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GuruPotionController;

use App\Http\Controllers\SortingHatController;

// Auth
Route::get('/',        [AuthController::class, 'showLogin'])->name('home');
Route::get('/login',   [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',  [AuthController::class, 'login'])->name('login.post');
Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Sorting Hat (student only, after register)
Route::middleware('auth.session')->group(function () {
    Route::get('/sorting-hat',        [SortingHatController::class, 'questions'])->name('sorting-hat.questions');
    Route::post('/sorting-hat',       [SortingHatController::class, 'assign'])->name('sorting-hat.assign');
    Route::get('/sorting-hat/result', [SortingHatController::class, 'result'])->name('sorting-hat.result');
});

// Student routes
Route::middleware(['auth.session', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile',    [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/photo', [UserController::class, 'uploadPhoto'])->name('profile.photo');
    Route::delete('/account', [UserController::class, 'deleteAccount'])->name('account.delete');
    Route::get('/rapor',     [RaporController::class, 'studentIndex'])->name('rapor');
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::delete('/inventory/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy');

    // Potions (Racik Ramuan)
    Route::get('/potions',          [PotionController::class, 'index'])->name('potions.index');
    Route::get('/potions/create',   [PotionController::class, 'create'])->name('potions.create');
    Route::post('/potions',         [PotionController::class, 'store'])->name('potions.store');
    Route::get('/potions/{id}',     [PotionController::class, 'show'])->name('potions.show');
    Route::delete('/potions/{id}',  [PotionController::class, 'destroy'])->name('potions.destroy');
});

// Guru routes
Route::middleware(['auth.session', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users',     [UserController::class, 'index'])->name('users');

    // Validasi Ramuan
    Route::get('/potions',              [GuruPotionController::class, 'index'])->name('potions');
    Route::get('/potions/{id}',         [GuruPotionController::class, 'show'])->name('potions.show');
    Route::post('/potions/{id}/validate', [GuruPotionController::class, 'validate'])->name('potions.validate');

    // Raport
    Route::get('/rapor',                              [RaporController::class, 'guruIndex'])->name('rapor');
    Route::get('/rapor/{student_name}/{semester}',    [RaporController::class, 'edit'])->name('rapor.edit');
    Route::put('/rapor/{student_name}/{semester}',    [RaporController::class, 'update'])->name('rapor.update');
});
