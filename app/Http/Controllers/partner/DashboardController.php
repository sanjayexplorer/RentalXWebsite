<?php

namespace App\Http\Controllers\partner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use App\Models\User;
use App\Models\cars;
use App\Models\carsMeta;
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
        $userId=auth()->user()->id;
        $cars=cars::where('user_id','=',$userId)->where('status','NOT LIKE','deleted')->orderBy('created_at','desc')->take(5)->get();
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');        
        $carsThisMonthCount= cars::where('status','NOT LIKE','deleted')->whereDate('created_at','>=', $startOfMonth)->whereDate('created_at','<=',$endOfMonth)->where('user_id','=',$userId)->count();
        $sevenDaysAgo = Carbon::now()->subDays(7)->toDateString();
        $sevenDaysCarsCount = cars::where('status','NOT LIKE','deleted')->whereDate('created_at', '>=', $sevenDaysAgo)->where('user_id','=',$userId)->count();
        return view('partner.dashboard',compact('cars','sevenDaysCarsCount','carsThisMonthCount') );
    }

    public function logout(Request $request)
    {
        Session::flush();
        Auth::logout();
        return redirect()->route('login');
    }
}
