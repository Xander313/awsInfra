<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('core.org')) {
            return;
        }

        DB::statement("
            SELECT setval(
                pg_get_serial_sequence('core.org', 'org_id'),
                COALESCE((SELECT MAX(org_id) FROM core.org), 1),
                TRUE
            )
        ");
    }

    public function down(): void
    {
        // No-op: resincronizar una secuencia es un ajuste correctivo no reversible.
    }
};
