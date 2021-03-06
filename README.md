# Yubikey U2F

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rawilk/yubikey-u2f.svg?style=flat-square)](https://packagist.org/packages/rawilk/yubikey-u2f)
![Tests](https://github.com/rawilk/yubikey-u2f/workflows/Tests/badge.svg?style=flat-square)
[![Total Downloads](https://img.shields.io/packagist/dt/rawilk/yubikey-u2f.svg?style=flat-square)](https://packagist.org/packages/rawilk/yubikey-u2f)


![social image](https://banners.beyondco.de/yubikey-u2f.png?theme=light&packageManager=composer+require&packageName=rawilk%2Fyubikey-u2f&pattern=parkayFloor&style=style_1&description=Add+Yubikey+U2F+authentication+to+Laravel.&md=1&showWatermark=0&fontSize=100px&images=finger-print)

**Important Note:** This package is not a webauthn package. This will only work using the OTP codes that are generated from the YubiKey device. This was also mostly just a learning package for me, and probably shouldn't actually be used in production. If you want to support security keys and other devices, consider using webauthn instead.

If you have a YubiKey from [Yubico](https://yubico.com), you can add two-factor support for a security key to your Laravel applications. Your user accounts
will be able to register up to 5 security keys (configurable) to their account, and then use those keys as a form of two-factor authentication for your application.

**Note:** This package only provides the backend code necessary for verifying and associating keys with users. You will need to make the UI necessary for this and also add the logic to your authentication workflows for two-factor authentication.

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

**Note:** `request()->otp` is just an example of retrieving the input sent to the server containing the security key signature that is generated
when touching the security key. Make sure to adjust accordingly depending on how you capture that.

## Documentation
For more documentation, please visit: https://randallwilk.dev/docs/yubikey-u2f

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

Inspiration for this package comes from:

-   [marcinkozak/yubikey](https://github.com/MarcinKozak/Yubikey)
-   [Yubico/php-yubico](https://github.com/Yubico/php-yubico)

## Alternatives

I've created this package since any existing solutions are either archived or not actively maintained anymore.
If you have an alternative to this package, please feel free to PR an update the README with your package on it.

-   [marcinkozak/yubikey](https://github.com/MarcinKozak/Yubikey)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
