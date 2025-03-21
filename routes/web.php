<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;

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

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

// Ruta para el dashboard (protegida por autenticación)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');





Route::get('/', function () {
    return view('inicio/index');
});

// WEB 
Route::prefix('/usuarios')->group(function () {
    Route::get('/', [UsuarioController::class, 'index']);
    Route::get('/nuevo', [UsuarioController::class, 'create']);
    Route::get('/{id}', [UsuarioController::class, 'show']);
    Route::get('/editar/{id}', [UsuarioController::class, 'edit']);
});

//api
Route::prefix('/api')->group(function () {
    Route::prefix('/usuarios')->group(function () {
        Route::post('/', [UsuarioController::class, 'store'])->name('guardar_usuario');
        Route::put('/{id}', [UsuarioController::class, 'update']);
        Route::delete('/{id}', [UsuarioController::class, 'delete']);
    });
});
