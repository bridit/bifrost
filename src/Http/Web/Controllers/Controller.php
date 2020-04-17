<?php

namespace Bifrost\Http\Web\Controllers;

use Bifrost\Services\ApplicationService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  /**
   * @var null|ApplicationService
   */
  protected ?ApplicationService $service;

  /**
   * Controller constructor.
   * @param null|ApplicationService $service
   */
  public function __construct(?ApplicationService $service = null)
  {
    $this->service = $service;
  }

}
