<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GraduateDetails extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'graduates_dtls';
    protected $fillable = [
        'created_by',
        'program_id',
        'number_of_student',
        'graduate_hdr'
    ];

    public function program_dtls(){
        return $this->belongsTo(Programs::class, 'program_id');
    }
}
