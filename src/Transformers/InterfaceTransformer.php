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

  protected function item($data, $transformer, $resourceKey = null)
  {
    if(blank($data)){
      return $this->null();
    }

    return parent::item($data, $transformer, $resourceKey);
  }

  protected function collection($data, $transformer, $resourceKey = null)
  {
    if(blank($data)){
      return $this->null();
    }

    return parent::collection($data, $transformer, $resourceKey);
  }

  protected function primitive($data, $transformer = null, $resourceKey = null)
  {
    if(blank($data)){
      return $this->null();
    }

    return parent::primitive($data, $transformer, $resourceKey);
  }
}
