<?php

namespace Bifrost\Providers;

use Bifrost\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RoutingServiceProvider extends ServiceProvider
{

  /**
   * Define your route model bindings, pattern filters, etc.
   *
   * @return void
   */
  public function boot()
  {
    parent::boot();
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    $this->app->extend('router', function ($router, $app) {
      return new Router($app['events'], $app);
    });
  }

}
