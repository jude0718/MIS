<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Programs extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'programs';
    protected $fillable = [
        'created_by',
        'program',
        'abbreviation'
    ];

    public function enrollment_dtls(){
        return $this->hasMany(Enrollment::class, 'program_id', 'id');
    }

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'created_by');
    }
}
