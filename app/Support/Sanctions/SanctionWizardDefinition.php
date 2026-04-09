<?php

namespace App\Support\Sanctions;

use Illuminate\Support\Arr;

class SanctionWizardDefinition
{
    private static ?array $data = null;

    public static function sessionKey(): string
    {
        return (string) self::get('wizard.session_key', 'sanctions.wizard');
    }

    public static function steps(): array
    {
        return self::get('steps', []);
    }

    public static function cdiCatalogForRole(?string $role): array
    {
        if (!$role) {
            return [];
        }

        return self::get("cdi.$role", []);
    }

    public static function pdiQuestions(): array
    {
        return self::get('pdi_questions', []);
    }

    public static function dataTypes(): array
    {
        return self::get('data_types', []);
    }

    public static function impactLevels(): array
    {
        return self::get('impact_levels', []);
    }

    public static function impactLevelsMap(): array
    {
        $map = [];

        foreach (self::impactLevels() as $item) {
            $map[$item['value']] = $item;
        }

        return $map;
    }

    public static function dataVolumeOptions(): array
    {
        return self::get('data_volume_options', []);
    }

    public static function dataVolumeOptionsMap(): array
    {
        $map = [];

        foreach (self::dataVolumeOptions() as $item) {
            $map[$item['value']] = $item;
        }

        return $map;
    }

    public static function dataSubjectCountBands(): array
    {
        return self::get('data_subject_count_bands', []);
    }

    public static function dataVolumeBands(): array
    {
        return self::get('data_volume_bands', []);
    }

    public static function intentionalityOptions(): array
    {
        return self::get('intentionality_options', []);
    }

    public static function intentionalityOptionsMap(): array
    {
        $map = [];

        foreach (self::intentionalityOptions() as $item) {
            $map[$item['value']] = $item;
        }

        return $map;
    }

    public static function assumptions(): array
    {
        return self::get('assumptions', []);
    }

    public static function documentation(): array
    {
        return self::get('documentation', []);
    }

    public static function monteCarlo(): array
    {
        return self::get('monte_carlo', []);
    }

    public static function dataTypesMap(): array
    {
        $map = [];

        foreach (self::dataTypes() as $item) {
            $map[$item['value']] = $item;
        }

        return $map;
    }

    public static function get(string $path, mixed $default = null): mixed
    {
        return Arr::get(self::all(), $path, $default);
    }

    public static function all(): array
    {
        if (self::$data !== null) {
            return self::$data;
        }

        $config = config('sanctions');
        if (is_array($config) && $config !== []) {
            return self::$data = $config;
        }

        $configPath = config_path('sanctions.php');
        if (is_file($configPath)) {
            $loaded = require $configPath;

            return self::$data = is_array($loaded) ? $loaded : [];
        }

        return self::$data = [];
    }
}
