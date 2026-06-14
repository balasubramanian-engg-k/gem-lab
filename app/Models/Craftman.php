<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Craftman extends Model
{
    protected $table = 'craftmen';

    protected $fillable = ['name', 'phone'];

    public function labourReceipts(): HasMany
    {
        return $this->hasMany(LabourReceipt::class, 'craftman_id');
    }
}
