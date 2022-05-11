<?php

declare(strict_types=1);

namespace Rawilk\Yubikey\Enums;

enum YubicoResponseStatus: string
{
    case OK = 'OK';
    case BAD_OTP = 'BAD_OTP';
    case REPLAYED_OTP = 'REPLAYED_OTP';
    case BAD_SIGNATURE = 'BAD_SIGNATURE';
    case MISSING_PARAMETER = 'MISSING_PARAMETER';
    case NO_SUCH_CLIENT = 'NO_SUCH_CLIENT';
    case OPERATION_NOT_ALLOWED = 'OPERATION_NOT_ALLOWED';
    case BACKEND_ERROR = 'BACKEND_ERROR';
    case NOT_ENOUGH_ANSWERS = 'NOT_ENOUGH_ANSWERS';
    case REPLAYED_REQUEST = 'REPLAYED_REQUEST';
}
