<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Task;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Task::with(['user', 'meeting'])->get());
    }

    public function store(Request $request): JsonResponse
    {
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
        return response()->json($task->load(['user', 'meeting']));
    }

    public function update(Request $request, Task $task): JsonResponse
    {
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
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully']);
    }
}
