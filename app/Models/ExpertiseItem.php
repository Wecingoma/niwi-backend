<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertiseItem extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'icon',
        'description',
        'sort_order',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
