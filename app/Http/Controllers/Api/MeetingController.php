<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Meeting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Room;
use Carbon\Carbon;
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
// ✅ Store a new meeting
public function store(Request $request)
{
    $validated = $request->validate([
        'title'           => 'required|string|max:255',
        'description'     => 'nullable|string',
        'target_audience' => 'nullable|string',
        'date'            => 'required|date',
        'time'            => 'nullable|regex:/^\d{2}:\d{2}(:\d{2})?$/', // HH:mm or HH:mm:ss
        'link'            => 'nullable|url|max:2048',
        'duration'        => 'required|integer|min:15|max:480', // minutes
        'room_id'         => 'required|exists:rooms,id',
        'user_id'         => 'required|exists:users,id',
    ]);

    // ✅ Convert duration (minutes → HH:MM:SS)
    $minutes = (int) $validated['duration'];
    $hours   = floor($minutes / 60);
    $mins    = $minutes % 60;
    $validated['duration'] = sprintf('%02d:%02d:00', $hours, $mins);

    // ✅ Overlap check
    if ($this->isOverlapping(
        $validated['room_id'],
        $validated['date'],
        $validated['time'],
        $minutes // ⚡ pass raw minutes, not the converted string
    )) {
        return response()->json([
            'error' => 'This room is already booked during this time slot.'
        ], 409);
    }

    // ✅ Create meeting
    $meeting = Meeting::create($validated);

    // ✅ Mark room unavailable
    Room::where('id', $meeting->room_id)->update(['status' => 'unavailable']);

    return response()->json([
        'message' => 'Meeting created successfully',
        'data'    => $meeting
    ], 201);
}



   public function update(Request $request, Meeting $meeting): JsonResponse
{
    $this->authorize('update', $meeting);

    $validator = Validator::make($request->all(), [
        'title'           => 'sometimes|required|string|max:255',
        'description'     => 'nullable|string',
        'target_audience' => 'nullable|string',
        'date'            => 'sometimes|required|date',
        'time'            => 'nullable|regex:/^\d{2}:\d{2}(:\d{2})?$/',
        'link'            => 'nullable|url|max:2048',
        'duration'        => 'sometimes|required|integer|min:15|max:480',
        'room_id'         => 'sometimes|required|exists:rooms,id',
        'user_id'         => 'sometimes|required|exists:users,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $data = $validator->validated();

    // ✅ Convert duration (minutes → HH:MM:SS) if provided
    if (isset($data['duration'])) {
        $minutes = (int) $data['duration'];
        $hours   = floor($minutes / 60);
        $mins    = $minutes % 60;
        $data['duration'] = sprintf('%02d:%02d:00', $hours, $mins);
    }

    // ✅ Handle room change
    if (isset($data['room_id']) && $data['room_id'] != $meeting->room_id) {
        // Make old room available
        Room::where('id', $meeting->room_id)->update(['status' => 'available']);
        // Make new room unavailable
        Room::where('id', $data['room_id'])->update(['status' => 'unavailable']);
    }

    // ✅ Overlap check
    $checkMinutes = isset($data['duration']) ? $minutes : $meeting->getRawOriginal('duration_in_minutes'); 
    // ⚡ if no new duration passed, use the old one (you can store a helper accessor in the model)

    if ($this->isOverlapping(
        $data['room_id'] ?? $meeting->room_id,
        $data['date'] ?? $meeting->date,
        $data['time'] ?? $meeting->time,
        $checkMinutes,
        $meeting->id
    )) {
        return response()->json(['error' => 'This room is already booked during this time slot.'], 409);
    }

    $meeting->update($data);

    return response()->json([
        'message' => 'Meeting updated successfully',
        'data'    => $meeting
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
   private function isOverlapping($roomId, $date, $time, $duration, $excludeMeetingId = null): bool
    {
        if (!$date || !$duration) {
            return false;
        }

        // normalize inputs
        $timePart = $time ?? '00:00';
        if (strlen($timePart) === 5) {
            $timePart .= ':00';
        }

        $start = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $timePart);
        $end   = $start->copy()->addMinutes((int)$duration);

        // query all meetings for this room
        $query = Meeting::where('room_id', $roomId);
        if ($excludeMeetingId) {
            $query->where('id', '<>', $excludeMeetingId);
        }

        $meetings = $query->get();

        foreach ($meetings as $m) {
            // normalize existing meeting start
            $mTime = $m->time ?? '00:00:00';
            if (strlen($mTime) === 5) {
                $mTime .= ':00';
            }
            $mStart = Carbon::createFromFormat('Y-m-d H:i:s', $m->date . ' ' . $mTime);

            // normalize existing duration
            $mDurationMinutes = 0;
            if (is_numeric($m->duration)) {
                $mDurationMinutes = (int)$m->duration;
            } else {
                $parts = explode(':', $m->duration);
                $mDurationMinutes = ((int)$parts[0]) * 60 + ((int)$parts[1]);
            }

            $mEnd = $mStart->copy()->addMinutes($mDurationMinutes);

            // check for overlap
            if ($start < $mEnd && $end > $mStart) {
                return true;
            }
        }

        return false;
    }
}




