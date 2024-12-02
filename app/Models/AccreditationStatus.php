<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccreditationStatus extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'accreditation_status';
    protected $fillable = [
        'module',
        'added_by',
        'program_id',
        'status_id',
        'visit_date'
    ];

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'added_by');
    } 

    public function status_dtls(){
        return $this->belongsTo(AccreditationStatusStatuses::class, 'status_id');
    }

    public function program_dtls(){
        return $this->belongsTo(Programs::class, 'program_id');
    }
}
