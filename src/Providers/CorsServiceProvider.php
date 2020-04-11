<?php

namespace Bifrost\Providers;

use Illuminate\Support\Str;
use Asm89\Stack\CorsService;
use Laravel\Lumen\Application as LumenApplication;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class CorsServiceProvider extends BaseServiceProvider
{
  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
    $this->mergeConfigFrom($this->configPath(), 'cors');

    $this->app->singleton(CorsService::class, function ($app) {
      $config = $app['config']->get('cors');

      $allowedMethods = $this->getFromProfile($config, 'allowed_methods') ?? $config['allowed_methods'];
      $allowedOrigins = $this->getFromProfile($config, 'allowed_origins') ?? $config['allowed_origins'];
      $allowedOriginsPatterns = $this->getFromProfile($config, 'allowed_origins_patterns') ?? $config['allowed_origins_patterns'];
      $allowedHeaders = $this->getFromProfile($config, 'allowed_headers') ?? $config['allowed_headers'];
      $exposedHeaders = $this->getFromProfile($config, 'exposed_headers') ?? $config['exposed_headers'];
      $maxAge = $this->getFromProfile($config, 'max_age') ?? $config['max_age'];
      $supportsCredentials = $this->getFromProfile($config, 'supports_credentials') ?? $config['supports_credentials'];

      if (!is_array($allowedMethods)) {
        throw new \RuntimeException('CORS config `allowed_methods` should be an array');
      }

      if (!is_array($allowedOrigins)) {
        throw new \RuntimeException('CORS config `allowed_origins` should be an array');
      }

      if (!is_array($allowedOriginsPatterns)) {
        throw new \RuntimeException('CORS config `allowed_origins_patterns` should be an array');
      }

      if (!is_array($allowedHeaders)) {
        throw new \RuntimeException('CORS config `allowed_headers` should be an array');
      }

      if ($exposedHeaders && !is_array($exposedHeaders)) {
        throw new \RuntimeException('CORS config `exposed_headers` should be `false` or an array');
      }

      if ($maxAge !== false && !is_numeric($maxAge)) {
        throw new \RuntimeException('CORS config `max_age` should be an integer or `false`');
      }

      // Convert case to supported options
      $options = [
        'allowedMethods' => $allowedMethods,
        'allowedOrigins' => $allowedOrigins,
        'allowedOriginsPatterns' => $allowedOriginsPatterns,
        'allowedHeaders' => $allowedHeaders,
        'exposedHeaders' => $exposedHeaders,
        'maxAge' => $maxAge,
        'supportsCredentials' => $supportsCredentials,
      ];

      // Transform wildcard pattern
      foreach ($options['allowedOrigins'] as $origin) {
        if (strpos($origin, '*') !== false) {
          $options['allowedOriginsPatterns'][] = $this->convertWildcardToPattern($origin);
        }
      }

      return new CorsService($options, $app);
    });
  }

  /**
   * Register the config for publishing
   *
   */
  public function boot()
  {
    if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
      $this->publishes([$this->configPath() => config_path('cors.php')], 'cors');
    } elseif ($this->app instanceof LumenApplication) {
      $this->app->configure('cors');
    }
  }

  /**
   * Set the config path
   *
   * @return string
   */
  protected function configPath()
  {
    return __DIR__ . '/../../config/cors.php';
  }

  /**
   * Create a pattern for a wildcard, based on Str::is() from Laravel
   *
   * @see https://github.com/laravel/framework/blob/5.5/src/Illuminate/Support/Str.php
   * @param string $pattern
   * @return string
   */
  protected function convertWildcardToPattern($pattern)
  {
    $pattern = preg_quote($pattern, '#');

    // Asterisks are translated into zero-or-more regular expression wildcards
    // to make it convenient to check if the strings starts with the given
    // pattern such as "library/*", making any string check convenient.
    $pattern = str_replace('\*', '.*', $pattern);

    return '#^' . $pattern . '\z#u';
  }

  /**
   * Get attributes from custom profile
   *
   * @param $config
   * @param $attribute
   * @return mixed
   */
  protected function getFromProfile($config, $attribute)
  {
    $methodName = Str::camel('get' . ucfirst($attribute));

    if (!isset($config['profile']) || empty($config['profile'])) {
      return null;
    }

    if (!class_exists($config['profile']) || !is_callable([$config['profile'], $methodName])) {
      return null;
    }

    $value = $config['profile']::$methodName();

    if ($attribute === 'max_age' && $value !== false && !is_numeric($value)) {
      return null;
    }

    if ($attribute === 'supports_credentials' && !is_bool($value)) {
      return null;
    }

    if (!is_array($value)) {
      return null;
    }

    return $value;
  }
}
