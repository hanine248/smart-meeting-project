<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\FileAttachment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FileAttachmentController extends Controller
{
    use AuthorizesRequests;

    public function index(): JsonResponse
    {
        $this->authorize('viewAny', FileAttachment::class);
        return response()->json(FileAttachment::all());
    }
    public function store(Request $request): JsonResponse
{
    $this->authorize('create', FileAttachment::class);

    $validator = Validator::make($request->all(), [
        'meeting_id' => 'required|exists:meetings,id',
        'file' => 'required|file|max:2048', // 2MB max
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // ✅ Save file to storage (public/files or wherever)
    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $path = $file->store('file_attachments', 'public'); // saves to storage/app/public/file_attachments
    } else {
        return response()->json(['error' => 'File upload failed.'], 400);
    }

    // ✅ Save path to DB
    $attachment = FileAttachment::create([
        'meeting_id' => $request->meeting_id,
        'path' => $path,
    ]);

    return response()->json($attachment, 201);
}


    

    public function show(FileAttachment $fileattachment): JsonResponse
    {
        $this->authorize('view', $fileattachment);
        return response()->json($fileattachment);
    }

    public function destroy(FileAttachment $fileattachment): JsonResponse
{
    $this->authorize('delete', $fileattachment);

    if ($fileattachment->path && Storage::disk('public')->exists($fileattachment->path)) {
        Storage::disk('public')->delete($fileattachment->path);
    }

    $fileattachment->delete();
    return response()->json(['message' => 'File deleted successfully']);
}

}
