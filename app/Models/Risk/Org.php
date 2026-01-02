<?php

namespace App\Models\Risk;

use Illuminate\Database\Eloquent\Model;

class Org extends Model
{
    protected $table = 'core.org';
    protected $primaryKey = 'org_id';
    public $incrementing = true;
    protected $keyType = 'int';

    // La tabla tiene created_at pero no updated_at
    public $timestamps = false;

    protected $fillable = [
        'name',
        'ruc',
        'industry',
        'created_at',
    ];
}
