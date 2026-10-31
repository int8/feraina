<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RefreshAnalytics extends Command
{
    protected $signature = 'analytics:refresh';

    protected $description = 'Re-vendor the Plausible tracking script into public/js';

    public function handle(): int
    {
        $host = config('beta.plausible.host');
        if (! $host) {
            $this->error('PLAUSIBLE_HOST is not configured.');

            return self::FAILURE;
        }

        // Extended build: hash routing + outbound-link + tagged-event tracking.
        $sourceUrl = "https://{$host}/js/script.hash.outbound-links.tagged-events.js";
        $dest = public_path('js/stats.js');

        $this->info("Fetching {$sourceUrl}...");

        $response = Http::get($sourceUrl);

        if (! $response->successful()) {
            $this->error("Unexpected status {$response->status()} fetching {$sourceUrl}");

            return self::FAILURE;
        }

        if (! is_dir(dirname($dest))) {
            mkdir(dirname($dest), 0755, true);
        }

        file_put_contents($dest, $response->body());
        $bytes = strlen($response->body());
        $this->info("Wrote {$bytes} bytes to {$dest}");

        return self::SUCCESS;
    }
}
