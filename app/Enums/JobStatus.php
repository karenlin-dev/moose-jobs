<?php

namespace App\Enums;

class JobStatus
{
    const OPEN        = 'open';
    const IN_PROGRESS = 'in_progress';
    const COMPLETED   = 'completed';
    const CANCELLED   = 'cancelled';

    public static function all(): array
    {
        return [
            self::OPEN,
            self::IN_PROGRESS,
            self::COMPLETED,
            self::CANCELLED,
        ];
    }
}
