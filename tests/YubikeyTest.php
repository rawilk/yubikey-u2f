<?php

use Illuminate\Support\Facades\Http;
use Rawilk\Yubikey\Enums\YubicoResponseStatus;
use Rawilk\Yubikey\Enums\YubicoTestTokens;
use Rawilk\Yubikey\Exceptions\YubikeyVerifyException;
use Rawilk\Yubikey\Facades\Yubikey;

// At this time, we're not really sure how to generate a valid OTP
// token to test this, but we can replay a known valid token
// which will result in a `REPLAYED_OTP` response, but we
// will know the code is working if that response is returned.
it('can verify otp codes', function () {
    $this->assertThrows(
        fn () => Yubikey::verify(YubicoTestTokens::REPLAYED_OTP->value),
        YubikeyVerifyException::class,
        YubikeyVerifyException::replayedOtp()->getMessage(),
    );
});

test('bad otp codes are rejected', function () {
    $this->assertThrows(
        fn () => Yubikey::verify(YubicoTestTokens::BAD_OTP->value),
        YubikeyVerifyException::class,
        YubikeyVerifyException::badOtp()->getMessage(),
    );
});

it('returns the identity for a valid otp token', function () {
    fakeApiCalls(YubicoResponseStatus::OK);

    $response = Yubikey::noVerifySignature()->verify(YubicoTestTokens::REPLAYED_OTP->value);

    expect($response)->toBeArray()->toHaveKey('identity');

    $expectedIdentity = Yubikey::getOtpIdentity(YubicoTestTokens::REPLAYED_OTP->value);

    expect($response['identity'])->toEqual($expectedIdentity);
});

it('throws an exception for unknown statuses', function () {
    fakeApiCalls();

    $this->assertThrows(
        fn () => Yubikey::noVerifySignature()->verify(YubicoTestTokens::REPLAYED_OTP->value),
        YubikeyVerifyException::class,
        YubikeyVerifyException::unknownStatus()->getMessage(),
    );
});

it('throws the appropriate exceptions', function (YubicoResponseStatus $status, string $otp, string $expectedMessage, ?string $configuredClientId = null) {
    if ($configuredClientId) {
        config([
            'yubikey-u2f.client_id' => $configuredClientId,
        ]);
    }

    fakeApiCalls($status);

    $this->assertThrows(
        fn () => Yubikey::noVerifySignature()->verify($otp),
        YubikeyVerifyException::class,
        $expectedMessage,
    );
})->with([
    // Yubico failed to verify the HMAC signature.
    [YubicoResponseStatus::BAD_SIGNATURE, YubicoTestTokens::REPLAYED_OTP->value, YubikeyVerifyException::badSignature()->getMessage()],

    // A required request parameter is missing (should never happen though).
    [YubicoResponseStatus::MISSING_PARAMETER, YubicoTestTokens::REPLAYED_OTP->value, YubikeyVerifyException::missingParameter()->getMessage()],

    // The configured api key is not allowed ot make requests.
    [YubicoResponseStatus::OPERATION_NOT_ALLOWED, YubicoTestTokens::REPLAYED_OTP->value, YubikeyVerifyException::operationNotAllowed()->getMessage()],

    // 500 error on a Yubico server.
    [YubicoResponseStatus::BACKEND_ERROR, YubicoTestTokens::REPLAYED_OTP->value, YubikeyVerifyException::backendError()->getMessage()],

    // Yubico server could not get the requested number of syncs before timeout.
    [YubicoResponseStatus::NOT_ENOUGH_ANSWERS, YubicoTestTokens::REPLAYED_OTP->value, YubikeyVerifyException::notEnoughAnswers()->getMessage()],

    // The Yubico server has already seen the OTP/Nonce combination before.
    [YubicoResponseStatus::REPLAYED_REQUEST, YubicoTestTokens::REPLAYED_OTP->value, YubikeyVerifyException::replayedRequest()->getMessage()],

    // The configured API key does not exist with Yubico.
    [YubicoResponseStatus::NO_SUCH_CLIENT, YubicoTestTokens::REPLAYED_OTP->value, YubikeyVerifyException::noSuchClient('1234')->getMessage(), '1234'],
]);

it('requires a client id', function () {
    config([
        'yubikey-u2f.client_id' => '',
    ]);

    $this->assertThrows(
        fn () => Yubikey::verify(YubicoTestTokens::REPLAYED_OTP->value),
        YubikeyVerifyException::class,
        YubikeyVerifyException::missingClientId()->getMessage(),
    );
});

it('requires a client secret', function () {
    config([
        'yubikey-u2f.secret_key' => '',
    ]);

    $this->assertThrows(
        fn () => Yubikey::verify(YubicoTestTokens::REPLAYED_OTP->value),
        YubikeyVerifyException::class,
        YubikeyVerifyException::missingClientSecret()->getMessage(),
    );
});

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
