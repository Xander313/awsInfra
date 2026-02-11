<?php

namespace App\Models\IAM;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRoleHistory extends Model
{
    protected $table = 'iam.user_role_history';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role_id',
        'action',
        'assigned_by',
        'created_at'
    ];

    // ✅ ESTA ES LA PARTE QUE SOLUCIONA EL ERROR
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(AppUser::class, 'assigned_by', 'user_id');
    }
}