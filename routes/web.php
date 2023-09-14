<?php

use App\Http\Controllers\SimplexController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [SimplexController::class, 'index']);
Route::post('/input', [SimplexController::class, 'input']);
Route::post('/hitung', [SimplexController::class, 'hitung']);
