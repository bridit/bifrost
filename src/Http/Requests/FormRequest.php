<?php

namespace Bifrost\Http\Requests;

use Illuminate\Support\Facades\Request;
use Illuminate\Contracts\Validation\Validator;
use Bifrost\Http\Api\JsonApi\Error\ValidationException;

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

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function getTitles()
  {
    $action = Request::route()->getActionMethod();
    $method = $action . 'Titles';

    if (method_exists($this, $method) && is_callable([$this, $method])) {
      return $this->{$method}();
    }

    if (method_exists($this, 'titles') && is_callable([$this, 'titles'])) {
      return $this->titles();
    }

    return [];
  }

  /**
   * Get the validator instance for the request.
   *
   * @return Validator
   */
  protected function getValidatorInstance()
  {
    parent::getValidatorInstance();

    if (method_exists($this->validator, 'setTitles') && is_callable([$this->validator, 'setTitles'])) {
      $this->validator->setTitles($this->getTitles());
    }

    return $this->validator;
  }

}
