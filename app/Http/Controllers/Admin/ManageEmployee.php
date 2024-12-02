<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departments;
use Illuminate\Support\Facades\Hash;
use App\Models\Positions;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Http\Request;

class ManageEmployee extends Controller
{
    public function index(){
        $main_title = 'Manage Employee';
        $nav ='Dashboard';
        $positions = $this->getPositions();
        $departments = $this->getDepartments();
        return view('admin.manage_employee', compact('main_title', 'positions', 'departments', 'nav'));
    }

    public function getEmployees(){
        $response = [];
        $data = User::orderBy('created_at', 'desc')->get();
        foreach($data as $key=>$items){
            $components = $this->buttons($items->id, $items->status);
            $response[] = [
                'no' => ++$key,
                'name' => ucwords($items->firstname.' '.$items->lastname),
                'email' => $items->email,
                'position' => ucwords($items->position_dtls->position),
                'department' => ucwords($items->department_dtls->department),
                'employee_id' => $items->employee_number,
                'status' => $components['badge'],
                'action' => $components['button'],      
            ];
        }
        return response()->json($response, 200);
    }

    public function getPositions(){
        $data = Positions::get();

        return $data;
    }

    public function getDepartments(){
        $data = Departments::get();

        return $data;
    }

    public function storeEmployee(Request $request){
        try{
            $validatedData = $request->validate([
                'firstname' => 'required|string',
                'lastname' => 'required',
                'department' => 'required',
                'position' => 'required',
                'email' => 'required'
            ]);
            $validatedData['status'] = 1;
            $validatedData['password'] = Hash::make(date('Y').''.$request->lastname);
            $validatedData['employee_number'] = date('Y').'-'.$this->generateRandomNumbers();
            User::create($validatedData);
            return response()->json(['message' => 'Employee Added successfully'], 200);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return response()->json(['errors' => $errors], 422);
        }
    }

    function generateRandomNumbers() {
        $randomNumbers = '';
    
        for ($i = 0; $i < 6; $i++) {
            $randomNumbers .= rand(1, 9);
        }
        return $randomNumbers;
    }

    public function buttons($id, $status){
        $buttons = '';
        $badges = '';
        switch($status){
            case 1:
                $buttons = '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                    <button type="button" class="btn btn-outline-warning d-flex align-items-center btn-sm px-3 edit-btn" data-id="'.$id.'"><i class="bx bxs-edit-alt"></i></button>
                    <button type="button" class="btn btn-outline-danger d-flex align-items-center btn-sm px-3 deactivate-btn" data-id="'.$id.'"><i class="bx bxs-user-x"></i></button>
                    <button type="button" class="btn btn-outline-info d-flex align-items-center btn-sm px-3 info-btn" data-id="'.$id.'"><i class="bx bxs-info-circle"></i></button>
                    </div>';
                    $badges = '<span class="badge rounded-pill bg-success">Active</span>';
                    break;
            case 2:
                $buttons = '<div class="btn-group" role="group" aria-label="Basic mixed styles example">
                    <button type="button" class="btn btn-warning d-flex align-items-center btn-sm px-3 edit-btn" data-id="'.$id.'"><i class="bx bxs-edit-alt"></i></button>
                    <button type="button" class="btn btn-success d-flex align-items-center btn-sm px-3 activate-btn" data-id="'.$id.'"><i class="bx bxs-user-check"></i></button>
                    <button type="button" class="btn btn-info d-flex align-items-center btn-sm px-3 info-btn" data-id="'.$id.'"><i class="bx bxs-info-circle"></i></button>
                    </div>';
                    $badges = '<span class="badge rounded-pill bg-danger text-dark">Inactive</span>';
                    break;  
        }   

        $components = [
            'badge' => $badges,
            'button' => $buttons
        ];
        return $components;
    }

    public function activateUser($id){
        $user = User::find($id);
        $user->status = 1;
        $user->save();
        return response()->json(['message' => 'Employee activated successfully'], 200);
    }

    public function deactivateUser($id){
        $user = User::find($id);
        $user->status = 2;
        $user->save();
        return response()->json(['message' => 'Employee deactivated successfully'], 200);
    }

    public function editEmployee($id){
        $response = [];
        $data = User::where('id', $id)->first();

        return response()->json($data, 200);
    }

    public function updateEmployee(Request $request, $id){
        try{
            $validatedData = $request->validate([
                'firstname' => 'required|string',
                'lastname' => 'required',
                'department' => 'required',
                'position' => 'required',
                'email' => 'required'
            ]);
            User::where('id', $id)->update($validatedData);
            return response()->json(['message' => 'Employee updated successfully'], 200);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return response()->json(['errors' => $errors], 422);
        }
    }
}
