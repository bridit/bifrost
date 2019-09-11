<?php

namespace Bifrost\Tasks;

use Illuminate\Support\Arr;
use Bridit\Laravel\Tasks\QueueableTask;

class CreateEntity extends QueueableTask
{

  public static function execute(string $entityClassName, array $attributes)
  {
    $entity = new $entityClassName();
    $validAttributes = Arr::only($attributes, $entity->getFillable());

    foreach ($validAttributes as $name => $value)
    {
      $entity->{$name} = $value;
    }

    $entity->save();

    return $entity;
  }

}