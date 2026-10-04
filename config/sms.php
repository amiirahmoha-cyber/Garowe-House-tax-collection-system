<?php

/**
 * GHTCS SMS Gateway Configuration
 * Values are pulled from .env — see .env.example for setup instructions.
 */

return [
    'enabled'   => env('SMS_ENABLED', false),
    'url'       => env('SMS_API_URL', ''),
    'username'  => env('SMS_API_USERNAME', ''),
    'password'  => env('SMS_API_PASSWORD', ''),
    'sender_id' => env('SMS_SENDER_ID', 'GHTCS'),
];
