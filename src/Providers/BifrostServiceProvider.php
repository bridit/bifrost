<?php

namespace Bifrost\Providers;

use Illuminate\Support\Facades\Config;

class BifrostServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    $this->publishes([
      __DIR__ . '/../../config/bifrost.php' => base_path('config/bifrost.php'),
    ]);

    $this->loadModulesAdditional();
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    $this->mergeConfigFrom(
      __DIR__ . '/../../config/bifrost.php', 'bifrost'
    );

    $this->app->register(RoutingServiceProvider::class);
    $this->app->register(Config::get('bifrost.app.service_provider', AppServiceProvider::class));
    $this->app->register(Config::get('bifrost.auth.service_provider', AuthServiceProvider::class));
    $this->app->register(Config::get('bifrost.http.service_provider', RouteServiceProvider::class));
    $this->app->register(Config::get('bifrost.event.service_provider', EventServiceProvider::class));
    $this->app->register(Config::get('bifrost.broadcast.service_provider', BroadcastServiceProvider::class));
    $this->app->register(CorsServiceProvider::class);
  }

  protected function loadModulesAdditional()
  {
    if (blank($this->modules)) {
      $this->loadAdditional();
    }

    foreach (array_keys($this->modules) as $module) {
      $this->loadAdditional($module);
    }
  }

  protected function loadAdditional(?string $module = null)
  {
    $bundleBasePath = base_path(config('bifrost.bundle_basedir', 'app'));

    $basePath = !blank($module)
      ? $bundleBasePath . '/' . $module
      : $bundleBasePath;

    $this->loadMigrationsFrom($basePath . '/Infrastructure/Database/Migrations');
    $this->loadViewsFrom($basePath . '/Interfaces/Http/Web/Views', $module);
    $this->loadViewsFrom($basePath . '/Application/Views', $module);
    $this->loadTranslationsFrom($basePath . '/Resources/Translations', $module);
  }

}
