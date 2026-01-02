<?php

namespace App\Models\IAM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $table = 'iam.permission';
    protected $primaryKey = 'perm_id';
    public $timestamps = false;

    protected $fillable = [
        'code',
        'description'
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'iam.role_permission',
            'perm_id',
            'role_id'
        );
    }
}
