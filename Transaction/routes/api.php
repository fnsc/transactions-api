<?php

use Illuminate\Support\Facades\Route;
use Transaction\Infra\Http\Controllers\TransfersController;

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
        ->middleware(['auth:sanctum', 'can.send.transfer'])
        ->name('.store');

    Route::get('forbidden', [TransfersController::class, 'forbidden'])
        ->name('.forbidden');

    Route::get('unauthorized', [TransfersController::class, 'unauthorized'])
        ->name('.unauthorized');
});
