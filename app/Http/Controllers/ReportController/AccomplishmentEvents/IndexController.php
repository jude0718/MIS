<?php

namespace App\Http\Controllers\ReportController\AccomplishmentEvents;
use App\Http\Controllers\Controller;
use App\Models\EventsAndAccomplishments;
use App\Models\FileArchive;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(){
        $accomplishments = EventsAndAccomplishments::get();
        $pdf = PDF::loadView('admin.reports.accomplishment.accomplishment',  compact('accomplishments'));
      
        $fileName = 'ACCOMPLISHMENTS_AND_EVENTS_' . date('Y_m_d_H_i_s') . '.pdf';
        
        $filePath = public_path('reports/' . $fileName);

        $pdf->save($filePath);
        FileArchive::create([
            'filename' => $fileName, 
            'module_id' => 9,
            'created_by' => auth()->user()->id,
        ]);

        return response()->json(['message' => 'Report generated successfully'], 200);
        // return $pdf->stream('RESEARCH_AND_EXTENSION_.pdf');
    }
}
