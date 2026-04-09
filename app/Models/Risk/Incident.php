<?php

namespace App\Models\Risk;

use App\Models\Core\Org;
use App\Models\Document\DocumentVersion;
use App\Models\IAM\AppUser;
use App\Models\Privacy\ProcessingActivity;
use App\Models\Privacyfase4\System;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $table = 'risk.incident';
    protected $primaryKey = 'incident_id';

    protected $fillable = [
        'org_id',
        'official_simulation_id',
        'incident_code',
        'title',
        'description',
        'incident_type',
        'status',
        'severity',
        'company_role',
        'system_id',
        'pa_id',
        'occurred_at',
        'detected_at',
        'closed_at',
        'data_subject_count',
        'data_volume_amount',
        'affected_data_types',
        'confidentiality_impact',
        'integrity_impact',
        'availability_impact',
        'vulnerable_groups_flag',
        'created_by_user_id',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'detected_at' => 'datetime',
        'closed_at' => 'datetime',
        'data_subject_count' => 'integer',
        'data_volume_amount' => 'decimal:2',
        'affected_data_types' => 'array',
        'vulnerable_groups_flag' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function org()
    {
        return $this->belongsTo(Org::class, 'org_id', 'org_id');
    }

    public function system()
    {
        return $this->belongsTo(System::class, 'system_id', 'system_id');
    }

    public function processingActivity()
    {
        return $this->belongsTo(ProcessingActivity::class, 'pa_id', 'pa_id');
    }

    public function creator()
    {
        return $this->belongsTo(AppUser::class, 'created_by_user_id', 'user_id');
    }

    public function incidentDocuments()
    {
        return $this->hasMany(IncidentDocument::class, 'incident_id', 'incident_id');
    }

    public function documentVersions()
    {
        return $this->belongsToMany(
            DocumentVersion::class,
            'risk.incident_document',
            'incident_id',
            'doc_ver_id'
        )->withPivot(['incident_doc_id', 'relation_type', 'description', 'attached_at']);
    }

    public function sanctionSimulations()
    {
        return $this->hasMany(SanctionSimulation::class, 'incident_id', 'incident_id');
    }

    public function officialSimulation()
    {
        return $this->belongsTo(SanctionSimulation::class, 'official_simulation_id', 'simulation_id');
    }
}
