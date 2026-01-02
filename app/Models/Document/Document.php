<?php

namespace App\Models\Document;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Org;
use App\Models\IAM\AppUser;

class Document extends Model
{
    protected $table = 'privacy.document';
    protected $primaryKey = 'doc_id';
    public $timestamps = false;

    protected $fillable = [
        'org_id',
        'title',
        'doc_type',
        'classification',
        'created_by',
    ];

    // Relación con versiones
    public function versions()
    {
        
        return $this->hasMany(DocumentVersion::class, 'doc_id', 'doc_id')
                    ->orderByDesc('version_no');
    }

    public function activeVersion()
    {
        return $this->hasOne(DocumentVersion::class, 'doc_id', 'doc_id')
                    ->where('active_flag', true)
                    ->orderByDesc('version_no');
    }

    // Document pertenece a una organización (core.org)
    public function org()
    {
        return $this->belongsTo(Org::class, 'org_id', 'org_id');
    }

    // Document pertenece al usuario que lo creó (iam.app_user)
    public function creator()
    {
        return $this->belongsTo(AppUser::class, 'created_by', 'user_id');
    }
}
