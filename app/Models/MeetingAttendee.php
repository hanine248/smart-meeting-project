<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingAttendee extends Model
{
    protected $fillable = ['user_id', 'meeting_id', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }
}
//look in the models we put the has many , and belong for each entity , 
// for the many to many we have a separate entity wich have 1 to 1 to each one 