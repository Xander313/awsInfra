<?php

namespace App\Models\Privacy;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $table = 'privacy.transfer';
    protected $primaryKey = 'transfer_id';
    public $timestamps = false;

    protected $fillable = [
        'pa_id',
        'recipient_id',
        'country_id',
        'transfer_type',
        'safeguard',
        'legal_basis_text',
    ];
    public function activity()
    {
        return $this->belongsTo(ProcessingActivity::class, 'pa_id');
    }


    public function transfers()
    {
        return $this->hasMany(Transfer::class, 'pa_id');
    }
}
