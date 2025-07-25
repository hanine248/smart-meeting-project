<?php
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\MeetingController;
use App\Http\Controllers\Api\MeetingAttendeeController;
use App\Http\Controllers\Api\MinuteController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\FileAttachmentController;

Route::apiResource('users', UserController::class);
Route::apiResource('roles', RoleController::class);
Route::apiResource('rooms', RoomController::class);
Route::apiResource('meetings', MeetingController::class);
Route::apiResource('meetingattendees', MeetingAttendeeController::class);
Route::apiResource('minutes', MinuteController::class);
Route::apiResource('tasks', TaskController::class);
Route::apiResource('fileattachments', FileAttachmentController::class);
