<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Minute;
use Illuminate\Auth\Access\HandlesAuthorization;

class MinutePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // All authenticated users can view all minutes
    }

    public function view(User $user, Minute $minute)
    {
        return true; // All authenticated users can view a specific minute
    }

    public function create(User $user)
    {
        return $user->role_id === 1; // Only admins can create minutes
    }

    public function update(User $user, Minute $minute)
    {
        return $user->role_id === 1; // Only admins can update minutes
    }

    public function delete(User $user, Minute $minute)
    {
        return $user->role_id === 1; // Only admins can delete minutes
    }
}
