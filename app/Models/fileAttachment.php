<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileAttachment extends Model
{
    protected $table = 'fileattachments';

    protected $fillable = ['path', 'type', 'meeting_id'];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }
}
