<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\GraduateDetails;
use App\Models\GraduateHeader;
use App\Models\Helper;
use App\Models\Programs;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GraduateController extends Controller
{

    public function index(){
        $main_title = 'Graduate';
        $nav = 'Student Profile';
        $programs = $this->programList();
        $academicYears = $this->academicYearList();
        return view('admin.student_profile.graduate', compact('main_title', 'nav', 'programs', 'academicYears'));
    }

    public function programList(){
        $data = Programs::get();

        return $data;
    }

    public function academicYearList(){
        $data = AcademicYear::get();

        return $data;
    }

    public function storeGraduateHeader(Request $request) {
        try {
            $validatedData = $request->validate([
                'graduate_date' => 'required',
                'semester' => 'required',
                'school_year' => 'required',
                'number_of_student' => 'required',
                'program_id' => 'required',
            ]);
    
            try {
                $validatedData['module'] = 2;
                $validatedData['created_by'] = auth()->user()->id;
                GraduateHeader::create($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Added Data in Student Profile Graduate ',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Added Data in Student Profile Graduate ',
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

    public function storeGraduateDetails(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'number_of_student' => 'required|integer',
                'program_id' => 'required|integer'
            ]);
    
            try {
                $validatedData['graduate_hdr'] = $id;
                GraduateDetails::create($validatedData);
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

    public function fetchGraduateHdrData(){
        $response = [];
        $data = GraduateHeader::orderBy('created_at', 'desc')->get();
        foreach ($data as $key=>$item) {
            $actions = $this->headerAction($item);
            $response[] = [
                'no' => ++$key,
                'name' => ucwords($item->created_by_dtls->firstname.' '.$item->created_by_dtls->lastname),
                'semester' => ucwords($item->semester),
                'school_year' => $item->school_year,
                'number_of_student' => $item->number_of_student,
                'program_id' => ucwords($item->program_dtls->program),
                'date' => date('M d, Y', strtotime($item->graduate_date)),
                'action' => $actions['button']
            ];
        }
        return response()->json($response);
    }

    public function headerAction($data){
        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-hdr-modal" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-graduate-hdr-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';

        return [
            'button' => $button,
        ];
    }

    public function viewGraduateDetailsData($id){
        $response = [];
        $data = GraduateHeader::where('id', $id)->get();
        foreach($data as $value){
            foreach($value->graduate_dtls as $key=>$item){
                $actions = $this->detailsAction($item);
                $response[] = [
                    'no' => ++$key,
                    'id' => $item->id,
                    'number_of_student' => $item->number_of_student,
                    'program' => ucwords($item->program_dtls->program),
                    'action' => $actions['button']
                ];
            }
        }
        return response()->json($response);
    }

    public function detailsAction($data){

        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-dlts-modal" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-graduate-dtls-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';

        return [
            'button' => $button,
        ];
    }

    public function viewGraduateHeaderData($id){
        $data = GraduateHeader::where('id', $id)->first();
        
        return response()->json([
            'semester' => $data->semester,
            'school_year' => $data->school_year,
            'number_of_student' => $data->number_of_student,
            'program_id' => $data->program_id,
            'graduate_date' => date('Y-m-d', strtotime($data->graduate_date))
        ]);
    }

    public function updateGraduateHeader(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'graduate_date' => 'required',
                'semester' => 'required',
                'school_year' => 'required',
                'number_of_student' => 'required',
                'program_id' => 'required',
            ]);
    
            try {
                GraduateHeader::where('id', $id)->update($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Updated Data in Student Profile Graduate ',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Updated Data in Student Profile Graduate ',
                );
                DB::commit();
                return response()->json(['message' => 'Data updated successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error updating the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function editGraduateDetailsData($id){
        $data = GraduateDetails::where('id', $id)->first();
        
        return response()->json([
            'number_of_student' => $data->number_of_student,
            'program' => $data->program_id,
        ]);
    }

    public function updateGraduateDetails(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'program_id' => 'required|integer',
                'number_of_student' => 'required|integer',
            ]);
    
            try {
                GraduateDetails::where('id', $id)->update($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Updated Data in Student Profile Graduate ',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Updated Data in Student Profile Graduate ',
                );
                DB::commit();
                return response()->json(['message' => 'Data updated successfully'], 200);
            }catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error updating the item: ' . $e->getMessage()], 500);
            }
        }catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function removeGraduateHeader($id){
        $data = GraduateHeader::find($id);
        $data->delete();
        Helper::storeNotifications(
            Auth::id(),
            'You Removed Data in Student Profile Graduate ',
            Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Added Data in Student Profile Graduate ',
        );
        return response()->json(['message' => 'Data removed successfully'], 200);
    }

    public function removeGraduateDetails($id){
        $data = GraduateDetails::find($id);
        $data->delete();
        Helper::storeNotifications(
            Auth::id(),
            'You Removed Data in Student Profile Graduate ',
            Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Removed Data in Student Profile Graduate ',
        );
        return response()->json(['message' => 'Data removed successfully'], 200);
    }
    
}
