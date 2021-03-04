<?php

return [

  /*
    |--------------------------------------------------------------------------
    | Bundle Namespace
    |--------------------------------------------------------------------------
    |
    | Set bundle root namespace (app directory).
    */
  'namespace' => env('APP_NAMESPACE', 'Bifrost'),

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
    'kernel' => \Bifrost\Http\Kernel::class,
    'service_provider' => \Bifrost\Providers\RouteServiceProvider::class,
    'proxies' => '*',
    'proxies_headers' => \Illuminate\Http\Request::HEADER_X_FORWARDED_ALL,
    'api' => [
      'rate_limit' => 60,
//      'serializer' => \League\Fractal\Serializer\ArraySerializer::class,
//      'serializer' => \League\Fractal\Serializer\DataArraySerializer::class,
//      'serializer' => \League\Fractal\Serializer\JsonApiSerializer::class,
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
      'default_per_page' => 25,
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

  'query_cache' => [
    'prefix' => 'Bifrost',
    'driver' => env('QUERY_CACHE_DRIVER', 'redis'),
    'time' => 30, // 30seg
    'cache_mode' => \Bifrost\Enums\BifrostCacheModeEnum::CLEAR_CACHE_BY_KEYS,
    'flush_cache_on_update' => true,
    'recreate_cache' => true,
    'time_for_find' => 300 // 5min
  ],

];

