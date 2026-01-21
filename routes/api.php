<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\LeadController;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/appointments', [LeadController::class, 'store'])->middleware('throttle:60,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Admin Routes
    Route::middleware('role:admin')->group(function () {
        Route::post('/admin/coordinators', [AdminController::class, 'createCoordinator']);
        Route::get('/admin/leads', [LeadController::class, 'index']);
    });

    // Coordinator Routes
    Route::middleware('role:coordinator')->group(function () {
        Route::get('/coordinator/leads', [LeadController::class, 'index']);
        Route::patch('/leads/{lead}/status', [LeadController::class, 'updateStatus']);
    });
});
