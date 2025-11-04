<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = ['name','description','width','height','principal'];


    public function assignments(){ return $this->hasMany(Assignment::class); }
}
