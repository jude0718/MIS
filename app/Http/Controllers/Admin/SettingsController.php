<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Helper;
use App\Models\Programs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(){
        $main_title = 'Settings';
        $nav = 'Dashboard';
    
        return view('admin.settings', compact('main_title', 'nav'));
    }

    public function storeProgram(Request $request) {
        try {
            $validatedData = $request->validate([
                'program' => 'required',
                'abbreviation' => 'required',
                
            ]);
    
            try {
                $validatedData['created_by'] = Auth::id();
                Programs::create($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Added New Program',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Added New Program',
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

    public function fetchProgram(){
        $response = [];
        $data = Programs::get();
        foreach ($data as $key=>$item) {
            $actions = $this->programAction($item);
            $response[] = [
                'no' => ++$key,
                'name' => ucwords($item->created_by_dtls->firstname.' '.$item->created_by_dtls->lastname),
                'program' => ucwords($item->program),
                'abbreviation' => ucwords($item->abbreviation),
                'action' => $actions['button']
            ];
        }
        return response()->json($response);
    }

    public function programAction($data){
        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-program-btn" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-program-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';
        return [
            'button' => $button,
        ];
    }

    public function viewProgram($id){
        $program = Programs::find($id);
        return response()->json($program);
    }

    public function updateProgram(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'program' => 'required',
                'abbreviation' => 'required',
                
            ]);
    
            try {
                Programs::where('id', $id)->update($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Updated a Program',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Updated a Program',
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

    public function removeProgram($id){
        $data = Programs::find($id);
        $data->delete();
        Helper::storeNotifications(
            Auth::id(),
            'You Removed a Program',
            Auth::user()->firstname .''. Auth::user()->lastname .' Removed a Program',
        );
        return response()->json(['message' => 'Data removed successfully'], 200);
    }

    public function storeAcademicYear(Request $request) {
        try {
            $validatedData = $request->validate([
                'year_start' => 'required|integer',
                'year_end' => 'required|integer',
                
            ]);
    
            try {
                $validatedData['created_by'] = Auth::id();
                AcademicYear::create($validatedData);
                Helper::storeNotifications(
                    Auth::id(),
                    'You Added New Academic Year',
                    Auth::user()->firstname . ' ' . Auth::user()->lastname . ' Added New Academic Year',
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

    public function fetchAcademicYear(){
        $response = [];
        $data = AcademicYear::get();
        foreach ($data as $key=>$item) {
            $actions = $this->academicYearAction($item);
            $response[] = [
                'no' => ++$key,
                'academic_year' => $item->year_start.'-'.$item->year_end,
                'action' => $actions['button']
            ];
        }
        return response()->json($response);
    }

    public function academicYearAction($data){
        $button = '
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-academic-year-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';
        return [
            'button' => $button,
        ];
    }

    public function removeAcademicYear($id){
        $data = AcademicYear::find($id);
        $data->delete();
        Helper::storeNotifications(
            Auth::id(),
            'You Removed Academic Year',
            Auth::user()->firstname .''. Auth::user()->lastname .'Removed Academic Year',
        );
        return response()->json(['message' => 'Data removed successfully'], 200);
    }

}
