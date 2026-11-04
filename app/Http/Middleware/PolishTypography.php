<?php

namespace App\Http\Middleware;

use App\Support\Typography;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/** Dokleja twarde spacje po jednoliterowych słowach w każdej stronie HTML, żeby „i”, „w”, „z” nie wisiały na końcu linii. */
class PolishTypography
{
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $response = $next($request);

        if ($response instanceof Response && str_starts_with((string) $response->headers->get('Content-Type'), 'text/html')) {
            $content = $response->getContent();
            if (is_string($content) && $content !== '') {
                $response->setContent(Typography::html($content));
            }
        }

        return $response;
    }
}
