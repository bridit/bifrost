<?php

namespace Bifrost\Services;

use Exception;
use Carbon\Carbon;
use Bifrost\Entities\Model;
use Bifrost\Entities\ModelContract;

/**
 * Class DomainService
 * @package Bifrost\Services
 */
abstract class DomainService
{

  /**
   * Create a new registry in the database.
   *
   * @param ModelContract $model
   * @return Model
   */
  public function create(ModelContract $model): Model
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
   * @param ModelContract $model
   * @return Model
   */
  public function update(ModelContract $model): Model
  {
    $model->updated_at ??= Carbon::now()->setTimezone('UTC');

    $model->save();

    return $model;
  }

  /**
   * Set a registry as inactive.
   *
   * @param ModelContract $model
   * @return void
   */
  public function delete(ModelContract $model)
  {
    $model->updated_at ??= Carbon::now()->setTimezone('UTC');
    $model->active ??= false;

    $model->save();
  }

  /**
   * Restore an inactive registry.
   *
   * @param ModelContract $model
   * @return void
   */
  public function restore(ModelContract $model)
  {
    $model->updated_at ??= Carbon::now()->setTimezone('UTC');
    $model->active ??= true;

    $model->save();
  }

  /**
   * Remove a registry from the database.
   *
   * @param ModelContract $model
   * @return void
   * @throws Exception
   */
  public function forceDelete(ModelContract $model)
  {
    $model->delete();
  }
}
