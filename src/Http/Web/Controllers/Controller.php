<?php

namespace Bifrost\Http\Web\Controllers;

use Illuminate\Http\Request;
use Bifrost\Validation\Validator;
use Bifrost\Services\ApplicationService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  /**
   * @var ApplicationService
   */
  protected ApplicationService $service;

  /**
   * Controller constructor.
   * @param ApplicationService $service
   */
  public function __construct(ApplicationService $service)
  {
    $this->service = $service;
  }

  /**
   * @return ApplicationService
   */
  public function getService(): ApplicationService
  {
    return $this->service;
  }

}
