<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'title', 'description', 'target_audience', 'date', 'duration', 'room_id', 'user_id'
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

}
