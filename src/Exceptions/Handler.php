<?php

namespace Bifrost\Exceptions;

use Throwable;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use League\Fractal\Serializer\JsonApiSerializer;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Bifrost\Http\Api\JsonApi\Error\Response as JsonApiErrorResponse;

class Handler extends ExceptionHandler
{
  /**
   * A list of the exception types that are not reported.
   *
   * @var array
   */
  protected $dontReport = [];

  /**
   * A list of the inputs that are never flashed for validation exceptions.
   *
   * @var array
   */
  protected $dontFlash = [
    'password',
    'password_confirmation',
  ];

  /**
   * @inheritDoc
   */
  public function report(Throwable $exception)
  {
    parent::report($exception);
  }

  /**
   * @inheritDoc
   */
  public function render($request, Throwable $exception)
  {
    return parent::render($request, $exception);
  }

  /**
   * @inheritDoc
   */
  protected function invalidJson($request, ValidationException $exception)
  {
    if (Config::get('bifrost.http.api.serializer', JsonApiSerializer::class) !== JsonApiSerializer::class) {
      return parent::invalidJson($request, $exception);
    }

    return JsonApiErrorResponse::createFromValidationException($exception)->json();
  }

  /**
   * @inheritDoc
   */
  protected function prepareJsonResponse($request, Throwable $e)
  {
    if (Config::get('bifrost.http.api.serializer', JsonApiSerializer::class) !== JsonApiSerializer::class) {
      return parent::prepareJsonResponse($request, $e);
    }

    $headers = $this->isHttpException($e) ? $e->getHeaders() : [];

    return JsonApiErrorResponse::createFromException($e, $headers)->json();
  }

}
