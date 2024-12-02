<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InfrastructureDevelopment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'infrastracture';
    protected $fillable = [
        'added_by',
        'infrastracture',
        'status',
        'module'
    ];

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'added_by');
    }
}
