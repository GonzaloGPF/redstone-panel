<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Driver
    |--------------------------------------------------------------------------
    |
    | "bedrock" talks to the real server, and "fake" gives believable data without touching any server,
    | for the tests and a public demo.
    |
    */

    'driver' => env('MINECRAFT_DRIVER', 'fake'),

    /*
    |--------------------------------------------------------------------------
    | Server
    |--------------------------------------------------------------------------
    |
    | Where the Bedrock server listens (UDP), and how long to wait for its answer, in seconds.
    |
    */

    'host' => env('MINECRAFT_HOST', '127.0.0.1'),

    'port' => (int) env('MINECRAFT_PORT', 19132),

    'timeout' => (float) env('MINECRAFT_TIMEOUT', 1.5),

    /*
    |--------------------------------------------------------------------------
    | Status cache
    |--------------------------------------------------------------------------
    |
    | Seconds the status is kept before the server is asked again, however many pages are open.
    |
    */

    'status_ttl' => (int) env('MINECRAFT_STATUS_TTL', 15),

];
