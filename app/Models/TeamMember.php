<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = [
        'name',
        'role',
        'image',
        'theme',
        'reverse',
        'summary',
        'skills',
        'contact',
        'is_carousel',
        'socials',
        'sort_order',
    ];

    protected $casts = [
        'reverse' => 'boolean',
        'is_carousel' => 'boolean',
        'summary' => 'array',
        'socials' => 'array',
    ];


    protected $appends = ['image_url'];


    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : null;
    }
}
