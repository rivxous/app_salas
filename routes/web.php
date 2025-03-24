<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Route::middleware(['auth', 'auth.session'])->group(function () {
// Route::get('/', function () {
// return view('inicio/index');
// });
// });

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservaController;


Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('usuarios/nuevo', [UserController::class, 'create']);

Route::get('/', function () {
    return view('inicio/index');
})->middleware('auth');

Route::prefix('auth')->middleware('auth')->group(function () {
    // WEB 
    Route::prefix('/usuarios')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/nuevo', [UserController::class, 'create']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::get('/editar/{id}', [UserController::class, 'edit']);
    });

    //api
    Route::prefix('/api')->group(function () {
        Route::prefix('/usuarios')->group(function () {
            Route::post('/', [UserController::class, 'store'])->name('guardar_usuario');
            Route::put('/{id}', [UserController::class, 'update']);
            Route::delete('/{id}', [UserController::class, 'delete']);
        });
    });
});
