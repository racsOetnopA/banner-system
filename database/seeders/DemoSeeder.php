<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zone;
use App\Models\Banner;
use App\Models\Assignment;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $zone = Zone::firstOrCreate(['name'=>'header'],['description'=>'Header zone']);
        $banner = Banner::create([
            'name'=>'Demo',
            'type'=>'image',
            'image_path'=>'banners/test.jpg', // coloca un test.jpg en storage/app/public/banners
            'link_url'=>'https://example.com',
            'active'=>true,
        ]);
        Assignment::create([
            'banner_id'=>$banner->id,
            'zone_id'=>$zone->id,
            'site_domain'=>'blog.com',
            'rotation_mode'=>'random',
            'weight'=>1,
        ]);
    }
}
