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
   * @param null|array $parameters
   * @param null|bool $camelCase
   */
  public function __construct(?array $parameters = [], ?bool $camelCase = true)
  {
    $this->fillFromArray($parameters, $camelCase);

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
   * @param bool $camelCase
   * @return static
   */
  public static function fromRequest(Request $request, bool $camelCase = true): self
  {
    return new static($request->all(), $camelCase);
  }

  /**
   * @param array $params
   * @param bool $camelCase
   * @return static
   */
  public static function fromArray(array $params, bool $camelCase = true): self
  {
    return new static($params, $camelCase);
  }

}
