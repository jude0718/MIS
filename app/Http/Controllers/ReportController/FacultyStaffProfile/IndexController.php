<?php

namespace App\Http\Controllers\ReportController\FacultyStaffProfile;
use App\Http\Controllers\Controller;
use App\Models\AcademicRank;
use App\Models\EducationalAttainment;
use App\Models\FacultyGraduteStudies;
use App\Models\FacultyScholars;
use App\Models\FileArchive;
use App\Models\NatureOfAppointment;
use App\Models\PaperPresentation;
use App\Models\RecognitionAndAwards;
use App\Models\SeminarsAndTraining;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(){
        $educational_attainments = EducationalAttainment::get()->groupBy('education');
        $nature_of_appointments = NatureOfAppointment::get()->groupBy('apointment_nature');
        $academic_ranks = AcademicRank::get()->groupBy('academic_rank');
        $faculty_scholars = FacultyScholars::get();
        $graduate_studies = FacultyGraduteStudies::get();
        $local_seminars = SeminarsAndTraining::where('seminar_category', 'Local')->get();
        $provincial_seminars = SeminarsAndTraining::where('seminar_category', 'Provincial')->get();
        $international_seminars = SeminarsAndTraining::where('seminar_category', 'International')->get();
        $national_seminars = SeminarsAndTraining::where('seminar_category', 'National')->get();
        $regional_seminars = SeminarsAndTraining::where('seminar_category', 'Regional')->get();
        $recognitions = RecognitionAndAwards::get();
        $papers = PaperPresentation::get();

        $pdf = PDF::loadView('admin.reports.faculty_staff_profile.faculty_staff_profile', 
        compact(
            'educational_attainments', 
            'nature_of_appointments',
            'academic_ranks', 
            'faculty_scholars',
            'graduate_studies',
            'local_seminars',
            'provincial_seminars',
            'international_seminars',
            'national_seminars',
            'regional_seminars',
            'recognitions',
            'papers'
        ));
      
        $fileName = 'FACULTY_STAFF_PROFILE_' . date('Y_m_d_H_i_s') . '.pdf';
        
        $filePath = public_path('reports/' . $fileName);

        $pdf->save($filePath);
        FileArchive::create([
            'filename' => $fileName, 
            'module_id' => 3,
            'created_by' => auth()->user()->id,
        ]);

        return response()->json(['message' => 'Report generated successfully'], 200);
        // return $pdf->stream('FACULTY_STAFF_PROFILE.pdf');
    }
}
