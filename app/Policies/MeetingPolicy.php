<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Meeting;

class MeetingPolicy
{
    public function viewAny(User $user)
    {
        return true; //  All users can view all meetings
    }

    public function view(User $user, Meeting $meeting)
    {
        return true; //  All users can view one meeting
    }

    public function create(User $user)
    {
        return $user->role_id === 1; //  Only admins can create
    }

    public function update(User $user, Meeting $meeting)
    {
        return $user->role_id === 1; //  Only admins can update
    }

    public function delete(User $user, Meeting $meeting)
    {
        return $user->role_id === 1; //  Only admins can delete
    }
}
