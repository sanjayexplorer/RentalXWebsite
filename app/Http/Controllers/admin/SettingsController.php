<?php

namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
class SettingsController extends Controller
{

   
    public function __construct(){
        if (!Auth::check()) {
            return view('auth.login');
        }
        $userId = auth()->user()->id;
        $user = User::whereId($userId)->with('roles')->first();
        $role = 'superAdmin';
        if (isset($user->roles[0]->name)) {
            $role = $user->roles[0]->name;
        }
        if (strcmp($role, 'superAdmin') == 0) {
            return redirect()->route('admin.users.list');
        } elseif (strcmp($role, 'partner') == 0) {
            return redirect()->route('partner.booking.list');
        } elseif (strcmp($role, 'agent') == 0) {
            return redirect()->route('agent.booking.list');
        } else {
            Auth::guard('web')->logout();
            return redirect()->route('login');
        }
    }


    public function index(){
    $user=user::whereId(auth()->user()->id)->whereIn('status', ['active', 'inactive'])->first()->toArray();
    return view('admin.settings.view',compact('user'));
    }

}
