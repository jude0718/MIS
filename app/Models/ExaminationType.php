<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExaminationType extends Model
{
    use HasFactory;
    protected $table = 'examination_type';
    protected $fillable = [
        'type'
    ];

    public function licensure_dtls(){
        return $this->hasMany(LicensureExamnination::class, 'examination_type', 'id');
    }
}
