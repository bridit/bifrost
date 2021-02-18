<?php

namespace Bifrost\Authorization;

use Illuminate\Auth\Access\HandlesAuthorization;

class Policy
{
  use HandlesAuthorization;

  public function before($user, $ability)
  {
    if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
      return true;
    }
  }

}
