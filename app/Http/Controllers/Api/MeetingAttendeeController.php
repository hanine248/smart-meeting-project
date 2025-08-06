<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\MeetingAttendee;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MeetingAttendeeController extends Controller
{
    use AuthorizesRequests;
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', MeetingAttendee::class);
        return response()->json(MeetingAttendee::with(['user', 'meeting'])->get());
    }

   public function store(Request $request): JsonResponse
{
    $request->merge(['user_id' => $request->user()->id]);

    $this->authorize('create', MeetingAttendee::class);

    $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'meeting_id' => 'required|exists:meetings,id',
        'status' => 'nullable|in:attended,absent',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Prevent duplicate subscription
    $exists = MeetingAttendee::where('user_id', $request->user_id)
        ->where('meeting_id', $request->meeting_id)
        ->exists();

    if ($exists) {
        return response()->json(['message' => 'Already subscribed'], 409);
    }

    $attendee = MeetingAttendee::create($validator->validated());

    return response()->json($attendee, 201);
}


    public function show(MeetingAttendee $meetingattendee): JsonResponse
    {
        $this->authorize('view', $meetingattendee);
        return response()->json($meetingattendee->load(['user', 'meeting']));
    }

    public function update(Request $request, MeetingAttendee $meetingattendee): JsonResponse
    {
        $this->authorize('update', $meetingattendee);
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
        $this->authorize('delete', $meetingattendee);
        $meetingattendee->delete();
        return response()->json(['message' => 'Meeting attendee deleted successfully']);
    }
    public function subscribe(Request $request): JsonResponse
{
    $request->validate([
        'meeting_id' => 'required|exists:meetings,id',
    ]);

    $user = $request->user();

    // Prevent duplicate subscription
    $existing = \App\Models\MeetingAttendee::where('user_id', $user->id)
                ->where('meeting_id', $request->meeting_id)
                ->first();

    if ($existing) {
        return response()->json(['message' => 'Already subscribed'], 409);
    }

    $attendee = \App\Models\MeetingAttendee::create([
        'user_id' => $user->id,
        'meeting_id' => $request->meeting_id,
        'status' => 'attended'
    ]);

    return response()->json($attendee, 201);
}

public function unsubscribe(Request $request): JsonResponse
{
    $request->validate([
        'meeting_id' => 'required|exists:meetings,id',
    ]);

    $user = $request->user();

    $attendee = \App\Models\MeetingAttendee::where('user_id', $user->id)
                ->where('meeting_id', $request->meeting_id)
                ->first();

    if (!$attendee) {
        return response()->json(['message' => 'Not subscribed'], 404);
    }

    $attendee->delete();

    return response()->json(['message' => 'Unsubscribed successfully']);
}

}
