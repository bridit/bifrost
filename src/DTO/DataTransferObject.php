<?php

namespace Bifrost\DTO;

use ReflectionClass;
use ReflectionProperty;
use ReflectionException;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class DataTransferObject
{

  public ?array $requestData;

  /**
   * DataTransferObject constructor.
   * @param array $parameters
   * @throws ReflectionException
   */
  public function __construct(array $parameters = [])
  {
    $class = new ReflectionClass(static::class);

    foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty){
      $property = $reflectionProperty->getName();
      $value = data_get($parameters, Str::snake($property));
      $setter = 'set' . ucfirst($reflectionProperty->getName());

      if ($class->hasMethod($setter)) {
        $this->$setter($value);
        continue;
      }

      $this->{Str::camel($property)} = $value;
    }

    $this->requestData = $parameters;
  }

  /**
   * @param Request $request
   * @return static
   * @throws ReflectionException
   */
  public static function fromRequest(Request $request): self
  {
    return new static($request->all());
  }

  /**
   * @param array $params
   * @return static
   * @throws ReflectionException
   */
  public static function fromArray(array $params): self
  {
    return new static($params);
  }

}
