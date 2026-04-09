<?php

namespace App\Services\Sanctions;

use App\Models\Risk\SanctionCoefficient;
use App\Support\Sanctions\SanctionWizardDefinition;
use Illuminate\Support\Collection;
use RuntimeException;

class SanctionCalculationService
{
    public function calculate(array $state): array
    {
        $coefficients = $this->loadCoefficients();
        $general = data_get($state, 'general', []);
        $cdiSelection = data_get($state, 'cdi', []);
        $pdiAnswers = collect(data_get($state, 'pdi.answers', []));
        $ndvInputs = data_get($state, 'ndv', []);
        $intSelection = data_get($state, 'int', []);
        $rerSelection = data_get($state, 'rer', []);

        $pdi = $this->calculatePdi($pdiAnswers, $coefficients);
        $ied = $this->calculateIed($ndvInputs, $coefficients);
        $int = $this->calculateIntentionality($intSelection);
        $rer = $this->calculateRer($rerSelection);
        $cdi = $this->calculateCdi($general, $cdiSelection, $pdi['score'], $coefficients);
        $sdi = $this->calculateSdi($ied['score'], $int['score'], $rer['score'], $coefficients);

        $fineAmount = $cdi['base_amount_usd'] * $sdi['score'];
        $monteCarlo = $this->calculateMonteCarlo($coefficients, $cdi, $ied, $int, $rer);

        return [
            'summary' => [
                'entity_type' => data_get($general, 'entity_type'),
                'company_role' => data_get($general, 'company_role'),
                'case_name' => data_get($general, 'case_name'),
                'reference_amount' => data_get($general, 'entity_type') === 'publica'
                    ? (float) data_get($general, 'sbu_reference')
                    : (float) data_get($general, 'business_volume_usd'),
                'reference_label' => data_get($general, 'entity_type') === 'publica' ? 'SBU de referencia' : 'Volumen de negocio USD',
                'selected_infraction' => data_get($cdiSelection, 'label'),
                'severity_label' => data_get($cdiSelection, 'severity_label'),
            ],
            'components' => [
                'cdi' => $cdi,
                'pdi' => $pdi,
                'ied' => $ied,
                'int' => $int,
                'rer' => $rer,
                'sdi' => $sdi,
                'fine' => [
                    'amount_usd' => $fineAmount,
                    'formatted_amount_usd' => $this->currency($fineAmount),
                    'formula' => 'MULTA = CDI x SDI',
                ],
            ],
            'metrics' => [
                [
                    'code' => 'CDI',
                    'label' => 'Categoría de infracción',
                    'value' => $this->currency($cdi['base_amount_usd']),
                    'detail' => $cdi['detail'],
                ],
                [
                    'code' => 'PDI',
                    'label' => 'Peso de la infracción',
                    'value' => number_format($pdi['score'], 4, '.', ''),
                    'detail' => $pdi['detail'],
                ],
                [
                    'code' => 'IED',
                    'label' => 'Índice de exposición del daño',
                    'value' => number_format($ied['score'], 4, '.', ''),
                    'detail' => $ied['detail'],
                ],
                [
                    'code' => 'INT',
                    'label' => 'Intencionalidad',
                    'value' => number_format($int['score'], 4, '.', ''),
                    'detail' => $int['detail'],
                ],
                [
                    'code' => 'RER',
                    'label' => 'Reiteración y reincidencia',
                    'value' => number_format($rer['score'], 4, '.', ''),
                    'detail' => $rer['detail'],
                ],
                [
                    'code' => 'SDI',
                    'label' => 'Severidad derivada integral',
                    'value' => number_format($sdi['score'], 4, '.', ''),
                    'detail' => $sdi['detail'],
                ],
                [
                    'code' => 'MULTA',
                    'label' => 'Multa estimada',
                    'value' => $this->currency($fineAmount),
                    'detail' => 'Resultado determinista standalone en USD.',
                ],
            ],
            'monte_carlo' => $monteCarlo,
            'assumptions' => SanctionWizardDefinition::assumptions(),
        ];
    }

