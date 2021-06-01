<?php

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

if (!function_exists('call_if')) {

  /**
   * Call function only if condition is true
   *
   * @param bool $condition
   * @param Closure $callable
   */
  function call_if(bool $condition, \Closure $callable)
  {
    if ($condition) {
      call_user_func($callable);
    }
  }

}

if (!function_exists('like')) {

  /**
   * Similar to SQL LIKE function.
   *
   * @param string|null $subject
   * @param string $pattern
   * @return bool
   */
  function like(?string $subject, string $pattern)
  {
    $pattern = str_replace('%', '.*', preg_quote($pattern, '/'));

    return (bool)preg_match("/^{$pattern}$/", $subject);
  }

}

if (!function_exists('ilike')) {

  /**
   * Similar to SQL ILIKE function (case insensitive LIKE).
   *
   * @param string|null $subject
   * @param string $pattern
   * @return bool
   */
  function ilike(?string $subject, string $pattern)
  {
    $pattern = str_replace('%', '.*', preg_quote($pattern, '/'));

    return (bool)preg_match("/^{$pattern}$/i", $subject);
  }

}

if (!function_exists('object_fill')) {

  /**
   * @param object $obj
   * @param array $parameters
   * @return void
   */
  function object_fill(object &$obj, array $parameters = []): void
  {

    $class = new ReflectionClass($obj);
    $defaultProperties = $class->getDefaultProperties();

    foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property)
    {
      $name = $property->getName();

      $value =
        $parameters[$name] ??
        $obj->{$name} ??
        $defaultProperties[$name] ??
        null;

      $setter = 'set' . Str::studly($property->getName());

      if ($class->hasMethod($setter)) {
        $obj->$setter($value);
        continue;
      }

      $attributeType = $property->getType()?->getName();

      if ($attributeType === 'Carbon\Carbon') {
        $obj->{$name} = !blank($value)
          ? Carbon::parse($value)->setTimezone(\Illuminate\Support\Facades\Config::get('app.timezone'))
          : null;
        continue;
      }

      if ($attributeType === 'Illuminate\Support\Collection') {
        $obj->{$name} = Collection::make($value ?? []);
        continue;
      }

      if (is_array($value ?? []) && class_exists($attributeType) && method_exists($attributeType, 'fromArray')) {
        $value = null !== $value ? array_convert_key_case($value, 'camel', false) : null;
        $obj->{$name} = $attributeType::fromArray($value ?? []);
        continue;
      }

      $obj->{$name} = $value;
    }

  }

}

if (!function_exists('array_convert_key_case')) {

  /**
   * @param array $array
   * @param callable|string $callback
   * @param bool $recursive
   * @return array
   */
  function array_convert_key_case(array $array, callable|string $callback, bool $recursive = false): array
  {
    if (is_string($callback)) {
      $callback = fn($key) => Str::$callback($key);
    }

    if (false === $recursive) {
      return array_combine(array_map($callback, array_keys($array)), array_values($array));
    }

    $result = [];

    foreach ($array as $key => $value)
    {
      $result[$callback($key)] = is_array($value) ? array_convert_key_case($value, $callback, $recursive) : $value;
    }

    return $result;
  }

}
