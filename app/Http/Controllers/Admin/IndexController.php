<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departments;
use App\Models\Enrollment;
use App\Models\ExaminationType;
use App\Models\ExtensionActivity;
use App\Models\LicensureExamnination;
use App\Models\Notifications;
use App\Models\Programs;
use App\Models\Research;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index(){

        $main_title = 'Home';
        $nav = 'Dashboard';
        $schoolYears = $this->getSchoolYears();
        $program_count = $this->programCount();
        $department_count = $this->departmentCount();
        $user_count = $this->userCount();
        $research_year = $this->getResearchYears();
        $extension_year = $this->getExntensionYears();
        $licensure_year = $this->getLicensureExamYears();
        $exam_types = $this->examTypeList();
        return view('admin.index', compact('main_title', 'nav', 'schoolYears', 'user_count', 
        'program_count', 'research_year', 'extension_year', 'department_count', 'licensure_year', 'exam_types'));
    }

    public function getSchoolYears() {
        $schoolYears = Enrollment::select('school_year')
            ->distinct()
            ->orderBy('school_year', 'desc')
            ->get();
    
        return $schoolYears;
    }

    public function getLicensureExamYears() {
        $data = LicensureExamnination::selectRaw('YEAR(exam_date) as year')
        ->distinct()
        ->orderBy('year', 'desc')
        ->get();

        return $data;
    }

    public function examTypeList(){
        $data = ExaminationType::get();

        return $data;
    }

    public function getResearchYears() {
        $data = Research::select('year')
        ->distinct()
        ->orderBy('year', 'desc')
        ->get();

        return $data;
    }

    public function getExntensionYears() {
        $data = ExtensionActivity::selectRaw('YEAR(activity_date) as year')
        ->distinct()
        ->orderBy('year', 'desc')
        ->get();

        return $data;
    }   
    
    public function getNotifications(){
        $response = [];
        $data = Notifications::where('sent_to', Auth::id())->orderBy('created_at', 'desc')->get();
        foreach($data as $item){
            $response[] = [
                'message_to_self' => $item->message_to_self,
                'message_to_others' =>  $item->message_to_others,
                'transact_by' => $item->transact_by_dtls->firstname.' '.$item->transact_by_dtls->lastname,
                'time' => $item->created_at->format('M d, Y h:i a'),
            ];
        }
        return response()->json($response);
    }

    public function notificationCount(){
        $data = Notifications::whereNull('read_at')->where('sent_to', Auth::id())->count();

        return response()->json($data);
    }

    public function annualProgramReportData(Request $request) {

        $schoolYear = $request->school_year;
        $semester = $request->semester;
        $programs = Programs::with(['enrollment_dtls' => function ($query) use ($schoolYear, $semester) {
            $query->where('school_year', $schoolYear);
            $query->where('semester', $semester);
        }])->get();

        $enrollmentData = [];

        foreach ($programs as $program) {
            $totalEnrollees = 0;

            foreach ($program->enrollment_dtls as $enrollment) {
                $totalEnrollees += $enrollment->number_of_student;
            }

            $enrollmentData[] = [
                'program' => $program->abbreviation, 
                'total_enrollees' => $totalEnrollees,
            ];
        }

        return response()->json([
            'data' => $enrollmentData,
        ]);
    }

    public function licensureExamReport(Request $request){
        $examYear = $request->exam_year;
        $examType = $request->exam_type;

        $exams = ExaminationType::with(['licensure_dtls' => function ($query) use ($examYear, $examType) {
        if ($examYear) {
            $query->whereYear('exam_date', $examYear); // Filter by year only
        }
        if ($examType) {
            $query->where('examination_type', $examType);
        }
        }])->get();

        $examData = [];
        foreach ($exams as $exam) {
            $localCount = $exam->licensure_dtls->sum('cvsu_total_passer');
            $nationalCount = $exam->licensure_dtls->sum('national_total_passer');
    
            $examData[] = [
                'exam_type' => $exam->type,
                'type' => 'cvsu',
                'count' => $localCount,
            ];
            $examData[] = [
                'exam_type' => $exam->type,
                'type' => 'national',
                'count' => $nationalCount,
            ];
        }
        return response()->json($examData);
    }

    public function researchCountReportData(Request $request){
        $researchYear = $request->research_year;
        $researchStatus = $request->research_status;

        $data = Research::selectRaw('YEAR(year) as year, COUNT(*) as total_count')
            ->when($researchYear, function ($query) use ($researchYear) {
                return $query->whereYear('year', $researchYear);
            })
            ->when($researchStatus, function ($query) use ($researchStatus) {
                return $query->where('status', $researchStatus);
            })
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->get();



        return response()->json($data);
    }

    public function extensionCountReportData(Request $request){
        $extensionYear = $request->extension_year;
        $data = ExtensionActivity::selectRaw('YEAR(activity_date) as year, COUNT(*) as count')
            ->groupBy('year')
            ->when($extensionYear, function ($query, $extensionYear) {
                return $query->whereYear('activity_date', $extensionYear);
            })
            ->orderBy('year', 'asc') // Use 'year' to keep consistency
            ->get();

        return response()->json($data);
    }

    public function readAllNotifications(){
        Notifications::where('sent_to', Auth::id())->update(['read_at' => now()]);

    }

    public function userCount(){
        $data = User::count();

        return $data;
    }

    public function programCount(){
        $data = Programs::count();

        return $data;
    }

    public function departmentCount(){
        $data = Departments::count();

        return $data;
    }

    
}
