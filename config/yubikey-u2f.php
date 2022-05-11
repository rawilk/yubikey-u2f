<?php

return [
    'client_id' => env('YUBIKEY_CLIENT_ID'),

    'secret_key' => env('YUBIKEY_SECRET_KEY'),

    'urls' => [
        'api.yubico.com/wsapi/2.0/verify',
        'api2.yubico.com/wsapi/2.0/verify',
        'api3.yubico.com/wsapi/2.0/verify',
        'api4.yubico.com/wsapi/2.0/verify',
        'api5.yubico.com/wsapi/2.0/verify',
    ],
];
