<?php

namespace Bifrost\Validation;

use Illuminate\Support\Facades\Validator as IlluminateValidator;

class Validator
{

  /**
   * @var array
   */
  public static $permissions = [];

  /*
   * @var array
   */
  public static $rules = [];

  /**
   * @param array $data
   * @param null|array $rules
   * @param null|string $methodName
   * @return array
   */
  public static function validate(array $data, ?array $rules = null, ?string $methodName = null)
  {
    $methodName = $methodName ?? debug_backtrace()[1]['function'];
    $rules = $rules ?? data_get(static::$rules, $methodName, []);

    return IlluminateValidator::make($data, $rules)->errors()->messages();
  }

  /**
   * @param null|string $methodName
   * @return array|string|null
   */
  public static function getPermissions(?string $methodName = null)
  {
    if (empty($methodName)) {
      return static::$permissions;
    }

    return isset(static::$permissions[$methodName])
      ? static::$permissions[$methodName]
      : null;
  }

  /**
   * @param null|string $methodName
   * @return array|string|null
   */
  public static function getRules(?string $methodName = null)
  {
    if (empty($methodName)) {
      return static::$rules;
    }

    return isset(static::$rules[$methodName])
      ? static::$rules[$methodName]
      : null;
  }

  /**
   * @param $methodName
   * @param $arguments
   * @return array
   */
  public static function __callStatic($methodName, $arguments)
  {
    $data = [];
    $rules = data_get(static::$rules, $methodName, []);

    foreach ($arguments as $argument)
    {
      if (!is_array($argument)) {
        continue;
      }

      $data = array_merge($data, $argument);
    }

    return IlluminateValidator::make($data, $rules)->errors()->messages();
  }

  /**
   * @param $method
   * @param $attributes
   * @return array
   */
  public static function check($method, array $attributes)
  {
    if (method_exists(static::class, $method)) {
      return forward_static_call_array([static::class, $method], ['data' => $attributes]);
    }

    if (property_exists(static::class, 'rules') && isset(static::$rules[$method])) {
      return static::validate($attributes, static::$rules[$method]);
    }

    return [];
  }

}
