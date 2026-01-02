<?php

namespace App\Models\Privacy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consent extends Model
{
    use HasFactory;

    protected $table = 'privacy.consent';
    protected $primaryKey = 'consent_id';
    public $timestamps = false;

    protected $fillable = [
        'subject_id',
        'notice_ver_id',
        'purpose_id',
        'given_at',
        'revoked_at'
    ];

    protected $casts = [
        'given_at' => 'datetime',
        'revoked_at' => 'datetime'
    ];

    public function dataSubject()
    {
        return $this->belongsTo(DataSubject::class, 'subject_id', 'subject_id');
    }

    public function isActive()
    {
        return is_null($this->revoked_at);
    }
}