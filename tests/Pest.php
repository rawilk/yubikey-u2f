<?php

use Illuminate\Support\Facades\Http;
use Rawilk\Yubikey\Enums\YubicoResponseStatus;
use Rawilk\Yubikey\Enums\YubicoTestTokens;
use Rawilk\Yubikey\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

// Helpers
function fakeApiCalls(?YubicoResponseStatus $status = null): void
{
    Http::fake([
        'https://api*' =>
            Http::response(implode("\r\n", yubicoFakeResponse($status))),
    ]);
}

function yubicoFakeResponse(?YubicoResponseStatus $status = YubicoResponseStatus::OK): array
{
    return [
        'h=lvan2kNzPaBviqKNeut89JgnF7c=',
        't=2022-05-11T20:12:15Z0099',
        'otp=' . YubicoTestTokens::REPLAYED_OTP->value,
        'nonce=0efe6fcb181c6bc14b50886a91216949',
        'sl=100',
        'status=' . $status?->value ?? 'some-random-status',
    ];
}
