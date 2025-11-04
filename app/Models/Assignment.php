<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = ['banner_id','zone_id','site_domain','rotation_mode','weight'];

    public function banner(){ return $this->belongsTo(Banner::class); }
    public function zone(){ return $this->belongsTo(Zone::class); }
}
