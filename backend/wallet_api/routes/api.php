<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProxyController;

// Auth
Route::prefix("auth")->group(function () {
  Route::post("/register", [ProxyController::class, "register"]);
  Route::post("/login", [ProxyController::class, "login"]);
});

// Wallet
Route::prefix("wallet")->group(function () {
  Route::post("/top-up", [ProxyController::class, "topUp"]);
  Route::get("/balance", [ProxyController::class, "balance"]);
});

// Payments
Route::prefix("payments")->group(function () {
  Route::post("/initiate-payment", [ProxyController::class, "initiatePayment"]);
  Route::post("/confirm-payment", [ProxyController::class, "confirmPayment"]);
  Route::post("/initiate-purchase", [ProxyController::class, "initiatePurchase"]);
  Route::post("/confirm-purchase", [ProxyController::class, "confirmPurchase"]);
});