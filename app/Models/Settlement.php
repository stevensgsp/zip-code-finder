<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Settlement extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'name',
        'zone_type',
        'settlement_type',
    ];
}
