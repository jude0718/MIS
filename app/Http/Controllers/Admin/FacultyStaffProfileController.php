<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicRank;
use App\Models\AcademicRankType;
use App\Models\AcademicYear;
use App\Models\EducationalAttainment;
use App\Models\EducationAttainmentType;
use App\Models\FacultyGraduteStudies;
use App\Models\FacultyScholars;
use App\Models\Helper;
use App\Models\NatureOfAppointment;
use App\Models\NatureOfAppointmentType;
use App\Models\PaperPresentation;
use App\Models\RecognitionAndAwards;
use App\Models\SeminarsAndTraining;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FacultyStaffProfileController extends Controller
{
    public function index(){
        $main_title = 'Faculty Staff Profile';
        $nav = 'Dashboard';
        $educationAttainmentType = $this->educationAttainmentTypeList();
        $appointments = $this->natureOfAppointmentTypeList();
        $academicRanks = $this->academicRankTypeList();
        $academicYears = $this->academicYearList();
        return view('admin.faculty_staff_profile.faculty_staff_profile', compact('main_title', 'nav', 'educationAttainmentType', 'appointments', 'academicRanks', 'academicYears'));
    }

    public function academicYearList(){
        $data = AcademicYear::get();

        return $data;
    }

    public function educationAttainmentTypeList(){
        $data = EducationAttainmentType::get();

        return $data;
    }

    public function natureOfAppointmentTypeList(){
        $data = NatureOfAppointmentType::get();

        return $data;
    }

    public function academicRankTypeList(){
        $data = AcademicRankType::get();

        return $data;
    }

    public function storeEducationalAttainment(Request $request) {
        try {
            $validatedData = $request->validate([
                'education' => 'required',
                'semester' => 'required',
                'school_year' => 'required',
                'number_of_faculty' => 'required|integer',
            ]);
    
            try {
                $validatedData['module'] = 3;
                $validatedData['added_by'] = auth()->user()->id;
                EducationalAttainment::create($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Added Data in Faculty Staff Profile Faculty profile by educational attainment',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Added Data in Faculty Staff Profile Faculty profile by educational attainment',
                );
                DB::commit();
                return response()->json(['message' => 'Data added successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error storing the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function fetchEducationalAttainment(){
        $response = [];
        $data = EducationalAttainment::orderBy('created_at', 'desc')->get();
        foreach ($data as $key=>$item) {
            $actions = $this->educationalAttainmentAction($item);
            $response[] = [
                'no' => ++$key,
                'name' => ucwords($item->created_by_dtls->firstname.' '.$item->created_by_dtls->lastname),
                'education' => ucwords($item->education_dtls->type),
                'semester' => ucwords($item->semester),
                'number_of_faculty' => $item->number_of_faculty,
                'school_year' => $item->school_year,
                'action' => $actions['button']
            ];
        }
        return response()->json($response);
    }

    public function educationalAttainmentAction($data){
        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-education-attainment-btn" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-educational-attainment-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';

        return [
            'button' => $button,
        ];
    }

    public function viewEducationalAttainment($id){
        $data = EducationalAttainment::where('id', $id)->first();

        return response()->json($data);
    }

    public function updateEducationalAttainment(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'education' => 'required',
                'semester' => 'required',
                'school_year' => 'required',
                'number_of_faculty' => 'required|integer',
            ]);
    
            try {
                EducationalAttainment::where('id', $id)->update($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Updated Data in Faculty Staff Profile Faculty profile by educational attainment',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Updated Data in Faculty Staff Profile Faculty profile by educational attainment',
                );
                DB::commit();
                return response()->json(['message' => 'Data updated successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error storing the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function removeEducationalAttainment($id){
        $data = EducationalAttainment::find($id);
        $data->delete();
        Helper::storeNotifications(
            Auth::id(),
            'You Removed Data in Faculty Staff Profile Faculty profile by educational attainment',
            Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Removed Data in Faculty Staff Profile Faculty profile by educational attainment',
        );
        return response()->json(['message' => 'Data removed successfully'], 200);
    }
    
    //Faculty profile by nature of appointment  
    public function storeNatureAppointment(Request $request) {
        try {
            $validatedData = $request->validate([
                'apointment_nature' => 'required',
                'semester' => 'required',
                'school_year' => 'required',
                'number_of_faculty' => 'required|integer',
            ]);
    
            try {
                $validatedData['module'] = 3;
                $validatedData['added_by'] = auth()->user()->id;
                NatureOfAppointment::create($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Added Data in Faculty Staff Profile Faculty profile by nature of appointment List',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Added Data in Faculty Staff Profile Faculty profile by nature of appointment List',
                );
                DB::commit();
                return response()->json(['message' => 'Data added successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error storing the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function fetchNatureAppointment(){
        $response = [];
        $data = NatureOfAppointment::orderBy('created_at', 'desc')->get();
        foreach ($data as $key=>$item) {
            $actions = $this->NatureAppointmentAction($item);
            $response[] = [
                'no' => ++$key,
                'name' => ucwords($item->created_by_dtls->firstname.' '.$item->created_by_dtls->lastname),
                'apointment_nature' => ucwords($item->apointment_nature_dtls->type),
                'semester' => ucwords($item->semester),
                'number_of_faculty' => $item->number_of_faculty,
                'school_year' => $item->school_year,
                'action' => $actions['button']
            ];
        }
        return response()->json($response);
    }

    public function NatureAppointmentAction($data){
        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-nature-appointment-btn" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-nature-appointment-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';

        return [
            'button' => $button,
        ];
    }

    public function viewNatureAppointment($id){
        $data = NatureOfAppointment::where('id', $id)->first();

        return response()->json($data);
    }

    public function updateNatureAppointment(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'apointment_nature' => 'required',
                'semester' => 'required',
                'school_year' => 'required',
                'number_of_faculty' => 'required|integer',
            ]);
    
            try {
                NatureOfAppointment::where('id', $id)->update($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Updated Data in Faculty Staff Profile Faculty profile by nature of appointment List',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Updated Data in Faculty Staff Profile Faculty profile by nature of appointment List',
                );
                DB::commit();
                return response()->json(['message' => 'Data updated successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error storing the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function removeNatureAppointment($id){
        $data = NatureOfAppointment::find($id);
        $data->delete();
        Helper::storeNotifications(
            Auth::id(),
            'You Removed Data in Faculty Staff Profile Faculty profile by nature of appointment List',
            Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Removed Data in Faculty Staff Profile Faculty profile by nature of appointment List',
        );
        return response()->json(['message' => 'Data removed successfully'], 200);
    }

    // Faculty profile by academic rank

    public function storeAcademicRank(Request $request) {
        try {
            $validatedData = $request->validate([
                'academic_rank' => 'required',
                'semester' => 'required',
                'school_year' => 'required',
                'number_of_faculty' => 'required|integer',
            ]);
    
            try {
                $validatedData['module'] = 3;
                $validatedData['added_by'] = auth()->user()->id;
                AcademicRank::create($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Added Data in Faculty Staff Profile Faculty profile by academic rank',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Added Data in Faculty Staff Profile Faculty profile by academic rank',
                );
                DB::commit();
                return response()->json(['message' => 'Data added successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error storing the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function fetchAcademicRank(){
        $response = [];
        $data = AcademicRank::orderBy('created_at', 'desc')->get();
        foreach ($data as $key=>$item) {
            $actions = $this->AcademicRankAction($item);
            $response[] = [
                'no' => ++$key,
                'name' => ucwords($item->created_by_dtls->firstname.' '.$item->created_by_dtls->lastname),
                'academic_rank' => ucwords($item->academic_rank_dtls->type),
                'semester' => ucwords($item->semester),
                'number_of_faculty' => $item->number_of_faculty,
                'school_year' => $item->school_year,
                'action' => $actions['button']
            ];
        }
        return response()->json($response);
    }

    public function AcademicRankAction($data){
        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-academic-rank-btn" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-academic-rank-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';

        return [
            'button' => $button,
        ];
    }
    
    public function viewAcademicRank($id){
        $data = AcademicRank::where('id', $id)->first();

        return response()->json($data);
    }

    public function updateAcademicRank(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'academic_rank' => 'required',
                'semester' => 'required',
                'school_year' => 'required',
                'number_of_faculty' => 'required|integer',
            ]);
    
            try {
                AcademicRank::where('id', $id)->update($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Updated Data in Faculty Staff Profile Faculty profile by academic rank',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Updated Data in Faculty Staff Profile Faculty profile by academic rank',
                );
                DB::commit();
                return response()->json(['message' => 'Data updated successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error storing the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function removeAcademicRank($id){
        $data = AcademicRank::find($id);
        $data->delete();
        Helper::storeNotifications(
            Auth::id(),
            'You Removed Data in Faculty Staff Profile Faculty profile by academic rank',
            Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Removed Data in Faculty Staff Profile Faculty profile by academic rank',
        );
        return response()->json(['message' => 'Data removed successfully'], 200);
    }

    // List of faculty scholars

    public function storeFacultyScholar(Request $request) {
        try {
            $validatedData = $request->validate([
                'faculty_name' => 'required',
                'scholarship' => 'required',
                'institution' => 'required',
                'program' => 'required',
            ]);
    
            try {
                $validatedData['module'] = 3;
                $validatedData['added_by'] = auth()->user()->id;
                FacultyScholars::create($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Added Data in Faculty Staff List of faculty scholars',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Added Data in Faculty Staff List of faculty scholars',
                );
                DB::commit();
                return response()->json(['message' => 'Data added successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error storing the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }
    
    public function fetchFacultyScholar(){
        $response = [];
        $data = FacultyScholars::orderBy('created_at', 'desc')->get();
        foreach ($data as $key=>$item) {
            $actions = $this->FacultyScholarAction($item);
            $response[] = [
                'no' => ++$key,
                'name' => ucwords($item->created_by_dtls->firstname.' '.$item->created_by_dtls->lastname),
                'faculty_name' => ucwords($item->faculty_name),
                'scholarship' => ucwords($item->scholarship),
                'institution' => ucwords($item->institution),
                'program' => ucwords($item->program),
                'action' => $actions['button']
            ];
        }
        return response()->json($response);
    }

    public function FacultyScholarAction($data){
        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-faculty-scholar-btn" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-faculty-scholar-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';

        return [
            'button' => $button,
        ];
    }

    public function viewFacultyScholar($id){
        $data = FacultyScholars::where('id', $id)->first();

        return response()->json($data);
    }

    public function updateFacultyScholar(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'faculty_name' => 'required',
                'scholarship' => 'required',
                'institution' => 'required',
                'program' => 'required',
            ]);
    
            try {
            
                FacultyScholars::where('id', $id)->update($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Updated Data in Faculty Staff List of faculty scholars',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Updated Data in Faculty Staff List of faculty scholars',
                );
                DB::commit();
                return response()->json(['message' => 'Data updated successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error storing the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function removeFacultyScholar($id){
        $data = FacultyScholars::find($id);
        $data->delete();
        Helper::storeNotifications(
            Auth::id(),
            'You Removed Data in Faculty Staff List of faculty scholars',
            Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Removed Data in Faculty Staff List of faculty scholars',
        );
        return response()->json(['message' => 'Data removed successfully'], 200);
    }

    // List of faculty Members who completed their Graduated Studies 

    public function storeFacultyGraduateStudies(Request $request) {
        try {
            $validatedData = $request->validate([
                'faculty_name' => 'required',
                'degree' => 'required',
                'institution' => 'required',
                'date_of_graduation' => 'required',
            ]);
    
            try {
                $validatedData['module'] = 3;
                $validatedData['added_by'] = auth()->user()->id;
                FacultyGraduteStudies::create($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Added Data in Faculty Staff List of faculty Members who completed their Graduated Studies',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Added Data in Faculty Staff List of faculty Members who completed their Graduated Studies',
                );
                DB::commit();
                return response()->json(['message' => 'Data added successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error storing the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function fetchFacultyGraduateStudies(){
        $response = [];
        $data = FacultyGraduteStudies::orderBy('created_at', 'desc')->get();
        foreach ($data as $key=>$item) {
            $actions = $this->FacultyGraduateStudiesAction($item);
            $response[] = [
                'no' => ++$key,
                'name' => ucwords($item->created_by_dtls->firstname.' '.$item->created_by_dtls->lastname),
                'faculty_name' => ucwords($item->faculty_name),
                'degree' => ucwords($item->degree),
                'institution' => ucwords($item->institution),
                'date_of_graduation' => date('M d, Y', strtotime($item->date_of_graduation)),
                'action' => $actions['button']
            ];
        }
        return response()->json($response);
    }

    public function FacultyGraduateStudiesAction($data){
        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-faculty-graduate-studies-btn" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-faculty-graduate-studies-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';

        return [
            'button' => $button,
        ];
    }

    public function viewFacultyGraduateStudies($id){
        $data = FacultyGraduteStudies::where('id', $id)->first();

        return response()->json([
            'faculty_name' => $data->faculty_name,
            'degree' => $data->degree,
            'institution' => $data->institution,
            'date_of_graduation' => date('Y-m-d', strtotime($data->date_of_graduation)),
        ]);
    }

    public function updateFacultyGraduateStudies(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'faculty_name' => 'required',
                'degree' => 'required',
                'institution' => 'required',
                'date_of_graduation' => 'required',
            ]);
    
            try {
                FacultyGraduteStudies::where('id', $id)->update($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Updated Data in Faculty Staff List of faculty Members who completed their Graduated Studies',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Updated Data in Faculty Staff List of faculty Members who completed their Graduated Studies',
                );
                DB::commit();
                return response()->json(['message' => 'Data updated successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error storing the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function removeFacultyGraduateStudies($id){
        $data = FacultyGraduteStudies::find($id);
        $data->delete();
        Helper::storeNotifications(
            Auth::id(),
            'You Removed Data in Faculty Staff List of faculty Members who completed their Graduated Studies',
            Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Removed Data in Faculty Staff List of faculty Members who completed their Graduated Studies',
        );
        return response()->json(['message' => 'Data removed successfully'], 200);
    }

    //List of local seminars and trainings attended by faculty members

    public function storeSeminarTraining(Request $request) {
        try {
            $validatedData = $request->validate([
                'conference_title' => 'required',
                'participants' => 'required',
                'date' => 'required',
                'venue' => 'required',
                'seminar_category' => 'required',
            ]);
    
            try {
                $validatedData['module'] = 3;
                $validatedData['added_by'] = auth()->user()->id;
                SeminarsAndTraining::create($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Added Data in Faculty Staff List of seminars and trainings attended by faculty members',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Added Data in Faculty Staff List of seminars and trainings attended by faculty members',
                );
                DB::commit();
                return response()->json(['message' => 'Data added successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error storing the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function fetchSeminarTraining(){
        $response = [];
        $data = SeminarsAndTraining::orderBy('created_at', 'desc')->get();
        foreach ($data as $key=>$item) {
            $actions = $this->SeminarTrainingAction($item);
            $response[] = [
                'no' => ++$key,
                'name' => ucwords($item->created_by_dtls->firstname.' '.$item->created_by_dtls->lastname),
                'conference_title' => ucwords($item->conference_title),
                'participants' => ucwords($item->participants),
                'venue' => ucwords($item->venue),
                'date' => date('M d, Y', strtotime($item->date)),
                'seminar_category' => ucwords($item->seminar_category),
                'action' => $actions['button']
            ];
        }
        return response()->json($response);
    }

    public function SeminarTrainingAction($data){
        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-seminar-training-btn" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-seminar-training-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';

        return [
            'button' => $button,
        ];
    }

    public function viewSeminarTraining($id){
        $data = SeminarsAndTraining::where('id', $id)->first();

        return response()->json([
            'conference_title' => $data->conference_title,
            'participants' => $data->participants,
            'venue' => $data->venue,
            'date' => date('Y-m-d', strtotime($data->date)),
            'seminar_category' => $data->seminar_category,
        ]);
    }

    public function updateSeminarTraining(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'conference_title' => 'required',
                'participants' => 'required',
                'date' => 'required',
                'venue' => 'required',
                'seminar_category' => 'required',
            ]);
    
            try {
            
                SeminarsAndTraining::where('id', $id)->update($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Updated Data in Faculty Staff List of seminars and trainings attended by faculty members',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Updated Data in Faculty Staff List of seminars and trainings attended by faculty members',
                );
                DB::commit();
                return response()->json(['message' => 'Data updated successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error storing the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function removeSeminarTraining($id){
        $data = SeminarsAndTraining::find($id);
        $data->delete();
        Helper::storeNotifications(
            Auth::id(),
            'You Removed Data in Faculty Staff List of seminars and trainings attended by faculty members',
            Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Removed Data in Faculty Staff List of seminars and trainings attended by faculty members',
        );
        return response()->json(['message' => 'Data removed successfully'], 200);
    }

    //List of recognition and award received by the faculty members

    public function storeRecognition(Request $request) {
        try {
            $validatedData = $request->validate([
                'award_type' => 'required',
                'awardee_name' => 'required',
                'award' => 'required',
                'agency' => 'required',
                'date_received' => 'required',
            ]);
    
            try {
                $validatedData['module'] = 3;
                $validatedData['added_by'] = auth()->user()->id;
                RecognitionAndAwards::create($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Added Data in Faculty Staff List of recognition and award received by the faculty members',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Added Data in Faculty Staff List of recognition and award received by the faculty members',
                );
                DB::commit();
                return response()->json(['message' => 'Data added successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error storing the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function fetchRecognition(){
        $response = [];
        $data = RecognitionAndAwards::orderBy('created_at', 'desc')->get();
        foreach ($data as $key=>$item) {
            $actions = $this->RecognitionAction($item);
            $response[] = [
                'no' => ++$key,
                'name' => ucwords($item->created_by_dtls->firstname.' '.$item->created_by_dtls->lastname),
                'award_type' => ucwords($item->award_type),
                'awardee_name' => ucwords($item->awardee_name),
                'award' => ucwords($item->award),
                'date_received' => date('M d, Y', strtotime($item->date_received)),
                'agency' => ucwords($item->agency),
                'action' => $actions['button']
            ];
        }
        return response()->json($response);
    }

    public function RecognitionAction($data){
        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-recognition-btn" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-recognition-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';

        return [
            'button' => $button,
        ];
    }

    public function viewRecognition($id){
        $data = RecognitionAndAwards::where('id', $id)->first();

        return response()->json([
            'award_type' => $data->award_type,
            'awardee_name' => $data->awardee_name,
            'award' => $data->award,
            'agency' => $data->agency,
            'date_received' => date('Y-m-d', strtotime($data->date_received)),
        ]);
    }

    public function updateRecognition(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'award_type' => 'required',
                'awardee_name' => 'required',
                'award' => 'required',
                'agency' => 'required',
                'date_received' => 'required',
            ]);
    
            try {
                $validatedData['module'] = 3;
                $validatedData['added_by'] = auth()->user()->id;
                RecognitionAndAwards::where('id', $id)->update($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Updated Data in Faculty Staff List of recognition and award received by the faculty members',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Updated Data in Faculty Staff List of recognition and award received by the faculty members',
                );
                DB::commit();
                return response()->json(['message' => 'Data update successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error storing the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function removeRecognition($id){
        $data = RecognitionAndAwards::find($id);
        $data->delete();
        Helper::storeNotifications(
            Auth::id(),
            'You Removed Data in Faculty Staff List of recognition and award received by the faculty members',
            Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Removed Data in Faculty Staff List of recognition and award received by the faculty members',
        );
        return response()->json(['message' => 'Data removed successfully'], 200);
    }

    //List of paper presentations of the faculty members

    public function storePresentation(Request $request) {
        try {
            $validatedData = $request->validate([
                'presentation_type' => 'required',
                'conference_name' => 'required',
                'paper_name' => 'required',
                'presenter_name' => 'required',
                'date' => 'required',
                'venue' => 'required',
            ]);
    
            try {
                $validatedData['module'] = 3;
                $validatedData['added_by'] = auth()->user()->id;
                PaperPresentation::create($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Removed Data in Faculty Staff List of paper presentations of the faculty members',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Removed Data in Faculty Staff List of paper presentations of the faculty members',
                );
                DB::commit();
                return response()->json(['message' => 'Data added successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error storing the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function fetchPresentation(){
        $response = [];
        $data = PaperPresentation::orderBy('created_at', 'desc')->get();
        foreach ($data as $key=>$item) {
            $actions = $this->PresentationAction($item);
            $response[] = [
                'no' => ++$key,
                'name' => ucwords($item->created_by_dtls->firstname.' '.$item->created_by_dtls->lastname),
                'presentation_type' => ucwords($item->presentation_type),
                'conference_name' => ucwords($item->conference_name),
                'paper_name' => ucwords($item->paper_name),
                'presenter_name' => ucwords($item->presenter_name),
                'date_venue' => date('M d, Y', strtotime($item->date)).' / '.ucwords($item->venue),
                'action' => $actions['button']
            ];
        }
        return response()->json($response);
    }

    public function PresentationAction($data){
        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-presentation-btn" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-presentation-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';

        return [
            'button' => $button,
        ];
    }

    public function viewPresentation($id){
        $data = PaperPresentation::where('id', $id)->first();

        return response()->json([
            'presentation_type' => $data->presentation_type,
            'conference_name' => $data->conference_name,
            'paper_name' => $data->paper_name,
            'presenter_name' => $data->presenter_name,
            'date' => date('Y-m-d', strtotime($data->date)),
            'venue' => $data->venue,
        ]);
    }

    public function updatePresentation(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'presentation_type' => 'required',
                'conference_name' => 'required',
                'paper_name' => 'required',
                'presenter_name' => 'required',
                'date' => 'required',
                'venue' => 'required',
            ]);
    
            try {
                PaperPresentation::where('id', $id)->update($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Updated Data in Faculty Staff List of paper presentations of the faculty members',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Updated Data in Faculty Staff List of paper presentations of the faculty members',
                );
                DB::commit();
                return response()->json(['message' => 'Data updated successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error storing the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function removePresentation($id){
        $data = PaperPresentation::find($id);
        $data->delete();
        Helper::storeNotifications(
            Auth::id(),
            'You Removed Data in Faculty Staff List of paper presentations of the faculty members',
            Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Removed Data in Faculty Staff List of paper presentations of the faculty members',
        );
        return response()->json(['message' => 'Data removed successfully'], 200);
    }
}


   

