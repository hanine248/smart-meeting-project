<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MeetingAttendee;
use Illuminate\Auth\Access\HandlesAuthorization;

class MeetingAttendeePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->role_id === 1; // Only admin can view full list
    }

    public function view(User $user, MeetingAttendee $attendee)
    {
        return $user->role_id === 1 || $user->id === $attendee->user_id;
    }

    public function create(User $user)
    {
        return  true ; 
    }
     public function update(User $user,MeetingAttendee $attendee)
    {
      return $user->id === $attendee->user_id || $user->role_id === 1;
    }

    public function delete(User $user, MeetingAttendee $attendee)
    {
      return $user->id === $attendee->user_id || $user->role_id === 1;
    }
}
