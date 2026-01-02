<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use App\Models\Audit\Audit;
use App\Models\Audit\Control;

class Org extends Model
{
    protected $table = 'core.org';
    protected $primaryKey = 'org_id';
    public $timestamps = false; // no usas updated_at
    protected $fillable = ['name', 'ruc', 'industry', 'created_at'];

    // Esto convierte created_at a Carbon automáticamente
    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relación: Una organización tiene muchas auditorías
    public function audits()
    {
        return $this->hasMany(Audit::class, 'org_id');
    }

    // Relación: Una organización tiene muchos controles
    public function controls()
    {
        return $this->hasMany(Control::class, 'org_id');
    }
}
