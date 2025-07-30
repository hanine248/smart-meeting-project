<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Task;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TaskController extends Controller
{
     use AuthorizesRequests;
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Task::class);
        return response()->json(Task::with(['user', 'meeting'])->get());
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Task::class);
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'due_date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'meeting_id' => 'required|exists:meetings,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $task = Task::create($validator->validated());
        return response()->json($task, 201);
    }

    public function show(Task $task): JsonResponse
    {
        $this->authorize('view',$task);
        return response()->json($task->load(['user', 'meeting']));
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);
        $validator = Validator::make($request->all(), [
            'description' => 'sometimes|required|string',
            'due_date' => 'sometimes|required|date',
            'user_id' => 'sometimes|required|exists:users,id',
            'meeting_id' => 'sometimes|required|exists:meetings,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $task->update($validator->validated());
        return response()->json($task);
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }
}
