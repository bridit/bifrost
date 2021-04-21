<?php

namespace Bifrost\Support\Concerns\Contracts;

use Illuminate\Support\Collection;

interface Arrayable
{

  /**
   * Get the instance as an array.
   *
   * @param string|null $case
   * @param bool $preserveEmpty
   * @return array
   */
  public function toArray(string $case = null, bool $preserveEmpty = true): array;

  /**
   * Get the instance as an collection object.
   *
   * @param string|null $case
   * @param bool $preserveEmpty
   * @return Collection
   */
  public function toCollection(string $case = null, bool $preserveEmpty = true): Collection;

  /**
   * Get the instance as an json string.
   *
   * @param string|null $case
   * @param bool $preserveEmpty
   * @return string
   */
  public function toJson(string $case = null, bool $preserveEmpty = true): string;

}