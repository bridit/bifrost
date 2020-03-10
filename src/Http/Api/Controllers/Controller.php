<?php

namespace Bifrost\Http\Api\Controllers;

use Bifrost\DTO\DataTransferObject;
use Bifrost\Services\ApplicationService;
use Illuminate\Http\Request;
use Bifrost\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Bifrost\Services\DomainService;
use Bifrost\Validation\ValidatesRequests;
use Bifrost\Http\Api\JsonApi\JsonApiAware;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests as IlluminateValidatesRequests;

abstract class Controller extends BaseController
{
  use
    AuthorizesRequests,
    DispatchesJobs,
    IlluminateValidatesRequests,
    JsonApiAware,
    ValidatesRequests;

  /**
   * @var Request
   */
  protected $request;

  /**
   * @var ApplicationService
   */
  protected $service;

  /**
   * @var Validator
   */
  protected $validator;

  /**
   * Controller constructor.
   * @param DomainService $service
   * @param Validator|null $validator
   */
  public function __construct(ApplicationService $service, ?Validator $validator = null)
  {
    $this->service = $service;
    $this->validator = $validator;
  }

  /**
   * @return ApplicationService
   */
  public function getService(): ApplicationService
  {
    return $this->service;
  }

  /**
   * @return null|Validator
   */
  public function getValidator(): ?Validator
  {
    return $this->validator;
  }

  /**
   * Execute an action on the controller.
   *
   * @param  string $method
   * @param  array $parameters
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function callAction($method, $parameters)
  {
    # Check ACL
    if (!$this->aclValidation($method)) {
      return $this->errorResponse([$this->getNotAuthorizedError()], 403);
    }

    # Check Attributes
    $validation = $this->dataValidation($method, $parameters);
    if (!blank($validation)) {
      return $this->errorResponse($validation, 422);
    }

    # Call Method
    return call_user_func_array([$this, $method], $parameters);
  }

  /**
   * @param Request $request
   * @return JsonResponse
   */
  public function index(Request $request)
  {
    return $this->response($this->service->findAll(), 200);
  }

  /**
   * @param mixed $id
   * @return JsonResponse
   */
  public function show($id)
  {
    return $this->response($this->service->find($id), 200);
  }

  /**
   * @param Request $request
   * @return mixed
   * @throws \ReflectionException
   */
  public function store(Request $request)
  {
    $dto = DataTransferObject::fromRequest($request);

    $result = $this->service->create($dto);

    return $result != null
      ? $this->response($result, 201)
      : $this->response(null, 422);
  }

  /**
   * @param $id
   * @param Request $request
   * @return \Bifrost\Entities\Model|string|null
   * @throws \ReflectionException
   */
  public function update($id, Request $request)
  {
    $dto = DataTransferObject::fromRequest($request);

    $result = $this->service->update($dto);

    return $result != null
      ? $this->response($result, 200)
      : $this->response(null, 422);
  }

  /**
   * @param Request $request
   * @return JsonResponse
   */
  public function destroy(Request $request)
  {
    $id = $request->route()->parameter('id') ?? $request->get('id');

    return $this->getService()->delete($id)
      ? $this->response(null, 204)
      : $this->response(null, 422);
  }

  /**
   * @param Request $request
   * @return mixed
   */
  public function restore(Request $request)
  {
    $id = $request->route()->parameter('id') ?? $request->get('id');

    return $this->getService()->restore($id)
      ? $this->response(null, 204)
      : $this->response(null, 422);
  }
}
