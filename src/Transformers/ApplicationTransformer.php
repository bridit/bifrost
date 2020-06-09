<?php

namespace Bifrost\Transformers;

use Bifrost\Entities\Model;
use Bifrost\DTO\DataTransferObject;
use Illuminate\Support\Facades\Request;

abstract class ApplicationTransformer
{
  public abstract function toModel(DataTransferObject $dto): Model;

  public abstract function prepareForUpdate(Model &$model, DataTransferObject $dto);

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
