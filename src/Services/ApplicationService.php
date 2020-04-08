<?php

namespace Bifrost\Services;

use Bifrost\Entities\Model;
use Illuminate\Support\Collection;
use Bifrost\DTO\DataTransferObject;
use Bifrost\Transformers\Transformer;
use Bifrost\Repositories\EntityRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class ApplicationService
{

  /**
   * @var DomainService|null
   */
  private ?DomainService $service;

  /**
   * @var Transformer|null
   */
  private ?Transformer $transformer;

  /**
   * @var EntityRepositoryContract|null
   */
  private EntityRepositoryContract $repository;

  /**
   * ApplicationService constructor.
   *
   * @param DomainService|null $service
   * @param Transformer|null $transformer
   * @param null|EntityRepositoryContract $repository
   */
  public function __construct(?DomainService $service = null, ?Transformer $transformer = null, ?EntityRepositoryContract $repository = null)
  {
    $this->service = $service;
    $this->repository = $repository;
    $this->transformer = $transformer;
  }

  /**
   * @return string
   */
  public function getEntityClassName()
  {
    return $this->repository->getEntityClassName();
  }

  /**
   * Finds an object by its primary key / identifier.
   *
   * @param mixed $id The identifier.
   *
   * @return \Illuminate\Database\Eloquent\Model|Collection|null The object.
   */

  public function find($id)
  {
    $result = $this->repository->find($id);

    return $this->transformer->transform($result);
  }

  /**
   * Finds all objects in the repository.
   *
   * @return Collection The objects.
   */
  public function findAll()
  {
    $result = $this->repository->findAll();

    return $this->transformer->transform($result);
  }

  /**
   * Finds objects by a set of criteria.
   *
   * Optionally sorting and limiting details can be passed. An implementation may throw
   * an UnexpectedValueException if certain values of the sorting or limiting details are
   * not supported.
   *
   * @param mixed[] $criteria
   * @param string[]|null $orderBy
   * @param int|null $limit
   * @param int|null $offset
   *
   * @return Collection The objects.
   */
  public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
  {
    return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
  }

  /**
   * Finds a single entity by a set of criteria.
   *
   * @param array $criteria
   * @param array|null $orderBy
   *
   * @return Model|null The entity instance or NULL if the entity can not be found.
   */
  public function findOneBy(array $criteria, ?array $orderBy = null)
  {
    return $this->repository->findOneBy($criteria, $orderBy);
  }

  /**
   * Counts entities by a set of criteria.
   *
   * @param null|array $criteria
   *
   * @return int The cardinality of the objects that match the given criteria.
   */
  public function count(?array $criteria = [])
  {
    return $this->repository->count($criteria);
  }

  /**
   * @return Collection
   */
  public function findWithQueryBuilder()
  {
    return $this->repository->findWithQueryBuilder();
  }

  /**
   * @return Model|null
   */
  public function findOneWithQueryBuilder()
  {
    return $this->repository->findOneWithQueryBuilder();
  }

  /**
   * @param int|null $perPage
   * @param int|null $pageNumber
   * @param array|null $columns
   * @return LengthAwarePaginator
   */
  public function paginate(?int $perPage, ?int $pageNumber, ?array $columns): LengthAwarePaginator
  {
    return $this->repository->paginate($perPage, $pageNumber, $columns);
  }


    /**
     * @param DataTransferObject $dto
     * @return Model|null
     */
  public function createOrUpdate(DataTransferObject $dto): ?Model
  {
    $model = $this->find(optional($dto)->id);

    if ($model === null) {
      $this->create($dto);
    }

    return $this->updateModel($model, $dto);
  }

    /**
     * @param DataTransferObject $dto
     * @return Model|null
     */
  public function create(DataTransferObject $dto): ?Model
  {
    $model = $this->transformer->toModel($dto);

    return $this->service->create($model);
  }

    /**
     * @param DataTransferObject $dto
     * @return Model|null
     */
  public function update(DataTransferObject $dto): ?Model
  {
    $model = $this->find(optional($dto)->id);

    if ($model === null) {
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

    if ($model === null) {
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

    if ($model === null) {
      return false;
    }

    $this->service->restore($model);

    return true;
  }

    /**
     * @param Model $model
     * @param DataTransferObject $dto
     * @return Model
     */
  protected function updateModel(Model $model, DataTransferObject $dto): Model
  {
    $this->transformer->prepareForUpdate($model, $dto);

    return $this->service->update($model);
  }

}
