<?php

namespace Bifrost\Repositories\QueryBuilder\Sorts;

use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Builder;

class SortByTranslatable implements Sort
{
  public function __invoke(Builder $query, bool $descending, string $property)
  {
    $locale = !Auth::guest() && method_exists(Auth::user(), 'getLocale')
      ? Auth::user()->getLocale()
      : Config::get('app.locale');

    return $descending ? $query->orderByDesc("{$property}->{$locale}") : $query->orderBy("{$property}->{$locale}");
  }
}
