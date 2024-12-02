<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GraduateHeader extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'graduates_hdr';
    protected $fillable = [
        'created_by',
        'program_id',
        'number_of_student',
        'semester',
        'graduate_date',
        'school_year',
        'program_id',
        'number_of_student',
        'module'
    ];

    public function program_dtls(){
        return $this->belongsTo(Programs::class, 'program_id');
    }

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function graduate_dtls(){
        return $this->hasMany(GraduateDetails::class, 'graduate_hdr', 'id');
    }
}
