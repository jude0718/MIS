<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleHeader extends Model
{
    use HasFactory;
    protected $table = 'module_hdr';

    public function module_dtls(){
        return $this->hasMany(ModuleDetails::class, 'module_hdr', 'id');
    }
}
