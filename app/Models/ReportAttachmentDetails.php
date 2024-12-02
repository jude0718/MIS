<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportAttachmentDetails extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'report_attachment_dtls';
    protected $fillable = [
        'attachment_hdr',
        'attachment',
    ];
}
