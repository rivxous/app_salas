<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\ReservasController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('buscar-salas-horios-disponibles', [ReservasController::class,'buscar_salas_horios_disponibles'])->name('buscar_salas_horios_disponibles');

Route::get('test' , function(){
    return ldap_connect(env('LDAP_HOST'));
});