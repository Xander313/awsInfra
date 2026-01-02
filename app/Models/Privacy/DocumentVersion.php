<?php

namespace App\Models\Privacy;

use Illuminate\Database\Eloquent\Model;

class DocumentVersion extends Model
{
    protected $table = 'privacy.document_version';
    protected $primaryKey = 'doc_ver_id';
    public $timestamps = false;

    protected $fillable = [
        // solo lo que exista en la tabla
    ];
}

