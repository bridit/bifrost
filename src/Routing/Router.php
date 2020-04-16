<?php

namespace Bifrost\Routing;

use Illuminate\Contracts\Container\BindingResolutionException;

class Router extends \Illuminate\Routing\Router
{

  /**
   * Get the applicable resource methods.
   *
   * @param array $defaults
   * @param array $options
   * @return array
   */
  public function getResourceMethodsDefaults($defaults, $options)
  {
    if (isset($options['withMultiple']) && $options['withMultiple'] === true) {
      $defaults = [...$defaults, ...['destroyMultiple']];
    }

    if (isset($options['withSoftDeletes']) && $options['withSoftDeletes'] === true) {
      $defaults = [...$defaults, ...['trashed', 'trash', 'untrash']];

      if (isset($options['withMultiple']) && $options['withMultiple'] === true) {
        $defaults = [...$defaults, ...['trashMultiple', 'untrashMultiple']];
      }
    }

    return $defaults;
  }

  /**
   * Route an api resource to a controller.
   *
   * @param string $name
   * @param string $controller
   * @param array $options
   * @return PendingResourceRegistration
   * @throws BindingResolutionException
   */
  public function apiResource($name, $controller, array $options = [])
  {
    $only = $this->getResourceMethodsDefaults(['index', 'show', 'store', 'update', 'destroy'], $options);

    if (isset($options['except'])) {
      $only = array_diff($only, (array)$options['except']);
    }

    return $this->resource($name, $controller, array_merge([
      'only' => $only,
    ], $options));
  }

  /**
   * Route an full api resource to a controller.
   *
   * @param string $name
   * @param string $controller
   * @param array $options
   * @return PendingResourceRegistration
   */
  public function fullApiResource($name, $controller, array $options = [])
  {
    return $this->apiResource($name, $controller, array_merge($options, ['withMultiple' => true, 'withSoftDeletes' => true]));
  }

  /**
   * Route an full api resource to a controller.
   *
   * @param string $name
   * @param string $controller
   * @param array $options
   * @return PendingResourceRegistration
   * @throws BindingResolutionException
   */
  public function fullResource($name, $controller, array $options = [])
  {
    return $this->resource($name, $controller, array_merge($options, ['withMultiple' => true, 'withSoftDeletes' => true]));
  }

  /**
   * Route a resource to a controller.
   *
   * @param string $name
   * @param string $controller
   * @param array $options
   * @return PendingResourceRegistration
   * @throws BindingResolutionException
   */
  public function resource($name, $controller, array $options = [])
  {
    if ($this->container && $this->container->bound(ResourceRegistrar::class)) {
      $registrar = $this->container->make(ResourceRegistrar::class);
    } else {
      $registrar = new ResourceRegistrar($this);
    }

    return new PendingResourceRegistration(
      $registrar, $name, $controller, $options
    );
  }
}
