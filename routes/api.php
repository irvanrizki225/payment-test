<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\EncryptionController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PaymentController;

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



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api', 'prefix' => 'v1'], function () {
    //encription
    Route::post('/encrypt', [EncryptionController::class, 'encrypt']);
    Route::post('/decrypt', [EncryptionController::class, 'decrypt']);

    //auth
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');

    //payments
    Route::middleware('throttle:30,1')->group(function () {
        Route::post('payment', [PaymentController::class, 'CreatePayment'])->middleware('auth:api');
        Route::post('payment/{id}/status', [PaymentController::class, 'ChangeStatus'])->middleware('auth:api');
        Route::get('payment/history', [PaymentController::class, 'HistoryPayment'])->middleware('auth:api');
        Route::get('payment/summary', [PaymentController::class, 'SummaryPayment'])->middleware('auth:api');
    });





  });
