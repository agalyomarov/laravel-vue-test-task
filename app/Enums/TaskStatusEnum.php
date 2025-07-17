<?php

namespace App\Enums;

enum TaskStatusEnum: string
{
    case BACKLOG = 'backlog';
    case WIP = 'wip';
    case DONE = 'done';
    case CANCELED = 'canceled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function options(): array
    {
        return array_map(
            fn(self $case) => ['key' => $case->name, 'value' => $case->value],
            self::cases()
        );
    }
}