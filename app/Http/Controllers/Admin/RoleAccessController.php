<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Helper;
use App\Models\Positions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Http\Request;

class RoleAccessController extends Controller
{
    public function index(){
        $main_title = 'Roles Access';
        $nav = 'Dashboard';
        $positions = $this->rolesList();
        return view('admin.roles_access', compact('main_title', 'nav', 'positions'));
    }

    public function rolesList(){
        $data = Positions::get();

        return $data;
    }

    public function fetchRoles(){
        $response = [];
        $data = User::orderBy('created_at', 'desc')->get();
        foreach ($data as $key=>$item) {
            $actions = $this->action($item);
            $response[] = [
                'no' => ++$key,
                'name' => ucwords($item->firstname.' '.$item->lastname),
                'employee_number' => $item->employee_number,
                'position' => ucwords($item->position_dtls->position),
                'action' => $actions['button']
            ];
        }
        return response()->json($response);
    }

    public function action($data){
        $button = '
            <button type="button" class="btn btn-outline-info btn-sm px-3" id="edit-user-modal" data-id="'.$data->id.'"><i class="bi bi-pencil-square"></i></button>
           
        ';

        return [
            'button' => $button,
        ];
    }

    public function storeUser(Request $request) {
        try {
            $validatedData = $request->validate([
                'firstname' => 'required',
                'lastname' => 'required',
                'position' => 'required',
                'email' => 'required|email'
            ]);
    
            try {
                $emp_number = Helper::generateRandomNumbers();
                $validatedData['employee_number'] = $emp_number;
                $validatedData['department'] = 1;
                $validatedData['password'] = Hash::make($emp_number);
                $validatedData['user_image'] = 'profile-img.png';
                $content = [
                    'email' => $validatedData['email'],
                    'password' => $emp_number,
                    'title' => 'Temporary Password',
                    'blade_file' => 'emails.user_password'
                ];

                Helper::emailNotification($content);
                User::create($validatedData);
                DB::commit();
                return response()->json(['message' => 'Account added successfully'], 200);
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

    public function viewUser($id){
        $data = User::where('id', $id)->first();

        return response()->json($data);
    }

    public function updateUser(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'position' => 'required',
            ]);
    
            try {
                User::where('id', $id)->update($validatedData);
                DB::commit();
                return response()->json(['message' => 'Account Updated successfully'], 200);
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
}
