<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Web extends Model
{
    protected $fillable = [
        'site_domain',
    ];

    public function zones()
    {
        return $this->hasMany(Zone::class);
    }
}
