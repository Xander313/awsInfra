<?php

namespace App\Models\IAM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $table = 'iam.role';
    protected $primaryKey = 'role_id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description'
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'iam.role_permission',
            'role_id',
            'perm_id'
        );
    }
}
