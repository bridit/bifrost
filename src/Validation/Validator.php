<?php

namespace Bifrost\Validation;

use Illuminate\Support\Facades\App;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Validation\DatabasePresenceVerifier;

class Validator extends \Illuminate\Validation\Validator
{

  /**
   * The data under validation.
   *
   * @var array
   */
  protected $titles = [];

  public function __construct(Translator $translator, array $data, array $rules, array $messages = [], array $customAttributes = [])
  {
    parent::__construct($translator, $data, $rules, $messages, $customAttributes);

    $this->setPresenceVerifier(App::make(DatabasePresenceVerifier::class));
  }

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
