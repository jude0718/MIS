<?php

namespace App\Http\Controllers\ReportController\Curicullum;
use App\Http\Controllers\Controller;
use App\Models\AccreditationStatus;
use App\Models\FacultyTVET;
use App\Models\LicensureExamnination;
use App\Models\ProgramsWithGovntRecognition;
use App\Models\StudentsTVET;
use App\Models\FileArchive;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(){
        $accreditations_status = AccreditationStatus::get();
        $gov_recognitions = ProgramsWithGovntRecognition::get();
        $licensure_exams = LicensureExamnination::get();
        $faculty_tvets = FacultyTVET::get();
        $student_tvets = StudentsTVET::get();
        $pdf = PDF::loadView('admin.reports.curriculum.curriculum', compact('accreditations_status', 'gov_recognitions', 'licensure_exams', 'faculty_tvets', 'student_tvets'));
        $fileName = 'CURRICULUM_' . date('Y_m_d_H_i_s') . '.pdf';
        $filePath = public_path('reports/' . $fileName);

        $pdf->save($filePath);
        FileArchive::create([
            'filename' => $fileName, 
            'module_id' => 1,
            'created_by' => auth()->user()->id,
        ]);

        return response()->json(['message' => 'Report generated successfully'], 200);
        // return $pdf->stream('CURRICULUM.pdf');
    }

}
