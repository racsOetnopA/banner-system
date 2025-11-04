<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
    'name','type','image_path','video_path','html_code','link_url',
    'active','start_date','end_date','format'
    ];

    protected $casts = [
        'active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function assignments(){ return $this->hasMany(Assignment::class); }
    public function views(){ return $this->hasMany(BannerView::class); }
    public function clicks(){ return $this->hasMany(BannerClick::class); }
}
