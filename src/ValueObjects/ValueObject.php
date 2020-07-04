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
     * @param null|bool $camelCase
     */
  public function __construct(?array $parameters = [], ?bool $camelCase = false)
  {
    $this->fillFromArray($parameters, $camelCase);
  }

  public function __toString()
  {
    return json_encode($this->toArray());
  }

    /**
     * @param array $params
     * @param bool $camelCase
     * @return static
     */
  public static function fromArray(array $params, bool $camelCase = false): self
  {
    return new static($params, $camelCase);
  }

}
