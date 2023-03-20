<?php

namespace Savks\InkyMailer\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Filesystem\Filesystem;

class PublishService extends Command
{
    protected $signature = 'inky-mailer:publish:service';

    public function handle(): int
    {
        $defaultConnection = \config('inky-mailer.default_connection');

        $connection = \config("inky-mailer.connections.{$defaultConnection}");

        $serviceFileName = \config('inky-mailer.service.file_name');

        $destDir = env('HOME') . '/.config/systemd/user';
        $dest = "{$destDir}/{$serviceFileName}.service";

        if (\file_exists($dest)) {
            $this->components->error("Service file <fg=magenta>\"{$dest}\"</> already exists.");

            return self::INVALID;
        }

        if (! \is_dir($destDir) && ! \mkdir($destDir, recursive: true)) {
            $this->components->error("Unable to create directory <fg=magenta>\"{$destDir}\"</>.");

            return self::FAILURE;
        }

        $content = \str_replace(
            [
                '%NAME%',
                '%LISTEN%',
                '%CWD%',
            ],
            [
                \config('inky-mailer.service.name'),
                $defaultConnection === 'tcp' ?
                    "--host={$connection['host']} --port={$connection['port']}" :
                    "--path={$connection['path']}",
                \config('inky-mailer.server_path'),
            ],
            \file_get_contents(
                \dirname(__DIR__, 2) . '/resources/systemd.service'
            )
        );

        (new Filesystem())->appendToFile($dest, $content);

        $this->newLine();

        $this->info("$ systemctl enable --user --now {$serviceFileName}");
        $this->info("$ systemctl status --user {$serviceFileName}");

        return self::SUCCESS;
    }
}
