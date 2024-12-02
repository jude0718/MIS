<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function index(){
        $main_title = 'Profile';
        $nav = 'Users';
      
        return view('admin.user_profile', compact('main_title', 'nav'));
    }
}
