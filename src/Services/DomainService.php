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
   * @return null|Model
   */
  public function create(Model $model): ?Model
  {
    $model->created_at ??= Carbon::now()->setTimezone('UTC');
    $model->updated_at = Carbon::now()->setTimezone('UTC');
    $model->active ??= true;

    return $model->save() ? $model : null;
  }

  /**
   * Update a registry in the database.
   *
   * @param Model $model
   * @return null|Model
   */
  public function update(Model $model): ?Model
  {
    $model->updated_at = Carbon::now()->setTimezone('UTC');

    return $model->save() ? $model : null;
  }

  /**
   * Set a registry as inactive.
   *
   * @param Model $model
   * @return bool
   */
  public function trash(Model $model): bool
  {
    $model->updated_at = Carbon::now()->setTimezone('UTC');
    $model->active = false;

    return $model->save();
  }

  /**
   * Set multiple registries as inactive.
   *
   * @todo Implement method return
   * @param iterable $models
   * @return void
   */
  public function trashMultiple(iterable $models): void
  {
    foreach ($models as $model)
    {
      $this->trash($model);
    }
  }

  /**
   * Restore an inactive registry.
   *
   * @param Model $model
   * @return bool
   */
  public function untrash(Model $model): bool
  {
    $model->updated_at = Carbon::now()->setTimezone('UTC');
    $model->active = true;

    return $model->save();
  }

  /**
   * Restore multiple inactive registries.
   *
   * @todo Implement method return
   * @param iterable $models
   * @return void
   */
  public function untrashMultiple(iterable $models): void
  {
    foreach ($models as $model)
    {
      $this->untrash($model);
    }
  }

  /**
   * Remove a registry from database.
   *
   * @param Model $model
   * @return null|bool
   * @throws Exception
   */
  public function delete(Model $model): ?bool
  {
    return $model->delete();
  }

  /**
   * Remove multiple registries from database.
   *
   * @todo Implement method return
   * @param iterable $models
   * @return void
   * @throws Exception
   */
  public function deleteMultiple(iterable $models): void
  {
    foreach ($models as $model)
    {
      $this->delete($model);
    }
  }

}
