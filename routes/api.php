<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\RazorpayController;
use App\Http\Controllers\WebhookController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/players', PlayerController::class);

    Route::post('/register', [RegistrationController::class, 'store']);

// Route::middleware(['auth:sanctum', VerifyCsrfToken::class])->group(function () {
    Route::post('/list', [RegistrationController::class, 'getRegistrations']);
// });
Route::get('/registrations/{id}', [RegistrationController::class, 'getRegistration']);
Route::post('/razorpay/create-order', [RazorpayController::class, 'createOrder']);
Route::post('/razorpay/verify-payment', [RazorpayController::class, 'verifyPayment']);
Route::post('/paymentsHook', [WebhookController::class, 'handle']);
