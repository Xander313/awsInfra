<?php

namespace App\Models\Risk;

use App\Models\Core\Org;
use Illuminate\Database\Eloquent\Model;

class SanctionSimulation extends Model
{
    protected $table = 'risk.sanction_simulation';
    protected $primaryKey = 'simulation_id';

    protected $fillable = [
        'org_id',
        'incident_id',
        'org_name',
        'created_by_user_id',
        'created_by_user_name',
        'rule_set',
        'case_name',
        'entity_type',
        'company_role',
        'deterministic_fine_usd',
        'monte_carlo_min_usd',
        'monte_carlo_mean_usd',
        'monte_carlo_max_usd',
        'wizard_snapshot',
        'incident_snapshot',
        'result_snapshot',
        'documentation_snapshot',
        'coefficient_snapshot',
    ];

    protected $casts = [
        'org_id' => 'integer',
        'incident_id' => 'integer',
        'created_by_user_id' => 'integer',
        'deterministic_fine_usd' => 'decimal:2',
        'monte_carlo_min_usd' => 'decimal:2',
        'monte_carlo_mean_usd' => 'decimal:2',
        'monte_carlo_max_usd' => 'decimal:2',
        'wizard_snapshot' => 'array',
        'incident_snapshot' => 'array',
        'result_snapshot' => 'array',
        'documentation_snapshot' => 'array',
        'coefficient_snapshot' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function org()
    {
        return $this->belongsTo(Org::class, 'org_id', 'org_id');
    }

    public function incident()
    {
        return $this->belongsTo(Incident::class, 'incident_id', 'incident_id');
    }

    public function officialForIncident()
    {
        return $this->hasOne(Incident::class, 'official_simulation_id', 'simulation_id');
    }
}
