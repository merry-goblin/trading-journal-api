<?php

namespace App\DTO;

abstract class AbstractMapper
{
    protected function stringOrEmpty(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_scalar($value)) {
            return trim((string) $value);
        }

        // arrays, objects, etc.
        return '';
    }

    protected function intOrEmpty(mixed $value): int
    {
        if ($value === null) {
            return 0;
        }

        if (is_scalar($value)) {
            return intval(round(trim($value), 0));
        }

        // arrays, objects, etc.
        return 0;
    }

    protected function intOrNull(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        if (is_scalar($value)) {
            return intval(round(trim($value), 0));
        }

        // arrays, objects, etc.
        return null;
    }
}
