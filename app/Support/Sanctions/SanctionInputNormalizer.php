<?php

namespace App\Support\Sanctions;

use Illuminate\Validation\ValidationException;

class SanctionInputNormalizer
{
    public static function parseHumanNumber(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $string = preg_replace('/[[:space:]\x{00A0}]+/u', '', trim((string) $value));

        if ($string === '') {
            return null;
        }

        if (!preg_match('/^[0-9.,]+$/', $string)) {
            return null;
        }

        if (preg_match('/^\d{1,3}([.,]\d{3})+$/', $string)) {
            return (float) str_replace([',', '.'], '', $string);
        }

        if (str_contains($string, ',') && str_contains($string, '.')) {
            $lastComma = strrpos($string, ',');
            $lastDot = strrpos($string, '.');
            $decimalPos = max($lastComma, $lastDot);
            $decimalLength = strlen($string) - $decimalPos - 1;

            if ($decimalLength >= 1 && $decimalLength <= 2) {
                $decimalSeparator = $string[$decimalPos];
                $thousandSeparator = $decimalSeparator === ',' ? '.' : ',';
                $normalized = str_replace($thousandSeparator, '', $string);
                $normalized = str_replace($decimalSeparator, '.', $normalized);

                return is_numeric($normalized) ? (float) $normalized : null;
            }

            return (float) str_replace([',', '.'], '', $string);
        }

        if (str_contains($string, ',')) {
            [$left, $right] = array_pad(explode(',', $string, 2), 2, '');

            if ($right !== '' && strlen($right) <= 2) {
                $normalized = $left . '.' . $right;
                return is_numeric($normalized) ? (float) $normalized : null;
            }

            return (float) str_replace(',', '', $string);
        }

        if (str_contains($string, '.')) {
            [$left, $right] = array_pad(explode('.', $string, 2), 2, '');

            if ($right !== '' && strlen($right) <= 2) {
                return is_numeric($string) ? (float) $string : null;
            }

            return (float) str_replace('.', '', $string);
        }

        return is_numeric($string) ? (float) $string : null;
    }

    public static function parseRequiredPositiveNumber(mixed $value, string $field, string $label): float
    {
        $parsed = self::parseHumanNumber($value);

        if ($parsed === null || $parsed <= 0) {
            throw ValidationException::withMessages([
                $field => "El campo {$label} debe contener un numero positivo valido.",
            ]);
        }

        return $parsed;
    }
}
