<?php

namespace App\Models\Privacy;

use Illuminate\Database\Eloquent\Model;

class DataCategory extends Model
{
    protected $table = 'privacy.data_category';
    protected $primaryKey = 'data_cat_id';
    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
        'is_sensitive',
        'description',
    ];
}