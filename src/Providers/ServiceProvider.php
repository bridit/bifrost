<?php

namespace Bifrost\Providers;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{

  /**
   * App modules base namespace.
   *
   * @var string
   */
  protected $modulesNamespace;

  /**
   * Bundle base directory.
   *
   * @var string
   */
  protected $bundleBasePath;

  /**
   * All app modules fetched from bifrost config.
   *
   * @var array
   */
  protected $modules;

  /**
   * Create a new service provider instance.
   *
   * @param  \Illuminate\Contracts\Foundation\Application  $app
   * @return void
   */
  public function __construct($app)
  {
    parent::__construct($app);

    $this->modules = config('bifrost.modules', []);
    $this->modulesNamespace = config('bifrost.namespace', 'App');
    $this->bundleBasePath = base_path(config('bifrost.bundle_basedir', 'app'));
  }

}
