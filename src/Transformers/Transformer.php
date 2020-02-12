<?php

namespace Bifrost\Transformers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use League\Fractal\TransformerAbstract;

class Transformer extends TransformerAbstract
{

  /**
   * @var string
   */
  private static string $entityName;

  protected function getResult(array $attributes, Model $entity)
  {
    $fields = array_filter(explode(',', Request::input('fields.' . $entity->getTable())));

    if (blank($fields)) {
      return $attributes;
    }

    $fields = array_unique(array_merge($fields, [$entity->getKeyName()]));

    return array_filter($attributes, fn($key) => in_array($key, $fields), ARRAY_FILTER_USE_KEY);
  }

}
