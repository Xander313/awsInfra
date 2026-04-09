<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk.sanction_simulation', function (Blueprint $table) {
            $table->bigIncrements('simulation_id');
            $table->unsignedInteger('org_id')->nullable();
            $table->string('org_name', 180)->nullable();
            $table->unsignedInteger('created_by_user_id')->nullable();
            $table->string('created_by_user_name', 180)->nullable();
            $table->string('rule_set', 100)->default('default');
            $table->string('case_name', 180)->nullable();
            $table->string('entity_type', 20);
            $table->string('company_role', 20);
            $table->decimal('deterministic_fine_usd', 18, 2);
            $table->decimal('monte_carlo_min_usd', 18, 2)->nullable();
            $table->decimal('monte_carlo_mean_usd', 18, 2)->nullable();
            $table->decimal('monte_carlo_max_usd', 18, 2)->nullable();
            $table->json('wizard_snapshot');
            $table->json('result_snapshot');
            $table->json('documentation_snapshot');
            $table->json('coefficient_snapshot')->nullable();
            $table->timestamps();

            $table->index(['org_id', 'created_at'], 'risk_sanction_sim_org_created_idx');
            $table->index(['created_by_user_id', 'created_at'], 'risk_sanction_sim_user_created_idx');
            $table->index(['case_name', 'created_at'], 'risk_sanction_sim_case_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk.sanction_simulation');
    }
};
