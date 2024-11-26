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

Route::namespace('Callback')->group(function () {
    Route::controller('CallbackController')->group(function () {

        /**ROUTE GS */
        Route::post('Seamless/{method}','seamles')->name('callbak.seamless');
        /**END ROUTE */

        /**ROUTE CALLBACK FIVERS AND WS */
        Route::post('gold_api/user_balance','getUserBalance')->name('callbak.user_balance');
        Route::post('gold_api/game_callback','gameCallback')->name('callbak.game_callback');
        Route::post('gold_api','gold_api')->name('callbak.gold_api');
        /**END ROUTE */
    });
});
