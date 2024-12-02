<?php

namespace App\Http\Controllers;

use App\Models\Departments;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function index(){
        $departments = $this->departmentList();
        return view('guest.login', compact('departments'));
    }

    public function login(Request $request){

        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6',
                'department' => 'required'
            ]);

            if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
                $user = Auth::user();

                if ($user->department != $credentials['department']) {
                    Auth::logout(); // Log the user out
                    return response()->json(['errors' => ['The selected department does not match the user\'s department.']], 422);
                }

                $request->session()->regenerate();
                return response()->json(['message' => 'Login successful'], 200);
            }

            // Handle invalid credentials
            return response()->json(['errors' => ['Invalid credentials']], 422);
        } catch (ValidationException $e) {
            // Handle validation errors
            $errors = $e->validator->errors()->toArray();
            return response()->json(['errors' => $errors], 422);
        }
    }


    public function departmentList(){
        $data = Departments::get();

        return $data;
    }

    public function logout(Request $request){
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
