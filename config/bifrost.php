<?php

return [

  /*
    |--------------------------------------------------------------------------
    | Bundle Namespace
    |--------------------------------------------------------------------------
    |
    | Set bundle root namespace (app directory).
    */
  'namespace' => env('APP_NAMESPACE', 'App'),

  /*
    |--------------------------------------------------------------------------
    | Exceptions Handler
    |--------------------------------------------------------------------------
    */
  'exceptions' => [
    'handler' => \Bifrost\Exceptions\Handler::class,
  ],

  /*
    |--------------------------------------------------------------------------
    | Console and HTTP Kernel
    |--------------------------------------------------------------------------
    */
  'console' => [
    'kernel' => \Bifrost\Console\Kernel::class,
  ],
  'http' => [
    'kernel' => [

      /*
       * The application's global HTTP middleware stack.
       *
       * These middleware are run during every request to your application.
       */
      'middleware' => [
        \Bifrost\Http\Middleware\TrustProxies::class,
        \Bifrost\Http\Middleware\HandleCors::class,
        \Bifrost\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Bifrost\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
      ],

      /*
       * The application's route middleware groups.
       */
      'middlewareGroups' => [
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
          'throttle:60,1',
          \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
      ],

      /*
       * The application's route middleware.
       *
       * These middleware may be assigned to groups or used individually.
       */
      'routeMiddleware' => [
        'auth' => \Bifrost\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \Bifrost\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
      ],
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Interfaces Config
    |--------------------------------------------------------------------------
    */
    'interfaces' => [
      [
        'prefix' => null,
        'versions' => [],
        'middleware' => 'web',
      ],
      [
        'prefix' => 'api',
        'versions' => [],
        'middleware' => 'api',
      ],
    ],
  ],

  /*
    |--------------------------------------------------------------------------
    | Application ORM Driver
    |--------------------------------------------------------------------------
    |
    | This value determines the driver that your application must use
    | when dealing with data and persistence.
    |
    */
  'orm' => [
    'driver' => 'eloquent',
    'pagination' => [
      'default_limit' => 25,
    ],
  ],

  /*
    |--------------------------------------------------------------------------
    | Application Modules
    |--------------------------------------------------------------------------
    |
    | Lists all bundles of your application, including some
    | important information about them.
    */
  'bundle_basedir' => 'app',
  'modules' => [],
];