    private function loadCoefficients(): array
    {
        $requiredKeys = [
            'pert_weight_most_probable',
            'sdi_multiplier',
            'ied_weight',
            'int_weight',
            'rer_weight',
            'tdp_weight',
            'tav_weight',
            'ndv_weight',
            'tev_weight',
            'mpriv_leve_min_pct',
            'mpriv_leve_max_pct',
            'mpriv_grave_min_pct',
            'mpriv_grave_max_pct',
            'mpub_leve_min_sbu',
            'mpub_leve_max_sbu',
            'mpub_grave_min_sbu',
            'mpub_grave_max_sbu',
            'sbu_default',
        ];

        $rows = SanctionCoefficient::query()
            ->where('rule_set', 'default')
            ->whereIn('coefficient_key', $requiredKeys)
            ->get();

        $map = $rows->mapWithKeys(function (SanctionCoefficient $coefficient) {
            $value = (float) $coefficient->value_numeric;

            if ($coefficient->coefficient_key === 'rer_weight' && !$coefficient->active_flag) {
                $value = 0.0;
            }

            return [$coefficient->coefficient_key => $value];
        })->all();

        $missing = collect($requiredKeys)->reject(fn ($key) => array_key_exists($key, $map))->values();
        if ($missing->isNotEmpty()) {
            throw new RuntimeException('Faltan coeficientes requeridos para el calculo: ' . $missing->join(', ') . '.');
        }

        return $map;
    }

    private function calculatePdi(Collection $answers, array $coefficients): array
    {
        $total = count(SanctionWizardDefinition::pdiQuestions());
        $yesCount = $answers->filter(fn ($value) => (bool) $value)->count();
        $noCount = max(0, $total - $yesCount);
        $rawRatio = $total > 0 ? $noCount / $total : 0.0;
        $delta = (float) SanctionWizardDefinition::get('pdi.smoothing_delta', 0.15);
        $optimistic = max(0.0, $rawRatio - $delta);
        $pessimistic = min(1.0, $rawRatio + $delta);
        $weight = (float) $coefficients['pert_weight_most_probable'];
        $pertScore = ($optimistic + ($weight * $rawRatio) + $pessimistic) / ($weight + 2.0);

        return [
            'yes_count' => $yesCount,
            'no_count' => $noCount,
            'total_questions' => $total,
            'raw_ratio' => $rawRatio,
            'score' => $pertScore,
            'detail' => sprintf(
                '%d respuestas negativas de %d preguntas. Ratio base de brecha %.2f%% con suavizado PERT.',
                $noCount,
                $total,
                $rawRatio * 100
            ),
        ];
    }

    private function calculateIed(array $ndvInputs, array $coefficients): array
    {
        $impactMap = SanctionWizardDefinition::impactLevelsMap();
        $dataTypeMap = SanctionWizardDefinition::dataTypesMap();
        $confidentiality = (float) data_get($impactMap, data_get($ndvInputs, 'confidentiality_impact') . '.score', 0.0);
        $integrity = (float) data_get($impactMap, data_get($ndvInputs, 'integrity_impact') . '.score', 0.0);
        $availability = (float) data_get($impactMap, data_get($ndvInputs, 'availability_impact') . '.score', 0.0);

        $ndvScore = ($confidentiality + $integrity + $availability) / 3;

        $selectedTypes = collect(data_get($ndvInputs, 'data_types', []));
        $tdpScore = $selectedTypes
            ->map(fn ($value) => (float) data_get($dataTypeMap, $value . '.impact_score', 0.0))
            ->max() ?? 0.0;

        $subjectCountScore = $this->subjectCountScore((int) data_get($ndvInputs, 'data_subject_count', 0));
        $volumeScore = $this->dataVolumeScore((float) data_get($ndvInputs, 'data_volume_amount', 0));
        $tavScore = ($subjectCountScore + $volumeScore) / 2;
        $tevScore = data_get($ndvInputs, 'vulnerable_groups') ? 1.0 : 0.0;

        $iedData = $this->calculateIedFromScores($tdpScore, $tavScore, $ndvScore, $tevScore, $coefficients);

        return $iedData + [
            'subject_count_score' => $subjectCountScore,
            'data_volume_score' => $volumeScore,
        ];
    }

