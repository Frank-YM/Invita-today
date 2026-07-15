<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = ['event_id', 'name', 'attending', 'companions', 'message'];

    protected $casts = [
        'attending' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
