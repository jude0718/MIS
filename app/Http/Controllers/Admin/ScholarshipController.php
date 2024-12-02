<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Helper;
use App\Models\Scholarship;
use App\Models\ScholarshipType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScholarshipController extends Controller
{
    public function index(){
        $main_title = 'Scholarship';
        $nav = 'Student Profile';
        $scholarships = $this->scholarshipList();
        $academicYears = $this->academicYearList();
        return view('admin.student_profile.scholarship', compact('main_title', 'nav', 'scholarships', 'academicYears'));
    }

    public function scholarshipList(){
        $data  = ScholarshipType::get();

        return $data;
    }

    public function academicYearList(){
        $data = AcademicYear::get();

        return $data;
    }

    public function storeScholarship(Request $request) {
        try {
            $validatedData = $request->validate([
                'number_of_scholars' => 'required|integer',
                'semester' => 'required',
                'school_year' => 'required',
                'scholarship_type' => 'required'
            ]);
    
            try {
                $validatedData['module'] = 2;
                $validatedData['created_by'] = auth()->user()->id;
                Scholarship::create($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Added Data in Scholarship ',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Added Data in Scholarship ',
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

    public function fetchScholarData(){
        $response = [];
        $data = Scholarship::orderBy('created_at', 'desc')->get();
        foreach ($data as $key=>$item) {
            $actions = $this->action($item);
            $response[] = [
                'no' => ++$key,
                'name' => ucwords($item->created_by_dtls->firstname.' '.$item->created_by_dtls->lastname),
                'type' => ucwords($item->scholarship_type_dtls->type),
                'semester' => ucwords($item->semester),
                'number_of_scholars' => $item->number_of_scholars,
                'school_year' => $item->school_year,
                'action' => $actions['button']
            ];
        }
        return response()->json($response);
    }

    public function action($data){
        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-modal-btn" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-scholarship-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';

        return [
            'button' => $button,
        ];
    }

    public function viewScholarshipData($id){
        $data = Scholarship::where('id', $id)->first();

        return response()->json($data);
    }

    public function updateScholarship(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'number_of_scholars' => 'required|integer',
                'semester' => 'required',
                'school_year' => 'required',
                'scholarship_type' => 'required'
            ]);
    
            try {
                Scholarship::where('id', $id)->update($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Updated Data in Scholarship ',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Updated Data in Scholarship ',
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

    public function removeScholarship($id){
        $data = Scholarship::find($id);
        $data->delete();
        Helper::storeNotifications(
            Auth::id(),
            'You Removed Data in Scholarship ',
            Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Removed Data in Scholarship ',
        );
        return response()->json(['message' => 'Data removed successfully'], 200);
    }
}
