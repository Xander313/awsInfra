<?php

namespace App\Models\IAM;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RolePermission extends Pivot
{
    protected $table = 'iam.role_permission';
    public $incrementing = false;
    protected $primaryKey = [
        'role_id',
        'perm_id'
    ];
}
