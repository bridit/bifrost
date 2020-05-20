<?php

namespace Bifrost\Support\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Contracts\Support\Arrayable as ArrayableContract;

trait NotEmptyArrayable
{

  /**
   * Get the instance as an array.
   *
   * @return array
   */
  public function toArray()
  {
    $attributes = in_array(HasMagicAttributes::class, class_uses($this))
      ? Arr::except(array_merge(get_object_vars($this), $this->getMagicAttributes()), 'magicAttributes')
      : get_object_vars($this);

    $array = array_map(fn($item) => $item instanceof ArrayableContract ? $item->toArray() : $item, $attributes);

    return array_filter($array, fn($item) => !blank($item));
  }

}
