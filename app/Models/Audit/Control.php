<?php

namespace App\Models\Audit;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Org;
use App\Models\IAM\AppUser;

class Control extends Model
{
    protected $table = 'audit.control';
    protected $primaryKey = 'control_id';
    public $timestamps = false;

    protected $fillable = [
        'org_id',
        'code',
        'name',
        'control_type',
        'description',
        'owner_user_id',
    ];

    public function org()
    {
        return $this->belongsTo(Org::class, 'org_id');
    }

    public function owner()
    {
        return $this->belongsTo(AppUser::class, 'owner_user_id');
    }

    public function findings()
    {
        return $this->hasMany(AuditFinding::class, 'control_id');
    }
}
