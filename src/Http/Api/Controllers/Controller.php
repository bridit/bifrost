<?php

namespace Bifrost\Http\Api\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Bifrost\Services\ApplicationService;
use Bifrost\Http\Api\JsonApi\JsonApiAware;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Bifrost\Transformers\InterfaceTransformer;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests as IlluminateValidatesRequests;

abstract class Controller extends BaseController
{
  use
    AuthorizesRequests,
    DispatchesJobs,
    IlluminateValidatesRequests,
    JsonApiAware;

  /**
   * @var Request
   */
  protected Request $request;

  /**
   * @var ApplicationService
   */
  protected ApplicationService $service;

  /**
   * @var InterfaceTransformer
   */
  protected InterfaceTransformer $transformer;

  /**
   * Controller constructor.
   * @param ApplicationService $service
   * @param InterfaceTransformer $transformer
   */
  public function __construct(ApplicationService $service, InterfaceTransformer $transformer)
  {
    $this->service = $service;
    $this->transformer = $transformer;
  }

  /**
   * @return ApplicationService
   */
  public function getService(): ApplicationService
  {
    return $this->service;
  }

  /**
   * @return InterfaceTransformer
   */
  public function getTransformer(): InterfaceTransformer
  {
    return $this->transformer;
  }

  /**
   * @param $request
   * @return JsonResponse
   */
  protected function doIndex($request)
  {
    if (!$request->has('page')) {
      return $this->response($this->service->findWithQueryBuilder());
    }

    $paginated = $this->service->paginate(
      $request->input('page.size', $request->input('page.limit', null)),
      $request->input('page.number', $request->input('page.offset', null)),
      ['*']
    );

    return $this->paginate($paginated);
  }
}
