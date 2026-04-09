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
            $table->boolean('is_required')->default(false);
            $table->boolean('is_editable')->default(true);
            $table->boolean('applies_conditionally')->default(false);
        });

        DB::table('risk.sanction_coefficient')
            ->where('coefficient_key', '!=', 'rer_weight')
            ->update([
                'is_required' => true,
                'is_editable' => true,
                'applies_conditionally' => false,
                'active_flag' => true,
            ]);

        DB::table('risk.sanction_coefficient')
            ->where('coefficient_key', 'rer_weight')
            ->update([
                'is_required' => false,
                'is_editable' => true,
                'applies_conditionally' => true,
            ]);
    }

    public function down(): void
    {
        Schema::table('risk.sanction_coefficient', function (Blueprint $table) {
            $table->dropColumn([
                'is_required',
                'is_editable',
                'applies_conditionally',
            ]);
        });
    }
};
