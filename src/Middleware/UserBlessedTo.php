<?php

namespace Nikservik\Users\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserBlessedTo
{
    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @param  string  $blessing
     * @return mixed
     */
    public function handle($request, Closure $next, $blessing)
    {
        if (! $request->user() || ! $request->user()->blessedTo($blessing)) {
            abort(403);
        }

        return $next($request);
    }
}
