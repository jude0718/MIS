<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualReportYearList extends Model
{
    use HasFactory;
    protected $table = 'annual_year_list';
    protected $fillable = ['year'];
}
