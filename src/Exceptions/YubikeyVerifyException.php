<?php

namespace Rawilk\Yubikey\Exceptions;

use Exception;

class YubikeyVerifyException extends Exception
{
    public static function backendError(): self
    {
        return new static('An unexpected error occurred with a Yubico server.');
    }

    public static function badOtp(): self
    {
        return new static('The OTP is an invalid format.');
    }

    public static function badSignature(): self
    {
        return new static('The signature verification failed from the server.');
    }

    public static function missingParameter(): self
    {
        return new static('The request is missing a required parameter.');
    }

    public static function noSuchClient(string $clientId): self
    {
        return new static(
            "Yubico could not validate the client id `{$clientId}`. Make sure you sign up for an api key here: https://upgrade.yubico.com/getapikey/"
        );
    }

    public static function notEnoughAnswers(): self
    {
        return new static('The Yubico server could not get the requested number of syncs before timeout.');
    }

    public static function operationNotAllowed(): self
    {
        return new static('Your api key is not allowed to verify OTPs.');
    }

    public static function replayedOtp(): self
    {
        return new static('The OTP has already been seen by the service.');
    }

    public static function invalidSignature(): self
    {
        return new static('An invalid signature was returned from the server.');
    }

    public static function replayedRequest(): self
    {
        return new static('The Yubico server has seen the OTP/Nonce combination before.');
    }

    public static function unknownStatus(): self
    {
        return new static('An unknown status was returned from the server.');
    }

    public static function missingClientId(): self
    {
        return new static('A client id must be provided to verify a YubiKey.');
    }

    public static function missingClientSecret(): self
    {
        return new static('A secret key must be provided to verify a YubiKey.');
    }
}
