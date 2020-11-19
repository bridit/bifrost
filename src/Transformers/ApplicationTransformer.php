<?php

namespace Bifrost\Transformers;

use Bifrost\Models\Model;
use Illuminate\Support\Str;
use Bifrost\DTO\DataTransferObject;
use Illuminate\Support\Facades\Request;

abstract class ApplicationTransformer
{
  /**
   * Attributes that are allowed for update
   * @var array|null
   */
  public ?array $updateAllowed = [];

  public abstract function toModel(DataTransferObject $dto): Model;

  public function prepareForUpdate(Model &$model, DataTransferObject $dto)
  {
    foreach ($this->updateAllowed as $property) {
      call_if($dto->filled($property), fn() => $model->{Str::snake($property)} = $dto->{Str::camel($property)});
    }
  }

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
