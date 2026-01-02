<?php

namespace App\Models\Privacyfase4;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Privacyfase4\System;

class DataStore extends Model
{
    protected $table = 'privacy.data_store';
    protected $primaryKey = 'store_id';
    public $timestamps = false;

    protected $fillable = [
        'system_id',
        'name',
        'store_type',
        'location',
        'encryption_flag',
        'backup_flag'
    ];

    public function system(): BelongsTo
    {
        return $this->belongsTo(System::class, 'system_id', 'system_id');
    }
}
