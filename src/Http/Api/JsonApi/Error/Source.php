<?php

namespace Bifrost\Http\Api\JsonApi\Error;

use Illuminate\Contracts\Support\Arrayable;
use Bifrost\Support\Concerns\NotEmptyArrayable;
use Bifrost\Support\Concerns\HasMagicAttributes;

class Source implements Arrayable
{

  use HasMagicAttributes, NotEmptyArrayable;

  /**
   * A JSON Pointer [RFC6901] to the associated entity in the request document
   * [e.g. "/data" for a primary data object, or "/data/attributes/title" for
   * a specific attribute].
   *
   * @var null|string
   */
  protected ?string $pointer;

  /**
   * A string indicating which URI query parameter caused the error.
   *
   * @var null|string
   */
  protected ?string $parameter;

  /**
   * @param string|null $pointer
   * @param string|null $parameter
   * @return Source
   */
  public static function create(?string $pointer = null, ?string $parameter = null)
  {
    return new self($pointer, $parameter);
  }

  /**
   * Source constructor.
   * @param string|null $pointer
   * @param string|null $parameter
   */
  public function __construct(?string $pointer = null, ?string $parameter = null)
  {
    $this->pointer = $pointer;
    $this->parameter = $parameter;
  }

  /**
   * @return string|null
   */
  public function getPointer()
  {
    return $this->pointer;
  }

  /**
   * @param string $pointer
   * @return $this
   */
  public function setPointer(string $pointer)
  {
    $this->pointer = $pointer;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getParameter()
  {
    return $this->parameter;
  }

  /**
   * @param string $parameter
   * @return $this
   */
  public function setParameter(string $parameter)
  {
    $this->parameter = $parameter;

    return $this;
  }

}
