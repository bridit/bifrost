<?php

namespace Bifrost\Entities;

use Bifrost\Repositories\EntityRepositoryContract;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;

/**
 * Class Model
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property bool $active
 * @package Bifrost\Entities
 */
class Model extends BaseModel
{

  /**
   * Indicates when model key is UUID.
   *
   * @var string
   */
  protected $uuidKey = true;

  /**
   * The "type" of the auto-incrementing ID.
   *
   * @var string
   */
  protected $keyType = 'string';
  
  /**
   * Indicates if the IDs are auto-incrementing.
   *
   * @var bool
   */
  public $incrementing = false;

  /**
   * Repository classname
   *
   * @var string
   */
  protected static $repositoryClass;

  /**
   * The "booting" method of the model.
   *
   * @return void
   */
  protected static function boot(): void
  {
    parent::boot();

    static::creating(function (self $model): void {
      if ($model->uuidKey && empty($model->{$model->getKeyName()})) {
        $model->{$model->getKeyName()} = Uuid::uuid4();
      }
    });
  }

  /**
   * Return class repository
   *
   * @return EntityRepositoryContract
   */
  public static function getRepository()
  {
    $repositoryClass = static::$repositoryClass
      ?? str_replace("\\Domain\\Entities\\", '\\Infrastructure\\Repositories\\', static::class) . 'Repository';

    return App::make($repositoryClass);
  }

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

  /**
   * Scope a query to only include active users.
   *
   * @param  Builder  $query
   * @return Builder
   */
  public function scopeActive($query)
  {
    return $query->where('active', true);
  }

  /**
   * Scope a query to only include inactive users.
   *
   * @param  Builder  $query
   * @return Builder
   */
  public function scopeInactive($query)
  {
    return $query->where('active', false);
  }

}