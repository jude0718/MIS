<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AwardsDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'awards_dtls';
    protected $fillable = [
        'awards_hdr',
        'game_placement',
        'award_details',
        'grantees_name',
        'medal_type',
        'program_id'
        
    ];
}
