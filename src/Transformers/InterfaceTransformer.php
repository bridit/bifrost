<?php

namespace Bifrost\Transformers;

use Illuminate\Support\Arr;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Primitive;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\NullResource;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class InterfaceTransformer extends TransformerAbstract
{

  /**
   * @param string $entityName
   * @param array $attributes
   * @param array|null $allowed
   * @return array
   * @throws ContainerExceptionInterface
   * @throws NotFoundExceptionInterface
   */
  protected function filter(string $entityName, array $attributes = [], ?array $allowed = null): array
  {
    $allowedAttributes = $allowed !== null
      ? Arr::only($attributes, $allowed)
      : $attributes;

    $requestFields = data_get(request()->get('fields', []), $entityName);
    $requestFields = array_filter(explode(',', $requestFields));

    # Return all allowed attributes
    if (blank($requestFields)) {
      return $allowedAttributes;
    }

    return Arr::only($allowedAttributes, $requestFields);
  }

  /**
   * @param mixed $data
   * @param callable|TransformerAbstract $transformer
   * @param null $resourceKey
   * @return NullResource|Item
   */
  protected function itemResource($data, $transformer, $resourceKey = null): NullResource|Item
  {
    return blank($data) ? parent::null() : parent::item($data, $transformer, $resourceKey);
  }

  /**
   * @param mixed $data
   * @param callable|TransformerAbstract $transformer
   * @param null $resourceKey
   * @return NullResource|Collection
   */
  protected function collectionResource($data, $transformer, $resourceKey = null): NullResource|Collection
  {
    return blank($data) ? parent::null() : parent::collection($data, $transformer, $resourceKey);
  }

  /**
   * @param mixed $data
   * @param null $transformer
   * @param null $resourceKey
   * @return NullResource|Primitive
   */
  protected function primitiveResource($data, $transformer = null, $resourceKey = null): NullResource|Primitive
  {
    return blank($data) ? parent::null() : parent::primitive($data, $transformer, $resourceKey);
  }

}
