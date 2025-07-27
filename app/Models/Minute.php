<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Meeting;
class Minute extends Model
{
    protected $fillable = ['meeting_id', 'status', 'decision', 'summary', 'note'];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class ,'meeting_id');
    }
}

