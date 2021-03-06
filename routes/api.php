<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('/predictions/')->group(function () {
    Route::get('/', 'ApiController@getAll')->name('predictions.getAll');
    Route::post('/{prediction}/status', 'ApiController@updateStatus')->name('predictions.update');
    Route::post('/', 'ApiController@create')->name('predictions.create');
});

Route::group(['prefix' => '/fallback'], function () {
    Route::get('/400', 'ApiController@error400')->name('api.fallback.400');
    Route::get('/400', 'ApiController@error404')->name('api.fallback.404');
});

