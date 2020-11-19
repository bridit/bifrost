<?php

namespace Bifrost\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package Bifrost\Providers
 */
class EventServiceProvider extends ServiceProvider
{
  /**
   * The event listener mappings for the application.
   *
   * @var array
   */
  protected $listen = [
    Registered::class => [
      SendEmailVerificationNotification::class,
    ],
  ];

  /**
   * Register any events for your application.
   *
   * @return void
   */
  public function boot()
  {
    parent::boot();

    $modules = config('bifrost.modules', []);
    foreach ($modules as $moduleName => $moduleConfig)
    {
      $subscribers = data_get($moduleConfig, 'eventSubscribers', []);
      foreach ($subscribers as $subscriber) {
        Event::subscribe($subscriber);
      }
    }
  }
}
