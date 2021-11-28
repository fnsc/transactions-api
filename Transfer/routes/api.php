<?php

use Illuminate\Support\Facades\Route;
use Transfer\Http\Controllers\TransfersController;

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

Route::group([
    'prefix' => 'v1/transfers',
    'as' => 'api.v1.transfers',
], function () {
    Route::post('', [TransfersController::class, 'store'])
        ->middleware(['auth', 'can.send.transfer'])
        ->name('.store');
});
