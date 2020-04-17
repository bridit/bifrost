<?php

namespace Bifrost\Entities;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Bifrost\Repositories\EntityRepositoryContract;

/**
 * Class BaseModel
 * @package Bifrost\Entities
 */
class BaseModel extends Model
{

  /**
   * Indicates when model key is UUID.
   *
   * @var string
   */
  protected $uuidKey = true;

  /**
   * @inheritdoc
   */
  protected $keyType = 'string';
  
  /**
   * @inheritdoc
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

}
