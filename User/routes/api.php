<?php

use Illuminate\Support\Facades\Route;
use Transaction\Infra\Http\Controllers\UsersController;

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
    'prefix' => 'v1/users',
    'as' => 'api.v1.users',
], function () {
    Route::post('', [UsersController::class, 'store'])->name('.store');
    Route::post('login', [UsersController::class, 'login'])->name('.login');
});
