<?php

namespace Bifrost\Routing;

class PendingResourceRegistration extends \Illuminate\Routing\PendingResourceRegistration
{

  /**
   * Tell the resource to include softDelete routes.
   *
   * @return PendingResourceRegistration
   */
  public function withSoftDeletes()
  {
    $this->options['withSoftDeletes'] = true;

    return $this;
  }

  /**
   * Tell the resource to include multiple (delete and restore) routes.
   *
   * @return PendingResourceRegistration
   */
  public function withMultiple()
  {
    $this->options['withMultiple'] = true;

    return $this;
  }

}
