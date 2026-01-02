<?php

namespace App\Models\Privacyfase4;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Privacyfase4\DataStore;  
use App\Models\Core\Org;
use App\Models\IAM\AppUser; //Cambiar a la ruta de la clase

class System extends Model
{
    protected $table = 'privacy.system';
    protected $primaryKey = 'system_id';
    public $timestamps = false;

    protected $fillable = [
        'org_id',
        'name',
        'type',
        'hosting',
        'owner_user_id',
        'criticality',
        'description'
    ];

    // Data Stores del sistema
    public function dataStores(): HasMany
    {
        return $this->hasMany(DataStore::class, 'system_id', 'system_id');
    }

    // Organización
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Org::class, 'org_id', 'org_id');
    }

    // Responsable (iam.app_user)
    public function owner(): BelongsTo
    {
        return $this->belongsTo(AppUser::class, 'owner_user_id', 'user_id');
    }
}
