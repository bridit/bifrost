<?php

namespace Bifrost\Support\Concerns;

use Carbon\Carbon;
use ReflectionClass;
use ReflectionProperty;
use Illuminate\Support\Str;

trait ConvertibleFromArray
{

  /**
   * Fill object attributes from given associative array
   * @param array $parameters
   */
  protected function fillFromArray(array $parameters = [])
  {
    $class = new ReflectionClass(static::class);
    $defaultProperties = $class->getDefaultProperties();

    foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty){
      $property = $reflectionProperty->getName();
      $value = data_get($parameters, Str::snake($property));
      $default = data_get($defaultProperties, Str::camel($property));
      $setter = 'set' . ucfirst($reflectionProperty->getName());

      if ($class->hasMethod($setter)) {
        $this->$setter($value);
        continue;
      }

      if ($reflectionProperty->getType()->getName() === 'Carbon\Carbon' && !blank($value ?? $default)) {
        $this->{Str::camel($property)} = Carbon::parse($value ?? $default);
        continue;
      }

      if (method_exists($reflectionProperty->getType()->getName(), 'fromArray') && is_array($value)) {
        $this->{Str::camel($property)} = call_user_func($reflectionProperty->getType()->getName() . '::fromArray', $value);
        continue;
      }

      $this->{Str::camel($property)} = $value ?? $default;
    }
  }

}
