<?php

namespace Bifrost\Validation\Rules;

use Bifrost\Repositories\EntityRepository;

abstract class EntityDontExistsWithAttributeRepositoryRule extends RepositoryRule
{

  /**
   * @inheritDoc
   */
  public function passes($attribute, $value)
  {
    return blank($this->repository->findOneBy([$attribute => $value]));
  }

  /**
   * @inheritDoc
   */
  public function message()
  {
    return $this->repository->getEntityClassName() . ' with given :attribute already exists.';
  }
}
