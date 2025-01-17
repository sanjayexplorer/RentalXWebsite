<?php

namespace App\Http\Middleware;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\CarsBookingDateStatus;
class PartnerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $userId = auth()->user()->id;
            $userStatus = auth()->user()->status;
            if(strcmp($userStatus,'deleted')==0 || strcmp($userStatus, 'inactive') == 0){
                Auth::guard('web')->logout();
                return redirect()->route('login');
            }else{
                $user = User::whereId($userId)->with('roles')->first();
                $role = 'partner';
                if (isset($user->roles[0]->name)) {
                    $role = $user->roles[0]->name;
                }
                if (strcmp($role, 'partner') == 0) {
                    $bookingData = session()->get('bookingData', []);

                    if (
                        !empty($bookingData['carId']) &&
                        !empty($bookingData['firstDate']) &&
                        !empty($bookingData['lastDate'])

                    ) {

                    $url = url()->previous();

                    $urlParts = parse_url($url);

                    $baseUrl = $urlParts['scheme'] . '://' . $urlParts['host'] . $urlParts['path'];


                    $targetUrl = 'https://app.rentalx.co/partner/car/booking/add';

                    if ($baseUrl === $targetUrl) {

                         $deletedRows = CarsBookingDateStatus::where('carId', '=', $bookingData['carId'])
                            ->where('start_date', '<=', $bookingData['firstDate'])
                            ->where('end_date', '>=', $bookingData['lastDate'])
                            ->delete();
                          $request->session()->forget('bookingData');
                    }

                    }

                    return $next($request);
                } else {
                    if (strcmp($role, 'superAdmin') == 0) {
                        return redirect()->route('admin.users.list')->with('message', 'You are not an authorised user');
                    }
                    if (strcmp($role, 'agent') == 0) {
                        return redirect()->route('agent.booking.list')->with('message', 'You are not an authorised user');
                    }
                }
            }
         } else {
            Auth::guard('web')->logout();
            return redirect()->route('login');
        }
    }
}
