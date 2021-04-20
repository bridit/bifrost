<?php

namespace Bifrost\Support\ValueObjects;

use Bifrost\Support\Concerns\Arrayable;
use Bifrost\Support\Concerns\ConvertibleFromArray;
use Bifrost\Support\Concerns\Contracts\Arrayable as ArrayableContract;

class ValueObject implements ArrayableContract
{

  use ConvertibleFromArray, Arrayable;

  /**
   * ValueObject constructor.
   * @param array $params
   * @param string $case
   */
  public function __construct(array $params = [], string $case = 'camel')
  {
    $this->fillFromArray($params, $case);
  }

  /**
   * @param array $params
   * @param string $case
   * @return static
   */
  public static function fromArray(array $params, string $case = 'camel'): self
  {
    return new static($params, $case);
  }

  /**
   * @param string $json
   * @param string $case
   * @return static
   */
  public static function fromJson(string $json, string $case = 'camel'): self
  {
    return new static(json_decode($json, true), $case);
  }

  public function __toString()
  {
    return $this->toJson(null, true);
  }

}
