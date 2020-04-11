<?php

namespace Bifrost\Http\Middleware;

use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
  /**
   * The URIs that should be excluded from CSRF verification.
   *
   * @var array
   */
  protected $except = [
    //
  ];

  /**
   * Create a new middleware instance.
   *
   * @param  Application  $app
   * @param  Encrypter  $encrypter
   * @return void
   */
  public function __construct(Application $app, Encrypter $encrypter)
  {
    $this->except = Config::get('bifrost.http.csrf_except');

    parent::__construct($app, $encrypter);
  }
}
