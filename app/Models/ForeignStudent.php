<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForeignStudent extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'foreign_student';
    protected $fillable = [
        'created_by',
        'program_id',
        'school_year',
        'country',
        'semester',
        'number_of_student',
        'module'
    ];

    public function program_dtls(){
        return $this->belongsTo(Programs::class, 'program_id');
    }

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'created_by');
    }
}
