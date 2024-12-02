<?php

namespace App\Http\Controllers\ReportController\AnnualReport;
use App\Http\Controllers\Controller;
use App\Models\FileArchive;
use App\Models\Linkages;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\AccreditationStatus;
use App\Models\FacultyTVET;
use App\Models\LicensureExamnination;
use App\Models\ProgramsWithGovntRecognition;
use App\Models\StudentsTVET;
use App\Models\AwardsHeader;
use App\Models\Enrollment;
use App\Models\ForeignStudent;
use App\Models\Scholarship;
use App\Models\GraduateHeader;
use App\Models\AcademicRank;
use App\Models\EducationalAttainment;
use App\Models\FacultyGraduteStudies;
use App\Models\FacultyScholars;
use App\Models\NatureOfAppointment;
use App\Models\PaperPresentation;
use App\Models\RecognitionAndAwards;
use App\Models\SeminarsAndTraining;
use App\Models\StudentOrganizations;
use App\Models\ExtensionActivity;
use App\Models\Research;
use App\Models\InfrastructureDevelopment;
use App\Models\EventsAndAccomplishments;
use App\Models\ReportAttachmentHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index($year){
        $hasRecords = AccreditationStatus::whereYear('created_at', $year)->exists() &&
                  ProgramsWithGovntRecognition::whereYear('created_at', $year)->exists() &&
                  LicensureExamnination::whereYear('created_at', $year)->exists() &&
                  FacultyTVET::whereYear('created_at', $year)->exists() &&
                  StudentsTVET::whereYear('created_at', $year)->exists() &&
                  Enrollment::whereYear('created_at', $year)->exists() &&
                  ForeignStudent::whereYear('created_at', $year)->exists() &&
                  GraduateHeader::whereYear('created_at', $year)->exists() &&
                  Scholarship::whereYear('created_at', $year)->exists() &&
                  AwardsHeader::whereYear('created_at', $year)->exists() &&
                  EducationalAttainment::whereYear('created_at', $year)->exists() &&
                  NatureOfAppointment::whereYear('created_at', $year)->exists() &&
                  AcademicRank::whereYear('created_at', $year)->exists() &&
                  FacultyScholars::whereYear('created_at', $year)->exists() &&
                  FacultyGraduteStudies::whereYear('created_at', $year)->exists() &&
                  SeminarsAndTraining::whereYear('created_at', $year)->exists() &&
                  RecognitionAndAwards::whereYear('created_at', $year)->exists() &&
                  PaperPresentation::whereYear('created_at', $year)->exists() &&
                  StudentOrganizations::whereYear('created_at', $year)->exists() &&
                  Research::whereYear('created_at', $year)->exists() &&
                  ExtensionActivity::whereYear('created_at', $year)->exists() &&
                  Linkages::whereYear('created_at', $year)->exists() &&
                  InfrastructureDevelopment::whereYear('created_at', $year)->exists() &&
                  EventsAndAccomplishments::whereYear('created_at', $year)->exists() &&
                  ReportAttachmentHeader::whereYear('created_at', $year)->exists();

        if (!$hasRecords) {
            return response()->json(['errors' => ["No records found for the year {$year}"]], 422);
        }
        //CURROCULLUM
        $accreditations_status = AccreditationStatus::get();
        $gov_recognitions = ProgramsWithGovntRecognition::get();
        $licensure_exams = LicensureExamnination::get();
        $faculty_tvets = FacultyTVET::get();
        $student_tvets = StudentsTVET::get();

        //STUDENT PROFILE
        $enrollments = Enrollment::get()->groupBy('program_id');
        $foreign_students = ForeignStudent::get()->groupBy('country');
        $graduates = GraduateHeader::get()->groupBy('program_id');
        $scholarships = Scholarship::get()->groupBy('scholarship_type');
        $awards = AwardsHeader::with('award_dtls')->get();

        //FACULTY STAFF PROFILE
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

        //STUDENT ORGANIZATION
        $organizations = StudentOrganizations::get();

        //RESEARCH AND EXTENSION
        $cvsu_researches = Research::whereNull('agency')->get();
        $outside_researches = Research::whereNotNull('agency')->get();
        $extensions = ExtensionActivity::get();

        //LINKAGES
        $linkages = Linkages::get();

        //INFRASTRUCTURE DEVELOPMENT
        $infrastructures = InfrastructureDevelopment::get();

        //OTHER ACCOMPLISMHENTS AND EVENTS
        $accomplishments = EventsAndAccomplishments::get();

        //ATTACHMENTS

        $attachments = ReportAttachmentHeader::get();
        $pdf = PDF::loadView('admin.reports.annual_report.annual_report',  
        compact(
            'faculty_tvets', 
            'student_tvets', 
            'accreditations_status',
            'gov_recognitions',
            'licensure_exams',

            'enrollments', 
            'foreign_students', 
            'graduates', 
            'scholarships',
            'awards',

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
            'papers',

            'organizations',

            'cvsu_researches', 
            'outside_researches', 
            'extensions',

            'linkages',

            'infrastructures',

            'accomplishments',

            'attachments'

        ));
      
        $fileName = 'ANNUAL_REPORT_OF_YEAR_'.$year. '.pdf';
        
        $filePath = public_path('reports/' . $fileName);

        $pdf->save($filePath);
        FileArchive::create([
            'filename' => $fileName, 
            'module_id' => 10,
            'created_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'Report generated successfully'], 200);
        return $pdf->stream('RESEARCH_AND_EXTENSION_.pdf');
    }

    public function generateReport(Request $request){
        try {
            $validatedData = $request->validate([
                'year' => 'required',
                
            ]);
            try {
                return $this->index($request->year);
                
            }catch (\Exception $e) {
                return response()->json(['error' => 'Error storing the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }
}
