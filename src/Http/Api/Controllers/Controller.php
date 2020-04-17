<?php

namespace Bifrost\Http\Api\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Bifrost\Services\ApplicationService;
use Bifrost\Http\Api\JsonApi\JsonApiAware;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Bifrost\Transformers\InterfaceTransformer;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController
{
  use
    AuthorizesRequests,
    DispatchesJobs,
    ValidatesRequests,
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
   * Get the map of resource methods to ability names.
   *
   * @return array
   */
  protected function resourceAbilityMap()
  {
    return [
      'index' => 'viewAny',
      'show' => 'view',
      'create' => 'create',
      'store' => 'create',
      'edit' => 'update',
      'update' => 'update',
      'trashed' => 'trashed',
      'trash' => 'trash',
      'untrash' => 'untrash',
      'untrashMultiple' => 'untrashMultiple',
      'destroy' => 'delete',
      'destroyMultiple' => 'deleteMultiple',
    ];
  }

  /**
   * Get the list of resource methods which do not have model parameters.
   *
   * @return array
   */
  protected function resourceMethodsWithoutModels()
  {
    return ['index', 'create', 'store', 'trashed', 'destroyMultiple', 'untrashMultiple'];
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
