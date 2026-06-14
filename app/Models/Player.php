<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'title',
        'father_mother_name', 'gender', 'state', 'district',
        'email', 'mobile_number', 'mother_tongue', 'address', 
        'pincode', 'date_of_birth', 'date_of_birth_registration',
        'fide_id', 'aicf_id', 'player_type', 'passport_photo', 'birth_certificate'
    ];
}
