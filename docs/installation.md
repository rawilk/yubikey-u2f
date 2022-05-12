---
title: Installation & Setup
sort: 3
---

## Installation

yubikey-u2f can be installed via composer:

```bash
composer require rawilk/yubikey-u2f
```

## Configuration

### Publishing the config file

You may publish the config file like this:

```bash
php artisan vendor:publish --tag="yubikey-u2f-config"
```

See the default configuration values [here](https://github.com/rawilk/yubikey-u2f/blob/main/config/ups.php).

### Configuring the package

You will need to provide your Yubico API credentials for this package to work. You can either enter your credentials in your `.env` file,
or directly in the config file (not recommended).

Add the following to your `.env` file:

```bash
YUBIKEY_CLIENT_ID=your-client-id
YUBIKEY_SECRET_KEY="your-secret-key"
```

> {tip} See the requirements page for generating an API key.

## Migrations

If you plan to use the table and model provided by this package, you will need to publish and run the package's migrations.

```bash
php artisan vendor:publish --tag="yubikey-u2f-migrations"
php artisan migrate
```

## Models

### YubikeyIdentity Model

The package provides a model for representing a tied security key with a user account. You may extend our model or use your own model by specifying it in the configuration.

```php
<?php

return [
    ...

    'database' => [
        ...

        'model' => \Rawilk\Yubikey\Models\YubikeyIdentity::class,
    ],
];
```

> {note} If you use your own model, it must implement the `\Rawilk\Yubikey\Contracts\YubikeyIdentity` interface!

### User Model

To associate and verify security keys with your user model, you need to add the `\Rawilk\Yubikey\Models\HasYubikeys` trait to your user model. Be sure to also run the migrations for this package.

## Translations

When using the provided model from the package, exceptions may be thrown with language lines in them when users try to add the same security key twice to their account. You may publish and modify the language files with this command:

```bash
php artisan vendor:publish --tag="yubikey-u2f-translations"
```
