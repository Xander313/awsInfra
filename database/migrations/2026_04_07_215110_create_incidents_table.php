<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS risk');

        Schema::create('risk.incident', function (Blueprint $table) {
            $table->bigIncrements('incident_id');
            $table->unsignedBigInteger('org_id');
            $table->string('incident_code', 100);
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('incident_type', 100)->nullable();
            $table->string('status', 50)->nullable();
            $table->string('severity', 50)->nullable();
            $table->string('company_role', 20)->nullable();
            $table->unsignedBigInteger('system_id')->nullable();
            $table->unsignedBigInteger('pa_id')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamp('detected_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->unsignedInteger('data_subject_count')->nullable();
            $table->decimal('data_volume_amount', 18, 2)->nullable();
            $table->jsonb('affected_data_types')->nullable();
            $table->string('confidentiality_impact', 50)->nullable();
            $table->string('integrity_impact', 50)->nullable();
            $table->string('availability_impact', 50)->nullable();
            $table->boolean('vulnerable_groups_flag')->default(false);
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();

            $table->unique(['org_id', 'incident_code'], 'risk_incident_org_code_unique');
            $table->index(['org_id', 'status'], 'risk_incident_org_status_idx');
            $table->index(['org_id', 'occurred_at'], 'risk_incident_org_occurred_idx');
            $table->index(['system_id', 'occurred_at'], 'risk_incident_system_occurred_idx');
            $table->index(['pa_id', 'occurred_at'], 'risk_incident_pa_occurred_idx');

            $table->foreign('org_id', 'risk_incident_org_fk')
                ->references('org_id')
                ->on('core.org')
                ->cascadeOnDelete();

            $table->foreign('system_id', 'risk_incident_system_fk')
                ->references('system_id')
                ->on('privacy.system')
                ->nullOnDelete();

            $table->foreign('pa_id', 'risk_incident_pa_fk')
                ->references('pa_id')
                ->on('privacy.processing_activity')
                ->nullOnDelete();

            $table->foreign('created_by_user_id', 'risk_incident_user_fk')
                ->references('user_id')
                ->on('iam.app_user')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk.incident');
    }
};
