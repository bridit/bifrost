<?php

namespace Bifrost\Support\Concerns;

trait HasMagicAttributes
{

  protected array $magicAttributes = [];

  /**
   * Dynamically retrieve attributes on the object.
   *
   * @param  string  $key
   * @return mixed
   */
  public function __get($key)
  {
    return $this->magicAttributes[$key];
  }

  /**
   * Dynamically set attributes on the object.
   *
   * @param  string  $key
   * @param  mixed  $value
   * @return void
   */
  public function __set($key, $value)
  {
    $this->magicAttributes[$key] = $value;
  }

  /**
   * @return array
   */
  public function getMagicAttributes(): array
  {
    return $this->magicAttributes;
  }

}
