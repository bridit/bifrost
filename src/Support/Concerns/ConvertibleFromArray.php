<?php

namespace Bifrost\Support\Concerns;

use Carbon\Carbon;
use ReflectionClass;
use ReflectionProperty;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait ConvertibleFromArray
{

  /**
   * Fill object attributes from given associative array
   * @param array $parameters
   * @param string|null $case
   */
  protected function fillFromArray(array $parameters = [], string $case = null)
  {
    $class = new ReflectionClass(static::class);
    $defaultProperties = $class->getDefaultProperties();

    foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
      $property = $this->getPropertyName($reflectionProperty->getName(), $case);
      $value = $this->getPropertyValue($parameters, $property);
      $default = Arr::get($defaultProperties, $property);
      $setter = 'set' . Str::studly($reflectionProperty->getName());

      if ($class->hasMethod($setter)) {
        $this->$setter($value);
        continue;
      }

      $attributeType = optional($reflectionProperty->getType())->getName();

      if ($attributeType === 'Carbon\Carbon' && !blank($value ?? $default)) {
        $this->{$property} = Carbon::parse($value ?? $default);
        continue;
      }

      if ($attributeType === 'Illuminate\Support\Collection') {
        $this->{$property} = Collection::make($value ?? []);
        continue;
      }

      if (is_array($value) && $attributeType !== null && method_exists($attributeType, 'fromArray')) {
        $this->{$property} = call_user_func_array($reflectionProperty->getType()->getName() . '::fromArray', [$value, $case]);
        continue;
      }

      $this->{$property} = $value ?? $default;
    }
  }

  /**
   * @param string $property
   * @param string|null $case
   * @return string
   */
  private function getPropertyName(string $property, string $case = null): string
  {
    return match ($case) {
      'snake' => Str::snake($property),
      'camel' => Str::camel($property),
      'slug', 'kebab' => Str::kebab($property),
      'studly' => Str::studly($property),
      default => $property,
    };
  }

  /**
   * @param array $parameters
   * @param string $property
   * @return mixed
   */
  private function getPropertyValue(array $parameters, string $property): mixed
  {
    if (array_key_exists($property, $parameters)) {
      return $parameters[$property];
    }

    $snakeCaseKey = Str::snake($property);
    if (array_key_exists($snakeCaseKey, $parameters)) {
      return $parameters[$snakeCaseKey];
    }

    $camelCaseKey = Str::camel($property);
    if (array_key_exists($camelCaseKey, $parameters)) {
      return $parameters[$camelCaseKey];
    }

    $kebabCaseKey = Str::kebab($property);
    if (array_key_exists($kebabCaseKey, $parameters)) {
      return $parameters[$kebabCaseKey];
    }

    $studlyCaseKey = Str::studly($property);
    if (array_key_exists($studlyCaseKey, $parameters)) {
      return $parameters[$studlyCaseKey];
    }

    return null;
  }

}
