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
    | Database
    |--------------------------------------------------------------------------
    |
    | Define the name of the table the package will use to store security key
    | identities to bind with user accounts.
    |
    */
    'database' => [
        'table' => 'yubikey_identities',

        /*
         * You may either extend our model or use your own model
         * to represent YubiKey security key identities associated
         * with user accounts.
         *
         * If you use your own model, it must implement the
         * \Rawilk\Yubikey\Contracts\YubikeyIdentity interface.
         */
        'model' => \Rawilk\Yubikey\Models\YubikeyIdentity::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Max Security Keys
    |--------------------------------------------------------------------------
    |
    | You may restrict the amount of security keys a user may add to their
    | account. Most websites limit to 5 keys.
    |
    | Set value to `null` for unlimited keys per user.
    |
    */
    'max_keys_per_user' => 5,

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
