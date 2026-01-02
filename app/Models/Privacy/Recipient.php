<?php

namespace App\Models\Privacy;

use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    protected $table = 'privacy.recipient';
    protected $primaryKey = 'recipient_id';
    public $timestamps = false;

    protected $fillable = [
        'org_id',
        'name',
        'recipient_type',
        'contact_email',
        'is_third_party',
    ];

    // Un recipient puede estar en muchas transfers
    public function transfers()
    {
        return $this->hasMany(Transfer::class, 'recipient_id');
    }
/*
    // Relación con la organización
    public function organization()
    {
        return $this->belongsTo(\App\Models\Core\Org::class, 'org_id', 'org_id');
    }*/
}
