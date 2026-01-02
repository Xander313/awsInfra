<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Org;
use App\Models\IAM\AppUser;

class Audit extends Model
{
    protected $table = 'audit.audit';
    protected $primaryKey = 'audit_id';
    public $timestamps = false;

    protected $fillable = [
        'org_id',
        'audit_type',
        'scope',
        'auditor_user_id',
        'planned_at',
        'executed_at',
        'status'
    ];

    public function org()
    {
        return $this->belongsTo(Org::class, 'org_id');
    }

    public function auditor()
    {
        return $this->belongsTo(AppUser::class, 'auditor_user_id');
    }

    public function findings()
    {
        return $this->hasMany(AuditFinding::class, 'audit_id');
    }
}
