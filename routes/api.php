<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransferController;
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
Route::prefix('v1')->group(function () {
    Route::prefix('contas')->group(function () {
        Route::post('', [AccountController::class, 'createAccount']);
    });
    Route::prefix('transfer')->group(function () {
        Route::put('', [TransferController::class, 'transferValue']);
    });
});

Route::get('/teste', function () {
    return response()->json(['message' => 'api-online']);
});
