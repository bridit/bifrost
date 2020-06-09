<?php

namespace Bifrost\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Bifrost\Providers\RouteServiceProvider;

class RedirectIfAuthenticated
{
  /**
   * Handle an incoming request.
   *
   * @param  Request  $request
   * @param  Closure  $next
   * @param  string|null  $guard
   * @return mixed
   */
  public function handle($request, Closure $next, $guard = null)
  {
    if (Auth::guard($guard)->check()) {
      return redirect(RouteServiceProvider::HOME);
    }

    return $next($request);
  }
}
