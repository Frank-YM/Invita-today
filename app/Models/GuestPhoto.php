<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestPhoto extends Model
{
    protected $fillable = ['event_id', 'guest_name', 'path'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
