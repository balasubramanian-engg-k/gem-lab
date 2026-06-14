<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Silver extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'rate_per_gram',
    ];
}
