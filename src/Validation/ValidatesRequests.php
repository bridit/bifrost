<?php

namespace Bifrost\Validation;

use Throwable;
use Illuminate\Support\Facades\Auth;
use Bifrost\Exceptions\JsonApiException;

trait ValidatesRequests
{

  /**
   * @param string $methodName
   * @return bool
   */
  protected function aclValidation(string $methodName): bool
  {
    $validator = $this->getValidator();
    $permissions = !blank($validator) ? $validator::getPermissions($methodName) : null;

    if (blank($permissions)) {
      return true;
    }

    return $this->authorized($permissions);
  }

  /**
   * @param string $permission
   * @return bool
   */
  protected function authorized(string $permission)
  {
    return !Auth::guest() && Auth::user()->can($permission);
  }

  /**
   * @param $method
   * @param $parameters
   * @return array
   */
  protected function dataValidation($method, array $parameters)
  {
    $validation = $this->getValidationResult($method, $parameters);

    return collect($validation)->map(function ($errors, $rule) {
      foreach ($errors as $error) {
        return new JsonApiException($error, 'Invalid Attribute: ' . $rule, 422);
      }
    })->toArray();
  }

  /**
   * @param $method
   * @param $parameters
   * @return array
   */
  protected function getValidationResult($method, array $parameters)
  {
    $validator = $this->getValidator();

    if (blank($validator)) {
      return [];
    }

    return $validator::check($method, array_merge(request()->all(), $parameters));
  }

  /**
   * @param string $message
   * @param int $code
   * @param Throwable|null $previous
   * @return JsonApiException
   */
  protected function getNotAuthorizedError($message = null, $code = 403, Throwable $previous = null): JsonApiException
  {
    $apiError = new JsonApiException($message, $code, $previous);
    $apiError->setDetail(trans('auth.acl-denied'));

    return $apiError;
  }

}
