<?php

namespace Bifrost\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

  /**
   * The Artisan commands provided by your application.
   *
   * @var array
   */
  protected $commands = [];

  /**
   * @inheritDoc
   */
  public function __construct(Application $app, Dispatcher $events)
  {
    parent::__construct($app, $events);
  }

  /**
   * Define the application's command schedule.
   *
   * @param  \Illuminate\Console\Scheduling\Schedule $schedule
   * @return void
   */
  protected function schedule(Schedule $schedule)
  {
    $modules = array_keys(config('bifrost.modules'));
    $bundleBasePath = base_path(config('bifrost.bundle_basedir', 'app'));

    if(blank($modules)) {
      require $bundleBasePath . '/Interfaces/Console/schedule.php';
      return;
    }

    foreach ($modules as $module) {
      require $bundleBasePath . '/' . $module . '/Interfaces/Console/schedule.php';
    }
  }

  /**
   * Register the commands for the application.
   *
   * @return void
   */
  public function commands()
  {
    $modules = array_keys(config('bifrost.modules'));
    $bundleBasePath = base_path(config('bifrost.bundle_basedir', 'app'));

    if(blank($modules)) {
      $this->load($bundleBasePath . '/Interfaces/Console/Commands');
      require $bundleBasePath . '/Interfaces/Console/console.php';
      return;
    }

    foreach ($modules as $module)
    {
      $this->load($bundleBasePath . '/' . $module . '/Interfaces/Console/Commands');
      require $bundleBasePath . '/' . $module . '/Interfaces/Console/console.php';
    }
  }
}
