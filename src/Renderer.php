<?php

namespace Savks\InkyMailer;

use GuzzleHttp\{
    Exception\GuzzleException,
    Client,
    RequestOptions
};

class Renderer
{
    public function __construct(protected readonly string $html)
    {
    }

    /**
     * @throws GuzzleException
     */
    public function render(): string
    {
        $defaultConnection = \config('inky-mailer.default_connection');
        $connection = \config("inky-mailer.connections.{$defaultConnection}");

        if ($defaultConnection === 'tcp') {
            $client = new Client([
                'base_uri' => "http://{$connection['host']}:{$connection['port']}",
            ]);
        } else {
            if (! \file_exists($connection['path'])) {
                throw new \RuntimeException(
                    "Inky render server socket file not found: {$connection['path']}"
                );
            }

            $client = new Client([
                'base_uri' => 'http://host/',
                'curl' => [
                    \CURLOPT_UNIX_SOCKET_PATH => $connection['path'],
                ],
            ]);
        }

        $response = $client->post('render', [
            RequestOptions::JSON => [
                'html' => $this->html,
                'url' => \url('/'),
                'options' => [
                    'inlineCss' => \config('inky-mailer.render_options.inline_css', false),
                    'minify' => \config('inky-mailer.render_options.minify', false),
                ],
            ],
        ]);

        $data = \json_decode(
            $response->getBody()->getContents(),
            true
        );

        return html_entity_decode($data['html']);
    }
}
