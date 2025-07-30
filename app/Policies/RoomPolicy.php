<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Room;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoomPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user)
    {
        return true; // ✅ All users can view all meetings
    }

    public function view(User $user, Room $room)
    {
        return true; // ✅ All users can view one meeting
    }

    public function create(User $user)
    {
        return $user->role_id === 1; // ✅ Only admins can create
    }

   public function update(User $user, Room $room)
{
    return $user->role_id === 1;
}

    public function delete(User $user, Room $room)
    {
        return $user->role_id === 1; // ✅ Only admins can delete
    }
}
