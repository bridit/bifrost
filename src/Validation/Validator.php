<?php

namespace Bifrost\Validation;

class Validator extends \Illuminate\Validation\Validator
{

  /**
   * The data under validation.
   *
   * @var array
   */
  protected $titles = [];

  /**
   * @return array
   */
  public function getTitles(): array
  {
    return $this->titles;
  }

  /**
   * @param array $titles
   * @return $this
   */
  public function setTitles(array $titles): self
  {
    $this->titles = $titles;

    return $this;
  }

}
