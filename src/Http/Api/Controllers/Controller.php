<?php

namespace Bifrost\Http\Api\Controllers;

use Bifrost\Exceptions\JsonApiException;
use Exception;
use Bifrost\Entities\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Bifrost\DTO\DataTransferObject;
use Illuminate\Support\Facades\Config;
use Bifrost\Services\ApplicationService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Bifrost\Transformers\InterfaceTransformer;
use League\Fractal\Serializer\JsonApiSerializer;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Bifrost\Http\Api\JsonApi\Error\Response as JsonApiErrorResponse;

abstract class Controller extends BaseController
{
  use
    AuthorizesRequests,
    DispatchesJobs,
    ValidatesRequests;

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
      'trash' => 'trash',
      'trashMultiple' => 'trashMultiple',
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
    return ['index', 'create', 'store', 'trashMultiple', 'untrashMultiple', 'destroyMultiple'];
  }

  /**
   * @param $request
   * @return JsonResponse
   */
  protected function findPaginated($request)
  {
    $paginated = $this->service->paginate(
      $request->input('page.size', $request->input('page.limit', null)),
      $request->input('page.number', $request->input('page.offset', null)),
      ['*']
    );

    return $this->paginate($paginated);
  }

  /**
   * @param Model $model
   * @return JsonResponse
   */
  protected function executeShow(Model $model)
  {
    return $this->response($model, 200);
  }

  /**
   * @param DataTransferObject $dto
   * @return JsonResponse
   * @throws ReflectionException
   */
  protected function executeStore(DataTransferObject $dto)
  {
    $model = $this->service->create($dto);

    if(blank($model)){
      return $this->errorResponse([new JsonApiException(class_basename($model) . ' could not be created. Please try again later.', 400)]);
    }

    return $this->response($model, 201);
  }

  /**
   * @param Model $model
   * @param DataTransferObject $dto
   * @return JsonResponse
   * @throws ReflectionException
   */
  protected function executeUpdate(Model $model, DataTransferObject $dto)
  {
    $model = $this->service->update($model, $dto);

    if(blank($model)){
      return $this->errorResponse([new JsonApiException(class_basename($model) . ' could not be updated. Please try again later.', 400)]);
    }

    return $this->response($model, 201);
  }

  /**
   * @param Model $model
   * @return JsonResponse
   * @throws Exception
   */
  protected function executeTrash(Model $model)
  {
    if($this->service->trash($model) === false){
      return $this->errorResponse([new JsonApiException(class_basename($model) . ' could not be trashed. Please try again later.', 400)]);
    }

    return $this->response(null, 204);
  }

  /**
   * @param Request $request
   * @return JsonResponse
   * @throws Exception
   */
  protected function executeTrashMultiple(Request $request)
  {
    $this->service->trashMultiple($request->get('id', []));

    return $this->response(null, 204);
  }

  /**
   * @param Model $model
   * @return JsonResponse
   */
  protected function executeUntrash(Model $model)
  {
    if($this->service->untrash($model) === false){
      return $this->errorResponse([new JsonApiException(class_basename($model) . ' could not be untrashed. Please try again later.', 400)]);
    }

    return $this->response(null, 204);
  }

  /**
   * @param Request $request
   * @return JsonResponse
   * @throws Exception
   */
  protected function executeUntrashMultiple(Request $request)
  {
    $this->service->untrashMultiple($request->get('id', []));

    return $this->response(null, 204);
  }

  /**
   * @param Model $model
   * @return JsonResponse
   * @throws Exception
   */
  protected function executeDestroy(Model $model)
  {
    if($this->service->delete($model) === false){
      return $this->errorResponse([new JsonApiException(class_basename($model) . ' could not be deleted. Please try again later.', 400)]);
    }

    return $this->response(null, 204);
  }

  /**
   * @param Request $request
   * @return JsonResponse
   * @throws Exception
   */
  protected function executeDestroyMultiple(Request $request)
  {
    $this->service->destroyMultiple($request->get('id', []));

    return $this->response(null, 204);
  }

  /**
   * @return mixed
   */
  protected function getApiSerializer()
  {
    $class = Config::get('bifrost.http.api.serializer', JsonApiSerializer::class);

    return new $class();
  }

    /**
     * @param $data
     * @param int|null $defaultHttpCode
     * @param array $headers
     * @return JsonResponse
     */
  protected function response($data, ?int $defaultHttpCode = 200, array $headers = [])
  {
    return fractal($data, $this->transformer)
      ->serializeWith($this->getApiSerializer())
      ->withResourceName(class_basename($this->service->getEntityClassName()))
      ->respond($defaultHttpCode, $this->getHeaders($headers));
  }

    /**
     * @param $data
     * @param int|null $defaultHttpCode
     * @param array $headers
     * @return JsonResponse
     */
  protected function arrayResponse($data, ?int $defaultHttpCode = 200, array $headers = [])
  {
    return new JsonResponse($data, $defaultHttpCode, $this->getHeaders($headers));
  }

  /**
   * @param LengthAwarePaginator $data
   * @param int|null $defaultHttpCode
   * @param array $headers
   * @return JsonResponse
   */
  protected function paginate(LengthAwarePaginator $data, ?int $defaultHttpCode = 200, array $headers = [])
  {
    return fractal($data, $this->transformer)
      ->serializeWith($this->getApiSerializer())
      ->withResourceName(class_basename($this->service->getEntityClassName()))
      ->paginateWith(new IlluminatePaginatorAdapter($data))
      ->respond($defaultHttpCode, $this->getHeaders($headers));
  }

  /**
   * @param $headers
   * @return array
   */
  protected function getHeaders($headers)
  {
    if (Config::get('bifrost.http.api.serializer', JsonApiSerializer::class) !== JsonApiSerializer::class) {
      return $headers;
    }

    unset($headers['content-type']);

    return array_merge($headers, ['Content-Type' => 'application/vnd.api+json']);
  }

  /**
   * @param array $errors
   * @param array $headers
   * @return JsonResponse
   * @deprecated
   */
  protected function errorResponse(array $errors, array $headers = [])
  {
    return (new JsonApiErrorResponse($errors, $this->getHeaders($headers)))->json();
  }

}
