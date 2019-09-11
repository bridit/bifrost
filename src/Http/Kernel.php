<?php

namespace Bifrost\Http;

use Illuminate\Routing\Router;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{

  /**
   * Create a new HTTP kernel instance.
   *
   * @param  \Illuminate\Contracts\Foundation\Application  $app
   * @param  \Illuminate\Routing\Router  $router
   * @return void
   */
  public function __construct(Application $app, Router $router)
  {
    $bifrostConfig = include realpath(base_path() . '/config/bifrost.php');

    $this->middleware = data_get($bifrostConfig, 'http.kernel.middleware', []);
    $this->middlewareGroups = data_get($bifrostConfig, 'http.kernel.middlewareGroups', []);
    $this->routeMiddleware = data_get($bifrostConfig, 'http.kernel.routeMiddleware', []);

    parent::__construct($app, $router);
  }
}
