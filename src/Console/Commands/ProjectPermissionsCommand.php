<?php

namespace Bifrost\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ProjectPermissionsCommand extends Command
{

  /**
   * @inheritdoc
   */
  protected $signature = 'bifrost:project-permissions {user} {group=www-data}';

  /**
   * @inheritdoc
   */
  protected $description = 'Fix project permissions';

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {

    /** --------------------------------------------------------------------
     *                               Chown
     *  -------------------------------------------------------------------- */
    $this->info('Setting project owner...');
    $this->executeCommand('chown -R ' . $this->argument('user') . ':' . $this->argument('group') . ' ' . base_path());

    /** --------------------------------------------------------------------
     *                             Files 664
     *  -------------------------------------------------------------------- */
    $this->info('Setting general file permissions...');
    $this->executeCommand('find ' . base_path() . ' -type f -exec chmod 664 {} \;');

    /** --------------------------------------------------------------------
     *                            Folders 775
     *  -------------------------------------------------------------------- */
    $this->info('Setting general folder permissions...');
    $this->executeCommand('find ' . base_path() . ' -type d -exec chmod 775 {} \;');

    /** --------------------------------------------------------------------
     *                          Storage and Cache
     *  -------------------------------------------------------------------- */
    $this->info('Setting "bootstrap/cache" and "storage" group and permissions...');
    $this->executeCommand('chgrp -R ' . $this->argument('group') . ' storage bootstrap/cache');
    $this->executeCommand('chmod -R ug+rwx storage bootstrap/cache');

  }

  /**
   * @param string $command
   * @return void
   */
  protected function executeCommand(string $command): void
  {
    $process = Process::fromShellCommandline($command, base_path());
    $process->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }
  }
}
