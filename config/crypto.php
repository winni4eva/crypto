<?php

return [
    'token' => env('BITGO_TOKEN'),
    'host' => env('BITGO_EXPRESS_HOST', 'https://localhost'),
    'port' => env('BITGO_EXPRESS_PORT', '3080'),
    'ip' => env('BITGO_ALLOWED_IP_ADDRESS'),
    'bitgoApiVersion' => '/api/v2',
    'currency' => '',
    'walletId' => '',
];
