<?php

namespace Bifrost\Repositories;

use Illuminate\Support\Collection;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface EntityRepositoryContract
{

  /**
   * Returns the class name of the object managed by the repository.
   *
   * @return string
   */
  public function getEntityClassName(): string;

  /**
   * Finds an object by its primary key / identifier.
   *
   * @param mixed $id The identifier.
   *
   * @return mixed The object.
   */
  public function find($id);

  /**
   * Finds all objects in the repository.
   *
   * @return object[] The objects.
   */
  public function findAll();

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
   * @return object[] The objects.
   *
   * @throws UnexpectedValueException
   */
  public function findBy(array $criteria, ?array $orderBy = [], ?int $limit = null, ?int $offset = null);

  /**
   * Finds a single object by a set of criteria.
   *
   * @param mixed[] $criteria The criteria.
   * @param string[]|null $orderBy
   *
   * @return object|null The object.
   */
  public function findOneBy(array $criteria, ?array $orderBy = []);

  /**
   * @param array|null $criteria
   * @return int
   */
  public function count(?array $criteria = []): int;

  /**
   * @return Collection
   */
  public function findWithQueryBuilder();

  /**
   * @return Model|null
   */
  public function findOneWithQueryBuilder();

  /**
   * Get QueryBuilder
   *
   * @return QueryBuilder The object.
   */
  public function getQueryBuilder(): QueryBuilder;

  /**
   * @param int|null $perPage
   * @param int|null $pageNumber
   * @param array|null $columns
   * @return LengthAwarePaginator
   */
  public function paginate(?int $perPage, ?int $pageNumber, ?array $columns): LengthAwarePaginator;
}