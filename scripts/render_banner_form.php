<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\View;
use App\Models\Zone;
use Illuminate\Support\View\View as ViewClass;
use Illuminate\Support\ViewErrorBag;

$zones = Zone::with('web')->get();
$banner = null;
$errors = new ViewErrorBag();
echo View::make('banners.partials.form', compact('banner','zones','errors'))->render();
