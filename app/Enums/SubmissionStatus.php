<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';

    public static function default(): self
    {
        return self::Pending;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
