<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Response;

class RoleCheck
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param \Illuminate\Contracts\Auth\Factory $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param array|null $roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = $this->auth->user();

        if (!isset($user->role))
            return response('Unauthorized.', Response::HTTP_UNAUTHORIZED);

        foreach ($roles as $role) {
            if ($role == strtolower($user->role->permission))
                return $next($request);
        }

        return response('Forbidden.', Response::HTTP_FORBIDDEN);
    }
}
