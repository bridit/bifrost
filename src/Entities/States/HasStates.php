<?php

namespace Bifrost\Entities\States;

/**
 * Trait HasStates
 * @package Bifrost\Entities\States
 *
 * @property-read string $status_type_id
 */
trait HasStates
{

  protected State $state;

  /**
   *
   * Get Base State Class for the entity
   *
   * @return mixed
   */
  protected abstract function getBaseStateClass();


  /**
   *
   * Case not instantiated, Hydrate state class based on statusTypeId
   *
   * @return State|null
   */
  public function getState(): ?State
  {

    if(blank($this->status_type_id))
      return null;

    if(!blank($this->state))
      return $this->state;

    return $this->getBaseStateClass()::getStateByStatusTypeId($this->status_type_id, $this);
  }

  public function setState(State $state)
  {
    $this->state = $state;
    $this->status_type_id = $state::getStatusTypeId();
  }
}
