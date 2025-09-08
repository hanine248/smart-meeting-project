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
Route::get('/test', function () {
    return response()->json(['message' => 'Hello from Laravel!']);
});

Route::get('/hello', function () {
    return ['message' => 'It works!'];
});

// Public routes (do not require token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require Sanctum token)
Route::middleware('auth:sanctum' )->group(function () {
    Route::get('/meetings/{id}/minutes', [MinuteController::class, 'getByMeeting']);
    Route::post('/meetings/subscribe', [MeetingAttendeeController::class, 'subscribe']);
    Route::post('/meetings/unsubscribe', [MeetingAttendeeController::class, 'unsubscribe']);
   Route::get('/meetings/{id}', [MeetingController::class, 'showbyid']);
// routes/api.php
Route::post('/meetings/{id}/attendees', [MeetingAttendeeController::class, 'storeForMeeting']);
// routes/api.php
Route::put('/attendees/{id}', [MeetingAttendeeController::class, 'update']);
Route::delete('/attendees/{id}', [MeetingAttendeeController::class, 'destroy']);
Route::get('/meetings/{id}/attachments', [FileAttachmentController::class, 'index']);
Route::post('/meetings/{id}/attachments', [FileAttachmentController::class, 'store']);
Route::delete('/attachments/{fileattachment}', [FileAttachmentController::class, 'destroy']);
Route::get('/rooms/{id}/meetings', [RoomController::class, 'getMeetings']);

    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('meetings', MeetingController::class);
    Route::apiResource('meetingattendees', MeetingAttendeeController::class);
    Route::apiResource('minutes', MinuteController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('fileattachments', FileAttachmentController::class);
    Route::get('/me', [AuthController::class, 'me']);
    // Optional logout
    Route::post('/logout', [AuthController::class, 'logout']);
});
