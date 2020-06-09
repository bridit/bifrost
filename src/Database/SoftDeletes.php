<?php

namespace Bifrost\Database;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method static static|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder withTrashed()
 * @method static static|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder onlyTrashed()
 * @method static static|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder withoutTrashed()
 */
trait SoftDeletes
{

  /**
   * Determine if the model instance has been soft-deleted.
   *
   * @return bool
   */
  public function trashed()
  {
    return $this->active === false;
  }

  /**
   * Scope a query to include active and inactive models.
   *
   * @param  Builder  $query
   * @return Builder
   */
  public function scopeWithTrashed($query)
  {
    return $query->whereIn('active', [true, false]);
  }

  /**
   * Scope a query to only include active models.
   *
   * @param  Builder  $query
   * @return Builder
   */
  public function scopeWithoutTrashed($query)
  {
    return $query->where('active', true);
  }

  /**
   * Scope a query to only include active models.
   *
   * @param  Builder  $query
   * @return Builder
   */
  public function scopeActive($query)
  {
    return $this->scopeWithoutTrashed($query);
  }

  /**
   * Scope a query to only include inactive models.
   *
   * @param  Builder  $query
   * @return Builder
   */
  public function scopeOnlyTrashed($query)
  {
    return $query->where('active', false);
  }

  /**
   * Scope a query to only include inactive models.
   *
   * @param  Builder  $query
   * @return Builder
   */
  public function scopeInactive($query)
  {
    return $this->scopeOnlyTrashed($query);
  }

  /**
   * Register a restoring model event with the dispatcher.
   *
   * @param \Closure|string $callback
   * @return void
   */
  public static function restoring($callback)
  {
    static::registerModelEvent('restoring', $callback);
  }

  /**
   * Register a restored model event with the dispatcher.
   *
   * @param \Closure|string $callback
   * @return void
   */
  public static function restored($callback)
  {
    static::registerModelEvent('restored', $callback);
  }

}
