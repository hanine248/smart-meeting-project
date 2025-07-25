<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\FileAttachment;

class FileAttachmentController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(FileAttachment::with('meeting')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx',
            'meeting_id' => 'required|exists:meetings,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        $path = $file->store('attachments', 'public');

        $attachment = FileAttachment::create([
            'path' => $path,
            'type' => $file->getClientOriginalExtension(),
            'meeting_id' => $request->meeting_id,
        ]);

        return response()->json($attachment, 201);
    }

    public function show(FileAttachment $fileattachment): JsonResponse
    {
        return response()->json($fileattachment->load('meeting'));
    }

    public function update(Request $request, FileAttachment $fileattachment): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => 'sometimes|file|mimes:pdf,jpg,jpeg,png,doc,docx',
            'meeting_id' => 'sometimes|exists:meetings,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('file')) {
            // delete old file
            Storage::disk('public')->delete($fileattachment->path);

            $file = $request->file('file');
            $path = $file->store('attachments', 'public');

            $fileattachment->path = $path;
            $fileattachment->type = $file->getClientOriginalExtension();
        }

        if ($request->meeting_id) {
            $fileattachment->meeting_id = $request->meeting_id;
        }

        $fileattachment->save();

        return response()->json($fileattachment);
    }

    public function destroy(FileAttachment $fileattachment): JsonResponse
    {
        Storage::disk('public')->delete($fileattachment->path);
        $fileattachment->delete();

        return response()->json(['message' => 'File attachment deleted successfully']);
    }
}
