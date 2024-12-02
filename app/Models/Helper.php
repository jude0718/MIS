<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Helper extends Model
{
    use HasFactory;

    public static function generateRandomNumbers() {
        $randomNumbers = [];
        for ($i = 0; $i < 5; $i++) {
            $randomNumbers[] = rand(0, 9); // generates a random digit between 0 and 9
        }
        return date('Y') . '-' . implode('', $randomNumbers);
    }

    public static function emailNotification($content){
        $user_email = $content['email'];
        Mail::send($content['blade_file'], $content, function($message) use ($user_email, $content){
            $message->to($user_email, 'EMPLOYEE OF CEIT')->subject
            ( $content['title'] );
            $message->from('miscvsucapstone@gmail.com', 'CEIT');
        });
    }

    public static function generateResetLinkToken($request) {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        $token = Str::random(40);
            DB::table('password_resets')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => $token,
                    'created_at' => now()
                ]
            );
        $resetLink = url("reset-password-form/{$user->id}?token={$token}");
        return $resetLink;
    }

    public static function storeNotifications($transact_by, $message_to_self, $message_to_others){
        $users = User::whereIn('position', [1, 2, 3, 5])->get();
        foreach ($users as $user) {
            Notifications::create([
                'transact_by' => $transact_by,
                'sent_to' => $user->id,
                'message_to_self' => $message_to_self,
                'message_to_others' => $message_to_others,
                'created_at' => Carbon::now('Asia/Manila'),  // Set accurate timestamp
                'updated_at' => Carbon::now('Asia/Manila')   // Set updated timestamp as well
            ]);
        }
    }


}
