<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EducationalAttainment extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'educational_attainment';
    protected $fillable = [
        'added_by',
        'number_of_faculty',
        'school_year',
        'semester',
        'module',
        'education'
    ];

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'added_by');
    }

    public function education_dtls(){
        return $this->belongsTo(EducationAttainmentType::class, 'education');
    }
}
