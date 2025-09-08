<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Room;
use App\Models\User;
use App\Models\Task;
use App\Models\Minute;
use App\Models\MeetingAttendee;
use App\Models\FileAttachment;
class Meeting extends Model
{
    protected $fillable = [
        'title', 'description', 'target_audience', 'date',   'time',   
         'link',  'duration', 'room_id', 'user_id'
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(MeetingAttendee::class);
    }

    public function minute(): HasOne
    {
        return $this->hasOne(Minute::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
    public function attachments(): HasMany
{
    return $this->hasMany(FileAttachment::class);
}

public function getEndTimeAttribute()
{
    return Carbon::parse($this->date)->addMinutes($this->duration);
}

}
