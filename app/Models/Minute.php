<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Minute extends Model
{
    protected $fillable = ['meeting_id', 'status', 'decision', 'summary', 'note'];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }
}

