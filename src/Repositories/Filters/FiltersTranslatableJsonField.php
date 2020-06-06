<?php

namespace Bifrost\Repositories\Filters;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\PostgresConnection;

class FiltersTranslatableJsonField implements Filter
{
  public function __invoke(Builder $query, $value, string $property) : Builder
  {
    $locale = Config::get('app.locale');

    if ($query->getConnection() instanceof PostgresConnection) {
      return $query->where("{$property}->{$locale}", 'ilike', '%' . mb_strtolower($value) . '%');
    }

    return $query->where(DB::raw("LOWER({$property}->>'{$locale}'::text)"), 'like', '%' . mb_strtolower($value) . '%');
  }
}
