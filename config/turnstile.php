<?php

return [
    'key' => env('TURNSTILE_KEY'),
    'secret' => env('TURNSTILE_SECRET'),
    'mode' => 'middleware',
    'send_client_ip' => false,
    'log_errors' => false,
];
