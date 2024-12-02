<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LicensureExamnination extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'licensure_examination';
    protected $fillable = [
        'module',
        'added_by',
        'examination_type',
        'cvsu_passing_rate',
        'national_passing_rate',
        'exam_date',
        'cvsu_total_passer',
        'cvsu_total_takers',
        'national_total_passer',
        'national_total_takers'
    ];

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'added_by');
    }

    public function examination_type_dtls(){
        return $this->belongsTo(ExaminationType::class, 'examination_type');
    }
}
