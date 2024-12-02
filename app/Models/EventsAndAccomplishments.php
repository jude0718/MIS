<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventsAndAccomplishments extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'accomplishments';
    protected $fillable = [
        'added_by',
        'faculty',
        'program_id',
        'program_dtls',
        'module',
        'university',
        'start_date',
        'end_date'
    ];

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'added_by');
    }

    public function program_details(){
        return $this->belongsTo(Programs::class, 'program_id');
    }
}
