<?php

namespace Bifrost\Models;

interface IModel
{

  /**
   * Get the table associated with the model.
   *
   * @return string
   */
  public function getTable();

  /**
   * Get the primary key for the model.
   *
   * @return string
   */
  public function getKeyName();

}
