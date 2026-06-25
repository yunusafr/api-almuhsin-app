<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AcademicYearController;
use App\Http\Controllers\API\StudentController;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes (Harus bawa Bearer Token)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);

        // --- AREA SUPER ADMIN ---
        Route::middleware(['role:Super Admin'])->group(function () {
            Route::apiResource('academic-years', AcademicYearController::class);

            Route::post('students/{student}/sync', [StudentController::class, 'sync']); // Tombol Sync
            Route::apiResource('students', StudentController::class); // CRUD Manual

        });
    });
});
