<?php

namespace App\Models\Privacy;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'privacy.country';
    protected $primaryKey = 'country_id';
    public $timestamps = false;

    protected $fillable = [
        'iso_code',
        'name',
    ];

    // Un país puede tener muchas transfers
    public function transfers()
    {
        return $this->hasMany(Transfer::class, 'country_id');
    }
}
