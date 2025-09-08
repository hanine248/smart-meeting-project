<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Meeting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Room;

    class MeetingController extends Controller
{
    use AuthorizesRequests;
    // List all meetings
    public function showbyid($id): JsonResponse
{
    $meeting = \App\Models\Meeting::with(['attendees.user'])->findOrFail($id);
    return response()->json($meeting);
}

    public function index(): JsonResponse
    {
        $this->authorize('viewAny',Meeting::class);
        $meetings = Meeting::with(['room', 'user', 'attendees.user', 'minute', 'tasks'])->get();
        return response()->json($meetings);
    }

    // Store a new meeting
// In your MeetingController store method


public function store(Request $request)
{


    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'target_audience' => 'nullable|string',
        'date' => 'required|date',
       'time' => 'nullable|regex:/^\d{2}:\d{2}(:\d{2})?$/',// input type="time" gives HH:mm
        'link'      => 'nullable|url|max:2048',
        'duration' => 'required|integer|min:15|max:480',
        'room_id' => 'required|exists:rooms,id',
        'user_id' => 'required|exists:users,id'
    ]);

    // Convert minutes → HH:MM:SS
    $hours = floor($validated['duration'] / 60);
    $minutes = $validated['duration'] % 60;
    $validated['duration'] = sprintf('%02d:%02d:00', $hours, $minutes);
    if ($this->isOverlapping(
        $request->room_id,
        $request->date,
        $request->time,
        $request->duration
    )) {
    return response()->json(['error' => 'This room is already booked during this time slot.'], 409);
}
    $meeting = Meeting::create($validated);

    // ✅ Mark the room unavailable BEFORE return
    Room::where('id', $meeting->room_id)->update(['status' => 'unavailable']);


    return response()->json([
        'message' => 'Meeting created successfully',
        'data' => $meeting
    ], 201);
}

    // Mark the room as unavailable
    

    // Show a single meeting
    public function show(Meeting $meeting): JsonResponse
    {
        return response()->json($meeting->load(['room', 'user', 'attendees.user', 'minute', 'tasks']));
    }

    // Update a meeting
   public function update(Request $request, Meeting $meeting): JsonResponse
{
    $this->authorize('update', $meeting);

    $validator = Validator::make($request->all(), [
        'title' => 'sometimes|required|string|max:255',
        'description' => 'nullable|string',
        'target_audience' => 'nullable|string',
        'date' => 'sometimes|required|date',
        'time' => 'nullable|regex:/^\d{2}:\d{2}(:\d{2})?$/',// input type="time" gives HH:mm
        'link'      => 'nullable|url|max:2048',
        'duration' => 'sometimes|required|integer|min:15|max:480',
        'room_id' => 'sometimes|required|exists:rooms,id',
        'user_id' => 'sometimes|required|exists:users,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $data = $validator->validated();

    // ✅ Convert duration (minutes → HH:MM:SS) if provided
    if (isset($data['duration'])) {
        $hours = floor($data['duration'] / 60);
        $minutes = $data['duration'] % 60;
        $data['duration'] = sprintf('%02d:%02d:00', $hours, $minutes);
    }

    // ✅ Handle room change
    if (isset($data['room_id']) && $data['room_id'] != $meeting->room_id) {
        // Make old room available
        Room::where('id', $meeting->room_id)->update(['status' => 'available']);
        // Make new room unavailable
        Room::where('id', $data['room_id'])->update(['status' => 'unavailable']);
    }
 if ($this->isOverlapping(
        $request->room_id,
        $request->date,
        $request->time,
        $request->duration,
        $id
    )) {
    return response()->json(['error' => 'This room is already booked during this time slot.'], 409);
}

    $meeting->update($data);

    return response()->json([
        'message' => 'Meeting updated successfully',
        'data' => $meeting
    ]);
}

    // Delete a meeting
    public function destroy(Meeting $meeting): JsonResponse
{
    $this->authorize('delete', $meeting);

    // ✅ Make the room available again before deleting the meeting
    Room::where('id', $meeting->room_id)->update(['status' => 'available']);

    $meeting->delete();

    return response()->json([
        'message' => 'Meeting deleted successfully'
    ]);
}
private function isOverlapping($roomId, $date, $time, $duration, $excludeMeetingId = null)
{
    $startTime = \Carbon\Carbon::parse("$date $time");
    $endTime = $startTime->copy()->addMinutes($duration);

    $query = Meeting::where('room_id', $roomId)
        ->where('date', $date);

    if ($excludeMeetingId) {
        $query->where('id', '!=', $excludeMeetingId);
    }

    $meetings = $query->get();

    foreach ($meetings as $m) {
        $mStart = \Carbon\Carbon::parse("{$m->date} {$m->time}");
        $mEnd = $mStart->copy()->addMinutes($m->duration);

        // Check if intervals overlap
        if ($startTime < $mEnd && $endTime > $mStart) {
            return true;
        }
    }

    return false;
}


}

