<?php

namespace Bifrost\Transformers;

use Illuminate\Support\Arr;
use League\Fractal\TransformerAbstract;

abstract class InterfaceTransformer extends TransformerAbstract
{

  /**
   * @param string $entityName
   * @param array $attributes
   * @param array|null $allowed
   * @return array
   */
  protected function filter(string $entityName, array $attributes = [], ?array $allowed = null): array
  {
    $allowedAttributes = $allowed !== null
      ? Arr::only($attributes, $allowed)
      : $attributes;

    $requestFields = data_get(request()->get('fields', []), $entityName);
    $requestFields = array_filter(explode(',', $requestFields));

    # Return all allowed attributes
    if (blank($requestFields)) {
      return $allowedAttributes;
    }

    return Arr::only($allowedAttributes, $requestFields);
  }

}
