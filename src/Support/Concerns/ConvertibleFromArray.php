<?php

namespace Bifrost\Support\Concerns;

use Carbon\Carbon;
use ReflectionClass;
use ReflectionProperty;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

trait ConvertibleFromArray
{

  /**
   * Fill object attributes from given associative array
   * @param null|array $parameters
   * @param null|bool $camelCase = true
   */
  protected function fillFromArray(?array $parameters = [], ?bool $camelCase = true)
  {
    $class = new ReflectionClass(static::class);
    $defaultProperties = $class->getDefaultProperties();

    foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty){
      $property = $reflectionProperty->getName();
      $value = $this->getPropertyValue($parameters ?? [], $property);
      $default = data_get($defaultProperties, $this->getPropertyName($property, $camelCase));
      $setter = 'set' . Str::studly($reflectionProperty->getName());

      if ($class->hasMethod($setter)) {
        $this->$setter($value);
        continue;
      }

      $attributeType = optional($reflectionProperty->getType())->getName();

      if ($attributeType === 'Carbon\Carbon' && !blank($value ?? $default)) {
        $this->{$this->getPropertyName($property, $camelCase)} = Carbon::parse($value ?? $default);
        continue;
      }

      if ($attributeType === 'Illuminate\Support\Collection') {
        $this->{$this->getPropertyName($property, $camelCase)} = Collection::make($value ?? []);
        continue;
      }

      if ($attributeType !== null && method_exists($attributeType, 'fromArray') && is_array($value)) {
        $this->{$this->getPropertyName($property, $camelCase)} = call_user_func($reflectionProperty->getType()->getName() . '::fromArray', $value);
        continue;
      }

      $this->{$this->getPropertyName($property, $camelCase)} = $value ?? $default;
    }
  }

  /**
   * @param string $property
   * @param bool $camelCase
   * @return string
   */
  private function getPropertyName(string $property, bool $camelCase): string
  {
    return $camelCase ? Str::camel($property) : Str::snake($property);
  }

  /**
   * @param array $parameters
   * @param string $property
   * @return mixed
   */
  private function getPropertyValue(array $parameters, string $property)
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

    $slugCaseKey = Str::slug($property);
    if (array_key_exists($slugCaseKey, $parameters)) {
      return $parameters[$slugCaseKey];
    }

    return null;
  }

}
