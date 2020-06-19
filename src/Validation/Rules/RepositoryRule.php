<?php

namespace Bifrost\Validation\Rules;

use Bifrost\Repositories\EntityRepository;

abstract class RepositoryRule extends Rule
{
  protected ?EntityRepository $repository;

  public function __construct(?EntityRepository $repository = null)
  {
    $this->repository = $repository;
  }
}
