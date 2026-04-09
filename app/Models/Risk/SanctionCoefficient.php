<?php

namespace App\Models\Risk;

use Illuminate\Database\Eloquent\Model;

class SanctionCoefficient extends Model
{
    public const CLASS_NORMATIVE_FIXED = 'normative_fixed';
    public const CLASS_MODEL_CONFIGURABLE = 'model_configurable';
    public const CLASS_OPTIONAL_CONDITIONAL = 'optional_conditional';

    public const NORMATIVE_FIXED_KEYS = [
        'mpriv_leve_min_pct',
        'mpriv_leve_max_pct',
        'mpriv_grave_min_pct',
        'mpriv_grave_max_pct',
        'mpub_leve_min_sbu',
        'mpub_leve_max_sbu',
        'mpub_grave_min_sbu',
        'mpub_grave_max_sbu',
    ];

    public const MODEL_CONFIGURABLE_KEYS = [
        'pert_weight_most_probable',
        'sdi_multiplier',
        'ied_weight',
        'int_weight',
        'tdp_weight',
        'tav_weight',
        'ndv_weight',
        'tev_weight',
        'sbu_default',
    ];

    public const OPTIONAL_CONDITIONAL_KEYS = [
        'rer_weight',
    ];

    protected $table = 'risk.sanction_coefficient';
    protected $primaryKey = 'coefficient_id';

    protected $fillable = [
        'rule_set',
        'group_name',
        'coefficient_key',
        'display_name',
        'coefficient_class',
        'value_numeric',
        'value_type',
        'description',
        'active_flag',
        'value_editable',
        'toggle_allowed',
        'sort_order',
    ];

    protected $casts = [
        'value_numeric' => 'decimal:6',
        'active_flag' => 'boolean',
        'value_editable' => 'boolean',
        'toggle_allowed' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'coefficient_class',
        'value_editable',
        'toggle_allowed',
    ];

    public static function inferClassFromKey(?string $coefficientKey): string
    {
        if (in_array($coefficientKey, self::NORMATIVE_FIXED_KEYS, true)) {
            return self::CLASS_NORMATIVE_FIXED;
        }

        if (in_array($coefficientKey, self::OPTIONAL_CONDITIONAL_KEYS, true)) {
            return self::CLASS_OPTIONAL_CONDITIONAL;
        }

        return self::CLASS_MODEL_CONFIGURABLE;
    }

    public static function requiredKeys(): array
    {
        return array_merge(self::NORMATIVE_FIXED_KEYS, self::MODEL_CONFIGURABLE_KEYS);
    }

    public function allowsValueEdit(): bool
    {
        return (bool) $this->value_editable;
    }

    public function allowsToggle(): bool
    {
        return (bool) $this->toggle_allowed;
    }

    public function mustRemainActive(): bool
    {
        return !$this->allowsToggle();
    }

    public function getCoefficientClassAttribute($value): string
    {
        if (is_string($value) && $value !== '') {
            return $value;
        }

        return self::inferClassFromKey($this->attributes['coefficient_key'] ?? null);
    }

    public function getValueEditableAttribute($value): bool
    {
        if ($value !== null) {
            return (bool) $value;
        }

        return $this->getCoefficientClassAttribute($this->attributes['coefficient_class'] ?? null) !== self::CLASS_NORMATIVE_FIXED;
    }

    public function getToggleAllowedAttribute($value): bool
    {
        if ($value !== null) {
            return (bool) $value;
        }

        return $this->getCoefficientClassAttribute($this->attributes['coefficient_class'] ?? null) === self::CLASS_OPTIONAL_CONDITIONAL;
    }

    public function getActiveFlagAttribute($value): bool
    {
        $class = $this->getCoefficientClassAttribute($this->attributes['coefficient_class'] ?? null);

        if ($class !== self::CLASS_OPTIONAL_CONDITIONAL) {
            return true;
        }

        return $value === null ? false : (bool) $value;
    }
}
