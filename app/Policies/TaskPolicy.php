<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Task;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;
    public function viewAny(User $user)
    {
        return true; // ✅ All users can view all meetings
    }

    public function view(User $user, Task $task)
    {
        return true; // ✅ All users can view one meeting
    }

    public function create(User $user)
    {
        return $user->role_id === 1; // ✅ Only admins can create
    }

   public function update(User $user, Task $task)
{
    return $user->role_id === 1;
}

    public function delete(User $user, Task $task)
    {
        return $user->role_id === 1; // ✅ Only admins can delete
    }
}
