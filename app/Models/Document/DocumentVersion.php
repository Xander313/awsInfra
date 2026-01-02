<?php

namespace App\Models\Document;

use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    protected $table = 'privacy.document_version';
    protected $primaryKey = 'doc_ver_id';
    public $timestamps = false;

    protected $fillable = [
        'doc_id',
        'version_no',
        'file_uri',
        'checksum',
        'created_at',
        'active_flag',
    ];

    protected $casts = [
        'active_flag' => 'boolean',
        'created_at'  => 'datetime',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'doc_id', 'doc_id');
    }
}
