<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;



class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Role::all());
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|unique:roles,name',
    ]);

    $role = Role::create(['name' => $request->name]);

    return response()->json($role, 201);
}


    public function show(Role $role): JsonResponse
    {
        return response()->json($role);
    }

    public function update(Request $request, Role $role): JsonResponse
    {
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
        $role->delete();
        return response()->json(['message' => 'Role deleted successfully']);
    }
}
