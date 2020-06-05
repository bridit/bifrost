<?php

namespace Bifrost\DTO;

use Carbon\Carbon;
use ReflectionClass;
use ReflectionProperty;
use ReflectionException;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class DataTransferObject
{

  /**
   * @var array|null
   */
  public ?array $requestData;

  /**
   * DataTransferObject constructor.
   * @param array $parameters
   */
  public function __construct(array $parameters = [])
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

      $this->{Str::camel($property)} = $value ?? $default;
    }

    $this->requestData = $parameters;
  }

  /**
   * @param string $key
   * @return bool
   */
  public function filled(string $key): bool
  {
    return array_key_exists($key, $this->requestData);
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
