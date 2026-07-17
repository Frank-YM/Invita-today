<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'user_id', 'slug',
        'title','subtitle','emoji','event_type','date','place','lat','lng',
        'color_primary','color_secondary','color_accent',
        'rsvp_button_text','share_message','dress_code','gift_info','extra_info',
        'show_countdown','show_messages','show_map','show_confirmed_count','show_gallery','is_published',
        'theme_character', 'template',
        'photo_1', 'photo_2',
        'reveal_image_1', 'reveal_image_2',
    ];

    protected $casts = [
        'date' => 'datetime',
        'show_countdown' => 'boolean',
        'show_messages' => 'boolean',
        'show_map' => 'boolean',
        'show_confirmed_count' => 'boolean',
        'show_gallery' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guests()
    {
        return $this->hasMany(Guest::class);
    }

    public function guestPhotos()
    {
        return $this->hasMany(GuestPhoto::class)->latest();
    }
}
