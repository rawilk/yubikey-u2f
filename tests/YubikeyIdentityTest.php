<?php

use Rawilk\Yubikey\Enums\YubicoResponseStatus;
use Rawilk\Yubikey\Enums\YubicoTestTokens;
use Rawilk\Yubikey\Exceptions\YubikeyIdentityException;
use Rawilk\Yubikey\Facades\Yubikey;
use Rawilk\Yubikey\Models\YubikeyIdentity;
use Rawilk\Yubikey\Tests\Models\User;

it('can associate a security key with a user', function () {
    $user = User::factory()->create();
    $user->associateYubikeyIdentity(YubicoTestTokens::OTP_IDENTITY->value);

    $this->assertDatabaseHas(
        YubikeyIdentity::class,
        [
            'user_id' => $user->getKey(),
            'key_id' => YubicoTestTokens::OTP_IDENTITY->value,
            'name' => 'Security key',
        ],
    );
});

it('prevents a user associating the same key twice', function () {
    $user = User::factory()->create();
    $user->associateYubikeyIdentity(YubicoTestTokens::OTP_IDENTITY->value);

    $this->assertThrows(
        fn () => $user->associateYubikeyIdentity(YubicoTestTokens::OTP_IDENTITY->value),
        YubikeyIdentityException::class,
        YubikeyIdentityException::alreadyBound()->getMessage(),
    );
});

it('prevents two users from using the same security key', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $user1->associateYubikeyIdentity(YubicoTestTokens::OTP_IDENTITY->value);

    $this->assertThrows(
        fn () => $user2->associateYubikeyIdentity(YubicoTestTokens::OTP_IDENTITY->value),
        YubikeyIdentityException::class,
        YubikeyIdentityException::alreadyBound()->getMessage(),
    );

    $this->assertDatabaseHas(
        YubikeyIdentity::class,
        [
            'user_id' => $user1->getKey(),
            'key_id' => YubicoTestTokens::OTP_IDENTITY->value,
        ],
    );

    $this->assertDatabaseMissing(
        YubikeyIdentity::class,
        [
            'user_id' => $user2->getKey(),
            'key_id' => YubicoTestTokens::OTP_IDENTITY->value,
        ],
    );
});

test('a user can have multiple security keys associated with their account', function () {
    $user = User::factory()->create();
    $user->associateYubikeyIdentity(YubicoTestTokens::OTP_IDENTITY->value);
    $user->associateYubikeyIdentity(YubicoTestTokens::OTHER_OTP_IDENTITY->value);

    expect($user->fresh()->yubikeys()->count())->toEqual(2);
});

test('a max number of keys can be configured for each user to use', function () {
    config([
        'yubikey-u2f.max_keys_per_user' => 1,
    ]);

    $user = User::factory()->create();
    $user->associateYubikeyIdentity(YubicoTestTokens::OTP_IDENTITY->value);

    $this->assertThrows(
        fn () => $user->associateYubikeyIdentity(YubicoTestTokens::OTHER_OTP_IDENTITY->value),
        YubikeyIdentityException::class,
        YubikeyIdentityException::maxReached()->getMessage(),
    );

    $this->assertDatabaseMissing(
        YubikeyIdentity::class,
        [
            'user_id' => $user->getKey(),
            'key_id' => YubicoTestTokens::OTHER_OTP_IDENTITY->value,
        ],
    );

    // We should be able to associate the other key with a different user.
    $otherUser = User::factory()->create();
    $otherUser->associateYubikeyIdentity(YubicoTestTokens::OTHER_OTP_IDENTITY->value);

    $this->assertDatabaseHas(
        YubikeyIdentity::class,
        [
            'user_id' => $otherUser->getKey(),
            'key_id' => YubicoTestTokens::OTHER_OTP_IDENTITY->value,
        ],
    );
});

it('can verify a security key is valid and belongs to a given user', function () {
    $user = User::factory()->create();
    $user->associateYubikeyIdentity(YubicoTestTokens::OTP_IDENTITY->value);

    Yubikey::noVerifySignature();
    fakeApiCalls(YubicoResponseStatus::OK);

    expect($user->verifyYubikeyIdentity(YubicoTestTokens::REPLAYED_OTP->value))
        ->toBeTrue();
});

it('can verify a security key is valid but does not belong to a given user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $user->associateYubikeyIdentity(YubicoTestTokens::OTP_IDENTITY->value);

    Yubikey::noVerifySignature();
    fakeApiCalls(YubicoResponseStatus::OK);

    expect($otherUser->verifyYubikeyIdentity(YubicoTestTokens::REPLAYED_OTP->value))
        ->toBeFalse();
});
