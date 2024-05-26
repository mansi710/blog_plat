<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use App\Traits\ApiResponser;
use Closure;
use Illuminate\Http\Request;

class AuthenticationApi extends Middleware
{
    use ApiResponser;
    /**
     * Handle an incoming request.
     * @param  Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards) {
        $token = $request->header('Authorization');
        if ($token) {
            return app(\PHPOpenSourceSaver\JWTAuth\Http\Middleware\Authenticate::class )->handle($request, function ($request) use ($next) {//JWT middleware
                return $next($request);
            });
        } else {
            return $this->errorResponse(trans('api.general.tokenMissing'), config('constants.RESPONSE_ERROR'));
        }
    }
}
