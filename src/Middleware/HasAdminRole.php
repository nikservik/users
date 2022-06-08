<?php

namespace Nikservik\Users\Middleware;

use Closure;
use Illuminate\Http\Request;

class HasAdminRole
{
    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @param  string|int  $role moderator|editor|admin|superadmin|1|2|3|4
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        if (strlen($role) > 1) {
            if ($role === 'editor') {
                $role = 2;
            } elseif ($role === 'admin') {
                $role = 3;
            } elseif ($role === 'superadmin') {
                $role = 4;
            } else {
                // если роль не определена, то считаем ее 1
                $role = 1;
            }
        } else {
            $role = (int) $role;
        }

        if (! $request->user() || $request->user()->adminRole < $role) {
            abort(403);
        }

        return $next($request);
    }
}
