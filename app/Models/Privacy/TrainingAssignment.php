<?php

namespace App\Models\Privacy;

use Illuminate\Database\Eloquent\Model;

class TrainingAssignment extends Model
{
    protected $table = 'privacy.training_assignment';
    protected $primaryKey = 'assignment_id';
    public $timestamps = false;

    protected $fillable = [
        'course_id',
        'user_id',
        'assigned_at',
        'due_at',
        'status',
    ];

    public function course()
    {
        return $this->belongsTo(
            TrainingCourse::class,
            'course_id',
            'course_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(
            \App\Models\IAM\AppUser::class,
            'user_id',
            'user_id'
        );
    }
}
