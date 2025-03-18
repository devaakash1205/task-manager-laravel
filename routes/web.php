<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
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

Route::get('/', [TaskController::class, 'index']);
Route::get('/tasks', [TaskController::class, 'getTasks']);
Route::get('/tasks/all', [TaskController::class, 'getAllTasks']);
Route::post('/tasks', [TaskController::class, 'store']);
Route::post('/tasks/{id}/complete', [TaskController::class, 'markAsCompleted']);
Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);

