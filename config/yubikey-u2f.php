<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Credentials
    |--------------------------------------------------------------------------
    |
    | To start verifying YubiKeys, you will need a client id and a secret key
    | for the API. You can generate these credentials at:
    | https://upgrade.yubico.com/getapikey/
    |
    | Note: You will need a YubiKey to generate the credentials.
    |
    */
    'client_id' => env('YUBIKEY_CLIENT_ID'),

    'secret_key' => env('YUBIKEY_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Verify URLs
    |--------------------------------------------------------------------------
    |
    | A GET request will be sent (in parallel) to each of the specified
    | URLs to verify the signature of a YubiKey. This is an advanced
    | setting and should not be modified in most cases.
    |
    */
    'urls' => [
        'https://api.yubico.com/wsapi/2.0/verify',
        'https://api2.yubico.com/wsapi/2.0/verify',
        'https://api3.yubico.com/wsapi/2.0/verify',
        'https://api4.yubico.com/wsapi/2.0/verify',
        'https://api5.yubico.com/wsapi/2.0/verify',
    ],
];
