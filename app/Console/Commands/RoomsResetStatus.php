<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Meeting;
use Carbon\Carbon;

class RoomsResetStatus extends Command
{
    protected $signature = 'rooms:reset-status';
    protected $description = 'Reset room status to available when meeting ends';

   public function handle()
{
    $now = \Carbon\Carbon::now();

    // Find meetings that have already ended
    $meetings = \App\Models\Meeting::where('date', '<', $now)->get();

    foreach ($meetings as $meeting) {
        if ($meeting->room) {
            $meeting->room->update(['status' => 'available']);
        }
    }

    $this->info('Room statuses have been reset for finished meetings.');
}

}
