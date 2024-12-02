<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacultyScholars extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'faculty_scholars';
    protected $fillable = [
        'added_by',
        'faculty_name',
        'program',
        'institution',
        'module',
        'scholarship'
    ];

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'added_by');
    } 
}
