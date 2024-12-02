<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacultyTVET extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'faculty_with_tvet';
    protected $fillable = [
        'module',
        'added_by',
        'certification_type',
        'certificate_details',
        'date',
        'certificate_holder'
    ];

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'added_by');
    }

    public function certification_type_dtls(){
        return $this->belongsTo(CertificateType::class, 'certification_type');
    }
}
