<?php

return [
    'default_connection' => env('INKY_MAILER_CONNECTION', 'tcp'),
    'server_path' => env('INKY_MAILER_SERVER_PATH'),
    'service' => [
        'name' => env('INKY_MAILER_SERVICE_NAME', 'Inky render server'),
        'file_name' => env('INKY_MAILER_SERVICE_FILE_NAME', 'inky-render-server'),
    ],
    'connections' => [
        'unix' => [
            'path' => env('INKY_MAILER_UNIX_PATH'),
        ],

        'tcp' => [
            'host' => env('INKY_MAILER_TCP_HOST'),
            'port' => env('INKY_MAILER_TCP_PORT'),
        ],
    ],

    'render_options' => [
        'inline_css' => env('INKY_MAILER_RENDER_OPTS_INLINE_CSS', false),
        'minify' => env('INKY_MAILER_RENDER_OPTS_MINIFY', false),
    ],
];
