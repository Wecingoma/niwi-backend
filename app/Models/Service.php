<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'icon',
        'description',
        'features',
        'benefits',
        'methodology',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'benefits' => 'array',
        'methodology' => 'array',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
