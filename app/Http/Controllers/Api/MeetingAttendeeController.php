<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\MeetingAttendee;
use Illuminate\Support\Facades\Validator;

class MeetingAttendeeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(MeetingAttendee::with(['user', 'meeting'])->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'meeting_id' => 'required|exists:meetings,id',
            'status' => 'required|in:attended,absent',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $attendee = MeetingAttendee::create($validator->validated());
        return response()->json($attendee, 201);
    }

    public function show(MeetingAttendee $meetingattendee): JsonResponse
    {
        return response()->json($meetingattendee->load(['user', 'meeting']));
    }

    public function update(Request $request, MeetingAttendee $meetingattendee): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'sometimes|required|exists:users,id',
            'meeting_id' => 'sometimes|required|exists:meetings,id',
            'status' => 'sometimes|required|in:attended,absent',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $meetingattendee->update($validator->validated());
        return response()->json($meetingattendee);
    }

    public function destroy(MeetingAttendee $meetingattendee): JsonResponse
    {
        $meetingattendee->delete();
        return response()->json(['message' => 'Meeting attendee deleted successfully']);
    }
}
