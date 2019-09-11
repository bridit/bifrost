<?php

namespace Bifrost\Tasks;

class DeleteEntity
{

  /**
   * @param string $entityClassName
   * @param $id
   * @return int
   */
  public static function execute(string $entityClassName, $id): int
  {
    return $entityClassName::destroy($id);
  }

}