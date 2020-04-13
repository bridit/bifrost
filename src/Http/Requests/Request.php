<?php

namespace Bifrost\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as IlluminateRequest;

class Request extends \Illuminate\Foundation\Http\FormRequest
{

  /**
   * @var null|string
   */
  protected ?string $namespace;

  /**
   * @var string
   */
  protected string $component = '';

  /**
   * There's a permission that allow all methods?
   *
   * @var null|string
   */
  protected ?string $generalPermissionName;

  /**
   * Are guests allowed to call this method?
   *
   * @var array
   */
  protected array $guestsAllowed = [];

  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    $action = IlluminateRequest::route()->getActionMethod();

    if (Auth::guest()) {
      return in_array($action, $this->guestsAllowed);
    }

    $permissionMethod = str_replace(
      ['index', 'show', 'store', 'edit', 'destroy'],
      ['list', 'view', 'create', 'update', 'delete'],
      $action
    );

    $permission = !blank($this->namespace)
      ? "$this->namespace:$this->component.$permissionMethod"
      : "$this->component.$permissionMethod";

    $user = Auth::user();

    return $user->isSuperAdmin() || $user->can($permission) || (!blank($this->generalPermissionName) && $user->can($this->generalPermissionName));
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    $action = IlluminateRequest::route()->getActionMethod();

    return method_exists($this, $action) && is_callable([$this, $action]) ? $this->{$action}() : [];
  }

}
