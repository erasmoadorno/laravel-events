<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [EventController::class, 'events']);
Route::get("/evento/novo", [EventController::class, 'newEvent'])->middleware('auth');
Route::post("submitnewevent", [EventController::class, 'store'])->middleware('auth');
Route::get("/dashboard", [EventController::class, 'dashboard'])->middleware('auth');
Route::get("/evento/editar/{id}", [EventController::class, 'editEvent'])->middleware('auth');
Route::put("/submiteditevent/{id}", [EventController::class, 'update'])->middleware("auth");
Route::delete("/evento/delete/{id}", [EventController::class, 'destroy'])->middleware("auth");
Route::get("/evento/{id}", [EventController::class, 'event']);
Route::get("/evento/entrar/{id}", [EventController::class, 'joinEvent'])->middleware('auth');
Route::post("/evento/convidar", [EventController::class, 'inviteEvent'])->middleware('auth');
Route::put("/evento/convite", [EventController::class, 'inviteDecision'])->middleware('auth');
Route::put("/evento/requisicao", [EventController::class, 'requireDecision'])->middleware('auth');
