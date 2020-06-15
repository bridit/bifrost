<?php

namespace Bifrost\Rules;

use Bifrost\Repositories\EntityRepository;
use Illuminate\Contracts\Validation\Rule as BaseRule;

abstract class Rule implements BaseRule
{
    protected ?EntityRepository $repository;

    public function __construct(?EntityRepository $repository = null)
    {
        $this->repository = $repository;
    }
}