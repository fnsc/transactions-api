<?php

use Illuminate\Support\Facades\Route;
use Transaction\Infra\Http\Controllers\TransactionsController;

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
    Route::post('', [TransactionsController::class, 'store'])
        ->middleware(['auth:sanctum', 'can.send.transfer'])
        ->name('.store');

    Route::get('forbidden', [TransactionsController::class, 'forbidden'])
        ->name('.forbidden');

    Route::get('unauthorized', [TransactionsController::class, 'unauthorized'])
        ->name('.unauthorized');
});
