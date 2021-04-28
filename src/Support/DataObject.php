<?php

namespace Bifrost\Support;

use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionProperty;
use InvalidArgumentException;
use Bifrost\Support\Attributes\Strict;
use Bifrost\Support\Concerns\Arrayable;
use Bifrost\Support\Concerns\Contracts\Arrayable as ArrayableContract;

class DataObject implements ArrayableContract
{

  use Arrayable;

  public function __construct(...$args)
  {
    if (is_array($args[0] ?? null)) {
      $args = $args[0];
    }

    $args = array_convert_key_case($args, 'camel', false);

    $reflectionClass = new ReflectionClass($this);
    $classProperties = array_map(fn($property) => $property->name, $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC));
    $intersectionCount = count(array_intersect(array_keys($args), $classProperties));

    if (! empty($reflectionClass->getAttributes(Strict::class)) && count($classProperties) !== $intersectionCount) {
      throw new InvalidArgumentException('Arguments of ' . class_basename($this) .
        ' missing: ' . implode(', ', array_diff($classProperties, array_keys($args)))
      );
    }

    object_fill($this, $args);
  }


  /**
   * @param array $params
   * @return static
   */
  public static function fromArray(array $params): self
  {
    return new static($params);
  }

  /**
   * @param Collection $collection
   * @return static
   */
  public static function fromCollection(Collection $collection): self
  {
    return new static($collection->toArray());
  }

  /**
   * @param string $json
   * @return static
   */
  public static function fromJson(string $json): self
  {
    return new static(json_decode($json, true));
  }

  public function merge(...$args)
  {
    $params = [];

    foreach ($args as $arg)
    {
      if (!is_array($arg) && (!is_object($arg) || !$arg instanceof ArrayableContract)) {
        continue;
      }

      $params = array_merge($params, is_array($arg) ? $arg : $arg->toArray());
    }

    $params = array_convert_key_case($params, 'camel', true);

    object_fill($this, $params);
  }

  public function clone(...$args): static
  {
    return new static(...array_merge($this->toArray(), $args));
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->toJson(null, true);
  }

}
