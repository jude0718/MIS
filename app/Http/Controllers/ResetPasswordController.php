<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    public function index($id, Request $request){
        $token = $request->query('token');
        $resetEntry = DB::table('password_resets')
                        ->where('token', $token)
                        ->where('created_at', '>=', Carbon::now()->subMinutes(15)) // Optional: set token expiration time
                        ->first();

        if (!$resetEntry || $resetEntry->email !== User::find($id)->email) {
            return response()->json(['message' => 'Invalid or expired link'], 403);
        }

        DB::table('password_resets')->where('token', $token)->delete();

        return view('guest.reset_password', ['user_id' => $id]);
    }

    public function resetPassword(Request $request, $id)    {

        $user = User::find($id);

         // Validate the request
        $request->validate([
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['message' => 'Password reset successfully.'], 200);
    }
}
