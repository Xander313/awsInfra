<?php

namespace Database\Seeders;

use App\Models\Risk\SanctionCoefficient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SanctionCoefficientSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $coefficients = [
            $this->row('GENERAL', 'pert_weight_most_probable', 'Peso PERT más probable', SanctionCoefficient::CLASS_MODEL_CONFIGURABLE, 4, 'integer', 'Peso usado por la estimación PERT para el valor más probable.', 10, $now),
            $this->row('GENERAL', 'sdi_multiplier', 'Multiplicador SDI', SanctionCoefficient::CLASS_MODEL_CONFIGURABLE, 2, 'integer', 'Multiplicador base para el índice SDI.', 20, $now),
            $this->row('GENERAL', 'ied_weight', 'Peso IED', SanctionCoefficient::CLASS_MODEL_CONFIGURABLE, 0.60, 'decimal', 'Peso global del componente IED dentro del cálculo.', 30, $now),
            $this->row('GENERAL', 'int_weight', 'Peso INT', SanctionCoefficient::CLASS_MODEL_CONFIGURABLE, 0.40, 'decimal', 'Peso global del componente INT dentro del cálculo.', 40, $now),
            $this->row('GENERAL', 'rer_weight', 'Peso RER', SanctionCoefficient::CLASS_OPTIONAL_CONDITIONAL, 0.20, 'decimal', 'Peso del ajuste RER en el cálculo final.', 50, $now),
            $this->row('IED_SUBFACTORS', 'tdp_weight', 'Peso TDP', SanctionCoefficient::CLASS_MODEL_CONFIGURABLE, 0.40, 'decimal', 'Peso del subfactor TDP dentro de IED.', 10, $now),
            $this->row('IED_SUBFACTORS', 'tav_weight', 'Peso TAV', SanctionCoefficient::CLASS_MODEL_CONFIGURABLE, 0.20, 'decimal', 'Peso del subfactor TAV dentro de IED.', 20, $now),
            $this->row('IED_SUBFACTORS', 'ndv_weight', 'Peso NDV', SanctionCoefficient::CLASS_MODEL_CONFIGURABLE, 0.20, 'decimal', 'Peso del subfactor NDV dentro de IED.', 30, $now),
            $this->row('IED_SUBFACTORS', 'tev_weight', 'Peso TEV', SanctionCoefficient::CLASS_MODEL_CONFIGURABLE, 0.20, 'decimal', 'Peso del subfactor TEV dentro de IED.', 40, $now),
            $this->row('MPRIV_RDM', 'mpriv_leve_min_pct', 'Privada leve mínimo %', SanctionCoefficient::CLASS_NORMATIVE_FIXED, 0.001, 'percentage', 'Rango mínimo porcentual para sanción leve en entidad privada.', 10, $now),
            $this->row('MPRIV_RDM', 'mpriv_leve_max_pct', 'Privada leve máximo %', SanctionCoefficient::CLASS_NORMATIVE_FIXED, 0.007, 'percentage', 'Rango máximo porcentual para sanción leve en entidad privada.', 20, $now),
            $this->row('MPRIV_RDM', 'mpriv_grave_min_pct', 'Privada grave mínimo %', SanctionCoefficient::CLASS_NORMATIVE_FIXED, 0.007, 'percentage', 'Rango mínimo porcentual para sanción grave en entidad privada.', 30, $now),
            $this->row('MPRIV_RDM', 'mpriv_grave_max_pct', 'Privada grave máximo %', SanctionCoefficient::CLASS_NORMATIVE_FIXED, 0.010, 'percentage', 'Rango máximo porcentual para sanción grave en entidad privada.', 40, $now),
            $this->row('MPUB_RDM', 'mpub_leve_min_sbu', 'Pública leve mínimo SBU', SanctionCoefficient::CLASS_NORMATIVE_FIXED, 1, 'integer', 'Rango mínimo en SBU para sanción leve en entidad pública.', 10, $now),
            $this->row('MPUB_RDM', 'mpub_leve_max_sbu', 'Pública leve máximo SBU', SanctionCoefficient::CLASS_NORMATIVE_FIXED, 10, 'integer', 'Rango máximo en SBU para sanción leve en entidad pública.', 20, $now),
            $this->row('MPUB_RDM', 'mpub_grave_min_sbu', 'Pública grave mínimo SBU', SanctionCoefficient::CLASS_NORMATIVE_FIXED, 10, 'integer', 'Rango mínimo en SBU para sanción grave en entidad pública.', 30, $now),
            $this->row('MPUB_RDM', 'mpub_grave_max_sbu', 'Pública grave máximo SBU', SanctionCoefficient::CLASS_NORMATIVE_FIXED, 20, 'integer', 'Rango máximo en SBU para sanción grave en entidad pública.', 40, $now),
            $this->row('MPUB_RDM', 'sbu_default', 'SBU por defecto', SanctionCoefficient::CLASS_MODEL_CONFIGURABLE, 470, 'currency_base', 'Salario básico unificado usado como referencia monetaria.', 50, $now),
        ];

        DB::table('risk.sanction_coefficient')->upsert(
            $coefficients,
            ['rule_set', 'coefficient_key'],
            ['group_name', 'display_name', 'coefficient_class', 'value_numeric', 'value_type', 'description', 'active_flag', 'value_editable', 'toggle_allowed', 'sort_order', 'updated_at']
        );
    }

    private function row(
        string $group,
        string $key,
        string $displayName,
        string $class,
        float|int $value,
        string $valueType,
        string $description,
        int $sortOrder,
        $timestamp
    ): array {
        return [
            'rule_set' => 'default',
            'group_name' => $group,
            'coefficient_key' => $key,
            'display_name' => $displayName,
            'coefficient_class' => $class,
            'value_numeric' => $value,
            'value_type' => $valueType,
            'description' => $description,
            'active_flag' => true,
            'value_editable' => $class !== SanctionCoefficient::CLASS_NORMATIVE_FIXED,
            'toggle_allowed' => $class === SanctionCoefficient::CLASS_OPTIONAL_CONDITIONAL,
            'sort_order' => $sortOrder,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ];
    }
}
