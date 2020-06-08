<?php

namespace Bifrost\ValueObjects;

use Bifrost\Support\Concerns\Arrayable;
use Bifrost\Support\Concerns\ConvertibleFromArray;

class ValueObject
{

  use ConvertibleFromArray, Arrayable;

  /**
   * ValueObject constructor.
   * @param null|array $parameters
   */
  public function __construct(?array $parameters = [])
  {
    $this->fillFromArray($parameters);
  }

  public function __toString()
  {
    return json_encode($this->toArray());
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
