<?php

namespace Bifrost\Services;

use Bifrost\Entities\Model;
use Carbon\Carbon;

/**
 * Class DomainService
 * @package Bifrost\Services
 */
abstract class DomainService
{

  /**
   * Create a new register in the database
   *
   * @param Model $model
   * @return Model
   */
  public function create(Model $model): Model
  {
    $model->created_at = Carbon::now()->setTimezone('UTC');
    $model->updated_at = Carbon::now()->setTimezone('UTC');
    $model->active = true;

    $model->save();

    return $model;
  }

  /**
   * Update a register in the database
   *
   * @param Model $model
   * @return Model
   */
  public function update(Model $model): Model
  {
    $model->updated_at = Carbon::now()->setTimezone('UTC');

    $model->save();

    return $model;
  }

  /**
   * Remove a register from the database
   *
   * @param Model $model
   */
  public function delete(Model $model)
  {
    $model->active = false;

    $model->save();
  }

  /**
   * Restore a register from the database
   *
   * @param Model $model
   */
  public function restore(Model $model)
  {
    $model->active = true;

    $model->save();
  }
}
