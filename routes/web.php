<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SalasController;
use App\Http\Controllers\ReservasController;


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


Route::get('p1', [ReservasController::class, 'prueba']);
Route::get('p2', [ReservasController::class, function () {
    $reservas = \App\Models\Reservas::get();
    return response()->json($reservas);

}]);
Route::get('p3', [ReservasController::class, function () {
    $salas = \App\Models\Salas::get();
    return response()->json($salas);

}]);
Route::get('/', function () {
    return redirect()->route('reservas.index');
})->name('/');
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('user/nuevo', [UserController::class, 'create']);
Route::post('user/nuevo', [UserController::class, 'store'])->name('guardar_usuario');

Route::prefix('/auth')->middleware('auth')->group(function () {

    Route::resource('salas', SalasController::class);
    Route::resource('reservas', ReservasController::class);

    // WEB
    Route::prefix('/usuarios')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/nuevo', [UserController::class, 'create']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::get('/editar/{id}', [UserController::class, 'edit']);
    });

    //api
    Route::prefix('/api')->group(function () {
        Route::prefix('/user')->group(function () {
//            Route::post('/', [UserController::class, 'store'])->name('guardar_usuario');
            Route::put('/{id}', [UserController::class, 'update']);
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        });
    });
});

