<?php

namespace Bifrost\Repositories;

use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class EntityRepository implements EntityRepositoryContract
{

  protected $entityClassName;
  protected $defaultSort;
  protected $allowedIncludes = [];
  protected $allowedFilters = [];
  protected $allowedSorts = [];
  protected $allowedFields = [];
  protected $allowedAppends = [];

  /**
   * @inheritDoc
   */
  public function getEntityClassName(): string
  {
    return $this->entityClassName;
  }

  /**
   * @inheritDoc
   */
  public function find($id)
  {
    return $this->getEntityClassName()::find($id);
  }

  /**
   * @inheritDoc
   */
  public function findAll()
  {
    return $this->getEntityClassName()::all();
  }

  /**
   * @inheritDoc
   */
  public function findBy(array $criteria, ?array $orderBy = [], ?int $limit = null, ?int $offset = null)
  {
    $qb = $this->getEntityClassName()::query();

    foreach ($criteria as $attribute => $value)
    {
      $qb = $qb->where($attribute, $value);
    }

    if (blank($orderBy) && !blank($this->defaultSort)) {
      $orderBy = substr($this->defaultSort, 0, 1) === '-'
        ? [$this->defaultSort => 'asc']
        : [substr($this->defaultSort, 1, strlen($this->defaultSort)) => 'desc'];
    }

    foreach ($orderBy as $attribute => $direction)
    {
      $qb = $qb->orderBy($attribute, $direction);
    }

    if ($limit !== null) {
      $qb = $qb->limit($limit);
    }

    if ($offset !== null) {
      $qb = $qb->offset($offset);
    }

    return $qb->get();
  }

  /**
   * @inheritDoc
   */
  public function findOneBy(array $criteria, ?array $orderBy = [])
  {
    $this->findBy($criteria, $orderBy, 1);
  }

  /**
   * @inheritDoc
   */
  public function count(?array $criteria = []): int
  {
    $qb = $this->getEntityClassName()::query();

    foreach ($criteria as $attribute => $value)
    {
      $qb = $qb->where($attribute, $value);
    }

    return $qb->count();
  }

  /**
   * @inheritDoc
   */
  public function findWithQueryBuilder()
  {
    return $this->getQueryBuilder()->get();
  }

  /**
   * @inheritDoc
   */
  public function findOneWithQueryBuilder()
  {
    return $this->getQueryBuilder()->first();
  }

  /**
   * @inheritDoc
   */
  public function getQueryBuilder(): QueryBuilder
  {
    $queryBuilder = QueryBuilder::for($this->getEntityClassName())
      ->allowedFields($this->allowedFields)
      ->allowedIncludes($this->allowedIncludes)
      ->allowedAppends($this->allowedAppends)
      ->allowedFilters($this->allowedFilters)
      ->allowedSorts($this->allowedSorts)
      ->defaultSort($this->defaultSort);

    $limit = request()->input('page.limit');
    $offset = request()->input('page.offset');

    if (!blank($limit)) {
      $queryBuilder = $queryBuilder->limit((int) $limit);
    }

    if (!blank($offset)) {
      $queryBuilder = $queryBuilder->offset((int) $offset);
    }

    return $queryBuilder;
  }

  /**
   * @inheritDoc
   */
  public function paginate(?int $perPage): LengthAwarePaginator
  {
    return $this->getQueryBuilder()->paginate($perPage ?? config('bifrost.orm.pagination.default_limit', 25));
  }
}