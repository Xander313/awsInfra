<?php

namespace App\Models\Risk;

use Illuminate\Database\Eloquent\Model;
use App\Models\Privacy\ProcessingActivity;

class Dpia extends Model
{
    protected $table = 'risk.dpia';
    protected $primaryKey = 'dpia_id';
    public $timestamps = false;

    protected $fillable = [
        'pa_id',
        'initiated_at',
        'status',
        'summary',
    ];

    protected $casts = [
        'initiated_at' => 'datetime',
    ];

    public function processingActivity()
    {
        return $this->belongsTo(ProcessingActivity::class, 'pa_id', 'pa_id');
    }

    /**
     * N:M con Risk via risk.dpia_risk (incluye rationale en pivot)
     */
    public function risks()
    {
        return $this->belongsToMany(
            Risk::class,
            'risk.dpia_risk',
            'dpia_id',
            'risk_id'
        )->withPivot('rationale');
    }
}
