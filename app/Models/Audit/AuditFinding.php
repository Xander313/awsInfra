<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;

class AuditFinding extends Model
{
    protected $table = 'audit.audit_finding';
    protected $primaryKey = 'finding_id';
    public $timestamps = false;

    protected $fillable = [
        'audit_id',
        'control_id',
        'severity',
        'description',
        'status'
    ];

    // ✅ Para que {finding} funcione con finding_id en rutas tipo resource
    public function getRouteKeyName()
    {
        return 'finding_id';
    }

    public function audit()
    {
        return $this->belongsTo(Audit::class, 'audit_id');
    }

    public function control()
    {
        return $this->belongsTo(Control::class, 'control_id');
    }

    public function correctiveActions()
    {
        return $this->hasMany(CorrectiveAction::class, 'finding_id');
    }
}
