<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentOrganizations;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class StudentDevelopmentController extends Controller
{
    public function index(){
        $main_title = 'Student Development';
        $nav = 'Dashboard';

        return view('admin.student_development.student_development', compact('main_title', 'nav'));
    }

    public function storeOrganization(Request $request) {
        try {
            $validatedData = $request->validate([
                'org_abbrev' => 'required',
                'program_abbrev' => 'required',
                'org_name' => 'required',
            ]);
    
            try {
                $validatedData['module'] = 4;
                $validatedData['added_by'] = auth()->user()->id;
                StudentOrganizations::create($validatedData);
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

    public function fetchstudentOrganization(){
        $response = [];
        $data = StudentOrganizations::orderBy('created_at', 'desc')->get();
        foreach ($data as $key=>$item) {
            $actions = $this->studentOrganizationAction($item);
            $response[] = [
                'no' => ++$key,
                'name' => ucwords($item->created_by_dtls->firstname.' '.$item->created_by_dtls->lastname),
                'org_abbrev' => ucwords($item->org_abbrev.' '.$item->program_abbrev. ' '.$item->org_name),
                'program_abbrev' => ucwords($item->program_abbrev),
                'org_name' => ucwords($item->org_name),
                'action' => $actions['button']
            ];
        }
        return response()->json($response);
    }

    public function studentOrganizationAction($data){
        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-organization-btn" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
            <button type="button" class="btn btn-outline-danger btn-sm px-3" id="remove-organization-btn" data-id="'.$data->id.'"><i class="bi bi-trash"></i></button>
        ';

        return [
            'button' => $button,
        ];
    }

    public function viewOrganization($id){
        $data = StudentOrganizations::where('id', $id)->first();

        return response()->json($data);
       
    }

    public function updateOrganization(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'org_abbrev' => 'required',
                'program_abbrev' => 'required',
                'org_name' => 'required',
            ]);
    
            try {
              
                StudentOrganizations::where('id', $id)->update($validatedData);
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

    public function removeOrganization($id){
        $data = StudentOrganizations::find($id);
        $data->delete();
        return response()->json(['message' => 'Data removed successfully'], 200);
    }
}
