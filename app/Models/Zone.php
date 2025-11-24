<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Web;

class Zone extends Model
{
    protected $fillable = ['name','description','width','height','web_id'];


    public function assignments(){ return $this->hasMany(Assignment::class); }

    public function web()
    {
        return $this->belongsTo(Web::class);
    }
}
