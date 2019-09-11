<?php

namespace Bifrost\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Contracts\Config\Repository;
use Fideloper\Proxy\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
  /**
   * The trusted proxies for this application.
   *
   * @var array
   */
  protected $proxies;

  /**
   * The headers that should be used to detect proxies.
   *
   * @var string
   */
  protected $headers = Request::HEADER_X_FORWARDED_ALL;

  public function __construct(Repository $config)
  {
    $this->proxies = config('bifrost.http.proxies');

    parent::__construct($config);
  }
}
