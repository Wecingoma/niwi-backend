<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutContent extends Model
{
    protected $fillable = [
        'title',
        'highlight',
        'intro',
        'mission_title',
        'mission_text',
        'vision_title',
        'vision_text',
        'approach_title',
        'approach_text',
        'services_title',
        'services_list',
        'cta_label',
        'cta_link',
    ];

    protected $casts = [
        'services_list' => 'array',
    ];
}
