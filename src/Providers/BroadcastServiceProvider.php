<?php

namespace Bifrost\Providers;

use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    Broadcast::routes();

    if (blank($this->modules)) {
      require_once $this->bundleBasePath . '/Interfaces/Http/Broadcast/channels.php';
      return;
    }

    $modulesNames = array_keys($this->modules);

    foreach ($modulesNames as $moduleName)
    {
      require_once $this->bundleBasePath . '/' . $moduleName . '/Interfaces/Http/Broadcast/channels.php';
    }
  }
}
