# Yubikey U2F

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rawilk/yubikey-u2f.svg?style=flat-square)](https://packagist.org/packages/rawilk/yubikey-u2f)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/rawilk/yubikey-u2f/run-tests?label=tests)](https://github.com/rawilk/yubikey-u2f/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/rawilk/yubikey-u2f.svg?style=flat-square)](https://packagist.org/packages/rawilk/yubikey-u2f)

---

If you have a YubiKey from [Yubico](https://yubico.com), you can add two-factor support for a security key to your Laravel applications.

### Requirements:

- [Buy a YubiKey](https://www.yubico.com/store/)
- [Generate an API Key](https://upgrade.yubico.com/getapikey/)

## Installation

You can install the package via composer:

```bash
composer require rawilk/yubikey-u2f
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Rawilk\Yubikey\YubikeyServiceProvider" --tag="yubikey-u2f-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="Rawilk\Yubikey\YubikeyServiceProvider" --tag="yubikey-u2f-config"
```

You can view the default configuration here: https://github.com/rawilk/yubikey-u2f/blob/main/config/yubikey-u2f.php

## Usage

``` php
$yubikey-u2f = new Rawilk\Yubikey;
echo $yubikey-u2f->echoPhrase('Hello, Rawilk!');
```

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security

Please review [my security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Randall Wilk](https://github.com/rawilk)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