    private function calculateIedFromScores(
        float $tdpScore,
        float $tavScore,
        float $ndvScore,
        float $tevScore,
        array $coefficients
    ): array {
        $iedScore =
            ((float) $coefficients['tdp_weight'] * $tdpScore) +
            ((float) $coefficients['tav_weight'] * $tavScore) +
            ((float) $coefficients['ndv_weight'] * $ndvScore) +
            ((float) $coefficients['tev_weight'] * $tevScore);

        return [
            'score' => $iedScore,
            'tdp_score' => $tdpScore,
            'tav_score' => $tavScore,
            'ndv_score' => $ndvScore,
            'tev_score' => $tevScore,
            'detail' => sprintf(
                'IED = %.2f*TDP + %.2f*TAV + %.2f*NDV + %.2f*TEV',
                $coefficients['tdp_weight'],
                $coefficients['tav_weight'],
                $coefficients['ndv_weight'],
                $coefficients['tev_weight']
            ),
        ];
    }

    private function calculateIntentionality(array $selection): array
    {
        $option = SanctionWizardDefinition::intentionalityOptionsMap()[data_get($selection, 'level')] ?? null;
        $score = (float) ($option['score'] ?? 0.0);

        return [
            'score' => $score,
            'label' => $option['label'] ?? data_get($selection, 'label', 'No definido'),
            'detail' => $option['description'] ?? 'Sin descripcion configurada.',
        ];
    }

    private function calculateRer(array $selection): array
    {
        $applies = (bool) data_get($selection, 'applies', false);
        $score = $applies
            ? (float) SanctionWizardDefinition::get('rer.applies_score', 1.0)
            : (float) SanctionWizardDefinition::get('rer.not_applies_score', 0.0);

        return [
            'applies' => $applies,
            'score' => $score,
            'detail' => $applies
                ? 'Se activa el componente de reiteracion y reincidencia.'
                : 'RER se computa como 0 para este caso.',
        ];
    }

    private function calculateCdi(array $general, array $selection, float $pdiScore, array $coefficients): array
    {
        $entityType = data_get($general, 'entity_type');
        $severity = data_get($selection, 'severity');

        if ($entityType === 'privada') {
            [$min, $max] = $severity === 'grave'
                ? [(float) $coefficients['mpriv_grave_min_pct'], (float) $coefficients['mpriv_grave_max_pct']]
                : [(float) $coefficients['mpriv_leve_min_pct'], (float) $coefficients['mpriv_leve_max_pct']];

            $rangeFactor = $min + (($max - $min) * $pdiScore);
            $referenceAmount = (float) data_get($general, 'business_volume_usd');
            $baseAmountUsd = $referenceAmount * $rangeFactor;

            return [
                'severity' => $severity,
                'severity_label' => data_get($selection, 'severity_label'),
                'infraction_label' => data_get($selection, 'label'),
                'range_min' => $min,
                'range_max' => $max,
                'interpolated_factor' => $rangeFactor,
                'reference_amount' => $referenceAmount,
                'base_amount_usd' => $baseAmountUsd,
                'detail' => sprintf(
                    'Entidad privada: %.4f%% a %.4f%% del volumen de negocio, interpolado por PDI.',
                    $min * 100,
                    $max * 100
                ),
            ];
        }

        [$minUnits, $maxUnits] = $severity === 'grave'
            ? [(float) $coefficients['mpub_grave_min_sbu'], (float) $coefficients['mpub_grave_max_sbu']]
            : [(float) $coefficients['mpub_leve_min_sbu'], (float) $coefficients['mpub_leve_max_sbu']];

        $units = $minUnits + (($maxUnits - $minUnits) * $pdiScore);
        $sbuReference = (float) data_get($general, 'sbu_reference');
        $baseAmountUsd = $units * $sbuReference;

        return [
            'severity' => $severity,
            'severity_label' => data_get($selection, 'severity_label'),
            'infraction_label' => data_get($selection, 'label'),
            'range_min' => $minUnits,
            'range_max' => $maxUnits,
            'interpolated_factor' => $units,
            'reference_amount' => $sbuReference,
            'base_amount_usd' => $baseAmountUsd,
            'detail' => sprintf(
                'Entidad publica: %.2f a %.2f SBU, convertido a USD con el SBU del caso.',
                $minUnits,
                $maxUnits
            ),
        ];
    }

    private function calculateSdi(float $iedScore, float $intScore, float $rerScore, array $coefficients): array
    {
        $weightedSum =
            ((float) $coefficients['ied_weight'] * $iedScore) +
            ((float) $coefficients['int_weight'] * $intScore) +
            ((float) $coefficients['rer_weight'] * $rerScore);

        $score = (float) $coefficients['sdi_multiplier'] * $weightedSum;

        return [
            'score' => $score,
            'weighted_sum' => $weightedSum,
            'detail' => sprintf(
                'SDI = %.2f x ((%.2f x IED) + (%.2f x INT) + (%.2f x RER))',
                $coefficients['sdi_multiplier'],
                $coefficients['ied_weight'],
                $coefficients['int_weight'],
                $coefficients['rer_weight']
            ),
        ];
    }

