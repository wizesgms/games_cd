<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::namespace('Api')->group(function () {
    Route::controller('ApiController')->group(function () {
        Route::post('v3/{method}', 'methode')->name('api.playerAcoount');
    });
});

Route::namespace('Callback')->group(function () {
    Route::controller('CallbackController')->prefix('Http/v3')->group(function () {
        Route::post('LaunchGame', 'launchGame')->name('api.GamesLaunch');
    });
});

Route::namespace('Site')->group(function () {
    Route::post('getBalance/{id}', 'DashboardController@getBalance')->name('agent.getBalance');
});
