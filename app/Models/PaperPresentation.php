<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaperPresentation extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'paper_presentation';
    protected $fillable = [
        'added_by',
        'presentation_type',
        'conference_name',
        'paper_name',
        'module',
        'presenter_name',
        'date',
        'venue'
    ];

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'added_by');
    } 
}
