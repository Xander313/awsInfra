<?php

namespace App\Models\Privacy;

use Illuminate\Database\Eloquent\Model;

class TrainingResult extends Model
{
    protected $table = 'privacy.training_result';
    protected $primaryKey = 'result_id';
    public $timestamps = false;

    protected $fillable = [
        'assign_id',
        'completed_at',
        'score',
        'certificate_doc_ver_id',
    ];

    public function assignment()
    {
        return $this->belongsTo(
            TrainingAssignment::class,
            'assign_id',   // ← ESTE ES EL NOMBRE REAL EN LA BD
            'assign_id'
        );
    }
}
