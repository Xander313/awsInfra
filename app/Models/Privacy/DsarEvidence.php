<?php

namespace App\Models\Privacy;

use Illuminate\Database\Eloquent\Model;
use App\Models\Privacy\DsarRequest;

class DsarEvidence extends Model
{
    protected $table = 'privacy.dsar_evidence';
    protected $primaryKey = 'dsar_ev_id';

    public $timestamps = false;

    protected $fillable = [
        'dsar_id',
        'doc_ver_id',
        'description',
        'attached_at',
    ];

    protected $casts = [
        'attached_at' => 'datetime',
    ];

    // Relación con la solicitud DSAR
    public function dsarRequest()
    {
        return $this->belongsTo(DsarRequest::class, 'dsar_id', 'dsar_id');
    }
}

