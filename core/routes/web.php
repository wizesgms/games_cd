<?php

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

Route::namespace('Api')->group(function () {
    Route::controller('ApiController')->group(function () {
        Route::any('/', 'methode')->name('api');
        Route::post('callback', 'callbacktb')->name('callbacktb');
        Route::post('callback_mb/{method}', 'callback_mb')->name('callback_mb');
        Route::get('/provider', 'provider_save')->name('api');
        Route::get('/GamesLobby', 'games_lobby')->name('games_lobby');
    });
});
