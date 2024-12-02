<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ChangePasswordController extends Controller
{
    public function index(){
        $main_title = 'Change Password';
        $nav = 'Settings';
        return view('admin.settings.change_password', compact('main_title', 'nav'));
    }

    public function updatePassword(Request $request)    {

        $user = Auth::user();

         // Validate the request
        $request->validate([
            'current_password' => 'required_unless:changepassword_at,null',
            'new_password' => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ]);

        // If 'changepassword_at' is not null, verify the current password
        if ($user->change_password_at !== null) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['errors' => 'Current password is incorrect'], 400);
            }
        }

        $user->password = Hash::make($request->new_password);
        $user->change_password_at = now(); // Update the timestamp
        $user->save();

        return response()->json(['message' => 'Password changed successfully.']);
    }
}
