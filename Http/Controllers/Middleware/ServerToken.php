<?php

namespace Septech\Snowflake\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ServerToken
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $secret = config('snowflake.sever_to_server_token');
        $token = $request->header('Authorization');

        if ($token) {
            // Remove Bearer text
            $token = substr($token, 7);
        }

        if (! $token) {
            $token = $request->get('token');
        }

        if ($token !== $secret) {
            abort(403);
        }

        return $next($request);
    }
}
