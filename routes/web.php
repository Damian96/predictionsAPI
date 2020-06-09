<?php

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

Route::middleware('web')->prefix('/v1/')->group(function () {
    Route::get('/predictions', 'ApiController@getAll')->name('predictions.getAll');
    Route::post('/predictions/{prediction}/status', 'ApiController@updateStatus')->name('predictions.update');
    Route::post('/predictions', 'ApiController@createPrediction')->name('predictions.create');
});
