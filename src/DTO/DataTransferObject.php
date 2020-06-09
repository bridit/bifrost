<?php

namespace Bifrost\DTO;

use Illuminate\Http\Request;
use Bifrost\Support\Concerns\ConvertibleFromArray;

class DataTransferObject
{

  use ConvertibleFromArray;

  /**
   * @var array|null
   */
  public ?array $requestData = [];

  /**
   * DataTransferObject constructor.
   * @param array $parameters
   */
  public function __construct(array $parameters = [])
  {
    $this->fillFromArray($parameters);

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
   */
  public static function fromRequest(Request $request): self
  {
    return new static($request->all());
  }

  /**
   * @param array $params
   * @return static
   */
  public static function fromArray(array $params): self
  {
    return new static($params);
  }

}
