<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecognitionAndAwards extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'recognition_awards';
    protected $fillable = [
        'added_by',
        'award_type',
        'awardee_name',
        'award',
        'module',
        'agency',
        'date_received'
    ];

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'added_by');
    } 

}
