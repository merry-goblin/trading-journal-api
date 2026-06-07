<?php

namespace App\DTO;

abstract class AbstractMapper
{
    protected function stringOrEmpty(mixed $value): string
    {
        if ($value === null) { return ''; }
        if (is_scalar($value)) { return trim((string) $value); }
        return '';
    }

    protected function stringOrNull(mixed $value): ?string
    {
        if ($value === null) { return null; }
        if (is_scalar($value)) {
            $trimmed = trim((string) $value);
            return $trimmed === '' ? null : $trimmed;
        }
        return null;
    }

    protected function intOrEmpty(mixed $value): int
    {
        if ($value === null) { return 0; }
        if (is_scalar($value)) { return intval(round(trim($value), 0)); }
        return 0;
    }

    protected function intOrNull(mixed $value): ?int
    {
        if ($value === null) { return null; }
        if (is_scalar($value)) { return intval(round(trim($value), 0)); }
        return null;
    }
}
