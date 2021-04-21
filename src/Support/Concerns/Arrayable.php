<?php

namespace Bifrost\Support\Concerns;

use Bifrost\Support\Concerns\Contracts\Arrayable as ArrayableContract;
use Illuminate\Support\Collection;

trait Arrayable
{



  /**
   * Get the instance as an array.
   *
   * @param callable|string|null $case
   * @param bool $preserveEmpty
   * @return array
   */
  public function toArray(callable|string $case = null, bool $preserveEmpty = true): array
  {
    $attributes = null === $case
      ? get_object_vars($this)
      : array_convert_key_case(get_object_vars($this), $case, true);

    $array = array_map(fn($item) => $item instanceof ArrayableContract ? $item->toArray($case, $preserveEmpty) : $item, $attributes);

    return true === $preserveEmpty
      ? $array
      : array_filter($array, fn($item) => !blank($item));
  }

  /**
   * Get the instance as an collection object.
   *
   * @param string|null $case
   * @param bool $preserveEmpty
   * @return Collection
   */
  public function toCollection(string $case = null, bool $preserveEmpty = true): Collection
  {
    return Collection::make($this->toArray($case, $preserveEmpty));
  }

  /**
   * Get the instance as an json string.
   *
   * @param string|null $case
   * @param bool $preserveEmpty
   * @return string
   */
  public function toJson(string $case = null, bool $preserveEmpty = true): string
  {
    return json_encode($this->toArray($case, $preserveEmpty));
  }

}
