<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MerchantOrderController;
use App\Http\Controllers\TransferOrderController;
use App\Http\Controllers\WalletController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('login', [LoginController::class, 'get'])->name('login');
Route::post('login', [LoginController::class, 'post']);
Route::post('register', [LoginController::class, 'register']);
Route::get('merchant_orders', [MerchantOrderController::class, 'show']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('numbers', function () {
        return response()->json([1, 2, 3, 4, 5, 6, 7, 8, 9]);
    });
    Route::get('wallets', [WalletController::class, 'show']);
    Route::post('transfer_order', [TransferOrderController::class, 'create']);
    Route::get('transfer_orders', [TransferOrderController::class, 'show']);
    Route::post('merchant_orders', [MerchantOrderController::class, 'create']);
});
