<?php

namespace App\Models\IAM;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AppUser extends Authenticatable
{
    protected $table = 'iam.app_user';
    protected $primaryKey = 'user_id';
    public $timestamps = false;

    protected $fillable = [
        'unit_id',
        'email',
        'cedula',
        'full_name',
        'status',
        'provincia',
        'canton',
        'password',
        'last_login_at',
        'created_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'last_login_at' => 'datetime',
        'created_at' => 'datetime',
        'password' => 'hashed'
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'iam.user_role',
            'user_id',
            'role_id'
        );
    }
}
