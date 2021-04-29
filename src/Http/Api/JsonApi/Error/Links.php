<?php

namespace Bifrost\Http\Api\JsonApi\Error;

class Links
{
  use \Bifrost\Support\Concerns\Arrayable;

  /**
   * A link that leads to further details about this particular occurrence of
   * the problem.
   *
   * @var string
   */
  protected string $about;

  /**
   * @return string
   */
  public function getAbout(): string
  {
    return $this->about;
  }

  /**
   * @param string $about
   */
  public function setAbout(string $about): void
  {
    $this->about = $about;
  }

}
