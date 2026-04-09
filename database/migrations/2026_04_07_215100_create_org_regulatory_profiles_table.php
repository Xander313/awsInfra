<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS core');

        Schema::create('core.org_regulatory_profile', function (Blueprint $table) {
            $table->bigIncrements('org_profile_id');
            $table->unsignedBigInteger('org_id');
            $table->string('entity_type', 20);
            $table->decimal('business_volume_usd', 18, 2)->nullable();
            $table->decimal('sbu_reference', 12, 2)->nullable();
            $table->unsignedSmallInteger('reference_year')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->timestamps();

            $table->unique('org_id', 'core_org_regulatory_profile_org_unique');
            $table->index(['entity_type', 'reference_year'], 'core_org_reg_profile_entity_year_idx');

            $table->foreign('org_id', 'core_org_reg_profile_org_fk')
                ->references('org_id')
                ->on('core.org')
                ->cascadeOnDelete();

            $table->foreign('updated_by_user_id', 'core_org_reg_profile_user_fk')
                ->references('user_id')
                ->on('iam.app_user')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core.org_regulatory_profile');
    }
};
