<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ForeignStudent;
use App\Models\Helper;
use App\Models\Programs;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForeignStudentController extends Controller
{
    public function index(){
        $main_title = 'Foreign Student';
        $nav = 'Student Profile';
        $countries = $this->getAddressList();
        $programs = $this->programList();
        $academicYears = $this->academicYearList();
        return view('admin.student_profile.foreign_student', compact('main_title', 'nav', 'countries', 'programs', 'academicYears'));
    }

    public function academicYearList(){
        $data = AcademicYear::get();

        return $data;
    }

    public function getAddressList(){
        $path = public_path('json/country_list.json');
        $countriesJson = File::get($path);
        $countriesArray = json_decode($countriesJson, true); // Decode JSON to array

        $countryNames = array_map(function($country) {
            return $country['name'];
        }, $countriesArray);

        return $countryNames;
    }

    public function programList(){
        $data = Programs::get();

        return $data;
    }

    public function storeForeignStudent (Request $request) {
        try {
            $validatedData = $request->validate([
                'number_of_student' => 'required|integer',
                'semester' => 'required',
                'school_year' => 'required',
                'program_id' => 'required|integer',
                'country' => 'required',
            ]);
    
            try {
                $validatedData['module'] = 2;
                $validatedData['created_by'] = auth()->user()->id;
                ForeignStudent::create($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Added Data in Student Profile Foreign Student',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Added Data in Student Profile Foreign Student',
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

    public function fetchForeignStudentData(){
        $response = [];
        $data = ForeignStudent::orderBy('created_at', 'desc')->get();
        foreach ($data as $key=>$item) {
            $actions = $this->action($item);
            $response[] = [
                'no' => ++$key,
                'name' => ucwords($item->created_by_dtls->firstname.' '.$item->created_by_dtls->lastname),
                'program' => ucwords($item->program_dtls->program),
                'semester' => ucwords($item->semester),
                'country' => ucwords($item->country),
                'student_count' => $item->number_of_student,
                'school_year' => $item->school_year,
                'action' => $actions['button']
            ];
        }
        return response()->json($response);
    }

    public function action($data){
        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-modal-btn" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-foreign-student-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';

        return [
            'button' => $button,
        ];
    }

    public function viewForeignData($id){
        $data = ForeignStudent::where('id', $id)->first();

        return response()->json($data);
    }

    public function updateForeignStudent(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'number_of_student' => 'required|integer',
                'semester' => 'required',
                'school_year' => 'required',
                'program_id' => 'required|integer',
                'country' => 'required',
            ]);
    
            try {
                ForeignStudent::where('id', $id)->update($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Updated Data in Student Profile Foreign Student',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Updated Data in Student Profile Foreign Student',
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

    public function removeForeignStudent($id){
        $data = ForeignStudent::find($id);
        $data->delete();
        Helper::storeNotifications(
            Auth::id(),
            'You Removed Data in Student Profile Foreign Student',
            Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Removed Data in Student Profile Foreign Student',
        );
        return response()->json(['message' => 'Data removed successfully'], 200);
    }
}
