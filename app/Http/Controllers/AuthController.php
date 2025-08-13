<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
      $request->validate([
    'name' => 'required|string',
    'email' => 'required|email|unique:users',
    'password' => 'required|min:6',
    // role_id is optional
      ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id ?? 2 // default to 2 if not passed
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['token' => $token], 201);
    }

  private function roleNameFromId(?int $roleId): string
    {
        return match ((int)($roleId ?? 0)) {
            1 => 'admin',
            2 => 'employee',
            default => 'employee',
        };
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        // create sanctum token
        $token = $user->createToken('api')->plainTextToken;

        // normalize user payload
        $roleName = $this->roleNameFromId($user->role_id);
        $payloadUser = [
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            'role_id'  => $user->role_id,
            'role'     => $roleName,          // <-- derived from role_id
            'is_admin' => (int)$user->role_id === 1,
        ];

        return response()->json([
            'token' => $token,
            'user'  => $payloadUser,
        ]);
    }

    // Optional: useful for refreshing user data after page reloads
    public function me(Request $request)
    {
        $u = $request->user();
        $roleName = $this->roleNameFromId($u->role_id);

        return [
            'id'       => $u->id,
            'name'     => $u->name,
            'email'    => $u->email,
            'role_id'  => $u->role_id,
            'role'     => $roleName,
            'is_admin' => (int)$u->role_id === 1,
        ];
    }
}

