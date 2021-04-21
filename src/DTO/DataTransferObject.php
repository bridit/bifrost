<?php

namespace Bifrost\DTO;

use Illuminate\Http\Request;

class DataTransferObject extends \Bifrost\Support\DTO\DataTransferObject
{

  /**
   * @var array|null
   */
  public ?array $requestData = [];

  /**
   * DataTransferObject constructor.
   * @param ...$args
   */
  public function __construct(...$args)
  {
    parent::__construct($args);

    $this->requestData = $args;
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
  public static function fromRequest(Request $request): static
  {
    return new static($request->all());
  }

}
