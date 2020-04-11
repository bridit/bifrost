<?php

namespace Bifrost\Services;

use Exception;
use Carbon\Carbon;
use Bifrost\Entities\Model;

/**
 * Class DomainService
 * @package Bifrost\Services
 */
abstract class DomainService
{

  /**
   * Create a new registry in the database.
   *
   * @param Model $model
   * @return Model
   */
  public function create(Model $model): Model
  {
    $model->created_at ??= Carbon::now()->setTimezone('UTC');
    $model->updated_at ??= Carbon::now()->setTimezone('UTC');
    $model->active ??= true;

    $model->save();

    return $model;
  }

  /**
   * Update a registry in the database.
   *
   * @param Model $model
   * @return Model
   */
  public function update(Model $model): Model
  {
    $model->updated_at ??= Carbon::now()->setTimezone('UTC');

    $model->save();

    return $model;
  }

  /**
   * Set a registry as inactive.
   *
   * @param Model $model
   * @return void
   */
  public function delete(Model $model): void
  {
    $model->updated_at ??= Carbon::now()->setTimezone('UTC');
    $model->active ??= false;

    $model->save();
  }

  /**
   * Restore an inactive registry.
   *
   * @param Model $model
   * @return void
   */
  public function restore(Model $model): void
  {
    $model->updated_at ??= Carbon::now()->setTimezone('UTC');
    $model->active ??= true;

    $model->save();
  }

  /**
   * Remove a registry from the database.
   *
   * @param Model $model
   * @return void
   * @throws Exception
   */
  public function forceDelete(Model $model): void
  {
    $model->delete();
  }
}
