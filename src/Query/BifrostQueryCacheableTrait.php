<?php

namespace Bifrost\Query;

use Illuminate\Support\Facades\Config;
use Rennokki\QueryCache\Traits\QueryCacheable;

trait BifrostQueryCacheableTrait
{
    use QueryCacheable;

    protected static function getFlushQueryCacheObserver()
    {
        return BifrostFlushQueryCacheObserver::class;
    }

    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootQueryCacheable()
    {
        if (isset(static::$flushCacheOnUpdate) && static::$flushCacheOnUpdate) {
            static::observe(
                static::getFlushQueryCacheObserver()
            );
        } else if(!isset(static::$flushCacheOnUpdate) && Config::get('bifrost.query_cache.flush_cache_on_update')) {
            static::observe(
                static::getFlushQueryCacheObserver()
            );
        }
    }


    /**
     * {@inheritdoc}
     */
    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();

        $builder = new BifrostBuilder(
            $connection,
            $connection->getQueryGrammar(),
            $connection->getPostProcessor()
        );

        if($this->cacheFor) {
            $builder->cacheFor($this->cacheFor);
        }

        if($this->cacheForFind) {
            $builder->cacheForFind($this->cacheForFind);
        }

        if ($this->cacheTags) {
            $builder->cacheTags($this->cacheTags);
        }

        if ($this->cachePrefix) {
            $builder->cachePrefix($this->cachePrefix);
        }

        if ($this->cacheDriver) {
            $builder->cacheDriver($this->cacheDriver);
        }

        if ($this->cacheUsePlainKey) {
            $builder->withPlainKey();
        }

        if($this->clearCacheMode) {
            $builder->clearCacheMode($this->clearCacheMode);
        }

        return $builder->cacheBaseTags($this->getCacheBaseTags());
    }
}
