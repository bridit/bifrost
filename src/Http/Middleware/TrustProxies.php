<?php

namespace Bifrost\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
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

  /**
   * Create a new trusted proxies middleware instance.
   *
   * @param Repository $config
   */
  public function __construct(Repository $config)
  {
    $this->proxies = Config::get('bifrost.http.proxies');
    $this->headers = Config::get('bifrost.http.proxies_headers', Request::HEADER_X_FORWARDED_ALL);

    parent::__construct($config);
  }
}
