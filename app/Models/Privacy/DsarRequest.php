<?php

namespace App\Models\Privacy;

use Illuminate\Database\Eloquent\Model;

class DsarRequest extends Model
{
    protected $table = 'privacy.dsar_request';
    protected $primaryKey = 'dsar_id';
    public $timestamps = false;

    protected $fillable = [
        'org_id',
        'subject_id',
        'request_type',
        'channel',
        'received_at',
        'due_at',
        'status',
        'assigned_to_user_id',
        'resolution_summary',
        'closed_at',
    ];

    protected $casts = [
        'received_at' => 'date',
        'due_at'      => 'date',
        'closed_at'   => 'date',
    ];

    // 👉 TITULAR
    public function subject()
    {
        return $this->belongsTo(
            DataSubject::class,
            'subject_id',
            'subject_id'
        );
    }

    // 👉 ASIGNADO
    public function assignedUser()
    {
        return $this->belongsTo(
            \App\Models\IAM\AppUser::class,
            'assigned_to_user_id',
            'user_id'
        );
    }

    // 👉 ESTADO EN ESPAÑOL (ACCESSOR)
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending'     => 'Pendiente',
            'in_progress' => 'En proceso',
            'closed'      => 'Cerrado',
            default       => ucfirst($this->status),
        };
    }

    public function evidences()
    {
        return $this->hasMany(DsarEvidence::class, 'dsar_id', 'dsar_id');
    }
}



