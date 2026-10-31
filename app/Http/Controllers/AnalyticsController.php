<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * First-party proxy for Plausible Analytics events.
 *
 * Forwards events through our own domain so ad/tracker blockers don't drop them.
 * The tracking script is a vendored static file at public/js/stats.js.
 */
class AnalyticsController extends Controller
{
    public function event(Request $request)
    {
        $host = config('beta.plausible.host');
        if (! $host) {
            return response('', 202);
        }

        $response = Http::withHeaders([
            'User-Agent' => $request->userAgent() ?? '',
            'X-Forwarded-For' => $request->ip(),
            'Content-Type' => 'application/json',
        ])->withBody($request->getContent(), 'application/json')
            ->post("https://{$host}/api/event");

        return response($response->body(), $response->status());
    }
}
