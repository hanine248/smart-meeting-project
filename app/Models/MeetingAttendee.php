<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingAttendee extends Model
{
    protected $table = 'meetingattendees';

    protected $fillable = ['user_id', 'meeting_id', 'status'];

    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

public function meeting()
{
    return $this->belongsTo(Meeting::class, 'meeting_id');
}

}
//look in the models we put the has many , and belong for each entity , 
// for the many to many we have a separate entity wich have 1 to 1 to each one 