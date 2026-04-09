<?php

namespace App\Models\Core;

use App\Models\IAM\AppUser;
use Illuminate\Database\Eloquent\Model;

class OrgRegulatoryProfile extends Model
{
    protected $table = 'core.org_regulatory_profile';
    protected $primaryKey = 'org_profile_id';

    protected $fillable = [
        'org_id',
        'entity_type',
        'business_volume_usd',
        'sbu_reference',
        'reference_year',
        'notes',
        'updated_by_user_id',
    ];

    protected $casts = [
        'business_volume_usd' => 'decimal:2',
        'sbu_reference' => 'decimal:2',
        'reference_year' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function org()
    {
        return $this->belongsTo(Org::class, 'org_id', 'org_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(AppUser::class, 'updated_by_user_id', 'user_id');
    }
}
