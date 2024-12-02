<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NatureOfAppointment extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'nature_appointment';
    protected $fillable = [
        'added_by',
        'number_of_faculty',
        'school_year',
        'semester',
        'module',
        'apointment_nature'
    ];

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'added_by');
    } 

    public function apointment_nature_dtls(){
        return $this->belongsTo(NatureOfAppointmentType::class, 'apointment_nature');
    } 
}
