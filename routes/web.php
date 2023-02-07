<?php

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

Route::get('/about-donate', function () {
    return view('aboutDonate');
});


Route::get('/feedback', function () {
    return view('feedback');
});

Route::post('/results', [ResultsController::class, 'index']);
Route::get('/results', [ResultsController::class, function(){
    return view('resultsGET');
}]);