    private function calculateMonteCarlo(array $coefficients, array $cdi, array $ied, array $int, array $rer): array
    {
        $config = SanctionWizardDefinition::monteCarlo();
        $iterations = max(100, (int) data_get($config, 'iterations', 1000));
        $bins = max(8, (int) data_get($config, 'histogram_bins', 16));
        $lambda = (float) data_get($config, 'pert_lambda', 4.0);
        $uncertainty = data_get($config, 'uncertainty', []);

        $samples = [];
        $iedSamples = [];
        $sdiSamples = [];

        for ($i = 0; $i < $iterations; $i++) {
            $simulatedTdp = $this->samplePertAround(
                (float) data_get($ied, 'tdp_score', 0.0),
                (float) data_get($uncertainty, 'tdp_delta', 0.15),
                $lambda
            );
            $simulatedTav = $this->samplePertAround(
                (float) data_get($ied, 'tav_score', 0.0),
                (float) data_get($uncertainty, 'tav_delta', 0.20),
                $lambda
            );
            $simulatedNdv = $this->samplePertAround(
                (float) data_get($ied, 'ndv_score', 0.0),
                (float) data_get($uncertainty, 'ndv_delta', 0.18),
                $lambda
            );
            $simulatedTev = $this->samplePertAround(
                (float) data_get($ied, 'tev_score', 0.0),
                (float) data_get($uncertainty, 'tev_delta', 0.25),
                $lambda
            );

            $iedSimulation = $this->calculateIedFromScores(
                $simulatedTdp,
                $simulatedTav,
                $simulatedNdv,
                $simulatedTev,
                $coefficients
            );

            $sdiSimulation = $this->calculateSdi(
                (float) $iedSimulation['score'],
                (float) data_get($int, 'score', 0.0),
                (float) data_get($rer, 'score', 0.0),
                $coefficients
            );

            $fineAmount = (float) data_get($cdi, 'base_amount_usd', 0.0) * (float) $sdiSimulation['score'];

            $samples[] = $fineAmount;
            $iedSamples[] = (float) $iedSimulation['score'];
            $sdiSamples[] = (float) $sdiSimulation['score'];
        }

        $summary = $this->summarizeSamples($samples);
        $iedSummary = $this->summarizeSamples($iedSamples);
        $sdiSummary = $this->summarizeSamples($sdiSamples);

        return [
            'iterations' => $iterations,
            'summary' => [
                'minimum' => $summary['min'],
                'mean' => $summary['mean'],
                'maximum' => $summary['max'],
                'formatted_minimum' => $this->currency($summary['min']),
                'formatted_mean' => $this->currency($summary['mean']),
                'formatted_maximum' => $this->currency($summary['max']),
            ],
            'ied_summary' => $iedSummary,
            'sdi_summary' => $sdiSummary,
            'histogram' => $this->buildHistogram($samples, $bins),
            'simulated_components' => data_get($config, 'simulated_components', []),
            'detail' => 'Monte Carlo recalcula IED, SDI y la multa usando distribuciones PERT sobre NDV, TDP, TAV y TEV. CDI, INT y RER se mantienen según el caso determinista.',
        ];
    }

    private function summarizeSamples(array $samples): array
    {
        if ($samples === []) {
            return ['min' => 0.0, 'mean' => 0.0, 'max' => 0.0];
        }

        $count = count($samples);

        return [
            'min' => min($samples),
            'mean' => array_sum($samples) / $count,
            'max' => max($samples),
        ];
    }

