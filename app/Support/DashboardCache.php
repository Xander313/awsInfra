<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

final class DashboardCache
{
    public static function key(int $orgId): string
    {
        return "dashboard_{$orgId}";
    }

    public static function forgetForOrg(?int $orgId): void
    {
        if ($orgId === null || $orgId <= 0) {
            return;
        }

        Cache::forget(self::key($orgId));
    }

    public static function forgetCurrentOrg(): void
    {
        $orgId = session('org_id');

        self::forgetForOrg($orgId !== null ? (int) $orgId : null);
    }
}
