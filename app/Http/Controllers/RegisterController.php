<?php

namespace App\Http\Controllers;

use App\Models\Helper;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function index(){
        return view('guest.register');
    }


    public function storeAccount(Request $request){
        try{
            $validatedData = $request->validate([
                'firstname' => 'required|string',
                'lastname' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8'
            ]);
            $validatedData['employee_number'] = Helper::generateRandomNumbers();
            $validatedData['password'] = Hash::make($validatedData['password']);
            $validatedData['department'] = 1;
            $validatedData['position'] = 5;
            User::create($validatedData);
            return response()->json(['message' => 'Account Added successfully'], 200);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->toArray();
            return response()->json(['errors' => $errors], 422);
        }
    }
}
