<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SalasController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\ReservasController;
use App\Http\Controllers\ReporteController;

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


Route::get('p1', [salasController::class, 'prueba']);
Route::get('p2', [ReservasController::class, function () {
    $reservas = \App\Models\Reservas::get();
    return response()->json($reservas);

}]);

Route::get('/reserva/{id}' , [ReservasController::class , 'show_reserva'])->name('show.reserva');
Route::get('p3', [ReservasController::class, function () {
    $salas = \App\Models\Salas::get();
    return response()->json($salas);

}]);
Route::get('home2',function (){
    return view('sidebard.index');
});

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('user/nuevo', [UserController::class, 'create']);
Route::post('user/nuevo', [UserController::class, 'store'])->name('guardar_usuario');

//ruta LDAP; sincronización usuarios
Route::post('/users/sync', [UserController::class, 'sync'])->name('users.sync');

Route::get('/', [InicioController::class, 'inicio'])->name('/')->middleware('auth');
Route::prefix('/auth')->middleware('auth')->group(function () {

    Route::resource('salas', SalasController::class); 
    Route::get('listar-todas-salas', [SalasController::class,'listarTodas']);
    Route::post('buscar-salas-horarios-disponibles', [ReservasController::class,'buscar_salas_horarios_disponibles'])
        ->name('buscar_salas_horarios_disponibles');
    Route::resource('reservas', ReservasController::class);
    Route::get('listar_reservas_calendario',[ReservasController::class,'listar_reservas_calendario'])
        ->name('listar_reservas_calendario');

    Route::resource('reportes', ReportesController::class);

    // WEB
    Route::prefix('/usuarios')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('usuarios.index');
        Route::get('/nuevo', [UserController::class, 'create'])->name('usuarios.create');
        Route::get('/{id}', [UserController::class, 'show'])->name('usuarios.show');
        Route::get('/editar/{id}', [UserController::class, 'edit'])->name('usuarios.edit');
    });
    //reportes index 
    Route::prefix('/reportes')->group(function () {
        Route::get('/' , [ReporteController::class ,'index'])->name("reportes.index");
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

