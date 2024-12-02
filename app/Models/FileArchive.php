<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileArchive extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'file_archives';
    protected $fillable = ['module_id', 'filename', 'created_by'];    

    public function created_by_dtls(){
        return $this->belongsTo(User::class, 'created_by');
    }

    public function module_dtls(){
        return $this->belongsTo(ModuleHeader::class,'module_id');
    }
}
