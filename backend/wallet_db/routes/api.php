<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\PaymentController;
use App\Http\Middleware\VerifySanctumTokenMiddleware;

// Auth routes
Route::prefix("auth")->group(function () {
    Route::post("/register", [AuthController::class, "register"]);
    Route::post("/login", [AuthController::class, "login"]);
});

// Wallet routes
Route::prefix("wallet")->middleware([VerifySanctumTokenMiddleware::class])->group(function () {
    Route::post('/top-up', [WalletController::class, 'topUp']);
    Route::get("/balance", [WalletController::class, "balance"]);
});

// Payment routes
Route::prefix("payments")->middleware([VerifySanctumTokenMiddleware::class])->group(function () {
    // User payments
    Route::post('/initiate-payment', [PaymentController::class, 'initiatePayment']);
    Route::post("/confirm-payment", [PaymentController::class, "confirmPayment"]);
    // User purchases
    Route::post('/initiate-purchase', [PaymentController::class, 'initiatePurchase']);
    Route::post("/confirm-purchase", [PaymentController::class, "confirmPurchase"]);
});
