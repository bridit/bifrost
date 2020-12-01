<?php

namespace Bifrost\Query;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\FlushQueryCacheObserver;

class BifrostFlushQueryCacheObserver extends FlushQueryCacheObserver
{
    /**
     * @param Model $model
     * @param bool $recreateCache
     */
    protected function invalidateCache(Model $model, bool $recreateCache = true): void
    {
        $class = get_class($model);

        $class::flushQueryCacheCustom(
            $model, $recreateCache
        );
    }

    /**
     * Handle the Model "forceDeleted" event.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function forceDeleted(Model $model)
    {
        $this->invalidateCache($model, false);
    }

    /**
     * Handle the Model "forceDeleted" event.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function deleted(Model $model)
    {
        $this->invalidateCache($model, false);
    }
}
