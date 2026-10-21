<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Prosta ochrona panelu wewnętrznego: HTTP Basic Auth z danymi z .env. */
class AdminBasicAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = (string) config('beta.admin.user');
        $password = (string) config('beta.admin.password');

        if ($user === '' || $password === '') {
            abort(404);
        }

        $givenUser = (string) $request->getUser();
        $givenPassword = (string) $request->getPassword();

        if (! hash_equals($user, $givenUser) || ! hash_equals($password, $givenPassword)) {
            return response('Wymagane logowanie.', 401, [
                'WWW-Authenticate' => 'Basic realm="Panel", charset="UTF-8"',
            ]);
        }

        return $next($request);
    }
}
