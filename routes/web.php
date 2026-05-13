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

/*AUTH*/

Route::get('/', [AuthController::class, 'showLogin'])->name('home');

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login')->name('login.post');

    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register')->name('register.post');

    Route::post('/logout', 'logout')->name('logout');
});

/*SORTING HAT*/

Route::middleware('auth.session')->group(function () {
    Route::get('/sorting-hat', [SortingHatController::class, 'questions'])
        ->name('sorting-hat.questions');

    Route::post('/sorting-hat', [SortingHatController::class, 'assign'])
        ->name('sorting-hat.assign');

    Route::get('/sorting-hat/result', [SortingHatController::class, 'result'])
        ->name('sorting-hat.result');
});

/*STUDENT ROUTES*/

Route::middleware(['auth.session', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        /*DASHBOARD & PROFILE*/

        Route::get('/dashboard', [StudentDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/profile', [UserController::class, 'profile'])
            ->name('profile');

        Route::post('/profile/photo', [UserController::class, 'uploadPhoto'])
            ->name('profile.photo');

        Route::delete('/account', [UserController::class, 'deleteAccount'])
            ->name('account.delete');

        /*JSON DATA (FOR VUE / AJAX)*/

        Route::get('/profile/data', function () {

            $json = app(\App\Services\JsonService::class);

            $user = collect($json->read('users'))
                ->firstWhere('id', session('user_id'));

            $potions = collect($json->read('potions'))
                ->where('student_id', session('user_id'));

            $wand = !empty($user['wand_id'])
                ? collect($json->read('wands'))
                    ->firstWhere('id', $user['wand_id'])
                : null;

            return response()->json([
                'user'  => $user,
                'wand'  => $wand,
                'stats' => [
                    'total'    => $potions->count(),
                    'approved' => $potions->where('status', 'approved')->count(),
                    'pending'  => $potions->where('status', 'pending')->count(),
                    'rejected' => $potions->where('status', 'rejected')->count(),
                ],
            ]);
        })->name('profile.data');

        Route::get('/dashboard/data', function () {

            $json = app(\App\Services\JsonService::class);

            $user = collect($json->read('users'))
                ->firstWhere('id', session('user_id'));

            $potions = collect($json->read('potions'))
                ->where('student_id', session('user_id'));

            return response()->json([
                'user' => $user,

                'stats' => [
                    'total'    => $potions->count(),
                    'approved' => $potions->where('status', 'approved')->count(),
                    'pending'  => $potions->where('status', 'pending')->count(),
                    'rejected' => $potions->where('status', 'rejected')->count(),
                ],

                'recent' => $potions
                    ->sortByDesc('created_at')
                    ->take(3)
                    ->values()
                    ->all(),
            ]);
        })->name('dashboard.data');

        Route::get('/inventory/data', function () {

            $json = app(\App\Services\JsonService::class);

            $inventory = collect($json->read('potions'))
                ->where('student_id', session('user_id'))
                ->where('status', 'approved')
                ->values()
                ->all();

            return response()->json($inventory);

        })->name('inventory.data');

        /*RAPOR*/

        Route::get('/rapor', [RaporController::class, 'studentIndex'])
            ->name('rapor');

        /*INVENTORY*/

        Route::get('/inventory', [InventoryController::class, 'index'])
            ->name('inventory');

        Route::delete('/inventory/{id}', [InventoryController::class, 'destroy'])
            ->name('inventory.destroy');

        /*POTIONS (RACIK RAMUAN)*/

        Route::get('/potions', [PotionController::class, 'index'])
            ->name('potions.index');

        Route::get('/potions/create', [PotionController::class, 'create'])
            ->name('potions.create');

        Route::post('/potions', [PotionController::class, 'store'])
            ->name('potions.store');

        Route::get('/potions/{id}', [PotionController::class, 'show'])
            ->name('potions.show');

        Route::delete('/potions/{id}', [PotionController::class, 'destroy'])
            ->name('potions.destroy');
    });

/*GURU ROUTES*/

Route::middleware(['auth.session', 'role:guru'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {

        /*DASHBOARD*/

        Route::get('/dashboard', [GuruDashboardController::class, 'index'])
            ->name('dashboard');

        /*USERS*/

        Route::get('/users', [UserController::class, 'index'])
            ->name('users');

        /*VALIDASI RAMUAN*/

        Route::get('/potions', [GuruPotionController::class, 'index'])
            ->name('potions');

        Route::get('/potions/{id}', [GuruPotionController::class, 'show'])
            ->name('potions.show');

        Route::post('/potions/{id}/validate', [GuruPotionController::class, 'validate'])
            ->name('potions.validate');

        /*RAPOR*/

        Route::get('/rapor', [RaporController::class, 'guruIndex'])
            ->name('rapor');

        Route::get('/rapor/{student_name}/{semester}', [RaporController::class, 'edit'])
            ->name('rapor.edit');

        Route::put('/rapor/{student_name}/{semester}', [RaporController::class, 'update'])
            ->name('rapor.update');
    });