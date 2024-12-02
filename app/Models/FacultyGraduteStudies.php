<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacultyGraduteStudies extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'faculty_graduate_studies';
    protected $fillable = [
        'added_by',
        'faculty_name',
        'degree',
        'institution',
        'module',
        'date_of_graduation'
    ];

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'added_by');
    } 
}
