<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

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


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('numbers', function () {
        return response()->json([1, 2, 3, 4, 5, 6, 7, 8, 9]);
    });
});
