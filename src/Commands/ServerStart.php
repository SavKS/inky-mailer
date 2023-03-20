<?php

namespace Savks\InkyMailer\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ServerStart extends Command
{
    protected $signature = 'inky-mailer:server:start';

    public function handle(): int
    {
        $serverPath = \config('inky-mailer.server_path');

        if (! \is_dir($serverPath)) {
            $this->components->error("Directory with server <fg=magenta>\"{$serverPath}\"</> not exists.");

            return self::INVALID;
        }

        $defaultConnection = \config('inky-mailer.default_connection');

        $connection = \config("inky-mailer.connections.{$defaultConnection}");

        $process = new Process([
            'yarn',
            'start',
            ...($defaultConnection === 'tcp' ?
                ["--host={$connection['host']}", "--port={$connection['port']}"] :
                ["--path={$connection['path']}"]),
        ], $serverPath);

        $process->setTty(true);

        $process->setTimeout(0);
        $process->setIdleTimeout(0);

        $process->run();

        return self::SUCCESS;
    }
}
