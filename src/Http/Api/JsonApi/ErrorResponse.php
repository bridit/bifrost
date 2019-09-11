<?php

namespace Bifrost\Http\Api\JsonApi;

use Illuminate\Support\Arr;
use Illuminate\Http\JsonResponse;

class ErrorResponse
{

  /**
   * @param $errors
   * @param int|null $defaultHttpCode
   * @param array $headers
   * @return JsonResponse
   */
  public static function get($errors, ?int $defaultHttpCode = null, array $headers = []): JsonResponse
  {
    return response()->json(['errors' => static::getContent($errors)], static::getHttpCode($errors, $defaultHttpCode), static::getHeaders($headers));
  }

  /**
   * @param $errors
   * @return array
   */
  protected static function getContent($errors)
  {
    $result = [];

    foreach ($errors as $error)
    {
      $result[] = $error->toArray();
    }

    return $result;
  }

  /**
   * @param $errors
   * @param int|null $defaultHttpCode
   * @return int|null
   */
  protected static function getHttpCode($errors, ?int $defaultHttpCode = null)
  {
    if (!blank($defaultHttpCode)) {
      return $defaultHttpCode;
    }

    $collection = collect($errors);

    $grouped = $collection->groupBy(function ($item, $key) {
      return $item->getStatus();
    });

    $sorted = $grouped->sortByDesc(function ($item, $key) {
      return $item->count();
    });

    $httpCode = $sorted->keys()->first();

    return $httpCode ?? 422;
  }

  /**
   * @param $headers
   * @return array
   */
  protected static function getHeaders($headers)
  {
    $contentType = Arr::get($headers, 'Content-Type', Arr::get($headers, 'content-type'));

    if ($contentType === null)
    {
      $headers['Content-Type'] = 'application/vnd.api+json';
    }

    return $headers;
  }

}
