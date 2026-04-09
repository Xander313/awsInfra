<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('risk.sanction_simulation', function (Blueprint $table) {
            $table->unsignedBigInteger('incident_id')->nullable()->after('org_id');
            $table->jsonb('incident_snapshot')->nullable()->after('wizard_snapshot');

            $table->index(['incident_id', 'created_at'], 'risk_sanction_sim_incident_created_idx');

            $table->foreign('incident_id', 'risk_sanction_sim_incident_fk')
                ->references('incident_id')
                ->on('risk.incident')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('risk.sanction_simulation', function (Blueprint $table) {
            $table->dropForeign('risk_sanction_sim_incident_fk');
            $table->dropIndex('risk_sanction_sim_incident_created_idx');
            $table->dropColumn(['incident_id', 'incident_snapshot']);
        });
    }
};
