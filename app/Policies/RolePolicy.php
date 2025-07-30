<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    //the parametere depends on the route parameter not in the rawjson1
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // All authenticated users can view all minutes
    }

    public function view(User $user, Role $role)
    {
        return true; // All authenticated users can view a specific minute
    }

    public function create(User $user)
    {
        return $user->role_id === 1; // Only admins can create minutes
    }

    public function update(User $user, Role $role)
    {
        return $user->role_id === 1; // Only admins can update minutes
    }

    public function delete(User $user, Role $role)
    {
        return $user->role_id === 1; // Only admins can delete minutes
    }
}
