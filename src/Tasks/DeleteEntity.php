<?php

namespace Bifrost\Tasks;

use Bridit\Laravel\Tasks\QueueableTask;

class DeleteEntity extends QueueableTask
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