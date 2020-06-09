<?php

namespace Bifrost\Transformers;

use Illuminate\Support\Arr;
use League\Fractal\TransformerAbstract;

abstract class InterfaceTransformer extends TransformerAbstract
{

  /**
   * @param string $entityName
   * @param array $attributes
   * @return array
   */
  protected function filter(string $entityName, array $attributes = [])
  {
    $fields = data_get(request()->get('fields'), $entityName);

    if (blank($fields)) {
      return $attributes;
    }

    return Arr::only($attributes, explode(',', $fields));
  }

}
