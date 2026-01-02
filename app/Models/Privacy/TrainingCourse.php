<?php

namespace App\Models\Privacy;

use Illuminate\Database\Eloquent\Model;

class TrainingCourse extends Model
{
    protected $table = 'privacy.training_course';
    protected $primaryKey = 'course_id';
    public $timestamps = false;

    protected $fillable = [
        'org_id',
        'name',
        'mandatory_flag',
        'renewal_days',
    ];
}
