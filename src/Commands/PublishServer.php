<?php

namespace Savks\InkyMailer\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Filesystem\Filesystem;

class PublishServer extends Command
{
    protected $signature = 'inky-mailer:publish:server';

    public function handle(): int
    {
        $dest = \config('inky-mailer.server_path');

        if (\is_dir($dest)) {
            $this->components->error("Directory <fg=magenta>\"{$dest}\"</> already exists.");

            return self::INVALID;
        }

        $parentDir = \dirname($dest);

        if ($parentDir !== '/' && ! \is_dir($parentDir)) {
            $this->components->error("Parent directory <fg=magenta>\"{$parentDir}\"</> not exists.");

            return self::INVALID;
        }

        $filesystem = new Filesystem();

        $filesystem->mirror(
            \dirname(__DIR__, 2) . '/resources/server',
            $dest
        );

        $this->newLine();

        $this->info("$ cd {$dest} && yarn");

        return self::SUCCESS;
    }
}
