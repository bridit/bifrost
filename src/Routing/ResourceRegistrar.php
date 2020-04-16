<?php

namespace Bifrost\Routing;

use Illuminate\Routing\Route;

class ResourceRegistrar extends \Illuminate\Routing\ResourceRegistrar
{
  /**
   * The router instance.
   *
   * @var Router
   */
  protected $router;

  /**
   * The verbs used in the resource URIs.
   *
   * @var array
   */
  protected static $verbs = [
    'create' => 'create',
    'edit' => 'edit',
    'destroy' => 'destroy',
    'destroyMultiple' => 'destroyMultiple',
    'trashed' => 'trashed',
    'trash' => 'trash',
    'trashMultiple' => 'trashMultiple',
    'untrash' => 'untrash',
    'untrashMultiple' => 'untrashMultiple',
  ];

  /**
   * Get the applicable resource methods.
   *
   * @param array $defaults
   * @param array $options
   * @return array
   */
  protected function getResourceMethods($defaults, $options)
  {
    return parent::getResourceMethods($this->router->getResourceMethodsDefaults($defaults, $options), $options);
  }

  /**
   * Add the trashed method for a resourceful route.
   *
   * @param string $name
   * @param string $base
   * @param string $controller
   * @param array $options
   * @return Route
   */
  protected function addResourceDestroyMultiple($name, $base, $controller, $options)
  {
    $uri = $this->getResourceUri($name);

    $action = $this->getResourceAction($name, $controller, 'destroyMultiple', $options);

    return $this->router->delete($uri, $action);
  }

  /**
   * Add the trashed method for a resourceful route.
   *
   * @param string $name
   * @param string $base
   * @param string $controller
   * @param array $options
   * @return Route
   */
  protected function addResourceTrashed($name, $base, $controller, $options)
  {
    $uri = $this->getResourceUri($name) . '/' . static::$verbs['trashed'];

    $action = $this->getResourceAction($name, $controller, 'trashed', $options);

    return $this->router->get($uri, $action);
  }

  /**
   * Add the restore method for a resourceful route.
   *
   * @param string $name
   * @param string $base
   * @param string $controller
   * @param array $options
   * @return Route
   */
  protected function addResourceTrash($name, $base, $controller, $options)
  {
    $uri = $this->getResourceUri($name) . '/{' . $base . '}/' . static::$verbs['trash'];

    $action = $this->getResourceAction($name, $controller, 'trash', $options);

    return $this->router->put($uri, $action);
  }

  /**
   * Add the restore method for a resourceful route.
   *
   * @param string $name
   * @param string $base
   * @param string $controller
   * @param array $options
   * @return Route
   */
  protected function addResourceTrashMultiple($name, $base, $controller, $options)
  {
    $uri = $this->getResourceUri($name) . '/' . static::$verbs['trashMultiple'];

    $action = $this->getResourceAction($name, $controller, 'trashMultiple', $options);

    return $this->router->put($uri, $action);
  }

  /**
   * Add the restore method for a resourceful route.
   *
   * @param string $name
   * @param string $base
   * @param string $controller
   * @param array $options
   * @return Route
   */
  protected function addResourceUntrash($name, $base, $controller, $options)
  {
    $uri = $this->getResourceUri($name) . '/{' . $base . '}/' . static::$verbs['untrash'];

    $action = $this->getResourceAction($name, $controller, 'untrash', $options);

    return $this->router->put($uri, $action);
  }

  /**
   * Add the restore method for a resourceful route.
   *
   * @param string $name
   * @param string $base
   * @param string $controller
   * @param array $options
   * @return Route
   */
  protected function addResourceUntrashMultiple($name, $base, $controller, $options)
  {
    $uri = $this->getResourceUri($name) . '/' . static::$verbs['untrashMultiple'];

    $action = $this->getResourceAction($name, $controller, 'untrashMultiple', $options);

    return $this->router->put($uri, $action);
  }
}
