<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Linkages extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'linkages';
    protected $fillable = [
        'added_by',
        'agency',
        'linkage_nature',
        'activity_title',
        'module',
        'venue',
        'date',
        'attendees',
        'facilitators'
    ];

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'added_by');
    } 

}
