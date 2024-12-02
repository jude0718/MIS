<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;
    protected $table = 'notifications';
    protected $fillable = [
        'transact_by',
        'sent_to',
        'message_to_self',
        'message_to_others',
        'read_at',
        'created_at',
        'updated_at',
    ];

    public function transact_by_dtls(){
        return $this->belongsTo(User::class, 'transact_by');
    }

    public function sent_to_dtls(){
        return $this->belongsTo(User::class, 'sent_to');
    }

    
}
