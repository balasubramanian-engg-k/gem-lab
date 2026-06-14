<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gem extends Model
{
    use HasFactory;

    protected $fillable = [
        'summary_no',
        'description',
        'gross_weight',
        'diamond_weight',
        'weight_type',
        'color',
        'clarity',
        'finish',
        'stone_type',
        'shape',
        'image',
        'comment', // New field
        'certificate_generated',
        'created_by',
    ];

    /**
     * Get the user who created this gem.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($gem) {
            $gem->summary_no = self::generateRandomId();
            $gem->color = '';
            $gem->finish = '';
            $gem->stone_type = '';
            $gem->shape = '';
            
            // Automatically set created_by to authenticated user if not set
            if (empty($gem->created_by) && auth()->check()) {
                $gem->created_by = auth()->id();
            }
        });
    }

    protected static function generateRandomId(): string
    {
        $prefix = 'GHC'; // 3 characters
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomPart = '';

        for ($i = 0; $i < 7; $i++) {   // exactly 7 chars
            $randomPart .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $prefix . $randomPart; // total 10 chars
    }
}
