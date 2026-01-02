<?php

namespace App\Models\Privacy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\Org; // AÑADE ESTE IMPORT

class DataSubject extends Model
{
    use HasFactory;

    protected $table = 'privacy.data_subject';
    protected $primaryKey = 'subject_id';
    public $timestamps = false;

    protected $fillable = [
        'org_id',
        'id_type',
        'id_number',
        'full_name',
        'email',
        'phone',
        'address',
        'verified_level'
    ];

    protected $casts = [
        'verified_level' => 'integer',
        'created_at' => 'datetime'
    ];

    // AÑADE ESTA RELACIÓN
    public function org()
    {
        return $this->belongsTo(\App\Models\Core\Org::class, 'org_id', 'org_id');
    }

    public function consents()
    {
        return $this->hasMany(Consent::class, 'subject_id', 'subject_id');
    }

    public function activeConsent()
    {
        return $this->consents()
            ->whereNull('revoked_at')
            ->latest('given_at')
            ->first();
    }
}