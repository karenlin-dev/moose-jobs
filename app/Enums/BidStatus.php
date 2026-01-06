<?php

namespace App\Enums;

class BidStatus
{
    const PENDING  = 'pending';
    const ACCEPTED = 'accepted';
    const REJECTED = 'rejected';

    public static function all(): array
    {
        return [
            self::PENDING,
            self::ACCEPTED,
            self::REJECTED,
        ];
    }
}
