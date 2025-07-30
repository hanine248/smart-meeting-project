<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->role_id === 1; // Only admin can see all users
    }

    public function view(User $user, User $model)
    {
        return $user->id === $model->id || $user->role_id === 1;
        // Admin OR the user themselves can see their profile
    }

    public function create(User $user)
    {
        return $user->role_id === 1;
    }

    public function update(User $user, User $model)
    {
        return $user->id === $model->id || $user->role_id === 1;
        // Users can edit their own profile, admin can edit anyone
    }

    public function delete(User $user, User $model)
    {
        return $user->role_id === 1; // Only admin can delete users
    }
}
