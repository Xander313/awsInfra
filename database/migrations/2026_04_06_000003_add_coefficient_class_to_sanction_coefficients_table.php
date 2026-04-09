<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('risk.sanction_coefficient', function (Blueprint $table) {
            $table->string('coefficient_class', 40)->default('model_configurable');
        });

        $normativeFixed = [
            'mpriv_leve_min_pct',
            'mpriv_leve_max_pct',
            'mpriv_grave_min_pct',
            'mpriv_grave_max_pct',
            'mpub_leve_min_sbu',
            'mpub_leve_max_sbu',
            'mpub_grave_min_sbu',
            'mpub_grave_max_sbu',
        ];

        $optionalConditional = ['rer_weight'];

        DB::table('risk.sanction_coefficient')
            ->whereIn('coefficient_key', $normativeFixed)
            ->update([
                'coefficient_class' => 'normative_fixed',
                'is_required' => true,
                'is_editable' => false,
                'applies_conditionally' => false,
                'active_flag' => true,
            ]);

        DB::table('risk.sanction_coefficient')
            ->whereIn('coefficient_key', $optionalConditional)
            ->update([
                'coefficient_class' => 'optional_conditional',
                'is_required' => false,
                'is_editable' => true,
                'applies_conditionally' => true,
            ]);

        DB::table('risk.sanction_coefficient')
            ->whereNotIn('coefficient_key', array_merge($normativeFixed, $optionalConditional))
            ->update([
                'coefficient_class' => 'model_configurable',
                'is_required' => true,
                'is_editable' => true,
                'applies_conditionally' => false,
                'active_flag' => true,
            ]);
    }

    public function down(): void
    {
        Schema::table('risk.sanction_coefficient', function (Blueprint $table) {
            $table->dropColumn('coefficient_class');
        });
    }
};