    private function buildHistogram(array $samples, int $binCount): array
    {
        if ($samples === []) {
            return [
                'labels' => [],
                'frequencies' => [],
                'ranges' => [],
            ];
        }

        $min = min($samples);
        $max = max($samples);

        if ($min === $max) {
            return [
                'labels' => [$this->currency($min)],
                'frequencies' => [count($samples)],
                'ranges' => [
                    [
                        'min' => $min,
                        'max' => $max,
                        'count' => count($samples),
                    ],
                ],
            ];
        }

        $width = ($max - $min) / $binCount;
        $frequencies = array_fill(0, $binCount, 0);
        $ranges = [];
        $labels = [];

        for ($i = 0; $i < $binCount; $i++) {
            $rangeMin = $min + ($i * $width);
            $rangeMax = $i === $binCount - 1 ? $max : $rangeMin + $width;

            $ranges[$i] = [
                'min' => $rangeMin,
                'max' => $rangeMax,
                'count' => 0,
            ];

            $labels[$i] = $this->currency($rangeMin) . ' - ' . $this->currency($rangeMax);
        }

        foreach ($samples as $sample) {
            $index = (int) floor(($sample - $min) / $width);
            if ($index >= $binCount) {
                $index = $binCount - 1;
            }

            $frequencies[$index]++;
            $ranges[$index]['count']++;
        }

        return [
            'labels' => $labels,
            'frequencies' => $frequencies,
            'ranges' => array_values($ranges),
        ];
    }

    private function samplePertAround(float $mode, float $delta, float $lambda): float
    {
        $mode = $this->clamp($mode, 0.0, 1.0);
        $delta = max(0.0, $delta);
        $min = $this->clamp($mode - $delta, 0.0, 1.0);
        $max = $this->clamp($mode + $delta, 0.0, 1.0);

        if ($min === $max) {
            return $mode;
        }

        return $this->samplePert($min, $mode, $max, $lambda);
    }

    private function samplePert(float $min, float $mode, float $max, float $lambda): float
    {
        if ($max <= $min) {
            return $min;
        }

        $mode = min(max($mode, $min), $max);
        $alpha = 1 + $lambda * (($mode - $min) / ($max - $min));
        $beta = 1 + $lambda * (($max - $mode) / ($max - $min));
        $sample = $this->sampleBeta($alpha, $beta);

        return $min + ($sample * ($max - $min));
    }

    private function sampleBeta(float $alpha, float $beta): float
    {
        $x = $this->sampleGamma($alpha);
        $y = $this->sampleGamma($beta);

        if (($x + $y) <= 0.0) {
            return 0.0;
        }

        return $x / ($x + $y);
    }

    private function sampleGamma(float $shape): float
    {
        if ($shape <= 0.0) {
            return 0.0;
        }

        if ($shape < 1.0) {
            $uniform = max($this->randomFloat(), 1.0E-12);

            return $this->sampleGamma($shape + 1.0) * pow($uniform, 1.0 / $shape);
        }

        $d = $shape - (1.0 / 3.0);
        $c = 1.0 / sqrt(9.0 * $d);

        while (true) {
            $x = $this->sampleStandardNormal();
            $v = 1.0 + ($c * $x);

            if ($v <= 0.0) {
                continue;
            }

            $v = $v * $v * $v;
            $u = $this->randomFloat();

            if ($u < 1.0 - (0.0331 * ($x ** 4))) {
                return $d * $v;
            }

            if (log($u) < (0.5 * ($x ** 2)) + $d * (1.0 - $v + log($v))) {
                return $d * $v;
            }
        }
    }

    private function sampleStandardNormal(): float
    {
        $u1 = max($this->randomFloat(), 1.0E-12);
        $u2 = max($this->randomFloat(), 1.0E-12);

        return sqrt(-2.0 * log($u1)) * cos(2.0 * M_PI * $u2);
    }

    private function randomFloat(): float
    {
        return mt_rand() / mt_getrandmax();
    }

    private function subjectCountScore(int $count): float
    {
        foreach (SanctionWizardDefinition::dataSubjectCountBands() as $band) {
            $min = (int) $band['min'];
            $max = $band['max'] !== null ? (int) $band['max'] : null;

            if ($count >= $min && ($max === null || $count <= $max)) {
                return (float) $band['score'];
            }
        }

        return 0.0;
    }

    private function dataVolumeScore(float $amount): float
    {
        foreach (SanctionWizardDefinition::dataVolumeBands() as $band) {
            $min = (float) $band['min'];
            $max = $band['max'] !== null ? (float) $band['max'] : null;

            if ($amount >= $min && ($max === null || $amount <= $max)) {
                return (float) $band['score'];
            }
        }

        return 0.0;
    }

    private function clamp(float $value, float $min, float $max): float
    {
        return min(max($value, $min), $max);
    }

    private function currency(float $amount): string
    {
        return 'USD ' . number_format($amount, 2, '.', ',');
    }
}
