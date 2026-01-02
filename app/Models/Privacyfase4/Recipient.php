<?php

namespace App\Models\Privacyfase4;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Core\Org;

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
        'is_third_party'
    ];

    protected $casts = [
        'is_third_party' => 'boolean',
    ];

    public function org(): BelongsTo
    {
        return $this->belongsTo(Org::class, 'org_id', 'org_id');
    }
}