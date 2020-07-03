<?php

namespace Bifrost\Entities\States;

use Bifrost\Entities\Model;

abstract class State
{
  /**
   *
   * Get database value for the specific state
   *
   * @return string
   */
  public abstract static function getStatusTypeId(): string;

  /**
   *
   * Get State class using a StatusTypeId
   *
   * @param string $statusTypeId
   * @param Model $model
   * @return State
   */
  public abstract static function getStateByStatusTypeId(string $statusTypeId, Model $model): State;

}
