<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['status', 'location', 'feature', 'capacity'];

  public function meetings()
{
    return $this->hasMany(Meeting::class);
}

}
