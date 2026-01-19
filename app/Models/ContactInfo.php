<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactInfo extends Model
{
    protected $fillable = [
        'phone',
        'email',
        'address',
        'hours',
        'map_embed_url',
        'social_links',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];
}
