<?php

namespace Savks\InkyMailer\Providers;

use Illuminate\Support\ServiceProvider;

use Savks\InkyMailer\Commands\{
    PublishServer,
    PublishService,
    ServerStart
};

class InkyMailerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            PublishServer::class,
            PublishService::class,
            ServerStart::class,
        ]);
    }

    public function boot(): void
    {
        $configFilepath = \dirname(__DIR__, 2) . '/resources/configs/inky-mailer.php';

        $this->publishes([
            $configFilepath => \config_path('inky-mailer'),
        ]);

        $this->mergeConfigFrom($configFilepath, 'inky-mailer');
    }
}
