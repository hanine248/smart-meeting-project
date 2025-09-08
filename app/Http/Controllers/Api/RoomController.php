<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Room;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class RoomController extends Controller
{
     use AuthorizesRequests;
 public function index()
{
    $rooms = Room::with(['meetings' => function($q) {
        $q->whereDate('date', '>=', now()->toDateString());
    }])->get();

    return response()->json($rooms);
}

public function getMeetings($id)
{
    $room = Room::with('meetings')->findOrFail($id);
    return response()->json($room->meetings);
}

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Room::class);
        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
            'location' => 'required|string',
            'feature' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $room = Room::create($validator->validated());
        return response()->json($room, 201);
    }

    public function show(Room $room): JsonResponse
    {
        return response()->json($room);
    }

    public function update(Request $request, Room $room): JsonResponse
    {
        $this->authorize('update', $room);
        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|required|string',
            'location' => 'sometimes|required|string',
            'feature' => 'nullable|string',
            'capacity' => 'sometimes|required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $room->update($validator->validated());
        return response()->json($room);
    }

    public function destroy(Room $room): JsonResponse
    {
        $this->authorize('delete', $room);
        $room->delete();
        return response()->json(['message' => 'Room deleted successfully']);
    }
}
