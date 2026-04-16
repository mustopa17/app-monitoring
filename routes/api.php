<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/monitors', [\App\Http\Controllers\Api\MonitorController::class, 'index']);
Route::post('/monitors', [\App\Http\Controllers\Api\MonitorController::class, 'store']);
Route::put('/monitors/{id}', [\App\Http\Controllers\Api\MonitorController::class, 'update']);
Route::delete('/monitors/{id}', [\App\Http\Controllers\Api\MonitorController::class, 'destroy']);
Route::get('/logs', [\App\Http\Controllers\Api\MonitorLogController::class, 'index']);
Route::get('/logs/export', [\App\Http\Controllers\Api\MonitorLogController::class, 'export']);
Route::delete('/logs/clear', [\App\Http\Controllers\Api\MonitorLogController::class, 'clearAll']);
Route::delete('/logs/{id}', [\App\Http\Controllers\Api\MonitorLogController::class, 'destroy']);
Route::get('/status', [\App\Http\Controllers\Api\StatusController::class, 'index']);
