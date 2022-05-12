<?php

namespace Rawilk\Yubikey\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Rawilk\Yubikey\Contracts\YubikeyIdentity as YubikeyIdentityContract;
use Rawilk\Yubikey\Exceptions\YubikeyIdentityException;
use Rawilk\Yubikey\Facades\Yubikey;

/**
 * Rawilk\Yubikey\Models\YubikeyIdentity
 *
 * @property int $id
 * @property string $key_id
 * @property int $user_id;
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\Rawilk\Yubikey\Models\YubikeyIdentity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Rawilk\Yubikey\Models\YubikeyIdentity query()
 * @mixin \Eloquent
 */
class YubikeyIdentity extends Model implements YubikeyIdentityContract
{
    protected $guarded = ['id'];

    protected $casts = [
        'user_id' => 'integer',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('yubikey-u2f.database.table');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    public function isOwnedBy($user): bool
    {
        return $user->getKey() === $this->user_id;
    }

    public static function associate(string $identity, $userId, string $keyName = 'Security key'): self
    {
        static::ensureKeyIsNotAlreadyBound($identity);
        static::ensureMaxKeysPerUserIsNotMet($userId);

        return tap(static::make([
            'key_id' => $identity,
            'user_id' => $userId,
            'name' => $keyName,
        ]))->save();
    }

    public static function verify(string $otp, $userId): bool
    {
        $response = Yubikey::verify($otp);

        return static::query()
            ->where('key_id', $response['identity'])
            ->where('user_id', $userId)
            ->exists();
    }

    protected static function ensureKeyIsNotAlreadyBound(string $identity): void
    {
        $exists = static::query()
            ->where('key_id', $identity)
            ->exists();

        if ($exists) {
            throw YubikeyIdentityException::alreadyBound();
        }
    }

    protected static function ensureMaxKeysPerUserIsNotMet($userId): void
    {
        $maxAllowed = config('yubikey-u2f.max_keys_per_user');
        if ($maxAllowed === null) {
            return;
        }

        $count = static::query()
            ->where('user_id', $userId)
            ->count();

        if ($count >= $maxAllowed) {
            throw YubikeyIdentityException::maxReached();
        }
    }
}
