<?php

namespace Bifrost\Support\Concerns;

use Illuminate\Support\Str;
use Bifrost\Support\Concerns\Contracts\Arrayable as ArrayableContract;

trait Arrayable
{

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

  /**
   * Get the instance as an array.
   *
   * @param string|null $case
   * @param bool $preserveEmpty
   * @return array
   */
  public function toArray(string $case = null, bool $preserveEmpty = true): array
  {
    $attributes = null !== $case
      ? $this->convertCase(get_object_vars($this), $case)
      : get_object_vars($this);

    $array = array_map(fn($item) => $item instanceof ArrayableContract ? $item->toArray($case, $preserveEmpty) : $item, $attributes);

    return true === $preserveEmpty
      ? $array
      : array_filter($array, fn($item) => !blank($item));
  }

  /**
   * @param array $attributes
   * @param string $case
   * @return array
   */
  private function convertCase(array $attributes, string $case): array
  {
    $result = [];

    foreach ($attributes as $key => $value)
    {
      $key = match($case)
      {
        'snake' => Str::snake($key),
        'camel'=> Str::camel($key),
        'slug', 'kebab' => Str::kebab($key),
        'studly' => Str::studly($key),
        default => $key,
      };

      $result[$key] = $value;
    }

    return $result;
  }

}
