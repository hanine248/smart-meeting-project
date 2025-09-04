<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoleController extends Controller
{
    use AuthorizesRequests;
    public function index(): JsonResponse
    {
        $this->authorize('viewAny',Role::Class);
        return response()->json(Role::all());
    }

    public function store(Request $request)
{
    $this->authorize('create', Minute::class);
    $request->validate([
        'name' => 'required|string|unique:roles,name',
    ]);

    $role = Role::create(['name' => $request->name]);

    return response()->json($role, 201);
}


    public function show(Role $role): JsonResponse
    {
        $this->authorize('view', $role);
        return response()->json($role);
    }

  public function update(Request $request, Meeting $meeting): JsonResponse
{
    $this->authorize('update', $meeting);

    $validator = Validator::make($request->all(), [
        'title' => 'sometimes|required|string|max:255',
        'description' => 'nullable|string',
        'target_audience' => 'nullable|string',
        'date' => 'sometimes|required|date',
        'duration' => 'sometimes|required|integer|min:15|max:480',
        'room_id' => 'sometimes|required|exists:rooms,id',
        'user_id' => 'sometimes|required|exists:users,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $data = $validator->validated();

    // Convert minutes → HH:MM:SS (same as store)
    if (isset($data['duration'])) {
        $hours = floor($data['duration'] / 60);
        $minutes = $data['duration'] % 60;
        $data['duration'] = sprintf('%02d:%02d:00', $hours, $minutes);
    }

    // If room_id changes → free old room + lock new room
    if (isset($data['room_id']) && $data['room_id'] != $meeting->room_id) {
        // Free the old room
        Room::where('id', $meeting->room_id)->update(['status' => 'available']);
        // Lock the new room
        Room::where('id', $data['room_id'])->update(['status' => 'unavailable']);
    }

    $meeting->update($data);

    return response()->json([
        'message' => 'Meeting updated successfully',
        'data' => $meeting->load(['room', 'user', 'attendees.user', 'minute', 'tasks'])
    ]);
}


    public function destroy(Role $role): JsonResponse
    {
        $this->authorize('delete', $role);
        $role->delete();
        return response()->json(['message' => 'Role deleted successfully']);
    }
}
