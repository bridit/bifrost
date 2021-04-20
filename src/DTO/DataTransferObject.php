<?php

namespace Bifrost\DTO;

use Illuminate\Http\Request;
use Bifrost\Support\ValueObjects\ValueObject;
use Bifrost\Support\Concerns\ConvertibleFromArray;

class DataTransferObject extends ValueObject
{

  use ConvertibleFromArray;

  /**
   * @var array|null
   */
  public ?array $requestData = [];

  /**
   * DataTransferObject constructor.
   * @param array $params
   * @param string $case
   */
  public function __construct(array $params = [], string $case = 'camel')
  {
    parent::__construct($params, $case);

    $this->requestData = $params;
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
   * @param string $case
   * @return static
   */
  public static function fromRequest(Request $request, string $case = 'camel'): self
  {
    return new static($request->all(), $case);
  }

}
