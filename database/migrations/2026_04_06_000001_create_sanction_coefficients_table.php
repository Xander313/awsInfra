<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk.sanction_coefficient', function (Blueprint $table) {
            $table->bigIncrements('coefficient_id');
            $table->string('rule_set', 100)->default('default');
            $table->string('group_name', 100);
            $table->string('coefficient_key', 120);
            $table->string('display_name', 180);
            $table->decimal('value_numeric', 15, 6);
            $table->string('value_type', 30)->default('decimal');
            $table->text('description')->nullable();
            $table->boolean('active_flag')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['rule_set', 'coefficient_key'], 'risk_sanction_coefficient_rule_key_unique');
            $table->index(['group_name', 'active_flag'], 'risk_sanction_coefficient_group_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk.sanction_coefficient');
    }
};
