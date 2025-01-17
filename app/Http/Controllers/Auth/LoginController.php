<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OtpVerification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Config;
class LoginController extends Controller


{
    public function index() {

        
        // $userAdmin=User::whereId(6)->first();
        // $userAdmin->assignRole('partner');
        // echo "Done";
        // die;
        if(Auth::check()){
            $userId=auth()->user()->id;
            $user=User::whereId($userId)->with('roles')->first();
            
            $role = 'agent';
            if(isset($user->roles[0]->name))
            {
                $role = $user->roles[0]->name;
            }
            if(strcmp($role,'superAdmin')==0)
            {
                return redirect()->route('admin.users.list');
            }
            if(strcmp($role,'agent')==0)
            {
                return redirect()->route('agent.booking.list');
            }
            else if(strcmp($role,'partner')==0)
            {
                return redirect()->route('partner.booking.list');
            }
            else
             {
              Auth::guard('web')->logout();
              return redirect()->route('login');
            }
        }else{
            return view('auth.login');
        }
    }

    public function login(Request $request)
    {
        $messages = [
            'mobile.required' => 'Mobile number is required.',
            'mobile.numeric' => 'Mobile number must be numeric.',
            'password.required' => 'Password is required.',
            'g-recaptcha-response.required' => 'Captcha is required.',
        ];

        $request->validate([
            'mobile' => 'required|numeric',
            'password' => 'required',
            'g-recaptcha-response' => 'required',
        ], $messages);


        $recaptchaSecretKey=env('RECAPTCHA_SECRET');
        if(!$recaptchaSecretKey){

            Session::flash('errorMessage', 'Recaptcha secret key Not found');
            return redirect()->route('login');

        }else{

            $recaptchaResponse = $request->input('g-recaptcha-response');

            $googleRecaptcha=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$recaptchaSecretKey."&response=".$recaptchaResponse."&remoteip=".$_SERVER['REMOTE_ADDR']), true);

            $mobile = $request->mobile;
            $remember = true;

            if (Auth::attempt(['mobile' => $mobile, 'password' => $request->password]) && $googleRecaptcha['success']) {
                $user = Auth::user();
                if (strcmp($user->status,'inactive')==0 ||strcmp($user->status,'deleted')==0 ) {
                    if(strcmp($user->status,'inactive')==0){
                        Session::flash('errorMessage', 'Your account is currently not active. Please contact admin to activate the account.');
                        Auth::logout();
                        return redirect()->route('login');
                    }
                    if(strcmp($user->status,'deleted')==0){
                        Session::flash('errorMessage', 'Invalid login credentials');
                        Auth::logout();
                        return redirect()->route('login');
                    }
                } else {
                    $user = User::whereId($user->id)->where('status','!=','deleted')->with('roles')->first();
                    $role = $user->roles->first()->name ?? 'agent';

                    switch ($role) {
                        case 'superAdmin':
                            return redirect()->route('admin.users.list');
                        case 'agent':
                            return redirect()->route('agent.booking.list');
                        case 'partner':
                            return redirect()->route('partner.booking.list');
                        default:
                            Auth::logout();
                            return redirect()->route('login');
                    }
                }
            } else {
                Session::flash('errorMessage', 'Invalid login credentials');
                Auth::logout();
                return redirect()->route('login');
            }

        }


    }

}
