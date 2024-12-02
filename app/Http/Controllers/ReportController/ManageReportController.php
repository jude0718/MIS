<?php

namespace App\Http\Controllers\ReportController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManageReportController extends Controller
{
    public function index($fileName){
        return view('admin.reports.view_report', compact('fileName'));
    }
}
