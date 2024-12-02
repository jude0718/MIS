<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'enrollment';
    protected $fillable = [
        'created_by',
        'number_of_student',
        'program_id',
        'school_year',
        'semester',
        'module'
    ];

    public function program_dtls(){
        return $this->belongsTo(Programs::class, 'program_id');
    }

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'created_by');
    } 
}
