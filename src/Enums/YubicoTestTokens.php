<?php

namespace Rawilk\Yubikey\Enums;

/**
 * This enum contains OTP tokens to use for testing.
 * The tokens used here are from:
 * https://github.com/Yubico/php-yubico/blob/master/tests/yubico_test.php
 */
enum YubicoTestTokens: string
{
    case BAD_OTP = 'vvincrediblegfnchniugtdcbrleehenethrlbihdijc';
    case REPLAYED_OTP = 'vvincrediblegfnchniugtdcbrleehenethrlbihdijv';
}
