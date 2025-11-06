<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
use App\Http\Middleware\VerifySanctumTokenMiddleware;

// Auth routes
Route::prefix("auth")->group(function () {
    Route::post("/register", [AuthController::class, "register"]);
    Route::post("/login", [AuthController::class, "login"]);
});

// Wallet routes
Route::prefix("wallet")->middleware([VerifySanctumTokenMiddleware::class])->group(function () {
    Route::post('/top-up', [WalletController::class, 'topUp']);
    // Route::get("/balance", [WalletController::class, "balance"]);
});

// Payment routes
