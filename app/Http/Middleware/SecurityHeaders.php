<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $plausible = config('beta.plausible.domain') ? parse_url(config('beta.plausible.script'), PHP_URL_HOST) : null;
        $plausibleOrigin = $plausible ? 'https://'.$plausible : '';

        $scriptSrc = "'self' ".$plausibleOrigin;
        $styleSrc = "'self'";
        $connectSrc = "'self' ".$plausibleOrigin;

        // Serwer deweloperski Vite wstrzykuje style i skrypty inline oraz używa websocketa HMR.
        if (app()->environment('local') && is_file(public_path('hot'))) {
            $scriptSrc .= ' http://localhost:5173 http://127.0.0.1:5173';
            $styleSrc .= " 'unsafe-inline' http://localhost:5173 http://127.0.0.1:5173";
            $connectSrc .= ' ws://localhost:5173 ws://127.0.0.1:5173 http://localhost:5173 http://127.0.0.1:5173';
        }

        $csp = implode('; ', [
            "default-src 'self'",
            "script-src {$scriptSrc}",
            "style-src {$styleSrc}",
            "img-src 'self' data:",
            "font-src 'self'",
            "connect-src {$connectSrc}",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "object-src 'none'",
        ]);

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
