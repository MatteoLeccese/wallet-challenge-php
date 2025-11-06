<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::prefix("auth")->group(function () {
    Route::post("/register", [\App\Http\Controllers\AuthController::class, "register"]);
    Route::post("/login", [\App\Http\Controllers\AuthController::class, "login"]);
});

// Payment routes
