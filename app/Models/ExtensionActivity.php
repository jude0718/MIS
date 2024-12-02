<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExtensionActivity extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'extension_activity';
    protected $fillable = [
        'added_by',
        'extension_activity',
        'extensionist',
        'number_of_beneficiaries',
        'module',
        'partner_agency',
        'activity_date'
       
    ];

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'added_by');
    } 
}
