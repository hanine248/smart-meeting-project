<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Meeting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

    class MeetingController extends Controller
{
    use AuthorizesRequests;
    // List all meetings
    public function index(): JsonResponse
    {
        this->authorize('viewAny',Meeting::class);
        $meetings = Meeting::with(['room', 'user', 'attendees.user', 'minute', 'tasks'])->get();
        return response()->json($meetings);
    }

    // Store a new meeting
    public function store(Request $request): JsonResponse
    {

        $this->authorize('create', Meeting::class);
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_audience' => 'nullable|string',
            'date' => 'required|date',
            'duration' => 'required',
            'room_id' => 'required|exists:rooms,id',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $meeting = Meeting::create($validator->validated());
        return response()->json($meeting, 201);
    }

    // Show a single meeting
    public function show(Meeting $meeting): JsonResponse
    {
        return response()->json($meeting->load(['room', 'user', 'attendees.user', 'minute', 'tasks']));
    }

    // Update a meeting
    public function update(Request $request, Meeting $meeting): JsonResponse
    {
         $this->authorize('update', $meeting );
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'target_audience' => 'nullable|string',
            'date' => 'sometimes|required|date',
            'duration' => 'sometimes|required',
            'room_id' => 'sometimes|required|exists:rooms,id',
            'user_id' => 'sometimes|required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $meeting->update($validator->validated());
        return response()->json($meeting);
    }

    // Delete a meeting
    public function destroy(Meeting $meeting): JsonResponse
    {
         $this->authorize('delete', $meeting);
        $meeting->delete();
        return response()->json(['message' => 'Meeting deleted successfully']);
    }
}

