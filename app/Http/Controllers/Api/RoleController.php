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

    public function update(Request $request, Role $role): JsonResponse
    {
        $this->authorize('update', $role);
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|unique:roles,name,' . $role->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role->update($validator->validated());
        return response()->json($role);
    }

    public function destroy(Role $role): JsonResponse
    {
        $this->authorize('delete', $role);
        $role->delete();
        return response()->json(['message' => 'Role deleted successfully']);
    }
}
