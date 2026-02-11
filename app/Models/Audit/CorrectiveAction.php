<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\Audit\AuditFinding;
use App\Models\IAM\AppUser;

class CorrectiveAction extends Model
{
    protected $table = 'audit.corrective_action';
    protected $primaryKey = 'ca_id';
    public $timestamps = false;

    protected $fillable = [
        'finding_id',
        'owner_user_id',
        'due_at',
        'status',
        'closed_at',
        'outcome'
    ];

    public function getRouteKeyName()
    {
        return 'ca_id';
    }

    public function finding()
    {
        return $this->belongsTo(AuditFinding::class, 'finding_id', 'finding_id');
    }

    public function owner()
    {
        return $this->belongsTo(AppUser::class, 'owner_user_id', 'user_id');
    }
}
