<?php


Route::get('/hello', function () {
    return ['message' => 'It works!'];
});


Route::apiResource('users', \App\Http\Controllers\Api\UserController::class);
Route::apiResource('roles', \App\Http\Controllers\Api\RoleController::class);
Route::apiResource('rooms', \App\Http\Controllers\Api\RoomController::class);
Route::apiResource('meetings', \App\Http\Controllers\Api\MeetingController::class);
Route::apiResource('meetingattendees', \App\Http\Controllers\Api\MeetingAttendeeController::class);
Route::apiResource('minutes', \App\Http\Controllers\Api\MinuteController::class);
Route::apiResource('tasks', \App\Http\Controllers\Api\TaskController::class);
Route::apiResource('fileattachments', \App\Http\Controllers\Api\FileAttachmentController::class);
