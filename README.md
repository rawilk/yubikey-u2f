# Yubikey U2F

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rawilk/yubikey-u2f.svg?style=flat-square)](https://packagist.org/packages/rawilk/yubikey-u2f)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/rawilk/yubikey-u2f/run-tests?label=tests)](https://github.com/rawilk/yubikey-u2f/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/rawilk/yubikey-u2f.svg?style=flat-square)](https://packagist.org/packages/rawilk/yubikey-u2f)

---

If you have a YubiKey from [Yubico](https://yubico.com), you can add two-factor support for a security key to your Laravel applications. Your user accounts
will be able to register up to 5 security keys (configurable) to their account, and then use those keys as a form of two-factor authentication for your application.

_Note:_ This package only provides the backend code necessary for verifying and associating keys with users. You will need to the UI necessary for this and also
the logic to your authentication workflows for two-factor authentication.

### Requirements:

-   [Buy a YubiKey](https://www.yubico.com/store/)
-   [Generate an API Key](https://upgrade.yubico.com/getapikey/)

## Installation

You can install the package via composer:

```bash
composer require rawilk/yubikey-u2f
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="yubikey-u2f-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="yubikey-u2f-config"
```

You can view the default configuration here: https://github.com/rawilk/yubikey-u2f/blob/main/config/yubikey-u2f.php

You can publish the language files provided by this package with:

```bash
php artisan vendor:publish --tag="yubikey-u2f-translations"
```

## Usage

First, add the `\Rawilk\Yubikey\Models\HasYubikeys` trait to your user model. Then you can verify/associate a key for a user like this:

```php
// An exception will be thrown if the key is not valid.
$response = \Rawilk\Yubikey\Facades\Yubikey::verify(request()->otp);

Auth::user()->associateYubikeyIdentity($response['identity']);

// On a login 2fa request, you can verify the key is valid and tied to the user like this:
$user->verifyYubikeyIdentity(request()->otp);
```

_Note:_ `request()->otp` is just an example of retrieving the input sent to the server containing the security key signature that is generated
when touching the security key. Make sure to adjust accordingly depending on how you capture that.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security

Please review [my security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

-   [Randall Wilk](https://github.com/rawilk)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
