<?php

use App\Http\Controllers\gameController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/' , [gameController::class , 'index']);
Route::post('/save' , [gameController::class , 'saveGame']);
Route::get('/history' , [gameController::class , 'history']);
Route::post('/history/show' , [gameController::class , 'show']);
