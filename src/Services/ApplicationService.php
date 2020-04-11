<?php

namespace Bifrost\Services;

use Exception;
use Bifrost\Entities\Model;
use Bifrost\DTO\DataTransferObject;
use Illuminate\Database\Eloquent\Collection;
use Bifrost\Transformers\ApplicationTransformer;
use Bifrost\Repositories\EntityRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class ApplicationService
{

  /**
   * @var DomainService|null
   */
  private ?DomainService $service;

  /**
   * @var ApplicationTransformer|null
   */
  private ?ApplicationTransformer $transformer;

  /**
   * @var EntityRepositoryContract|null
   */
  private EntityRepositoryContract $repository;

  /**
   * ApplicationService constructor.
   *
   * @param DomainService|null $service
   * @param ApplicationTransformer|null $transformer
   * @param null|EntityRepositoryContract $repository
   */
  public function __construct(?DomainService $service = null, ?ApplicationTransformer $transformer = null, ?EntityRepositoryContract $repository = null)
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
   * @return Model|Collection|null The object.
   */
  public function find($id)
  {
    return $this->repository->find($id);
  }

  /**
   * Finds all objects in the repository.
   *
   * @return Collection The objects.
   */
  public function findAll()
  {
    return $this->repository->findAll();
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
  public function findOneWithQueryBuilder(): ?Model
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
   * @return Model
   */
  public function createOrUpdate(DataTransferObject $dto): Model
  {
    $model = $this->find(optional($dto)->id);

    if ($model === null) {
      return $this->create($dto);
    }

    return $this->updateModel($model, $dto);
  }

  /**
   * @param DataTransferObject $dto
   * @return Model
   */
  public function create(DataTransferObject $dto): Model
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

    return $model !== null ? $this->updateModel($model, $dto) : null;
  }

  /**
   * @param int|string $id
   * @return void
   */
  public function delete($id)
  {
    $model = $this->find($id);

    if ($model !== null) {
      $this->service->delete($model);
    }
  }

  /**
   * @param int|string $id
   * @return void
   */
  public function restore($id)
  {
    $model = $this->find($id);

    if ($model !== null) {
      $this->service->restore($model);
    }
  }

  /**
   * @param int|string $id
   * @return void
   * @throws Exception
   */
  public function forceDelete($id)
  {
    $model = $this->find($id);

    if ($model !== null) {
      $this->service->forceDelete($model);
    }
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
