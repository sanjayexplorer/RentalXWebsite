<?php

namespace App\Http\Controllers\agent;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Session;
use Auth;
use App\Models\cars;
use App\Models\carsMeta;
use App\Models\carImages;
use App\Models\master_images;
use Illuminate\Support\Carbon;
class DashboardController extends Controller
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
        $cars = cars::orderBy('created_at', 'desc')->where('status', 'NOT LIKE', 'deleted')->get();
        $partners = User::where('id','!=',auth()->user()->id)
        ->where('status', 'NOT LIKE', 'inactive')
        ->whereHas(
        'roles', function($q){
            $q->where('name','LIKE', 'partner');
        }
        )
        ->with('roles')
        ->orderBy('created_at','Desc')->get();
         $cars = cars::orderBy('created_at','Desc')->where('status', 'NOT LIKE', 'deleted')->get();

         $userId = auth()->user()->id;

        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');        
       $usersThisMonthCount= User::where('status', 'NOT LIKE', 'inactive')->whereDAte('created_at','>=', $startOfMonth)->whereDate('created_at','<=',$endOfMonth)->where('id','!=',$userId)->count();

       $sevenDaysAgo = Carbon::now()->subDays(7)->toDateString();

       $sevenDaysUsersCount = User::where('status', 'NOT LIKE', 'inactive')->whereDate('created_at', '>=', $sevenDaysAgo)->where('id','!=',$userId)->count();

       
        return view('agent.dashboard',compact('cars','partners','usersThisMonthCount','sevenDaysUsersCount'));
    }
    public function logout(Request $request)
    {
        Session::flush();
        Auth::logout();
        return redirect()->route('login');
    }
}
