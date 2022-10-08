<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudflare Turnstile Credentials
    |--------------------------------------------------------------------------
    |
    | You can get a key and secret from your Cloudflare account.
    |
    */

    'key' => env('TURNSTILE_KEY'),
    'secret' => env('TURNSTILE_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Run mode
    |--------------------------------------------------------------------------
    |
    | You have two options:
    |
    |  "middleware"   Automatically checks every web request for a Turnstile
    |                 response code and returns to the previous page (with all
    |                 input) on failure.
    |
    |  "validation"   You must run a request's "cf-turnstile-response" data
    |                 through the provided validation rule during your regular
    |                 form handling.
    |
    | The validation mode is preferable in most cases because it allows you
    | more control over when/how the error message is displayed.
    |
    | Note: The middleware mode only attached itself to the "web" middleware
    |       group, so you will need to use validation mode if you are using
    |       Turnstile under other groups.
    |
    */

    'mode' => 'middleware',

    /*
    |--------------------------------------------------------------------------
    | Send the client IP
    |--------------------------------------------------------------------------
    |
    | Should the client's IP be sent to Cloudflare when testing their Turnstile
    | response code.
    |
    */

    'send_client_ip' => false,

    /*
    |--------------------------------------------------------------------------
    | Log any API errors
    |--------------------------------------------------------------------------
    |
    | Enable this if you would like to log all API responses that are errors.
    |
    | They will be sent to the default log channel as debug messages.
    |
    */

    'log_errors' => false,

];
