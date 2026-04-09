<?php

use App\Models\Risk\SanctionCoefficient;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('risk.sanction_coefficient')) {
            return;
        }

        $hasCoefficientClass = Schema::hasColumn('risk.sanction_coefficient', 'coefficient_class');
        $hasValueEditable = Schema::hasColumn('risk.sanction_coefficient', 'value_editable');
        $hasToggleAllowed = Schema::hasColumn('risk.sanction_coefficient', 'toggle_allowed');

        if (!$hasCoefficientClass || !$hasValueEditable || !$hasToggleAllowed) {
            Schema::table('risk.sanction_coefficient', function (Blueprint $table) use ($hasCoefficientClass, $hasValueEditable, $hasToggleAllowed) {
                if (!$hasCoefficientClass) {
                    $table->string('coefficient_class', 40)->default(SanctionCoefficient::CLASS_MODEL_CONFIGURABLE);
                }

                if (!$hasValueEditable) {
                    $table->boolean('value_editable')->default(true);
                }

                if (!$hasToggleAllowed) {
                    $table->boolean('toggle_allowed')->default(false);
                }
            });
        }

        $this->backfillClassification();
    }

    public function down(): void
    {
        if (!Schema::hasTable('risk.sanction_coefficient')) {
            return;
        }

        $dropColumns = [];

        if (Schema::hasColumn('risk.sanction_coefficient', 'value_editable')) {
            $dropColumns[] = 'value_editable';
        }

        if (Schema::hasColumn('risk.sanction_coefficient', 'toggle_allowed')) {
            $dropColumns[] = 'toggle_allowed';
        }

        if ($dropColumns !== []) {
            Schema::table('risk.sanction_coefficient', function (Blueprint $table) use ($dropColumns) {
                $table->dropColumn($dropColumns);
            });
        }
    }

    private function backfillClassification(): void
    {
        $this->updateClassGroup(
            SanctionCoefficient::NORMATIVE_FIXED_KEYS,
            [
                'coefficient_class' => SanctionCoefficient::CLASS_NORMATIVE_FIXED,
                'value_editable' => false,
                'toggle_allowed' => false,
                'active_flag' => true,
                'updated_at' => now(),
            ],
            [
                'is_required' => true,
                'is_editable' => false,
                'applies_conditionally' => false,
            ]
        );

        $this->updateClassGroup(
            SanctionCoefficient::MODEL_CONFIGURABLE_KEYS,
            [
                'coefficient_class' => SanctionCoefficient::CLASS_MODEL_CONFIGURABLE,
                'value_editable' => true,
                'toggle_allowed' => false,
                'active_flag' => true,
                'updated_at' => now(),
            ],
            [
                'is_required' => true,
                'is_editable' => true,
                'applies_conditionally' => false,
            ]
        );

        $this->updateClassGroup(
            SanctionCoefficient::OPTIONAL_CONDITIONAL_KEYS,
            [
                'coefficient_class' => SanctionCoefficient::CLASS_OPTIONAL_CONDITIONAL,
                'value_editable' => true,
                'toggle_allowed' => true,
                'updated_at' => now(),
            ],
            [
                'is_required' => false,
                'is_editable' => true,
                'applies_conditionally' => true,
            ]
        );
    }

    private function updateClassGroup(array $keys, array $values, array $legacyValues): void
    {
        if ($keys === []) {
            return;
        }

        DB::table('risk.sanction_coefficient')
            ->whereIn('coefficient_key', $keys)
            ->update($values + $this->legacyCompatibilityValues($legacyValues));
    }

    private function legacyCompatibilityValues(array $values): array
    {
        $compatibility = [];

        if (Schema::hasColumn('risk.sanction_coefficient', 'is_required')) {
            $compatibility['is_required'] = $values['is_required'];
        }

        if (Schema::hasColumn('risk.sanction_coefficient', 'is_editable')) {
            $compatibility['is_editable'] = $values['is_editable'];
        }

        if (Schema::hasColumn('risk.sanction_coefficient', 'applies_conditionally')) {
            $compatibility['applies_conditionally'] = $values['applies_conditionally'];
        }

        return $compatibility;
    }
};
