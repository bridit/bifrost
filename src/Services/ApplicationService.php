<?php

namespace Bifrost\Services;

use Bifrost\DTO\DataTransferObject;
use Bifrost\Entities\Model;
use Bifrost\Transformers\Transformer;

abstract class ApplicationService
{
  private ?DomainService $service;

  private ?Transformer $transformer;

  /**
   * ApplicationService constructor.
   * @param Transformer|null $transformer
   * @param DomainService|null $service
   */
  public function __construct(?Transformer $transformer = null, ?DomainService $service = null)
  {
    $this->transformer = $transformer;
    $this->service = $service;
  }

  public abstract function getEntityClassName();

  /**
   * @param string $id
   * @return Model
   */
  public function find(string $id): string
  {
    $result = $this->getEntityClassName()::find($id);
    return $this->transformer->transform($result);
  }

  /**
   * @return Model
   */
  public function findAll(): string
  {
    $result = $this->getEntityClassName()::all();
    return $this->transformer->transform($result);
  }

  /**
   * @param DataTransferObject $dto
   * @return string|null
   */
  public function createOrUpdate(DataTransferObject $dto): ?string
  {
    $model = $this->find(optional($dto)->id);

    if($model == null){
      $this->create($dto);
    }

    return $this->updateModel($model, $dto);
  }

  /**
   * @param DataTransferObject $dto
   * @return string|null
   */
  public function create(DataTransferObject $dto): ?string
  {
    $model = $this->transformer->toModel($dto);

    $model = $this->service->create($model);

    return $this->transformer->transform($model);
  }

  /**
   * @param DataTransferObject $dto
   * @return string|null
   */
  public function update(DataTransferObject $dto): ?string
  {
    $model = $this->find(optional($dto)->id);

    if($model == null){
      return null;
    }

    return $this->updateModel($model, $dto);
  }

  /**
   * @param string $id
   * @return bool
   */
  public function delete(string $id): bool
  {
    $model = $this->find($id);

    if($model == null){
      return false;
    }

    $this->service->delete($model);

    return true;
  }

  /**
   * @param string $id
   * @return bool
   */
  public function restore(string $id): bool
  {
    $model = $this->find($id);

    if($model == null){
      return false;
    }

    $this->service->restore($model);

    return true;
  }

  /**
   * @param Model $model
   * @param DataTransferObject $dto
   * @return string
   */
  protected function updateModel(Model $model, DataTransferObject $dto): string
  {
    $this->transformer->prepareForUpdate($model, $dto);

    $this->service->update($model);

    return $this->transformer->transform($model);
  }

}