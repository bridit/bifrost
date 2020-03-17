<?php

namespace Bifrost\Services;

use Bifrost\Tasks\CreateEntity;
use Bifrost\Tasks\DeleteEntity;
use Bifrost\Tasks\UpdateEntity;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Bifrost\Repositories\EntityRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class DomainService
 * @package Bifrost\Services
 */
class DomainService
{

  /**
   * @var EntityRepositoryContract
   */
  protected $repository;

  /**
   * DomainService constructor.
   * @param null|EntityRepositoryContract $repository
   */
  public function __construct(?EntityRepositoryContract $repository = null)
  {
    $this->repository = $repository;
  }

  /**
   * @return string
   */
  public function getEntityClassName()
  {
    return $this->repository->getEntityClassName();
  }

  /**
   * @return string
   */
  public function getTransformerClassName()
  {
    return str_replace('\\Entities\\', '\\Transformers\\', $this->getEntityClassName()) . 'Transformer';
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
   * @param array $attributes
   * @return Model|\Bifrost\Entities\Model
   */
  public function create(array $attributes)
  {
    return CreateEntity::execute($this->getEntityClassName(), $attributes);
  }

  /**
   * @param mixed $id
   * @param array $attributes
   * @return Model|\Bifrost\Entities\Model
   */
  public function update($id, array $attributes)
  {
    return UpdateEntity::execute($this->getEntityClassName(), $id, $attributes);
  }

  /**
   * @param mixed $id
   * @return bool
   */
  public function delete($id): bool
  {
    $count = DeleteEntity::execute($this->getEntityClassName(), $id);

    return is_array($id) ? $count === count($id) : $count === 1;
  }

}
