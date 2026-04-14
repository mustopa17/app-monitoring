<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/logs', [\App\Http\Controllers\Api\MonitorLogController::class, 'index']);
Route::post('/monitors', [\App\Http\Controllers\Api\MonitorController::class, 'store']);
