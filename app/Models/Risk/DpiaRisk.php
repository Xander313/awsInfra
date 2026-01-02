<?php

namespace App\Models\Risk;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DpiaRisk extends Pivot
{
    protected $table = 'risk.dpia_risk';
    public $timestamps = false;

    // La tabla pivote tiene PK compuesta (dpia_id, risk_id)
    public $incrementing = false;

    protected $fillable = [
        'dpia_id',
        'risk_id',
        'rationale',
    ];
}
