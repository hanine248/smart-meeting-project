<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\MeetingController;
use App\Http\Controllers\Api\MeetingAttendeeController;
use App\Http\Controllers\Api\MinuteController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\FileAttachmentController;

Route::get('/hello', function () {
    return ['message' => 'It works!'];
});

// Public routes (do not require token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require Sanctum token)
Route::middleware('auth:sanctum' )->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('meetings', MeetingController::class);
    Route::apiResource('meetingattendees', MeetingAttendeeController::class);
    Route::apiResource('minutes', MinuteController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('fileattachments', FileAttachmentController::class);

    // Optional logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
