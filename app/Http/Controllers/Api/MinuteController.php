<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Minute;
use Illuminate\Support\Facades\Validator;

class MinuteController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Minute::with('meeting')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'meeting_id' => 'required|exists:meetings,id',
            'status' => 'required|in:draft,submitted,approved',
            'decision' => 'nullable|string',
            'summary' => 'nullable|string',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $minute = Minute::create($validator->validated());
        return response()->json($minute, 201);
    }

    public function show(Minute $minute): JsonResponse
    {
        return response()->json($minute->load('meeting'));
    }

    public function update(Request $request, Minute $minute): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|required|in:draft,submitted,approved',
            'decision' => 'nullable|string',
            'summary' => 'nullable|string',
            'note' => 'nullable|string',
            'meeting_id' => 'sometimes|required|exists:meetings,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $minute->update($validator->validated());
        return response()->json($minute);
    }

    public function destroy(Minute $minute): JsonResponse
    {
        $minute->delete();
        return response()->json(['message' => 'Minute deleted successfully']);
    }
}
