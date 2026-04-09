<?php

namespace App\Models\Risk;

use App\Models\Document\DocumentVersion;
use Illuminate\Database\Eloquent\Model;

class IncidentDocument extends Model
{
    protected $table = 'risk.incident_document';
    protected $primaryKey = 'incident_doc_id';
    public $timestamps = false;

    protected $fillable = [
        'incident_id',
        'doc_ver_id',
        'relation_type',
        'description',
        'attached_at',
    ];

    protected $casts = [
        'attached_at' => 'datetime',
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class, 'incident_id', 'incident_id');
    }

    public function documentVersion()
    {
        return $this->belongsTo(DocumentVersion::class, 'doc_ver_id', 'doc_ver_id');
    }
}
