<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Connection settings
    |--------------------------------------------------------------------------
    |
    | Point the url to the Zaak- en Documentservices environment which you want to use.
    |
    */
    'url'               => env('ZAAKDMS_URL'),
    'webview_url'       => env('ZAAKDMS_WEBVIEW_URL'),
    'ping_failure_notification' => env('ZAAKDMS_PING_FAILURE_NOTIFICATION', false),

    // TLS settings for the connection. There are no default paths: set these in the
    // environment. 'cert' and 'cert_key' are the client certificate and its private key,
    // 'verify' is the CA bundle used to verify the server (or true/false).
    // see https://stackoverflow.com/questions/7147988/creating-a-php-soap-request-with-a-certificate
    'cert'              => env('ESB__SEND_CERT'),
    'cert_key'          => env('ESB_SEND_PK'),

    'verify'            => env('ESB_VERIFY'),
    'log_file_requests' => env('ZAAKDMS_LOG_FILE_REQUESTS', false),

    // sender information
    'sender'            => [
        'application'       => env('ZAAKDMS_SENDER_APPLICATION'),
        'administrative'    => env('ZAAKDMS_SENDER_ADMINISTRATIVE'),
        'organization'      => env('ZAAKDMS_SENDER_ORGANISATION'),
        'user'              => env('ZAAKDMS_SENDER_USER', 'Systeemgebruiker'),
    ],
    'receiver'            => [
        'application'       => env('ZAAKDMS_SENDER_APPLICATION'),
        'administrative'    => env('ZAAKDMS_SENDER_ADMINISTRATIVE'),
        'organization'      => env('ZAAKDMS_SENDER_ORGANISATION'),
        'user'              => env('ZAAKDMS_SENDER_USER', 'Systeemgebruiker'),
    ],

    'date_format'        => env('ZAAKDMS_DATE_FORMAT', 'Ymd'), 
    'cache'              => [
        'default'        => false,
        'time'           => env('ZAAKDMS_CACHE_TIME')
    ]
];
