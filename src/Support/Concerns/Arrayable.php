<?php

namespace Bifrost\Support\Concerns;

use Illuminate\Contracts\Support\Arrayable as ArrayableContract;

trait Arrayable
{

  /**
   * Get the instance as an array.
   *
   * @return array
   */
  public function toArray()
  {
    $attributes = $this instanceof HasMagicAttributes ? array_merge(get_object_vars($this), $this->getMagicAttributes()) : get_object_vars($this);

    return array_map(fn($item) => $item instanceof ArrayableContract ? $item->toArray() : $item, $attributes);
  }

}
