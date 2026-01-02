<?php

namespace App\Models\Risk;

use Illuminate\Database\Eloquent\Model;

class Risk extends Model
{
    protected $table = 'risk.risk';
    protected $primaryKey = 'risk_id';
    public $timestamps = false;

    protected $fillable = [
        'org_id',
        'name',
        'description',
        'risk_type',
        'status',
    ];

    /**
     * N:M con DPIA via risk.dpia_risk (incluye rationale en pivot)
     */
    public function dpias()
    {
        return $this->belongsToMany(
            Dpia::class,
            'risk.dpia_risk',
            'risk_id',
            'dpia_id'
        )->withPivot('rationale');
    }
}
