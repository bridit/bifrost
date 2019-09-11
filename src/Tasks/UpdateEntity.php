<?php

namespace Bifrost\Tasks;

use Bifrost\Entities\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Bridit\Laravel\Tasks\QueueableTask;

class UpdateEntity extends QueueableTask
{

  /**
   * @param string $entityClassName
   * @param $id
   * @param array $attributes
   * @return Collection|Model|\Illuminate\Database\Eloquent\Model
   */
  public static function execute(string $entityClassName, $id, array $attributes)
  {
    $entity = $entityClassName::find($id);

    if (!is_iterable($entity)) {
      return static::setEntityAttributes($entity, $attributes);
    }

    $result = [];
    foreach ($entity as $item)
    {
      $result[] = static::setEntityAttributes($item, $attributes);
    }

    return collect($result);
  }

  protected static function setEntityAttributes($entity, array $attributes)
  {
    $validAttributes = Arr::only($attributes, $entity->getFillable());

    foreach ($validAttributes as $name => $value)
    {
      $entity->{$name} = $value;
    }

    $entity->save();

    return $entity;
  }

}