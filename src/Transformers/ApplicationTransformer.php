<?php

namespace Bifrost\Transformers;

use ReflectionClass;
use ReflectionProperty;
use Bifrost\Models\IModel;
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

  /**
   * @return \Bifrost\Models\IModel
   */
  protected abstract function getModelInstance(): IModel;

  /**
   * @param \Bifrost\Models\IModel $model
   * @param \Bifrost\DTO\DataTransferObject $dto
   * @param string $property
   */
  protected function setAttributeFromDTO(IModel &$model, DataTransferObject $dto, string $property): void
  {
    $model->{Str::snake($property)} = $dto->{$property};
  }

  /**
   * @param \Bifrost\DTO\DataTransferObject $dto
   * @return \Bifrost\Models\IModel
   */
  public function toModel(DataTransferObject $dto): IModel
  {
    $model = $this->getModelInstance();

    $class = new ReflectionClass($dto);

    foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty)
    {
      $this->setAttributeFromDTO($model, $dto, $reflectionProperty->getName());
    }

    return $model;
  }

  /**
   * @param \Bifrost\Models\IModel $model
   * @param \Bifrost\DTO\DataTransferObject $dto
   * @return void
   */
  public function prepareForUpdate(IModel &$model, DataTransferObject $dto): void
  {
    foreach ($this->updateAllowed as $property) {
      call_if($dto->filled($property), fn() => $this->setAttributeFromDTO($model, $dto, Str::camel($property)));
    }
  }

  /**
   * @param array $attributes
   * @param \Bifrost\Models\IModel $model
   * @return array
   */
  protected function getResult(array $attributes, IModel $model): array
  {
    $fields = array_filter(explode(',', Request::input('fields.' . $model->getTable())));

    if (blank($fields)) {
      return $attributes;
    }

    $fields = array_unique(array_merge($fields, [$model->getKeyName()]));

    return array_filter($attributes, fn($key) => in_array($key, $fields), ARRAY_FILTER_USE_KEY);
  }

}
