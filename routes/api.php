<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MonitorController;
use App\Http\Controllers\Api\MonitorLogController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route publik (tidak butuh login)
Route::post('/login', [AuthController::class, 'login']);

// Route yang butuh login (semua role)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        $user = $request->user();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->getRoleNames()->first(),
            'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
        ];
    });
    Route::get('/status', [StatusController::class, 'index']);

    // Dashboard & View Data (Bisa diakses semua role yang login)
    Route::get('/monitors', [MonitorController::class, 'index']);
    Route::get('/logs', [MonitorLogController::class, 'index']);

    // Monitor & Logs Management → Hanya super_admin dan admin
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::post('/monitors', [MonitorController::class, 'store']);
        Route::put('/monitors/{id}', [MonitorController::class, 'update']);
        Route::delete('/monitors/{id}', [MonitorController::class, 'destroy']);
        Route::delete('/logs/{id}', [MonitorLogController::class, 'destroy']);
        Route::delete('/logs/clear', [MonitorLogController::class, 'clearAll']);
        Route::get('/logs/export', [MonitorLogController::class, 'export']);
    });

    // User Management → hanya super_admin
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        // Role & Permission Management
        Route::get('/roles', [RoleController::class, 'index']);
        Route::post('/roles', [RoleController::class, 'store']);
        Route::put('/roles/{id}', [RoleController::class, 'update']);
        Route::delete('/roles/{id}', [RoleController::class, 'destroy']);
        Route::get('/permissions', [PermissionController::class, 'index']);
    });
});
