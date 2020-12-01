<?php

namespace Bifrost\Query;

use Bifrost\Enums\BifrostCacheModeEnum;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Support\Facades\Config;
use Rennokki\QueryCache\Query\Builder;
use Exception;
use DateTime;


class BifrostBuilder extends Builder
{
  /**
   * @var string
   */
  protected $clearCacheMode;

  protected $cacheTimeFind;

  /**
   * BifrostBuilder constructor.
   * @param ConnectionInterface $connection
   * @param Grammar|null $grammar
   * @param Processor|null $processor
   * @throws Exception
   */
  public function __construct(ConnectionInterface $connection, Grammar $grammar = null, Processor $processor = null)
  {
    $this->cacheDriver(Config::get('bifrost.query_cache.driver'))
      ->cachePrefix(Config::get('bifrost.query_cache.prefix'))
      ->clearCacheMode(Config::get('bifrost.query_cache.cache_mode', BifrostCacheModeEnum::CLEAR_CACHE_BY_KEYS))
      ->cacheFor(Config::get('bifrost.query_cache.time'))
      ->cacheForFind(Config::get('bifrost.query_cache.time_for_find'));

    parent::__construct($connection, $grammar, $processor);
  }

  /**
   * @param Model $model
   * @param bool $canRecreateCache
   * @return bool
   * @throws Exception
   */
  public function flushQueryCacheCustom(Model $model, bool $canRecreateCache = true): bool
  {
    $isDynamoDB = Config::get('cache.stores.' . $this->cacheDriver . '.driver') === 'dynamodb';

    if ($isDynamoDB && $this->clearCacheMode === BifrostCacheModeEnum::CLEAR_CACHE_BY_TAGS) {
      throw new Exception('Dynamodb only supports clear cache by keys');
    }

    if ($this->clearCacheMode === BifrostCacheModeEnum::CLEAR_CACHE_BY_TAGS) {
      return $this->clearCacheByTags($model);
    }

    return $this->clearCacheByKeys($model);
  }

  /**
   * @param string $method
   * @param string[] $columns
   * @param null $id
   * @return array|mixed
   */
  public function getFromQueryCache(string $method = 'get', $columns = ['*'], $id = null)
  {
    if (is_null($this->columns)) {
      $this->columns = $columns;
    }

    $key = $this->getCacheKey('get');
    $cache = $this->getCache();
    $callback = $this->getQueryCacheCallback($method, $columns, $id);

    $time = $this->isQueryById()
      ? $this->getCacheForFind()
      : $this->getCacheTime();

    if ($time instanceof DateTime || $time > 0) {
      return $cache->remember($key, $time, $callback);
    }

    return $cache->rememberForever($key, $callback);
  }

  /**
   * @param Model $model
   * @return false
   * @throws Exception
   */
  protected function clearCacheByTags(Model $model)
  {
    if (!method_exists($this->getCacheDriver(), 'tags')) {
      return false;
    }

    $tags = $model->getCacheTagsToInvalidateOnUpdate();
    $class = get_class($model);

    if (!$tags) {
      throw new Exception('Automatic invalidation for ' . $class . ' works only if at least one tag to be invalidated is specified.');
    }

    $tags = $model->getCacheTagsToInvalidateOnUpdate();

    if (!$tags) {
      $tags = $this->getCacheBaseTags();
    }

    foreach ($tags as $tag) {
      self::flushQueryCacheWithTag($tag);
    }

    return true;
  }

  /**
   * @param Model $model
   * @param bool $canRecreate
   * @return bool
   */
  protected function clearCacheByKeys(Model $model, bool $canRecreate = true)
  {
    $key = $this->getCustomCacheKey($model);

    if (blank($key)) {
      return false;
    }

    $cacheDriver = $this->getCacheDriver();

    $cacheDriver->forget($key);

    if (
      ( $model->recreateCache OR (!isset($model->recreateCache) && Config::get('bifrost.query_cache.recreate_cache')))
      && $canRecreate
    ) {

      $callback = $this->getCustomQueryCallback($model);

      $cacheTimeForFind = $model->cacheForFind ?? $this->getCacheForFind();
      $cacheTime = $model->cacheFor ?? $this->getCacheTime();

      $time = $this->isQueryById()
        ? $cacheTimeForFind
        : $cacheTime;

      if ($time instanceof DateTime || $time > 0) {
        $cacheDriver->remember($key, $time, $callback);
      } else {
        $cacheDriver->rememberForever($key, $callback);
      }
    }

    return true;
  }

