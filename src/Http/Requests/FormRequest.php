<?php

namespace Bifrost\Http\Requests;

use Illuminate\Support\Facades\Request;

class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    $action = Request::route()->getActionMethod();

    return method_exists($this, $action) && is_callable([$this, $action]) ? $this->{$action}() : [];
  }

}
