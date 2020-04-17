<?php

namespace Bifrost\Entities;

use Carbon\Carbon;
use Bifrost\Database\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Model
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property bool $active
 * @package Bifrost\Entities
 */
class Model extends BaseModel
{

  use SoftDeletes;

  /**
   * Scope a query to only include created_at between two dates.
   *
   * @param  Builder  $query
   * @param  mixed    $initial
   * @param  mixed    $final
   * @return Builder
   */
  public function scopeCreatedBetween($query, $initial, $final)
  {
    return $query->whereBetween('created_at', [$initial, $final]);
  }

  /**
   * Scope a query to only include created_at between two dates.
   *
   * @param  Builder  $query
   * @param  mixed    $initial
   * @param  mixed    $final
   * @return Builder
   */
  public function scopeUpdatedBetween($query, $initial, $final)
  {
    return $query->whereBetween('updated_at', [$initial, $final]);
  }

}
