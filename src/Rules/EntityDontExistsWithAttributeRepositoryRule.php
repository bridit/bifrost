<?php

namespace Bifrost\Rules;

use Bifrost\Repositories\EntityRepository;

abstract class EntityDontExistsWithAttributeRepositoryRule extends RepositoryRule
{
    protected string $entityName = 'Entity';

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
        return $this->entityName . ': ' . ucfirst(':attribute') . ' \':value\' already exists.';
    }
}