<?php

namespace Bifrost\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{

  /**
   * The path to the "home" route for your application.
   *
   * @var string
   */
  public const HOME = '/home';

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
   * All http interfaces fetched from bifrost config.
   *
   * @var array
   */
  protected $interfaces;

  /**
   * Create a new service provider instance.
   *
   * @param  Application  $app
   * @return void
   */
  public function __construct($app)
  {
    parent::__construct($app);

    $this->modules = config('bifrost.modules') ?? [];
    $this->interfaces = config('bifrost.http.interfaces', []);
    $this->modulesNamespace = config('bifrost.namespace', 'App');
    $this->bundleBasePath = base_path(config('bifrost.bundle_basedir', 'app'));
  }

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
   * Define the routes for the application.
   *
   * @return void
   */
  public function map()
  {

    foreach ($this->interfaces as $interface)
    {
      $prefix = data_get($interface, 'prefix') ?? '';
      $versions = data_get($interface, 'versions') ?? [];
      $versions = !blank($versions) && is_array($versions) ? $versions : [''];
      $middleware = data_get($interface, 'middleware') ?? '';
      $dirname = ucfirst(str_replace('auth:', '', data_get($interface, 'dirname', $middleware)));
      $filename = data_get($interface, 'filename', 'routes');

      $this->mapRoutes($middleware, $prefix, $dirname, $filename, $versions);
    }

  }

  /**
   * @param string $middleware
   * @param string $prefix
   * @param string $dirname
   * @param string $filename
   * @param array $versions
   */
  protected function mapRoutes(string $middleware, string $prefix, string $dirname, string $filename, array $versions)
  {
    $modules = !blank($this->modules) ? $this->modules : [''];

    foreach ($modules as $module)
    {
      $moduleNamespace = !blank($module) ? $module . '\\' : '';
      $modulePath = !blank($module) ? $module . '/' : '';

      foreach ($versions as $version)
      {
        $versionNamespace = !blank($version) ? $version . '\\' : '';
        $versionPath = !blank($version) ? $version . '/' : '';

        $finalPrefix = $prefix;
        if (!blank($version)) {
          $finalPrefix .= !blank($prefix) ? '/' . $version : $version;
        }

        $namespace = $this->modulesNamespace . '\\' . $moduleNamespace . 'Interfaces\\Http\\' . $dirname . '\\' . $versionNamespace . 'Controllers';
        $group = $this->bundleBasePath . '/' . $modulePath . 'Interfaces/Http/' . $dirname . '/' . $versionPath . $filename . '.php';

        if (!blank($middleware)) {
          Route::prefix($finalPrefix)->middleware($middleware)->namespace($namespace)->group($group);
        } else {
          Route::prefix($finalPrefix)->namespace($namespace)->group($group);
        }
      }
    }
  }

}