  /**
   * @param string $clearCacheMode
   * @return $this
   * @throws Exception
   */
  public function clearCacheMode(string $clearCacheMode)
  {
    if (!in_array($clearCacheMode, [
      BifrostCacheModeEnum::CLEAR_CACHE_BY_TAGS, BifrostCacheModeEnum::CLEAR_CACHE_BY_KEYS
    ])) {
      throw new Exception('Clear cache mode not supported');
    }

    $this->clearCacheMode = $clearCacheMode;

    return $this;
  }

  /**
   * @return string
   */
  public function getClearCacheMode()
  {
    return $this->clearCacheMode;
  }

  /**
   * Indicate that the query results should be cached.
   *
   * @param \DateTime|int $time
   * @return \Rennokki\QueryCache\Query\Builder
   */
  public function cacheFor($time)
  {
    if ($time instanceof DateTime || $time >= 0) {
      $this->cacheTime = $time;
      $this->avoidCache = false;
    } else {
      $this->cacheTime = $time;
      $this->avoidCache = true;
    }

    return $this;
  }

  public function cacheForFind($time)
  {
    if ($time instanceof DateTime || $time >= 0) {
      $this->cacheTimeFind = $time;
    } else {
      $this->cacheTimeFind = $this->cacheTime;
    }

    return $this;
  }

  public function getCacheForFind()
  {
    return $this->cacheTimeFind;
  }

  /**
   * @param Model $model
   * @return string|null
   */
  public function getCustomCacheKey(Model $model): ?string
  {
    $key = $this->generateCustomCacheKey($model);

    if (blank($key)) {
      return null;
    }

    $prefix = $this->getCachePrefix();

    return "{$prefix}:{$key}";
  }

  /**
   * @param Model $model
   * @return string|null
   */
  public function generateCustomCacheKey(Model $model): ?string
  {
    $key = $this->generateCustomPlainCacheKey($model);

    if (blank($key)) {
      return null;
    }

    if ($this->shouldUsePlainKey()) {
      return $key;
    }

    return hash('sha256', $key);
  }

  /**
   * @param Model $model
   * @param string $method
   * @return string|null
   */
  public function generateCustomPlainCacheKey(Model $model, string $method = 'get')
  {
    if (blank($model->id)) {
      return null;
    }

    $name = $this->connection->getName();

    // Count has no Sql, that's why it can't be used ->toSql()
    if ($method === 'count') {
      return $name . $method . serialize($this->getBindings());
    }

    /**
     * @var \Illuminate\Database\Eloquent\Builder $query
     */
    $query = get_class($model)::where( $model->getTable() . '.id', '=', $model->id)->take(1);

    $sql = $query->toSql();

    $bindings = $query->getBindings();

    return $name . $method . $sql . serialize($bindings);
  }

  public function getCustomQueryCallback(Model $model)
  {
    return function () use ($model) {
      return collect([(object) $model->getAttributes()]);
    };
  }

  public function isQueryById()
  {
    $wheres = $this->wheres;

    if(count($wheres) > 1 OR blank($wheres)) {
      return false;
    }

    $column = strtolower($wheres[0]['column']);

    if($column === 'id') {
      return true;
    }

    $from = strtolower($this->from);

    if($column === $from. '.id') {
      return true;
    }

    return false;
  }

  /**
   * Get the cache time attribute.
   *
   * @return int|\DateTime
   */
  public function getCacheTime()
  {
    return $this->cacheTime;
  }

}
