<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ParticipationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Fetch only (Open APIs)
Route::name('api.')->group(function () {
    Route::get('/students', [StudentController::class, 'index']);
    Route::get('/students/{id}', [StudentController::class, 'show']);

    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{id}', [EventController::class, 'show']);

    Route::get('/participations', [ParticipationController::class, 'index']);
    Route::get('/participations/{id}', [ParticipationController::class, 'show']);
}); 

// Admin-only (Protected APIs)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/participations', [ParticipationController::class, 'store']);
    Route::delete('/participations/{id}', [ParticipationController::class, 'destroy']);
    Route::put('/participations/{id}', [ParticipationController::class, 'update']);

    Route::post('/events', [EventController::class, 'store']);
    Route::put('/events/{id}', [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);

    Route::post('/students', [StudentController::class, 'store']);
    Route::put('/students/{id}', [StudentController::class, 'update']);
    Route::delete('/students/{id}', [StudentController::class, 'destroy']);
});