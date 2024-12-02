<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AwardsHeader extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'awards_hdr';
    protected $fillable = [
        'created_by',
        'award',
        'granting_agency',
        'module',
        'start_year',
        'end_year'
    ];

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function award_dtls(){
        return $this->hasMany(AwardsDetails::class, 'awards_hdr', 'id');
    }
    
}
