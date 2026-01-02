<?php

namespace App\Models\Privacy;

use Illuminate\Database\Eloquent\Model;

class ProcessingActivity extends Model
{
    protected $table = 'privacy.processing_activity';
    protected $primaryKey = 'pa_id';
    public $timestamps = false;

    protected $fillable = [
        'org_id',
        'owner_unit_id',
        'name',
    ];

    
 
    // 1:N Retención
    public function retentionRules()
    {
        return $this->hasMany(RetentionRule::class, 'pa_id');
    }

    // 1:N Transferencias
    public function transfers()
    {
        return $this->hasMany(Transfer::class, 'pa_id');
    }


    // Relación N:M con DataCategory
    public function categories()
    {
        return $this->belongsToMany(
            DataCategory::class,     // Modelo relacionado
            'privacy.pa_data_category', // Tabla pivote
            'pa_id',                // FK de esta tabla en pivote
            'data_cat_id'           // FK del modelo relacionado en pivote
        )->withPivot('collection_source'); // si quieres usar collection_source
    }

    // Relación RetentionRule
    /*
    public function retentionRules()
    {
        return $this->hasMany(RetentionRule::class, 'pa_id', 'pa_id');
    }

    // Relación Transfer
    public function transfers()
    {
        return $this->hasMany(Transfer::class, 'pa_id', 'pa_id');
    }*/
}
