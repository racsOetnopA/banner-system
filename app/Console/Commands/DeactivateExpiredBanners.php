<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Banner;
use Illuminate\Support\Facades\Log;

class DeactivateExpiredBanners extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'banners:deactivate-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate banners whose end_date is in the past (or equal to now)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = now();

        $query = Banner::where('active', true)
            ->whereNotNull('end_date')
            ->where('end_date', '<=', $now);


        $count = $query->count();

        if ($count === 0) {
            $this->info('No expired banners to deactivate.');
            return 0;
        }

        // Collect IDs to log them after update
        $ids = $query->pluck('id')->all();

        $query->update(['active' => false]);

        $this->info("Deactivated {$count} expired banner(s): " . implode(', ', $ids));
        Log::info('DeactivateExpiredBanners: Deactivated ' . $count . " banner(s) with end_date <= {$now}.", ['ids' => $ids]);

        return 0;
    }
}
