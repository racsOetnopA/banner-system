<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Zone;
$zones = Zone::with('web')->get();
if ($zones->isEmpty()) {
    echo "NO_ZONES\n";
    exit;
}
foreach ($zones as $z) {
    $web = $z->web ? $z->web->site_domain : '(no web)';
    echo "{$z->id} | {$z->name} | {$web}\n";
}
