<?php

#TODO alphabetical order and move to controller
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResultsController;





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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/results', [ResultsController::class, 'index']);