<?php

namespace Bifrost\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
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

  protected int $apiRateLimit;

  /**
   * Create a new service provider instance.
   *
   * @param  Application  $app
   * @return void
   */
  public function __construct($app)
  {
    parent::__construct($app);

    $this->modules = config('bifrost.modules', []);
    $this->interfaces = config('bifrost.http.interfaces', []);
    $this->modulesNamespace = config('bifrost.namespace', 'App');
    $this->apiRateLimit = config('bifrost.http.api.rate_limit', 60);
    $this->bundleBasePath = base_path(config('bifrost.bundle_basedir', 'app'));
  }

  /**
   * Configure the rate limiters for the application.
   *
   * @return void
   */
  protected function configureRateLimiting()
  {
    RateLimiter::for('api', function (Request $request) {
      return Limit::perMinute($this->apiRateLimit)->by(optional($request->user())->id ?: $request->ip());
    });
  }

  /**
   * Define your route model bindings, pattern filters, etc.
   *
   * @return void
   */
  public function boot()
  {
    $this->configureRateLimiting();

    Route::macro('fullResource', function ($name, $controller, array $options = []) {
      $prefix = data_get($this->getGroupStack(), '0.prefix') ?? data_get($this->getGroupStack(), '1.prefix') ?? '';
      $prefix = substr($prefix, 0, 1) === '/' ? substr($prefix, 1, strlen($prefix)) : $prefix;
      $name = substr($name, 0, 1) === '/' ? substr($name, 1, strlen($name)) : $name;
      $modelName = array_map(fn($item) => Str::singular($item), explode('-', $name));
      $modelName = Str::camel(implode('_', $modelName));

      Arr::set($options, "parameters.$name", $modelName);

      $this->get('/' . $name . '/trashed', $controller . '@trashed')->name("$prefix.$name.trashed");
      $this->put('/' . $name . '/{' . $modelName . '}/trash', $controller . '@trash')->name("$prefix.$name.trash");
      $this->put('/' . $name . '/trash', $controller . '@trashMultiple')->name("$prefix.$name.trashMultiple");
      $this->put('/' . $name . '/{' . $modelName . '}/untrash', $controller . '@untrash')->name("$prefix.$name.untrash");
      $this->put('/' . $name . '/untrash', $controller . '@untrashMultiple')->name("$prefix.$name.untrashMultiple");
      $this->delete('/' . $name, $controller . '@destroyMultiple')->name("$prefix.$name.destroyMultiple");
      $this->resource('/' . $name, $controller, $options);
    });

    Route::macro('fullApiResource', function ($name, $controller, array $options = []) {
      $prefix = data_get($this->getGroupStack(), '0.prefix') ?? data_get($this->getGroupStack(), '1.prefix') ?? '';
      $prefix = substr($prefix, 0, 1) === '/' ? substr($prefix, 1, strlen($prefix)) : $prefix;
      $name = substr($name, 0, 1) === '/' ? substr($name, 1, strlen($name)) : $name;
      $modelName = array_map(fn($item) => Str::singular($item), explode('-', $name));
      $modelName = Str::camel(implode('_', $modelName));

      Arr::set($options, "parameters.$name", $modelName);

      $this->get('/' . $name . '/trashed', $controller . '@trashed')->name("$prefix.$name.trashed");
      $this->put('/' . $name . '/{' . $modelName . '}/trash', $controller . '@trash')->name("$prefix.$name.trash");
      $this->put('/' . $name . '/trash', $controller . '@trashMultiple')->name("$prefix.$name.trashMultiple");
      $this->put('/' . $name . '/{' . $modelName . '}/untrash', $controller . '@untrash')->name("$prefix.$name.untrash");
      $this->put('/' . $name . '/untrash', $controller . '@untrashMultiple')->name("$prefix.$name.untrashMultiple");
      $this->delete('/' . $name, $controller . '@destroyMultiple')->name("$prefix.$name.destroyMultiple");
      $this->match(['get', 'head'], $name, $controller . '@index')->name("$prefix.$name.index");
      $this->post($name, $controller . '@store')->name("$prefix.$name.store");
      $this->match(['get', 'head'],"/$name/{" . $modelName . '}', $controller . '@show')->name("$prefix.$name.show");
      $this->match(['put', 'patch'],"/$name/{" . $modelName . '}', $controller . '@update')->name("$prefix.$name.update");
      $this->match(['delete'],"/$name/{" . $modelName . '}', $controller . '@destroy')->name("$prefix.$name.destroy");
    });

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
