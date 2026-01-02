<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\Audit\Audit;
use App\Models\Audit\Control;

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
