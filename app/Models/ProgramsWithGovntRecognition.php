<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramsWithGovntRecognition extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'programs_with_government_recognition';
    protected $fillable = [
        'module',
        'added_by',
        'program_id',
        'status_id',
        'copc_number',
        'date'
    ];

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'added_by');
    } 

    public function program_dtls(){
        return $this->belongsTo(Programs::class, 'program_id');
    }

    public function status_dtls(){
        return $this->belongsTo(ProgramsWithGovntRecognitionStatuses::class, 'status_id');
    }
}
