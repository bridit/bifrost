<?php

namespace Bifrost\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
  /**
   * The application's global HTTP middleware stack.
   *
   * These middleware are run during every request to your application.
   *
   * @var array
   */
  protected $middleware = [
    // \Bifrost\Http\Middleware\TrustHosts::class,
    \Bifrost\Http\Middleware\TrustProxies::class,
    \Bifrost\Http\Middleware\HandleCors::class,
    \Bifrost\Http\Middleware\PreventRequestsDuringMaintenance::class,
    \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
    \Bifrost\Http\Middleware\TrimStrings::class,
    \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
  ];

  /**
   * The application's route middleware groups.
   *
   * @var array
   */
  protected $middlewareGroups = [
    'web' => [
      \Bifrost\Http\Middleware\EncryptCookies::class,
      \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
      \Illuminate\Session\Middleware\StartSession::class,
      // \Illuminate\Session\Middleware\AuthenticateSession::class,
      \Illuminate\View\Middleware\ShareErrorsFromSession::class,
      \Bifrost\Http\Middleware\VerifyCsrfToken::class,
      \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],

    'api' => [
      'throttle:api',
      \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
  ];

  /**
   * The application's route middleware.
   *
   * These middleware may be assigned to groups or used individually.
   *
   * @var array
   */
  protected $routeMiddleware = [
    'auth' => \Bifrost\Http\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
    'can' => \Illuminate\Auth\Middleware\Authorize::class,
    'guest' => \Bifrost\Http\Middleware\RedirectIfAuthenticated::class,
    'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
    'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
    'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
  ];
}
