<?php

use Illuminate\Support\Facades\Gate;
use function Pest\Laravel\actingAs;
use Rawilk\Yubikey\Enums\YubicoTestTokens;
use Rawilk\Yubikey\Tests\Models\User;

it('allows a user to rename their own key', function () {
    $user = User::factory()->create();
    $identity = $user->associateYubikeyIdentity(YubicoTestTokens::OTP_IDENTITY->value);

    actingAs($user);
    expect(Gate::allows('rename', $identity))->toBeTrue();
});

it('prevents a user from renaming another user account security key', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $identity = $user->associateYubikeyIdentity(YubicoTestTokens::OTP_IDENTITY->value);

    actingAs($otherUser);
    expect(Gate::allows('rename', $identity))->toBeFalse();
});

it('allows a user to delete an associated security key', function () {
    $user = User::factory()->create();
    $identity = $user->associateYubikeyIdentity(YubicoTestTokens::OTP_IDENTITY->value);

    actingAs($user);
    expect(Gate::allows('delete', $identity))->toBeTrue();
});

it('prevents a user from deleting a security key belonging to another user', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $identity = $user->associateYubikeyIdentity(YubicoTestTokens::OTP_IDENTITY->value);

    actingAs($otherUser);
    expect(Gate::allows('delete', $identity))->toBeFalse();
});
