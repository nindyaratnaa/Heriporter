<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Middleware\ApiAuthMiddleware;

// Public
Route::post('/auth/login',    [ApiController::class, 'login']);
Route::post('/auth/register', [ApiController::class, 'register']);

// Protected
Route::middleware(ApiAuthMiddleware::class)->group(function () {

    // Auth
    Route::get('/auth/me', [ApiController::class, 'me']);

    // Users (Guru only) — [Khaula]
    Route::get('/users',      [ApiController::class, 'users']);
    Route::get('/users/{id}', [ApiController::class, 'userShow']);

    // Potions — [Nya]
    Route::get('/potions',                    [ApiController::class, 'potions']);
    Route::post('/potions',                   [ApiController::class, 'potionStore']);
    Route::get('/potions/{id}',               [ApiController::class, 'potionShow']);
    Route::delete('/potions/{id}',            [ApiController::class, 'potionDestroy']);
    Route::post('/potions/{id}/validate',     [ApiController::class, 'potionValidate']);

    // Inventory — [Sefina]
    Route::get('/inventory',          [ApiController::class, 'inventory']);
    Route::delete('/inventory/{id}',  [ApiController::class, 'inventoryDestroy']);

    // Raport — [Adzkia]
    Route::get('/rapor',          [ApiController::class, 'rapor']);
    Route::get('/rapor/{id}',     [ApiController::class, 'raporShow']);
    Route::put('/rapor/{id}',     [ApiController::class, 'raporUpdate']);
});
