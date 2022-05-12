<?php

namespace Rawilk\Yubikey\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Rawilk\Yubikey\Models\HasYubikeys;
use Rawilk\Yubikey\Tests\database\factories\UserFactory;

class User extends Authenticatable
{
    use HasFactory;
    use HasYubikeys;

    protected $guarded = [];

    protected $hidden = ['password', 'remember_token'];

    protected static function newFactory(): UserFactory
    {
        return new UserFactory;
    }
}
