<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class ZipCode extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'zip_code',
        'locality',
        'federal_entity',
        'municipality',
    ];

    /**
     * @return \Jenssegers\Mongodb\Relations\EmbedsMany
     */
    public function settlements()
    {
        return $this->embedsMany(Settlement::class);
    }
}
