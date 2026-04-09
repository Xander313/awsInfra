<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('risk.incident', function (Blueprint $table) {
            $table->unsignedBigInteger('official_simulation_id')->nullable()->after('created_by_user_id');
            $table->index('official_simulation_id', 'risk_incident_official_sim_idx');

            $table->foreign('official_simulation_id', 'risk_incident_official_sim_fk')
                ->references('simulation_id')
                ->on('risk.sanction_simulation')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('risk.incident', function (Blueprint $table) {
            $table->dropForeign('risk_incident_official_sim_fk');
            $table->dropIndex('risk_incident_official_sim_idx');
            $table->dropColumn('official_simulation_id');
        });
    }
};
