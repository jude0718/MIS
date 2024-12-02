<?php

namespace App\Http\Controllers;

use App\Models\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function index(){
        return view('guest.forgot_password');
    }

    public function resetPassword(Request $request){
        // Validate the request
        $validatedData = $request->validate([
            'email' =>'required|email',
        ]);

        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'error' => 'This email does not exist in our records.'
            ], 404);
        }

        $content = [
            'email' => $validatedData['email'],
            'title' => 'Reset Password Link',
            'blade_file' => 'emails.forgot_password',
            'user_id' => $user->id,
            'reset_link' => Helper::generateResetLinkToken($request)
        ];
        Helper::emailNotification($content);

        return response()->json([
            'message' => 'Reset link was sent to your email successfully'
        ], 200); // Use an array for JSON response
    }
}
