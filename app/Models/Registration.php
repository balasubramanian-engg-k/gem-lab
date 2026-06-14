<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $appends = ['status_text']; // Append custom field

    protected $casts = [
        'user_status' => 'integer',
    ];

    protected $fillable = [
        'mdcc_id','first_name', 'last_name', 'email', 'mobilenumber', 
        'parent_name', 'relationship', 'mother_tounge', 'gender', 
        'date_of_birth', 'address', 'state', 'district', 'pincode', 
        'photo', 'birth_certificate', 'middle_name', 'title', 'fide_id', 'aicp_id', 'player_type', 'dob_registration', 'status', 'user_status', 'tnsca_id', 'registration_type', 'taluk'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($registration) {
            $maxId = self::max('id') + 1; // Get max ID and add 1
            $registration->mdcc_id = 'MDCC000' . $maxId;
        });
    }

    public function getStatusTextAttribute()
    {
        return $this->attributes['status'] == 1 ? 'Active' : 'Inactive';
    }

}