<?php

namespace Bifrost\Repositories;

use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Bifrost\Repositories\QueryBuilder\Filters\FiltersTranslatableJsonField;

class EntityRepository implements EntityRepositoryContract
{

  protected string $entityClassName;
  protected string $defaultSort;
  protected array $allowedIncludes = [];
  protected array $allowedFilters = [];
  protected array $allowedSorts = [];
  protected array $allowedFields = [];
  protected array $allowedScopes = [];
  protected array $allowedAppends = [];
  protected array $exactFilters = [];
  protected array $partialFilters = [];
  protected array $translatableJsonFilters = [];

  public function __construct()
  {
    $this->setAllowedFilters();
  }

  /**
   * @return array
   */
  private function setAllowedFilters()
  {
    $exactFilters = array_map(function ($item) {
      return AllowedFilter::exact($item);
    }, $this->exactFilters);

    $partialFilters = array_map(function ($item) {
      return AllowedFilter::partial($item);
    }, $this->partialFilters);

    $scopeFilters = array_map(function ($item) {
      return AllowedFilter::scope($item);
    }, $this->allowedScopes);

    $translatableJsonFilters = array_map(function ($item) {
      return AllowedFilter::custom($item, new FiltersTranslatableJsonField());
    }, $this->translatableJsonFilters);

    $this->allowedFilters = array_merge($exactFilters, $partialFilters, $scopeFilters, $translatableJsonFilters);
  }

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
      $orderBy = $this->defaultSort[0] !== '-'
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
    return $this->findBy($criteria, $orderBy, 1)->first();
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
  public function getQueryBuilder(?bool $applyCustomFilters = true): QueryBuilder
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

    if ($applyCustomFilters) {
      return $this->applyQueryBuilderCustomFilters($queryBuilder);
    }

    return $queryBuilder;
  }

  /**
   * @param QueryBuilder $queryBuilder
   * @return QueryBuilder
   */
  protected function applyQueryBuilderCustomFilters(QueryBuilder $queryBuilder): QueryBuilder
  {
    return $queryBuilder;
  }

  /**
   * @inheritDoc
   */
  public function paginate(?int $perPage, ?int $pageNumber, ?array $columns): LengthAwarePaginator
  {
    return $this->getQueryBuilder()->paginate($perPage ?? config('bifrost.orm.pagination.default_limit', 25), $columns ?? ['*'], 'page', $pageNumber ?? 1);
  }
}
