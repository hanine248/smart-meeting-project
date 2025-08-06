<?php

namespace App\Policies;

use App\Models\FileAttachment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FileAttachmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user) {
    return true; // Everyone can view
}

public function view(User $user, FileAttachment $file) {
    return true; // Everyone can view one
}

public function create(User $user) {
    return $user->role_id === 1; // Only admin can upload
}

public function delete(User $user, FileAttachment $file) {
    return $user->role_id === 1; // Only admin can delete
}

}
