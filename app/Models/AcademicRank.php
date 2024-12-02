<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicRank extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'academic_rank';
    protected $fillable = [
        'added_by',
        'number_of_faculty',
        'school_year',
        'semester',
        'module',
        'academic_rank'
    ];

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'added_by');
    } 

    public function academic_rank_dtls(){
        return $this->belongsTo(AcademicRankType::class, 'academic_rank');
    } 
}
