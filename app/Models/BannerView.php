<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerView extends Model
{
    use HasFactory;

    protected $fillable = [
        'banner_id',
        'assignment_id',
        'zone_id',
        'site_domain',
        'ip',
        'user_agent',
    ];

    public function banner()     { return $this->belongsTo(Banner::class); }
    public function assignment() { return $this->belongsTo(Assignment::class); }
    public function zone()       { return $this->belongsTo(Zone::class); }
}
