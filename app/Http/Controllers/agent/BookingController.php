<?php
namespace App\Http\Controllers\agent;
use App\Http\Controllers\Controller;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Session;
use Auth;
use Helper;
use App\Models\User;
use App\Models\cars;
use App\Models\users;
use App\Models\UserMetas;
use App\Models\carsMeta;
use App\Models\carImages;
use App\Models\CarBooking;
use App\Models\bookingMeta;
use App\Models\booking_payments;
use App\Models\CarsBookingDateStatus;
use App\Models\master_images;
use App\Models\shareCars;
use Carbon\Carbon;
use Intervention\Image\Facades\Image;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
// use App\Events\BookingSender;
class BookingController extends Controller
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

    protected function getBookedCarsQuery()
    {
        date_default_timezone_set('Asia/Kolkata');
        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);

        $agentId = auth()->user()->id;

        return CarBooking::where('booking_owner_id','=',$agentId)
        ->where(function ($query) {
                $query->whereNull('status')->orWhere('status','LIKE','delivered');
        })
        ->orderByRaw("CASE
            WHEN status = 'delivered' THEN dropoff_date
            WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
            ELSE pickup_date
        END ASC");

    }

    protected function getBookedCarsWithFilter($request)
    {
        date_default_timezone_set('Asia/Kolkata');
        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);

        $customerName = isset($request->customerName) ? $request->customerName : null;
        // $countryCode = isset($request->countryCode) ? $request->countryCode : null;
        $customerMobile = isset($request->customerMobile) ? $request->customerMobile : null;

        $agentId = auth()->user()->id;

        $requestData = $request->all();

        if (isset($requestData['form_data']))
        {
            parse_str($requestData['form_data'], $formArray);
            $start_date = $formArray['start_date'] ?? null;
            $end_date = $formArray['end_date'] ?? null;
            // $booking_ids = $formArray['booking_id'] ?? [];
            // for booking id give empty
            $formArray['booking_id'] = [];
        }

        return CarBooking::where('booking_owner_id','=',$agentId)->where(function ($query) use ($customerName, $customerMobile) {
                if ($customerName) {
                    $query->where('customer_name', '=', $customerName);
                }

                if ($customerMobile) {
                    $query->where('customer_mobile', '=', $customerMobile);
                }
            })
            ->where(function ($query) use ($formArray) {
                if (!empty($formArray['start_date']) && !empty($formArray['end_date'])) {
                    if (!empty($formArray['booking_id'])) {
                        $query->whereIn('id', $formArray['booking_id'])
                            ->whereDate('start_date', '>=', $formArray['start_date'])
                            ->whereDate('end_date', '<=', $formArray['end_date']);
                    }
                    else {
                        // Only start date and end date provided
                        $query->whereDate('start_date', '>=', $formArray['start_date'])->whereDate('end_date', '<=', $formArray['end_date']);
                    }
                } elseif (!empty($formArray['start_date']) && empty($formArray['end_date']) && !empty($formArray['booking_id'])) {
                    // Only start date provided with booking IDs
                    $query->whereIn('id', $formArray['booking_id'])->whereDate('start_date', '=', $formArray['start_date']);
                } elseif (!empty($formArray['start_date']) && empty($formArray['end_date']) && empty($formArray['booking_id'])) {
                    // Only start date provided
                    $query->where('start_date', '=', $formArray['start_date']);
                } elseif (empty($formArray['start_date']) && empty($formArray['end_date']) && !empty($formArray['booking_id'])) {
                    $query->whereIn('id', $formArray['booking_id']);
                }
            })
            ->where(function ($query) {
                $query->whereNull('status')->orWhere('status','LIKE','delivered');
            })
            ->orderByRaw("CASE
                WHEN status = 'delivered' THEN dropoff_date
                WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
                ELSE pickup_date
            END ASC");

    }


    public function list()
    {
        $countBookedCars = $this->getBookedCarsQuery()
        ->count();

        $booked_cars = $this->getBookedCarsQuery()
        ->take(5)
        ->get();

        $allBookedCars = $this->getBookedCarsQuery()
        ->get();

        // dd($allBookedCars);
        date_default_timezone_set('Asia/Kolkata');
        $currentDateTime = Carbon::now();

        $pickup_date_time_after_oneday = '';
        $dropoff_date_time_after_oneday = '';
        foreach ($allBookedCars as $value) {
            $pickup_date_time = $value->pickup_date;
            $dropoff_date_time = $value->dropoff_date;
            $carbonPickupDateTime = Carbon::parse($pickup_date_time);
            $carbonDropoffDateTime = Carbon::parse($dropoff_date_time);
            $pickup_date_time_after_oneday = $carbonPickupDateTime->copy()->addDay();
            $dropoff_date_time_after_oneday = $carbonDropoffDateTime->copy()->addDay();
            // this will check if current date/time  is one day later than both ( pickup date/time and dropoff date/time )
            if($pickup_date_time_after_oneday->lt($currentDateTime) && $dropoff_date_time_after_oneday->lt($currentDateTime)){
                CarBooking::whereId($value->id)->update([
                    'status'=>'collected',
                    'delivered_time'=>$dropoff_date_time_after_oneday,
                    'collected_time'=>$dropoff_date_time_after_oneday,
                ]);
            }else{
                // this will check if current date/time  is one day later than pickup date/time
                if($pickup_date_time_after_oneday->lt($currentDateTime)){
                    if ($value->status === null) {
                        CarBooking::whereId($value->id)->update([
                                'status'=>'delivered',
                                'delivered_time'=>$pickup_date_time_after_oneday
                        ]);
                    }
                }
                // this will check if current date/time  is one day later than dropoff date/time
                if($dropoff_date_time_after_oneday->lt($currentDateTime)){
                        if(strcmp($value->status,'delivered')==0){
                        CarBooking::whereId($value->id)->update([
                            'status'=>'collected',
                            'collected_time'=>$dropoff_date_time_after_oneday
                        ]);
                    }
                }
            }
        }

        return view('agent.bookings.list', compact('booked_cars','allBookedCars','countBookedCars') );
    }


    ////// loadmore list start here //////
    public function loadMoreBookings(Request $request)
    {
        $agentId = auth()->user()->id;

        $currentDate = Carbon::now();

        $test = $request->checkLoadMore;

        if(strcmp($request->checkLoadMore, 'list') == 0)
        {
            $offset = $request->input('offset', 0);

            $booked_cars = $this->getBookedCarsQuery()
            ->skip($offset)
            ->take(5)
            ->get();
        }
        elseif((strcmp($request->checkLoadMore, 'desktop_search') == 0) || (strcmp($request->checkLoadMore, 'mobile_search') == 0) )
        {
            $offset = $request->input('offset', 0);

            $booked_cars = $this->getBookedCarsWithFilter($request)
            ->skip($offset)
            ->take(5)
            ->get();
        }
        elseif((strcmp($request->checkLoadMore, 'autocomplete_search') == 0) )
        {
            $offset = $request->input('offset', 0);

            $booked_cars = $this->getBookedCarsWithFilter($request)
            ->skip($offset)
            ->take(5)
            ->get();
        }


        if(count($booked_cars))
        {
            $result =  $this->collectionOfBookedCars($booked_cars);

            $desktopBookingHtml = $result['desktopBookingHtml'];
            $mobileBookingHtml = $result['mobileBookingHtml'];

            return response()->json(['success' => true, 'error' => false, 'booked_cars'=> $booked_cars, 'desktopBookingHtml'=>$desktopBookingHtml, 'mobileBookingHtml'=> $mobileBookingHtml,
            'test'=> $test, 'offset'=> $offset
            ]);

        }
        else
        {
            return response()->json(['success' => false, 'error' => true, 'booked_cars'=>'', 'desktopBookingHtml'=>'','mobileBookingHtml'=>'', 'test'=>$test, 'offset'=>$offset
            ]);
        }

    }

    ////// cars calendar start here  //////
    public function calendar(Request $request)
    {
        $agentId = auth()->user()->id;

        $sharedCarId = shareCars::where('agent_id','=',$agentId)->pluck('car_id');

        $userIds = cars::whereIn('id',$sharedCarId)->where('status', 'NOT LIKE', 'deleted')->orderBy('created_at', 'desc')->pluck('user_id');

        $carIds = cars::whereIn('user_id',$userIds)->where('status', 'NOT LIKE', 'deleted')->orderBy('created_at', 'desc')->pluck('id');

        $cars = cars::whereIn('id', $sharedCarId)->where('status', 'NOT LIKE', 'deleted')->orderBy('created_at', 'desc')
        ->get();

        $partnerIds = $cars->pluck('user_id');

        $partners = User::join('cars', 'users.id', '=', 'cars.user_id')
        ->whereIn('users.id', $partnerIds)
        ->where('users.id', '!=', auth()->user()->id)
        ->where('users.status', 'NOT LIKE', 'inactive')
        ->where('cars.status', 'LIKE', 'available')
        ->whereHas('roles', function ($q) {
            $q->where('name', 'LIKE', 'partner');
        })
        ->with('roles')
        ->select('users.*')
        ->orderBy('users.created_at', 'desc')
        ->distinct()
        ->paginate(3, ['users.*']);

        $car_type = '';
        $car_name = '';
        $partner_id = '';

        $requestData = $request->all();

        $start_date = isset($requestData['start_date']) ? $requestData['start_date'] : '';
        $end_date = isset($requestData['end_date']) ? $requestData['end_date'] : '';
        $car_type = isset($requestData['car_type']) ? $requestData['car_type'] : '';
        $transmission = isset($requestData['transmission']) ? $requestData['transmission'] : '';
        $car_name = isset($requestData['car_name']) ? $requestData['car_name'] : '';
        $partner_id = isset($requestData['partner_id']) ? $requestData['partner_id'] : '';

        $formArray = [

            'start_date' => isset($requestData['start_date']) ? $requestData['start_date'] : null,
            'end_date' =>isset($requestData['end_date']) ? $requestData['end_date'] : null,
            'car_type' => $requestData['car_type'] ?? [],
            'transmission' => $requestData['transmission'] ?? [],
            'car_name' => $requestData['car_name'] ?? [],
            'partner_id' => $requestData['partner_id'] ?? [],
        ];

        if($requestData){


            $cars = cars::select('cars.id','cars.name','cars.registration_number','cars.transmission','cars.fuel_type',
            'cars.manufacturing_year','cars.car_type','cars.price','cars.seats','cars.status',
            'cars.user_id','cars.created_at','cars.updated_at')
            ->whereIn('cars.id',$sharedCarId)
            ->where('cars.status','NOT LIKE','deleted')
            ->where(function($query) use($formArray,$userIds,$sharedCarId){

            if( (isset($formArray['partner_id'] )&& !empty($formArray['partner_id'])) &&
                (isset($formArray['car_name'])&& !empty($formArray['car_name'])) &&
                (isset($formArray['car_type']) &&  !empty($formArray['car_type'])) &&
                (isset($formArray['transmission']) &&  !empty($formArray['transmission'])) &&
                (isset($formArray['start_date']) && !empty($formArray['start_date']) )&&
                (isset($formArray['end_date'])&& !empty($formArray['end_date']))
            ){
                // echo' when all 4 filter applied';

                $query->whereNotExists(function ($subQuery) use ($formArray) {

                    $subQuery->select(DB::raw(1))
                    ->from('car_bookings')
                    ->whereRaw('cars.id = car_bookings.carId')
                    ->where(function ($query) use ($formArray) {
                        $query->where(function ($q) use ($formArray) {
                            $q->whereBetween('car_bookings.start_date', [$formArray['start_date'], $formArray['end_date']])
                                ->orWhereBetween('car_bookings.end_date', [$formArray['start_date'], $formArray['end_date']]);
                        });
                    });
                })->whereIn('cars.name',$formArray['car_name'])->whereIn('cars.car_type',$formArray['car_type'])->whereIn('cars.transmission',$formArray['transmission'])->whereIn('cars.user_id',$formArray['partner_id']);

            }



            elseif( !empty($formArray['car_type']) &&
            !empty($formArray['transmission'])
            && !empty($formArray['car_name'])
            &&!empty($formArray['partner_id']) && !empty($formArray['start_date']) && empty($formArray['end_date']) ){
            // echo 'allfilter applied except endDate';
            $query->whereNotExists(function ($subQuery) use ($formArray) {
                $subQuery->select(DB::raw(1))
                    ->from('car_bookings')
                    ->whereRaw('cars.id = car_bookings.carId')
                    ->where(function ($query) use ($formArray) {
                        $query->where('car_bookings.start_date', '<=', $formArray['start_date'])
                        ->where('car_bookings.end_date', '>=', $formArray['start_date']);
                    });
            })->whereIn('cars.name',$formArray['car_name'])->whereIn('cars.car_type',$formArray['car_type'])->whereIn('cars.transmission',$formArray['transmission'])->whereIn('cars.user_id',$formArray['partner_id']);

             }

              elseif( (!empty($formArray['transmission']) && !empty($formArray['car_name']) && !empty($formArray['partner_id']) && !empty($formArray['start_date'])) &&  (empty($formArray['end_date']) && empty($formArray['car_type']))  ){
                // echo'allfilter applied except endDate and car_type';
                 $query->whereNotExists(function ($subQuery) use ($formArray) {
                    $subQuery->select(DB::raw(1))
                        ->from('car_bookings')
                        ->whereRaw('cars.id = car_bookings.carId')
                        ->where(function ($query) use ($formArray) {
                            $query->where('car_bookings.start_date', '<=', $formArray['start_date'])
                            ->where('car_bookings.end_date', '>=', $formArray['start_date']);
                        });
                })->whereIn('cars.transmission',$formArray['transmission'])->whereIn('cars.name',$formArray['car_name'])->whereIn('cars.user_id',$formArray['partner_id']);

             }
              elseif( !empty($formArray['car_type']) &&  !empty($formArray['transmission'])
                && !empty($formArray['partner_id']) && !empty($formArray['start_date']) && empty($formArray['end_date']) && empty($formArray['car_name'])  ){
                // echo 'allfilter applied except endDate and car_name';
                 $query->whereNotExists(function ($subQuery) use ($formArray) {
                    $subQuery->select(DB::raw(1))
                        ->from('car_bookings')
                        ->whereRaw('cars.id = car_bookings.carId')
                        ->where(function ($query) use ($formArray) {
                            $query->where('car_bookings.start_date', '<=', $formArray['start_date'])
                            ->where('car_bookings.end_date', '>=', $formArray['start_date']);
                        });
                })->whereIn('cars.transmission',$formArray['transmission'])->whereIn('cars.car_type',$formArray['car_type'])->whereIn('cars.user_id',$formArray['partner_id']);

             }
              elseif( !empty($formArray['car_type'])  && !empty($formArray['transmission'])
                && !empty($formArray['car_name'])
                 && !empty($formArray['start_date']) && empty($formArray['end_date']) ){
                // echo'allfilter applied except endDate and partner_id';
                 $query->whereNotExists(function ($subQuery) use ($formArray) {
                    $subQuery->select(DB::raw(1))
                        ->from('car_bookings')
                        ->whereRaw('cars.id = car_bookings.carId')
                        ->where(function ($query) use ($formArray) {
                            $query->where('car_bookings.start_date', '<=', $formArray['start_date'])
                            ->where('car_bookings.end_date', '>=', $formArray['start_date']);
                        });
                })->whereIn('cars.transmission',$formArray['transmission'])->whereIn('cars.car_type',$formArray['car_type'])->whereIn('cars.name',$formArray['car_name']);

             }
              elseif( (!empty($formArray['car_type']) && !empty($formArray['transmission'])
                && !empty($formArray['car_name'])
                && !empty($formArray['partner_id']))
                 && (empty($formArray['start_date']) && empty($formArray['end_date'])) ){
                // echo'allfilter applied except endDate and startDate';
                 $query->whereIn('cars.transmission',$formArray['transmission'])->whereIn('cars.car_type',$formArray['car_type'])->whereIn('cars.name',$formArray['car_name'])->whereIn('cars.user_id',$formArray['partner_id']);

             }
              elseif( ( !empty($formArray['car_name']) && !empty($formArray['transmission'])
                && !empty($formArray['partner_id']))
                 && (empty($formArray['start_date']) && empty($formArray['end_date']) && empty($formArray['car_type']) ) ){
                // echo'allfilter applied except endDate,startDate and car_type';
                 $query->whereIn('cars.transmission',$formArray['transmission'])->whereIn('cars.name',$formArray['car_name'])->whereIn('cars.user_id',$formArray['partner_id']);

             }
              elseif( ( !empty($formArray['car_type']) && !empty($formArray['transmission'])
                && !empty($formArray['partner_id']))
                 && (empty($formArray['start_date']) && empty($formArray['end_date']) && empty($formArray['car_name']) ) ){
                // echo'allfilter applied except endDate,startDate and car_name';
                 $query->whereIn('cars.transmission',$formArray['transmission'])->whereIn('cars.car_type',$formArray['car_type'])->whereIn('cars.user_id',$formArray['partner_id']);
             }

              elseif( ( !empty($formArray['car_type']) && !empty($formArray['transmission'])
                && !empty($formArray['car_name']))
                 && (empty($formArray['start_date']) && empty($formArray['end_date']) && empty($formArray['partner_id']) ) ){
                // echo'allfilter applied except endDate,startDate and partner_id';
                 $query->whereIn('cars.transmission',$formArray['transmission'])->whereIn('cars.car_type',$formArray['car_type'])->whereIn('cars.name',$formArray['car_name']);
             }

              elseif( !empty($formArray['car_type'])
                 && (empty($formArray['start_date']) && empty($formArray['end_date']) && empty($formArray['transmission']) && empty($formArray['partner_id']) && empty($formArray['car_name']) ) ){
                // echo'only car_type';
                 $query->whereIn('cars.car_type',$formArray['car_type']);
             }

              elseif( !empty($formArray['partner_id'])
                 && (empty($formArray['start_date']) && empty($formArray['end_date']) && empty($formArray['transmission']) && empty($formArray['car_type']) && empty($formArray['car_name']) ) ){
                // echo'only partner_id';
                 $query->whereIn('cars.user_id',$formArray['partner_id']);
                //  dd($query);
             }

              elseif( !empty($formArray['car_name'])
                 && (empty($formArray['start_date']) && empty($formArray['end_date']) && empty($formArray['transmission']) && empty($formArray['car_type']) && empty($formArray['partner_id']) ) )
                {
                //  echo'only car_name';
                 $query->whereIn('cars.name',$formArray['car_name']);

             }
             elseif( !empty($formArray['transmission'])
             && (empty($formArray['start_date']) && empty($formArray['end_date']) && empty($formArray['car_type']) && empty($formArray['partner_id']) && empty($formArray['car_name'] ) ) )
            {
            //  echo'only transmission';
             $query->whereIn('cars.transmission',$formArray['transmission']);
            //  dd($query);
         }

              elseif( !empty($formArray['start_date']) && (empty($formArray['car_name']) && empty($formArray['transmission']) && empty($formArray['end_date']) && empty($formArray['car_type']) && empty($formArray['partner_id']) && empty($formArray['transmission']) ) )
              {
                // echo'only start_date';
                  $query->whereNotExists(function ($subQuery) use ($formArray) {
                    $subQuery->select(DB::raw(1))
                        ->from('car_bookings')
                        ->whereRaw('cars.id = car_bookings.carId')
                        ->where(function ($query) use ($formArray) {
                            $query->where('car_bookings.start_date', '<=', $formArray['start_date'])
                            ->where('car_bookings.end_date','>=', $formArray['start_date']);
                        });
                });

             }
              elseif( (!empty($formArray['start_date']) && !empty($formArray['end_date']) ) && ( empty($formArray['transmission']) && empty($formArray['car_name']) && empty($formArray['car_type']) && empty($formArray['partner_id'])  ) )
             {

                // echo'only both start_date and end_date';

                 $query->whereNotExists(function ($subQuery) use ($formArray) {
                    $subQuery->select(DB::raw(1))
                    ->from('car_bookings')
                    ->whereRaw('cars.id = car_bookings.carId')
                    ->where(function ($query) use ($formArray) {
                        $query->where(function ($q) use ($formArray) {
                            $q->whereBetween('car_bookings.start_date', [$formArray['start_date'], $formArray['end_date']])
                                ->orWhereBetween('car_bookings.end_date', [$formArray['start_date'], $formArray['end_date']]);
                        });
                    });
                 });

             }
              elseif( (!empty($formArray['start_date']) && !empty($formArray['end_date']) && !empty($formArray['car_name']) ) && ( empty($formArray['car_type']) &&
              empty($formArray['transmission']) && empty($formArray['partner_id']) ) )
             {

                // echo' both start_date and end_date with car_name';

                 $query->whereNotExists(function ($subQuery) use ($formArray) {
                    $subQuery->select(DB::raw(1))
                    ->from('car_bookings')
                    ->whereRaw('cars.id = car_bookings.carId')
                    ->where(function ($query) use ($formArray) {
                        $query->where(function ($q) use ($formArray) {
                            $q->whereBetween('car_bookings.start_date', [$formArray['start_date'], $formArray['end_date']])
                                ->orWhereBetween('car_bookings.end_date', [$formArray['start_date'], $formArray['end_date']]);
                        });
                    });
                 })->whereIn('cars.name',$formArray['car_name']);

             }
              elseif( (!empty($formArray['start_date']) && !empty($formArray['end_date']) && !empty($formArray['car_type']) ) && ( empty($formArray['car_name']) && empty($formArray['transmission']) && empty($formArray['partner_id']) ) )
             {

                // echo' both start_date and end_date with car_type';

                 $query->whereNotExists(function ($subQuery) use ($formArray) {
                    $subQuery->select(DB::raw(1))
                    ->from('car_bookings')
                    ->whereRaw('cars.id = car_bookings.carId')
                    ->where(function ($query) use ($formArray) {
                        $query->where(function ($q) use ($formArray) {
                            $q->whereBetween('car_bookings.start_date', [$formArray['start_date'], $formArray['end_date']])
                                ->orWhereBetween('car_bookings.end_date', [$formArray['start_date'], $formArray['end_date']]);
                        });
                    });
                 })->whereIn('cars.car_type',$formArray['car_type']);

             }
              elseif( (!empty($formArray['start_date']) && !empty($formArray['end_date']) && !empty($formArray['partner_id']) ) && ( empty($formArray['transmission']) &&  empty($formArray['car_name']) && empty($formArray['car_type']) && empty($formArray['transmission']) ) )
             {

                // echo' both start_date and end_date with partner_id';

                 $query->whereNotExists(function ($subQuery) use ($formArray) {
                    $subQuery->select(DB::raw(1))
                    ->from('car_bookings')
                    ->whereRaw('cars.id = car_bookings.carId')
                    ->where(function ($query) use ($formArray) {
                        $query->where(function ($q) use ($formArray) {
                            $q->whereBetween('car_bookings.start_date', [$formArray['start_date'], $formArray['end_date']])
                                ->orWhereBetween('car_bookings.end_date', [$formArray['start_date'], $formArray['end_date']]);
                        });
                    });
                 })->whereIn('cars.user_id',$formArray['partner_id']);

             }
              elseif( (!empty($formArray['start_date']) && !empty($formArray['end_date']) && !empty($formArray['partner_id']) && !empty($formArray['car_type']) ) && empty($formArray['car_name']) )
             {

                // echo' both start_date and end_date with partner_id and car_type except car_name ';

                 $query->whereNotExists(function ($subQuery) use ($formArray) {
                    $subQuery->select(DB::raw(1))
                    ->from('car_bookings')
                    ->whereRaw('cars.id = car_bookings.carId')
                    ->where(function ($query) use ($formArray) {
                        $query->where(function ($q) use ($formArray) {
                            $q->whereBetween('car_bookings.start_date', [$formArray['start_date'], $formArray['end_date']])
                                ->orWhereBetween('car_bookings.end_date', [$formArray['start_date'], $formArray['end_date']]);
                        });
                    });
                 })->whereIn('cars.user_id',$formArray['partner_id'])->whereIn('cars.car_type',$formArray['car_type']);

             }
              elseif( (!empty($formArray['start_date']) && !empty($formArray['end_date']) && !empty($formArray['partner_id']) && !empty($formArray['car_name']) ) && empty($formArray['car_type'])  )
             {

                // echo' both start_date and end_date with partner_id and car_name except car_type';

                 $query->whereNotExists(function ($subQuery) use ($formArray) {
                    $subQuery->select(DB::raw(1))
                    ->from('car_bookings')
                    ->whereRaw('cars.id = car_bookings.carId')
                    ->where(function ($query) use ($formArray) {
                        $query->where(function ($q) use ($formArray) {
                            $q->whereBetween('car_bookings.start_date', [$formArray['start_date'], $formArray['end_date']])
                                ->orWhereBetween('car_bookings.end_date', [$formArray['start_date'], $formArray['end_date']]);
                        });
                    });
                 })->whereIn('cars.user_id',$formArray['partner_id'])->whereIn('cars.car_type',$formArray['car_name']);

             }
              elseif( (!empty($formArray['start_date']) && !empty($formArray['end_date']) && !empty($formArray['car_type']) && !empty($formArray['car_name']) )  && empty($formArray['partner_id']) )
             {

                // echo' both date with car_type and car_name except partner_id';

                 $query->whereNotExists(function ($subQuery) use ($formArray) {
                    $subQuery->select(DB::raw(1))
                    ->from('car_bookings')
                    ->whereRaw('cars.id = car_bookings.carId')
                    ->where(function ($query) use ($formArray) {
                        $query->where(function ($q) use ($formArray) {
                            $q->whereBetween('car_bookings.start_date', [$formArray['start_date'], $formArray['end_date']])
                                ->orWhereBetween('car_bookings.end_date', [$formArray['start_date'], $formArray['end_date']]);
                        });
                    });
                 })->whereIn('cars.name',$formArray['car_name'])->whereIn('cars.car_type',$formArray['car_type']);

             }
             elseif( (!empty($formArray['start_date']) && !empty($formArray['end_date']) && !empty($formArray['car_type']) && !empty($formArray['car_name']) )  && empty($formArray['partner_id']) )
             {
                // echo' both date with car_type and car_name except partner_id';
                 $query->whereNotExists(function ($subQuery) use ($formArray) {
                    $subQuery->select(DB::raw(1))
                    ->from('car_bookings')
                    ->whereRaw('cars.id = car_bookings.carId')
                    ->where(function ($query) use ($formArray) {
                        $query->where(function ($q) use ($formArray) {
                            $q->whereBetween('car_bookings.start_date', [$formArray['start_date'], $formArray['end_date']])
                                ->orWhereBetween('car_bookings.end_date', [$formArray['start_date'], $formArray['end_date']]);
                        });
                    });
                 })->whereIn('cars.name',$formArray['car_name'])->whereIn('cars.car_type',$formArray['car_type']);
             }


              elseif( (!empty($formArray['start_date']) && !empty($formArray['car_type']) ) && (empty($formArray['car_name']) && empty($formArray['partner_id']) && empty($formArray['end_date'])  ) )
             {

                // echo'  start_date with car_type';

                 $query->whereNotExists(function ($subQuery) use ($formArray) {
                    $subQuery->select(DB::raw(1))
                        ->from('car_bookings')
                        ->whereRaw('cars.id = car_bookings.carId')
                        ->where(function ($query) use ($formArray) {
                            $query->where('car_bookings.start_date', '<=', $formArray['start_date'])
                            ->where('car_bookings.end_date', '>=', $formArray['start_date']);
                        });
                })->whereIn('cars.car_type',$formArray['car_type']);

             }
            elseif((!empty($formArray['start_date']) && !empty($formArray['car_name']) ) && (empty($formArray['car_type']) && empty($formArray['partner_id']) && empty($formArray['end_date'])  ))

             {

                // echo'  start_date with car_name';

                 $query->whereNotExists(function ($subQuery) use ($formArray) {
                    $subQuery->select(DB::raw(1))
                        ->from('car_bookings')
                        ->whereRaw('cars.id = car_bookings.carId')
                        ->where(function ($query) use ($formArray) {
                            $query->where('car_bookings.start_date', '<=', $formArray['start_date'])
                            ->where('car_bookings.end_date', '>=', $formArray['start_date']);
                        });
                })->whereIn('cars.name',$formArray['car_name']);

             }
              elseif((!empty($formArray['start_date']) && !empty($formArray['partner_id']) ) && (empty($formArray['car_name']) && empty($formArray['car_type']) && empty($formArray['end_date'])  ))
             {

                // echo'  start_date with partner_id';

                 $query->whereNotExists(function ($subQuery) use ($formArray) {
                    $subQuery->select(DB::raw(1))
                        ->from('car_bookings')
                        ->whereRaw('cars.id = car_bookings.carId')
                        ->where(function ($query) use ($formArray) {
                            $query->where('car_bookings.start_date', '<=', $formArray['start_date'])
                            ->where('car_bookings.end_date', '>=', $formArray['start_date']);
                        });
                })->whereIn('cars.user_id',$formArray['partner_id']);

             }
             elseif((!empty($formArray['start_date']) &&  !empty($formArray['transmission']) && empty($formArray['partner_id']) ) && (empty($formArray['car_name']) && empty($formArray['car_type']) && empty($formArray['end_date'])  ))
             {

                // echo'  start_date with transmission';

                 $query->whereNotExists(function ($subQuery) use ($formArray) {
                    $subQuery->select(DB::raw(1))
                        ->from('car_bookings')
                        ->whereRaw('cars.id = car_bookings.carId')
                        ->where(function ($query) use ($formArray) {
                            $query->where('car_bookings.start_date', '<=', $formArray['start_date'])
                            ->where('car_bookings.end_date', '>=', $formArray['start_date']);
                        });
                })->whereIn('cars.transmission',$formArray['transmission']);

             }

             // Combination of start_date, car_type, and transmission
            elseif (!empty($formArray['start_date']) && !empty($formArray['car_type']) && !empty($formArray['transmission'])
            && empty($formArray['car_name']) && empty($formArray['partner_id']) && empty($formArray['end_date'])) {

            $query->whereNotExists(function ($subQuery) use ($formArray) {
                $subQuery->select(DB::raw(1))
                    ->from('car_bookings')
                    ->whereRaw('cars.id = car_bookings.carId')
                    ->where(function ($query) use ($formArray) {
                        $query->where('car_bookings.start_date', '<=', $formArray['start_date'])
                            ->where('car_bookings.end_date', '>=', $formArray['start_date']);
                    });
            })
            ->whereIn('cars.car_type', $formArray['car_type'])
            ->whereIn('cars.transmission', $formArray['transmission']);
            }

            // Combination of car_type and partner_id
            elseif (!empty($formArray['car_type']) && !empty($formArray['partner_id'])
            && empty($formArray['car_name']) && empty($formArray['start_date']) && empty($formArray['end_date'])) {

            $query->whereIn('cars.car_type', $formArray['car_type'])
                ->whereIn('cars.user_id', $formArray['partner_id']);

            if (!empty($formArray['transmission'])) {
                $query->whereIn('cars.transmission', $formArray['transmission']);
            }

            if (!empty($formArray['car_name'])) {
                $query->whereIn('cars.name', $formArray['car_name']);
            }
            }

            // Combination of start_date, car_name, and transmission
            elseif (!empty($formArray['start_date']) && !empty($formArray['car_name']) && !empty($formArray['transmission'])
            && empty($formArray['car_type']) && empty($formArray['partner_id']) && empty($formArray['end_date'])) {

            $query->whereNotExists(function ($subQuery) use ($formArray) {
                $subQuery->select(DB::raw(1))
                    ->from('car_bookings')
                    ->whereRaw('cars.id = car_bookings.carId')
                    ->where(function ($query) use ($formArray) {
                        $query->where('car_bookings.start_date', '<=', $formArray['start_date'])
                            ->where('car_bookings.end_date', '>=', $formArray['start_date']);
                    });
            })
            ->whereIn('cars.name', $formArray['car_name'])
            ->whereIn('cars.transmission', $formArray['transmission']);
            }

            // Combination of start_date, partner_id, and transmission
            elseif (!empty($formArray['start_date']) && !empty($formArray['partner_id']) && !empty($formArray['transmission'])
            && empty($formArray['car_name']) && empty($formArray['car_type']) && empty($formArray['end_date'])) {

            $query->whereNotExists(function ($subQuery) use ($formArray) {
                $subQuery->select(DB::raw(1))
                    ->from('car_bookings')
                    ->whereRaw('cars.id = car_bookings.carId')
                    ->where(function ($query) use ($formArray) {
                        $query->where('car_bookings.start_date', '<=', $formArray['start_date'])
                            ->where('car_bookings.end_date', '>=', $formArray['start_date']);
                    });
            })
            ->whereIn('cars.user_id', $formArray['partner_id'])
            ->whereIn('cars.transmission', $formArray['transmission']);
            }

            // Combination of car_type and transmission without start_date
            elseif (!empty($formArray['car_type']) && !empty($formArray['transmission'])
            && empty($formArray['car_name']) && empty($formArray['partner_id']) && empty($formArray['start_date']) && empty($formArray['end_date'])) {

            $query->whereIn('cars.car_type', $formArray['car_type'])
                ->whereIn('cars.transmission', $formArray['transmission']);
            }

            // Combination of car_name and transmission without start_date
            elseif (!empty($formArray['car_name']) && !empty($formArray['transmission'])
            && empty($formArray['car_type']) && empty($formArray['partner_id']) && empty($formArray['start_date']) && empty($formArray['end_date'])) {

            $query->whereIn('cars.name', $formArray['car_name'])
                ->whereIn('cars.transmission', $formArray['transmission']);
            }

            // Combination of partner_id and transmission without start_date
            elseif (!empty($formArray['partner_id']) && !empty($formArray['transmission'])
            && empty($formArray['car_name']) && empty($formArray['car_type']) && empty($formArray['start_date']) && empty($formArray['end_date'])) {

            $query->whereIn('cars.user_id', $formArray['partner_id'])
                ->whereIn('cars.transmission', $formArray['transmission']);
            }


        })->orderBy('cars.created_at', 'Desc')->take(10)->get();

        }
        else{
            // dd('have not any filter');
            $cars = cars::whereIn('id',$sharedCarId)->where('status', 'NOT LIKE', 'deleted')->orderByDesc('created_at')->take(10)->get();
        }

        $startDate='';
        $endDate='';

        $allCarsFilter = cars::whereIn('id', $sharedCarId)->where('status', 'NOT LIKE', 'deleted')
        ->distinct()
        ->get();



        $allPartnersIds = cars::whereIn('id',$sharedCarId)->where('status', 'LIKE', 'available')->orderBy('created_at', 'desc')->pluck('user_id');

        $allPartners=User::whereIn('id',$allPartnersIds)->get();


        $get_partner_ids = [];

        // dd('cars:',$cars->toArray());

        foreach ($cars as $car) {
            $get_partner_ids[] = $car['user_id'];
        }

        $get_partner_ids = array_unique($get_partner_ids);
        $carsByPartner = [];

        foreach ($get_partner_ids as $partnerId) {
                $carsByPartner[$partnerId] = [];
                foreach ($cars as $car) {
                    if ($car['user_id'] == $partnerId) {
                        $carsByPartner[$partnerId][] = $car;
                    }
                }
        }

        if((isset($formArray['start_date'])&&
        !empty($formArray['start_date']))&&
        (isset($formArray['end_date'])&&
        !empty($formArray['end_date']))){

        $dates = [];
        $startDate = new Carbon($formArray['start_date']);
        $currentDate = $startDate->copy();
        $endDate=  new Carbon($startDate->copy()->addDays(29));
        while ($currentDate->lte($endDate)) {

            $dates[] = [
            'day' => $currentDate->day,
            'month' => $currentDate->format('M'),
            'full_date' => $currentDate->toDateString(),
            'day_name' => $currentDate->format('D'),
            ];

             $currentDate = $currentDate->addDay();
            }

        }

        elseif((isset($formArray['start_date']) && !empty($formArray['start_date'])) ){
            $dates = [];
            $startDate = new Carbon(($formArray['start_date']));
            $currentDate = $startDate->copy();
            $endDate=  new Carbon($startDate->copy()->addDays(29));

              while ($currentDate->lte($endDate)) {
              $dates[] = [
                'day' => $currentDate->day,
                'month' => $currentDate->format('M'),
                'full_date' => $currentDate->toDateString(),
                'day_name' => $currentDate->format('D'),
            ];

                $currentDate->addDay();
            }

        }

        elseif( (isset($formArray['end_date']) && !empty($formArray['end_date'])) && !empty($formArray['start_date'] )){

            $startDate = now();
            $dates = [];
            $currentDate = $startDate->copy();
            $endDate = new Carbon($formArray['end_date']);
            while ($currentDate <= $endDate) {
                $dates[] = [
                    'day' => $currentDate->day,
                    'month' => $currentDate->format('M'),
                    'full_date' => $currentDate->toDateString(),
                    'day_name' => $currentDate->format('D'),
                ];
                $currentDate->addDay();
            }
        }

        elseif(empty($formArray['end_date']) && empty($formArray['start_date'])){
            $currentDate = new Carbon(now());
            $startDate = $currentDate->copy()->subDays(4);
            $endDate = new Carbon(now()->addDays(29));
             $currentDate = $startDate->copy();
            $dates = [];
                while ($currentDate <= $endDate) {
                    $dates[] = [
                        'day' => $currentDate->day,
                        'month' => $currentDate->format('M'),
                        'full_date' => $currentDate->toDateString(),
                        'day_name' => $currentDate->format('D'),
                    ];
                 $currentDate->addDay();
            }
        }

        $filtersCount= $this->checkFiltersCount($start_date,$end_date,$car_type,$car_name,$partner_id,$transmission );

        return view('agent.bookings.calendar',compact('get_partner_ids','dates','cars','partners','allCarsFilter','allPartners','startDate','endDate','start_date','end_date','car_type','car_name','partner_id','transmission','filtersCount'));

    }

    ////// calendar post for get booking details  //////
    public function calendarPost(Request $request)
    {

        $carId=[];

        $carId = $request['carDetails']['carId'];

        $firstDate= $request['carDetails']['start_date'];

        $pickupTime = isset($request['start_time']) ? $request['start_time'] : '';

        $lastDate= $request['carDetails']['end_date'];

        $dropoffTime = isset($request['end_time']) ? $request['end_time'] : '';

        $actionType=$request->action_type;

        $carbonFirstDate = Carbon::parse($firstDate);

        $carbonLastDate = Carbon::parse($lastDate);

        $pickup_date = $carbonFirstDate->format('Y-m-d') . ' ' . $pickupTime;

        $dropoff_date = $carbonLastDate->format('Y-m-d') . ' ' . $dropoffTime;

        $pickupDate = date('Y-m-d H:i:s', strtotime($pickup_date));

        $dropoffDate = date('Y-m-d H:i:s', strtotime($dropoff_date));

        $convertedformattedStartDate = Carbon::parse($pickupDate);

        $convertedformattedEndDate = Carbon::parse($dropoffDate);

        $dateDifference = $convertedformattedEndDate->diff($convertedformattedStartDate);

        $roundedDays = $dateDifference->days;

        $convertedformattedStartTime = Carbon::parse($pickupTime);

        $convertedformattedEndTime = Carbon::parse($dropoffTime);

        $pickupMoment = Carbon::parse($convertedformattedStartTime->format('h:i A'));

        $dropoffMoment = Carbon::parse($convertedformattedEndTime->format('h:i A'));

        if ($pickupMoment->lt(Carbon::parse('9:00 AM'))) {
                $roundedDays++;
        }

        if ($dropoffMoment->gt(Carbon::parse('9:00 AM'))) {
                $roundedDays++;
        }
        //

        $flag = $this->checkBookingStatus($carId, $pickupDate, $dropoffDate,$actionType);

        if ($flag) {

        return response()->json(['success' => false, 'error' => true, 'msg' => 'Please select a valid date, Either dates are Locked or Booked !!' ]);

        }
        else{

        $car = cars::whereId($carId)->where('status', 'NOT LIKE', 'deleted')->first();

        $userId = $car->user_id;

        if($car){

        Session::put('bookingStarted','yes');

         CarsBookingDateStatus::create([
            'carId'=>$carId,
            'start_date'=>$firstDate,
            'end_date'=>$lastDate,
         ]);

         $per_day_rental_charges = $car->price;

         $total_booking_amount = $per_day_rental_charges * $roundedDays;

         $pickup_location = ($request->pickup_location == 'Other') ? $request->other_pickup_location : $request->pickup_location;

         $dropoff_location = ($request->dropoff_location == 'Other') ? $request->other_dropoff_location : $request->dropoff_location;

         $booking = CarBooking::create([
            'carId' => $carId,
            'user_id' => $userId,
            'booking_owner_id'=>auth()->user()->id,
            'car_name' => $car->name,
            'registration_number' => $car->registration_number,
            'start_date' => date('Y-m-d H:i:s', strtotime($carbonFirstDate)),
            'pickup_date' => date('Y-m-d H:i:s', strtotime($pickupDate)),
            'end_date' => date('Y-m-d H:i:s', strtotime($carbonLastDate)),
            'dropoff_date' => date('Y-m-d H:i:s', strtotime($dropoffDate)),
            'pickup_time' => $pickupTime,
            'dropoff_time' => $dropoffTime,
            'pickup_location' => $pickup_location,
            'dropoff_location' => $dropoff_location,
            'bookingId' => 1001,
            'per_day_rental_charges'=>$per_day_rental_charges,
            'number_of_days'=>$roundedDays,
            'total_booking_amount'=>$total_booking_amount,
            'booking_type' => 'normal',
        ]);

        CarsBookingDateStatus::where('carId','=',$carId)->where('start_date','<=',$carbonFirstDate)->where('end_date','>=',$carbonLastDate)->delete();

        $maxBookingId = CarBooking::where('user_id',$userId)->max('bookingId');

        $newBookingId = ($maxBookingId !== null) ? ($maxBookingId + 1) : 1001;

        CarBooking::whereId($booking->id)->update([
           'bookingId' => $newBookingId,
        ]);

        return response()->json(['success' => true, 'error' => false, 'msg' => 'succeed','bookingId'=>$booking->id]);

        }
        else{
            return response()->json(['success' => false, 'error' => true, 'msg' => 'car not available' ]);
         }
        }
    }

   ////// calendar load more list  //////
    public function loadMoreList(Request $request)
    {
        $dates = [];
        $startDate = new Carbon($request->start_date);
        $endDate = new Carbon($request->last_date);
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dates[] = [
                'day' => $currentDate->day,
                'month' => $currentDate->format('M'),
                'full_date' => $currentDate->toDateString(),
                'day_name' => $currentDate->format('D'),
            ];
            $currentDate->addDay();
        }

        $start = $request->input('start');
        $requestData = $request->all();
        $userIds = array_unique($requestData['userIds']);
        $carIds = array_unique($requestData['carIds']);

        // $partners = User::join('cars', 'users.id', '=', 'cars.user_id')
        // ->where('users.id', '!=', auth()->user()->id)
        // ->where('users.status', 'NOT LIKE', 'inactive')
        // ->where('cars.status', 'NOT LIKE', 'deleted')
        // ->whereHas('roles', function ($q) {
        //     $q->where('name', 'LIKE', 'partner');
        // })
        // ->with('roles')
        // ->orderBy('users.created_at', 'desc')
        // ->distinct()
        // ->paginate(3, ['users.*']);

        //  $userIds = [];
        //  foreach ($partners as $partner) {
        //     $userIds[] = $partner['id'];
        //  }

        //  dd($userIds);

        $agentId = auth()->user()->id;

        $sharedCarId = shareCars::where('agent_id', $agentId)->pluck('car_id');



        $fltrStartDate = isset($requestData['filter_start_date']) ? $requestData['filter_start_date'] : '';
        $fltrEndDate = isset($requestData['filter_end_date']) ? $requestData['filter_end_date'] : '';
        $carType = isset($requestData['car_type']) ? $requestData['car_type'] : '';
        $carName = isset($requestData['car_name']) ? $requestData['car_name'] : '';
        $partnerIds = isset($requestData['partner_id ']) ? $requestData['partner_id '] : '';



         if( !empty($fltrStartDate) ||  !empty($fltrEndDate) ||  !empty($carType) || !empty($carName) ){

           $cars = cars::where('status', 'NOT LIKE', 'deleted')
            ->whereIn('id', $sharedCarId)
            // ->where('user_id', '=', $userId)
            ->whereNotIn('id', $carIds)
            ->where(function ($query) use ($fltrStartDate, $fltrEndDate, $carName, $carType, $partnerIds) {

                if (!empty($carName)) {
                    $query->whereIn('name', $carName);
                } elseif (!empty($carType)) {

                    $query->whereIn('car_type', $carType);
                }elseif(!empty($partnerIds)){
                    $query->whereIn('user_id', $partnerIds);
                }

                if (!empty($carName) && !empty($carType)&& !empty($partnerIds)) {
                    $query->whereIn('name', $carName)->whereIn('car_type', $carType)->whereIn('user_id', $partnerIds);
                }elseif(!empty($carName) && !empty($carType)&& empty($partnerIds)){
                    $query->whereIn('name', $carName)->whereIn('car_type', $carType);

                }elseif(!empty($carName) && empty($carType)&& !empty($partnerIds)){
                    $query->whereIn('name', $carName)->whereIn('user_id', $partnerIds);

                }elseif(empty($carName) && !empty($carType)&& !empty($partnerIds)){
                    $query->whereIn('user_id', $partnerIds)->whereIn('car_type', $carType);

                }

                if (!empty($fltrStartDate) && !empty($fltrEndDate)) {
                    // dd('both dates  :',$fltrStartDate,$fltrEndDate);
                    $query->whereNotExists(function ($subQuery) use ($fltrStartDate, $fltrEndDate) {
                        $subQuery->from('car_bookings')
                            ->whereRaw('cars.id = car_bookings.carId')
                            ->where(function ($query) use ($fltrStartDate, $fltrEndDate) {
                                $query->whereBetween('car_bookings.start_date', [$fltrStartDate, $fltrEndDate])
                                      ->orWhereBetween('car_bookings.end_date', [$fltrStartDate, $fltrEndDate]);
                            });
                    });
                }

                if (!empty($fltrStartDate) && empty($fltrEndDate)) {

                    // dd('only fltrStartDate :',$fltrStartDate);

                    $query->whereNotExists(function ($subQuery) use ($fltrStartDate) {
                        $subQuery->from('car_bookings')
                            ->whereRaw('cars.id = car_bookings.carId')
                            ->where('car_bookings.start_date', '<=', $fltrStartDate)
                            ->where('car_bookings.end_date', '>=', $fltrStartDate);
                    });
                }
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(10, ['cars.*']);



        }
        else{
            // dd('under else');
             $cars = cars::where('status', 'NOT LIKE', 'deleted')
         ->whereIn('id', $sharedCarId)
         ->whereNotIn('id', $carIds)
         ->orderBy('created_at', 'DESC')->paginate(10, ['cars.*']);

        }




         $date_data = '';
         $main_loop = '';

         $carsCount= count($cars);

        $get_partner_ids = [];
        foreach ($cars as $car) {
           $get_partner_ids[] = $car['user_id'];
        }
        $get_partner_ids = array_unique($get_partner_ids);
        $carsByPartner = [];
        foreach ($get_partner_ids as $partnerId) {
           $carsByPartner[$partnerId] = [];
            foreach ($cars as $car) {
               if ($car['user_id'] == $partnerId) {
                   $carsByPartner[$partnerId][] = $car;
               }
           }
        }

        foreach ($carsByPartner as $partnerId => $partnerCars){
            foreach ($partnerCars as $car){
            $main_loop .= '<div class="main_loop">';
            $thumbnails = Helper::getFeaturedSetCarPhotoById($car->id);
            $featureImageUrl = asset('images/no_image.svg');
            if (count($thumbnails) > 0) {
                foreach ($thumbnails as $thumbnail) {
                    if (strcmp($thumbnail->featured, 'set') == 0) {
                        $featuredImage = Helper::getPhotoById($thumbnail->imageId);
                        $featureImageUrl = $featuredImage->url;
                    }
                }
            }
           $modifiedImgUrl = $featureImageUrl;
           $partnerName = Helper::getUserMeta($car->user_id, 'company_name') ? Helper::getUserMeta($car->user_id, 'company_name') : 'Not available';
           $defaultImageUrl = asset('images/no_image.svg');
           if (strcmp(Helper::getUserMeta($car->user_id, 'CompanyImageId'), '') !==0) {
               $profileImage = Helper::getPhotoById(Helper::getUserMeta($car->user_id, 'CompanyImageId'));
               if ($profileImage) {
               $defaultImageUrl = $profileImage->url;
               }
           }
          $modifiedUrl = $defaultImageUrl;

           $main_loop .='<div class="car_status_container h-[200px] md:h-[151px]">
           <div class="car_status_main_content">
            <div class="car_status_row relative">
                <div class="flex justify-start sticky w-max left-[28px] sm:left-[14px]">
                    <div class="flex justify-start md:mb-[10px]">
                        <div class="car_status_row_sec  md:w-[85px] md:p-[5px] md:bg-[#fff] md:rounded-[17px] left_section car_showcase_main_card md:bg-#fff">
                            <div class="h-[67px] md:h-[45px] py-[10px] overflow-hidden flex justify-center items-center md:py-[5px] sm:py-1">
                                <img src="'.$modifiedImgUrl.'" alt="car image" class="object-contain h-full">
                            </div>
                        </div>
                    </div>

                    <div class="car_status_row_sec pl-[15px] right_section md:flex md:flex-row  flex flex-col md:items-start">
                        <div class="car_details_content md:flex-col md:items-start flex justify-start items-center">

                            <div class="car_name_sec">
                                <a href="'.route('agent.car.view',$car->id).'" class="links_item_cta text-base md:text-center font-medium capitalize showcar_title_b hover:underline transition-all duration-300 ease-out   text-purple md:font-bold md:text-sm">
                                    '.$car->name.' ('.ucfirst($car->transmission).')
                                </a>
                            </div>
                            <div class="md:block hidden cursor-default ">
                                <p class="py-1 showcase_ct_b text-[14px] font-normal text-textGray500  md:text-sm uppercase sm:text-xs">
                                        '.$car->registration_number.'
                                </p>
                            </div>
                            <div class="car_ragisteration_sec md:hidden pl-2 cursor-default">
                                <p class="py-1 showcase_ct_b text-[14px] font-normal text-textGray500 md:text-sm uppercase">
                                    '.$car->registration_number.'
                                </p>
                            </div>

                        </div>

                        <div class="flex items-center mb-[25px] md:mb-[10px] lg:mb-[15px] md:ml-[8px] ">
                            <div class="mr-[8px] h-[36px] md:h-[25px] overflow-hidden">
                                <img class="object-contain h-full" src="'.$modifiedUrl.'">
                            </div>
                            <h2 class="md:hidden text-base font-medium leading-normal text-black">'.$partnerName.'</h2>
                        </div>
                    </div>
                </div>
             <ul class="list selectable" data-car-id="'.$car->id.'" data-user-id="'.$car->user_id.'">';

             foreach ($dates as $date) {



                $fullDate = $date['full_date'];

                if(Helper::isBooked($car->id,$date['full_date']))
                {

                    $additionalBookingData = Helper::getAdditionalBookingsByCarId($car->id, $fullDate);
                    $bookingData = Helper::getBookingDataByCarId($car->id, $fullDate);

                    $key = $fullDate;
                    // $key = $car->id . '_' . $fullDate;
                    $carId=$car->id;
                    $value = [
                        'additional' => [
                            'dropoff_date' => $additionalBookingData->dropoff_date,
                            'pickup_date' => $additionalBookingData->pickup_date,
                            'pickup_time' => $additionalBookingData->pickup_time,
                            'dropoff_time' => $additionalBookingData->dropoff_time,
                            'customer_name' => $additionalBookingData->customer_name,
                            'customer_mobile_country_code' => $additionalBookingData->customer_mobile_country_code,
                            'customer_mobile' => $additionalBookingData->customer_mobile,
                            'pickup_location' => $additionalBookingData->pickup_location,
                            'dropoff_location' => $additionalBookingData->dropoff_location,
                            'bookingId' => $additionalBookingData->bookingId,
                             'id'=>$additionalBookingData->id,
                             'booking_owner_id'=>$additionalBookingData->booking_owner_id
                        ],
                        'booking' => [
                            'bookingId' => $bookingData->bookingId,
                            'pickup_date' => $bookingData->pickup_date,
                            'dropoff_date' => $bookingData->dropoff_date,
                            'customer_name' => $bookingData->customer_name,
                            'customer_mobile_country_code' => $bookingData->customer_mobile_country_code,
                            'customer_mobile' => $bookingData->customer_mobile,
                            'pickup_location' => $bookingData->pickup_location,
                            'dropoff_location' => $bookingData->dropoff_location,
                            'pickup_time' => $bookingData->pickup_time,
                            'dropoff_time' => $bookingData->dropoff_time,
                            'id'=>$bookingData->id,
                            'booking_owner_id'=>$bookingData->booking_owner_id
                        ]
                    ];

                    $bookedArr[$key][$car->id] = $value;



                }

                $lockedData = Helper::getlockedDataByCarId($car->id, $fullDate);
                $hasAdditionalBookings = Helper::hasAdditionalBookingsByCarId($car->id,$fullDate);
                $overlapLockedOrNot = Helper::overlapLockedOrNot($car->id,$fullDate);

                $isBooked = Helper::isBooked($car->id, $fullDate);
                $isLocked = Helper::isLocked($car->id, $fullDate);
                  $titleAttribute = date("d M Y", strtotime($date['full_date']));
                  $main_loop .='<li class="inline-block adj_margin clickable relative"
                  data-date-full_date="'.$date['full_date'].'"
                  data-date-month="'.$date['month'].'" data-date-day="'.$date['day'].'"
                  data-car-id="'.$car->id.'">
                  <a href="javascript:void(0);" ' . ($isBooked ?
                  'data-fancybox data-src="#booking_Details_showcase_popup"'.
                  'data-overlap-bookingId="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['bookingId'] : ''  ). '"' .
                  'data-overlap-pickupdate="' .(array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['pickup_date'] : '' ) . '"' .
                  'data-overlap-dropoffdate="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['dropoff_date'] : '' ). '"' .
                  'data-overlap-pickuptime="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['pickup_time'] : ''  ). '"' .
                  'data-overlap-dropofftime="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['dropoff_time'] : '' ). '"' .
                  'data-overlap-customer-country-code="' .( array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['customer_mobile_country_code'] : ''  ). '"' .
                  'data-overlap-customer-name="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['customer_name'] : '' ). '"' .
                  'data-overlap-customer-number="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['customer_mobile'] : ''  ). '"' .
                  'data-overlap-dropoff-location="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['dropoff_location'] : '' ) . '"' .
                  'data-overlap-pickup-location="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['pickup_location'] : ''  ). '"' .
                  'data-overlap-booked-id="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['id'] : ''  ). '"' .
                  'data-overlap-booking-owner-id="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['booking_owner_id'] : ''  ). '"' .

                  'data-booked-id="' .(array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['bookingId'] : ''  ). '"' .
                  'data-booking-id="' .(array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['id'] : ''  ). '"' .
                  'data-booked-customer-country-code="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['customer_mobile_country_code'] : '' ). '"' .
                  'data-booked-startDate="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['pickup_date'] : '' ). '"' .
                  'data-booked-endDate="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['dropoff_date'] : '' ). '"' .
                  'data-booked-customer-name="' .(array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['customer_name'] : '' ). '"' .

                  'data-booked-customer-number="' .(array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['customer_mobile'] : '' ). '"' .

                  'data-booked-pickup-location="' .(array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['pickup_location'] : '' ). '"' .

                  'data-booked-dropoff-location="' .(array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['dropoff_location'] : '' ). '"' .

                  'data-booked-booking-owner-id="' .(array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['booking_owner_id'] : '' ). '"' .

                  'data-pickupTime="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['pickup_time'] : '' ). '"' .
                  'data-dropoffTime="' .(array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['dropoff_time'] : '' ). '"' : '') . '
                  ' . ($isLocked ?
                  'data-locked-startdate="' . $lockedData->start_date . '"' .
                  'data-locked-enddate="' . $lockedData->end_date . '"' : '')
                  . '
                  data-fancybox data-src="#open_popup" class="custom_pick_drop_popup inline-flex flex-col items-center justify-center links
                  '.($isBooked ? 'booked adj_status_width' :
                   ($isLocked ? 'locked adj_status_width' : 'activeDate')).'">

                   <div class="'.($hasAdditionalBookings ? 'overlap_booking' :''). ($overlapLockedOrNot ? 'overlap_locked' :'').'">
                   <div class="relative range_active spaces_around_status" title="' . $titleAttribute . '">
                   <div class="mx-auto leading-[0] car_status_container_inner flex items-center cell_dates_day justify-center w-[36px] h-[36px]  md:w-[30px] md:h-[30px] rounded-full
                   '.($isBooked ? '' :
                    ($isLocked ? '' : 'bg-[#E4FFDD]   border border-[#25BE00]')).'">' .($isBooked? '': ($isLocked?'' : '<span class="inline-block date_day ">'.$date['day'].'</span>' )).'
                   </div>
                   </div>
                   <div class="w-full car_status_title spaces_around_status">
                       <span class="text-xs font-normal capitalize title"></span>
                      </div>
                      </div>
                      </a>
                  </li>';
              }
             $main_loop .= '</ul>
              </div>
              </div>
            </div>';
            $main_loop .= '</div>';
            }
        }

        return response()->json([ 'data' => $main_loop,'next' => $start + 10]);

    }

    ///// booking list filter //////
    public function listAjaxFilter(Request $request)
    {

        $currentDate = Carbon::now();

        $booked_cars = $this->getBookedCarsWithFilter($request)
        ->take(5)
        ->get();

        // dd(count($booked_cars));

        if(count($booked_cars))
        {
            $desktopBookingHtml="";
            $mobileBookingHtml="";

            $result =  $this->collectionOfBookedCars($booked_cars);

            $desktopBookingHtml = $result['desktopBookingHtml'];
            $mobileBookingHtml = $result['mobileBookingHtml'];

            return response()->json(['success' => true, 'error' => false, 'booked_cars'=>$booked_cars, 'desktopBookingHtml'=>$desktopBookingHtml,'mobileBookingHtml'=>$mobileBookingHtml ]);
        }
        else
        {
            return response()->json(['success' => false, 'error' => true, 'booked_cars'=>$booked_cars, 'desktopBookingHtml'=>'','mobileBookingHtml'=>'' ]);
        }
    }

    ////// add customer booking details   //////
    public function add(Request $request)
    {
        $carId = $request->input('carId');
        $firstDate = $request->input('firstDate');
        $pickupTime = $request->input('pickupTime');
        $lastDate = $request->input('lastDate');
        $dropoffTime = $request->input('dropoffTime');
        $action_type=$request->input('actionType');
        if (session()->has('bookingStarted')) {
            session()->forget('bookingStarted');
            $car = cars::where('id', $carId)->where('status', 'NOT LIKE', 'deleted')->first();
            $userId = $car->user_id;
            $carbonFirstDate = Carbon::parse($firstDate);
            $carbonLastDate = Carbon::parse($lastDate);
            $diffInDays = $carbonLastDate->diffInDays($carbonFirstDate);
            $perDayRentalCharges = $car->price;
            $totalDayRentalCharges = $perDayRentalCharges * $diffInDays;
            $bookingStatus = CarsBookingDateStatus::where('carId', '=', $carId)
            ->where('start_date', '<=', $firstDate)
            ->where('end_date', '>=', $lastDate)
            ->first();

            session()->put('bookingData', [
            'carId' => $carId,
            'firstDate' => $firstDate,
            'lastDate' => $lastDate,
            ]);

        if ($car && $bookingStatus) {
            $bookingStatusId = $bookingStatus->id;
            return view('agent.bookings.add', compact('car', 'carId','userId','firstDate', 'lastDate', 'pickupTime', 'dropoffTime', 'bookingStatusId','totalDayRentalCharges','perDayRentalCharges','diffInDays','action_type'));
        } else {
             return redirect()->route('agent.booking.calendar')->with('error', 'Invalid car selection or booking status.');
        }
        }
        else {
            // dd('fff');
            $bookingStatus = CarsBookingDateStatus::where('carId', '=', $carId)
            ->where('start_date', '<=', $firstDate)
            ->where('end_date', '>=', $lastDate)
            ->delete();
            return redirect()->route('agent.booking.calendar')->with('error', 'Time has been expired.');
        }
    }


    ////// edit customer booking details   //////
    public function edit($id)
    {
        if (!$id) {
            return redirect()->route('agent.booking.list');
        } else {
            $userId = Auth::user()->id;
            $car_booking = CarBooking::where('id', $id)->first();

            if (!$car_booking || (!$car_booking->booking_owner_id && $car_booking->booking_owner_id !== null)) {
                return redirect()->route('agent.booking.list');
            }

            if(strcmp($userId,$car_booking->booking_owner_id) !==0){
                return redirect()->route('agent.booking.list');
            }

            $carId = $car_booking->carId;

            $car = cars::find($carId);

            $partner = User::find($car->user_id);

            $booking_payments = booking_payments::where('bookingId', $id)->get();

            return view('agent.bookings.edit', compact('car_booking', 'car', 'carId', 'booking_payments', 'partner'));
        }
    }


    public function editPost(Request $request ,$id)
    {

        $messages = [
            'customer_name.required' => 'Customer name is required',
            'customer_mobile.required' => 'Customer mobile is required',
            'pickup_location.required' => 'Pickup location is required',
            'dropoff_location.required' => 'Dropoff location is required',
            'per_day_rental_charges.required' => 'Per day rental charges is required',
            'number_of_days.required' => 'Number of days is required',
            'pickup_charges.required' => 'Pickup charges is required',
            'dropoff_charges.required' => 'Dropoff charges is required',
            'advance_booking_amount.required' => 'Advance booking amount is required',
            'refundable_security_deposit.required' => 'Refundable security deposit is required',
        ];


        $validator = Validator::make($request->all(), [
        'customer_name' => 'required',
        'customer_mobile' => 'required',
        'pickup_location' => 'required',
        'dropoff_location' => 'required',
        'per_day_rental_charges' => 'required',
        'number_of_days' => 'required',
        'pickup_charges' => 'required',
        'dropoff_charges' => 'required',
        'advance_booking_amount' => 'required',
        'refundable_security_deposit' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Validation error. Please correct the errors and try again.');
        }

        $start_date = Carbon::parse($request->start_date);
        $start_time = $request->start_time;
        $end_date = Carbon::parse($request->end_date);
        $end_time = $request->end_time;

        $pickup_date = $start_date->format('Y-m-d') . ' ' . $start_time;
        $dropoff_date = $end_date->format('Y-m-d') . ' ' . $end_time;

        $userId = $request->userId;
        $advance_booking_amount = $request->advance_booking_amount;
        $per_day_rental_charges = $request->per_day_rental_charges;
        $pickup_charges = $request->pickup_charges;
        $dropoff_charges = $request->dropoff_charges;
        $discount = $request->discount;
        $total_booking_amount = $request->total_booking_amount;
        $refundable_security_deposit = $request->refundable_security_deposit;
        $due_at_delivery = $request->due_at_delivery;
        $agent_commission = $request->agent_commission;
        $agent_commission_received = $request->agent_commission_received;
        $trimAdvanceAmount = 0;

        $registeration_number = ($request->registeration_number)?$request->registeration_number:'';
        $car_name = ($request->car_name)?$request->car_name:'';

        if ($advance_booking_amount) {
            $trimAdvanceAmount = preg_replace('/[₹,]+/', '', $advance_booking_amount);
            if (!is_numeric($trimAdvanceAmount)) {
                $trimAdvanceAmount = 0;
            }
        }
        $trim_per_day_rental_charges = 0;
        if ($per_day_rental_charges) {
            $trim_per_day_rental_charges = preg_replace('/[₹,]+/', '', $per_day_rental_charges);
            if (!is_numeric($trim_per_day_rental_charges)) {
                $trim_per_day_rental_charges = 0;
            }
        }


        $trim_pickup_charges = 0;
        if ($pickup_charges) {
            $trim_pickup_charges = preg_replace('/[₹,]+/', '', $pickup_charges);
            if (!is_numeric($trim_pickup_charges)) {
                $trim_pickup_charges = 0;
            }
        }


        $trim_dropoff_charges = 0;
        if ($dropoff_charges) {
            $trim_dropoff_charges = preg_replace('/[₹,]+/', '', $dropoff_charges);
            if (!is_numeric($trim_dropoff_charges)) {
                $trim_dropoff_charges = 0;
            }
        }


        $trim_discount = 0;
        if ($discount) {
            $trim_discount = preg_replace('/[₹,]+/', '', $discount);
            if (!is_numeric($trim_discount)) {
                $trim_discount = 0;
            }
        }


        $trim_total_booking_amount = 0;
        if ($total_booking_amount) {
            $trim_total_booking_amount = preg_replace('/[₹,]+/', '', $total_booking_amount);
            if (!is_numeric($trim_total_booking_amount)) {
                $trim_total_booking_amount = 0;
            }
        }

        $trim_refundable_security_deposit = 0;
        if ($refundable_security_deposit) {
            $trim_refundable_security_deposit = preg_replace('/[₹,]+/', '', $refundable_security_deposit);
            if (!is_numeric($trim_refundable_security_deposit)) {
                $trim_refundable_security_deposit = 0;
            }
        }

        $trim_due_at_delivery = 0;
        if ($due_at_delivery) {
            $trim_due_at_delivery = preg_replace('/[₹,]+/', '', $due_at_delivery);
            if (!is_numeric($trim_due_at_delivery)) {
                $trim_due_at_delivery = 0;
            }
        }

        $trim_agent_commission = 0;
        if ($agent_commission) {
            $trim_agent_commission = preg_replace('/[₹,]+/', '', $agent_commission);
            if (!is_numeric($trim_agent_commission)) {
                $trim_agent_commission = 0;
            }
        }

        $trim_agent_commission_received = 0;
        if ($agent_commission_received) {
            $trim_agent_commission_received = preg_replace('/[₹,]+/', '', $agent_commission_received);
            if (!is_numeric($trim_agent_commission_received)) {
                $trim_agent_commission_received = 0;
            }
        }


            $pickup_location = ($request->pickup_location == 'Other') ? $request->other_pickup_location : $request->pickup_location;
            $dropoff_location = ($request->dropoff_location == 'Other') ? $request->other_dropoff_location : $request->dropoff_location;
            $getUserId = CarBooking::whereId($id)->first();
            $userId = $getUserId->user_id;

            $CarBooking= CarBooking::whereId($id)->update([
            'carId' => $request->carId,
            'user_id' => $userId,
            'car_name' => $car_name,
            'registration_number' => $registeration_number,
            'customer_name' => $request->customer_name,
            'customer_mobile' => $request->customer_mobile,
            'alt_customer_mobile' => $request->alt_customer_mobile,
            'customer_email' => $request->customer_email,
            'customer_city' => $request->customer_city,
            'pickup_location' => $pickup_location,
            'dropoff_location' => $dropoff_location,
            'start_date' => date('Y-m-d H:i:s', strtotime($start_date)),
            'pickup_date' => date('Y-m-d H:i:s', strtotime($pickup_date)),
            'end_date' => date('Y-m-d H:i:s', strtotime($end_date)),
            'dropoff_date' => date('Y-m-d H:i:s', strtotime($dropoff_date)),
            'pickup_time' => $start_time,
            'dropoff_time' => $end_time,
            'advance_booking_amount' => $trimAdvanceAmount,
            'bookingId' => $request->bookingId,
            'per_day_rental_charges'=>$trim_per_day_rental_charges,
            'number_of_days'=>$request->number_of_days,
            'pickup_charges'=>$trim_pickup_charges,
            'dropoff_charges'=>$trim_dropoff_charges,
            'discount'=>$trim_discount,
            'total_booking_amount'=>$trim_total_booking_amount,
            'refundable_security_deposit'=>$trim_refundable_security_deposit,
            'due_at_delivery'=>$trim_due_at_delivery,
            'booking_remarks'=>$request->booking_remarks,
            'agent_commission'=>$trim_agent_commission,
            'agent_commission_received'=>$trim_agent_commission_received,
            'customer_mobile_country_code'=>$request->customer_mobile_country_code,
            'alt_customer_mobile_country_code'=>$request->alt_customer_mobile_country_code,
        ]);
         $bookingId = $id;
        //  $message= 'booking updated';
        //  $url= route('partner.booking.view',$id);
        //  $notification_type= 'booking';
        //  event(new BookingSender($id,$message,$url,$notification_type));

        // dd('check',$bookingId,'updated_booking:',$CarBooking);
        return redirect()->route('agent.booking.view',$bookingId)->with('success', 'You successfully updated your booking');
    }


    ////// add customer booking for customer   //////
    public function addPost(Request $request, $carId)
    {
        $actionType=$request->action_type;

        $messages = [
            'customer_name.required' => 'Customer name is required',
            'customer_mobile.required' => 'Customer mobile is required',
            'pickup_location.required' => 'Pickup location is required',
            'dropoff_location.required' => 'Dropoff location is required',
            'per_day_rental_charges.required' => 'Per day rental charges is required',
            'number_of_days.required' => 'Number of days is required',
            'pickup_charges.required' => 'Pickup charges is required',
            'dropoff_charges.required' => 'Dropoff charges is required',
            'advance_booking_amount.required' => 'Advance booking amount is required',
            'refundable_security_deposit.required' => 'Refundable security deposit is required',
        ];


        $validator = Validator::make($request->all(), [
        'customer_name' => 'required',
        'customer_mobile' => 'required',
        'pickup_location' => 'required',
        'dropoff_location' => 'required',
        'per_day_rental_charges' => 'required',
        'number_of_days' => 'required',
        'pickup_charges' => 'required',
        'dropoff_charges' => 'required',
        'advance_booking_amount' => 'required',
        'refundable_security_deposit' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors($validator)
                ->with('error', 'Validation error. Please correct the errors and try again.');
        }

        $start_date = Carbon::parse($request->start_date);
        $start_time = $request->start_time;
        $end_date = Carbon::parse($request->end_date);
        $end_time = $request->end_time;

        $pickup_date = $start_date->format('Y-m-d') . ' ' . $start_time;
        $dropoff_date = $end_date->format('Y-m-d') . ' ' . $end_time;

        $pickupDate = date('Y-m-d H:i:s', strtotime($pickup_date));
        $dropoffDate = date('Y-m-d H:i:s', strtotime($dropoff_date));

        $userId = $request->userId;
        $advance_booking_amount = $request->advance_booking_amount;
        $per_day_rental_charges = $request->per_day_rental_charges;
        $pickup_charges = $request->pickup_charges;
        $dropoff_charges = $request->dropoff_charges;
        $discount = $request->discount;
        $total_booking_amount = $request->total_booking_amount;
        $refundable_security_deposit = $request->refundable_security_deposit;
        $due_at_delivery = $request->due_at_delivery;
        $agent_commission = $request->agent_commission;
        $agent_commission_received = $request->agent_commission_received;
        $trimAdvanceAmount = 0;
        $actionType=$request->action_type;
        $registeration_number = ($request->registeration_number)?$request->registeration_number:'';
        $car_name = ($request->car_name)?$request->car_name:'';

        if ($advance_booking_amount) {
            $trimAdvanceAmount = preg_replace('/[₹,]+/', '', $advance_booking_amount);
            if (!is_numeric($trimAdvanceAmount)) {
                $trimAdvanceAmount = 0;
            }
        }
        $trim_per_day_rental_charges = 0;
        if ($per_day_rental_charges) {
            $trim_per_day_rental_charges = preg_replace('/[₹,]+/', '', $per_day_rental_charges);
            if (!is_numeric($trim_per_day_rental_charges)) {
                $trim_per_day_rental_charges = 0;
            }
        }


        $trim_pickup_charges = 0;
        if ($pickup_charges) {
            $trim_pickup_charges = preg_replace('/[₹,]+/', '', $pickup_charges);
            if (!is_numeric($trim_pickup_charges)) {
                $trim_pickup_charges = 0;
            }
        }


        $trim_dropoff_charges = 0;
        if ($dropoff_charges) {
            $trim_dropoff_charges = preg_replace('/[₹,]+/', '', $dropoff_charges);
            if (!is_numeric($trim_dropoff_charges)) {
                $trim_dropoff_charges = 0;
            }
        }


        $trim_discount = 0;
        if ($discount) {
            $trim_discount = preg_replace('/[₹,]+/', '', $discount);
            if (!is_numeric($trim_discount)) {
                $trim_discount = 0;
            }
        }


        $trim_total_booking_amount = 0;
        if ($total_booking_amount) {
            $trim_total_booking_amount = preg_replace('/[₹,]+/', '', $total_booking_amount);
            if (!is_numeric($trim_total_booking_amount)) {
                $trim_total_booking_amount = 0;
            }
        }

        $trim_refundable_security_deposit = 0;
        if ($refundable_security_deposit) {
            $trim_refundable_security_deposit = preg_replace('/[₹,]+/', '', $refundable_security_deposit);
            if (!is_numeric($trim_refundable_security_deposit)) {
                $trim_refundable_security_deposit = 0;
            }
        }

        $trim_due_at_delivery = 0;
        if ($due_at_delivery) {
            $trim_due_at_delivery = preg_replace('/[₹,]+/', '', $due_at_delivery);
            if (!is_numeric($trim_due_at_delivery)) {
                $trim_due_at_delivery = 0;
            }
        }

        $trim_agent_commission = 0;
        if ($agent_commission) {
            $trim_agent_commission = preg_replace('/[₹,]+/', '', $agent_commission);
            if (!is_numeric($trim_agent_commission)) {
                $trim_agent_commission = 0;
            }
        }

        $trim_agent_commission_received = 0;
        if ($agent_commission_received) {
            $trim_agent_commission_received = preg_replace('/[₹,]+/', '', $agent_commission_received);
            if (!is_numeric($trim_agent_commission_received)) {
                $trim_agent_commission_received = 0;
            }
        }

        $pickup_location = ($request->pickup_location == 'Other') ? $request->other_pickup_location : $request->pickup_location;
        $dropoff_location = ($request->dropoff_location == 'Other') ? $request->other_dropoff_location : $request->dropoff_location;

        $user = User::where('status','NOT LIKE','inactive')->find($userId);
        if (!$user) {
            abort(404);
        }


        $isBookingExist = $this->checkBooking($carId, $pickupDate, $dropoffDate,$actionType);

    if( $isBookingExist==false)
    {


        $booking = CarBooking::create([
            'booking_type'=>'normal',
            'booking_owner_id'=>auth()->user()->id,
            'carId' => $carId,
            'user_id' => $userId,
            'car_name'=> $car_name,
            'registration_number'=> $registeration_number,
            'customer_name' => $request->customer_name,
            'customer_mobile' => $request->customer_mobile,
            'alt_customer_mobile' => $request->alt_customer_mobile,
            'customer_email' => $request->customer_email,
            'customer_city' => $request->customer_city,
            'pickup_location' => $pickup_location,
            'dropoff_location' => $dropoff_location,
            'start_date' => date('Y-m-d H:i:s', strtotime($start_date)),
            'pickup_date' => date('Y-m-d H:i:s', strtotime($pickup_date)),
            'end_date' => date('Y-m-d H:i:s', strtotime($end_date)),
            'dropoff_date' => date('Y-m-d H:i:s', strtotime($dropoff_date)),
            'pickup_time' => $start_time,
            'dropoff_time' => $end_time,
            'advance_booking_amount' => $trimAdvanceAmount,
            'bookingId' => 1001,
            'per_day_rental_charges'=>$trim_per_day_rental_charges,
            'number_of_days'=>$request->number_of_days,
            'pickup_charges'=>$trim_pickup_charges,
            'dropoff_charges'=>$trim_dropoff_charges,
            'discount'=>$trim_discount,
            'total_booking_amount'=>$trim_total_booking_amount,
            'refundable_security_deposit'=>$trim_refundable_security_deposit,
            'due_at_delivery'=>$trim_due_at_delivery,
            'booking_remarks'=>$request->booking_remarks,
            'agent_commission'=>$trim_agent_commission,
            'agent_commission_received'=>$trim_agent_commission_received,
            'customer_mobile_country_code'=>$request->customer_mobile_country_code,
            'alt_customer_mobile_country_code' => $request->alt_customer_mobile_country_code,
        ]);



        $maxBookingId = CarBooking::where('user_id', $user->id)
         ->max('bookingId');

        $newBookingId = ($maxBookingId !== null) ? ($maxBookingId + 1) : 1001;


        CarBooking::whereId($booking->id)->update([
            'bookingId' => $newBookingId,
        ]);


        // dd('bookingId',$booking);
        //$message= 'booking created';
        // $url= route('partner.booking.view',$booking->id);
        // $notification_type= 'booking';

        // event(new BookingSender($booking->id,$message,$url,$notification_type));

            // for removing the locked dates from db after booking creation
        $deleted = CarsBookingDateStatus::where('carId','=',$carId)->where('start_date','<=',$start_date)->
        where('end_date','>=',$end_date)->delete();

        $bookingId = $booking->id;

        return redirect()->route('agent.booking.view', $bookingId)->with('success', 'You successfully created your booking');
    }
    else{

        return redirect()->route('agent.booking.calendar')->with('warning', 'Please select valid date, Either dates are Locked or Booked !!');

    }

    }

    ////// check car booking available or not  //////
    private function checkBookingStatus($carId, $firstDate, $lastDate,$actionType)
    {

        if(strcmp($actionType,'overlapped_date')!==0){

            $bookingCarStatus = CarBooking::where('carId', '=', $carId)
            ->where(function ($query) use ($firstDate, $lastDate) {
                $query->whereBetween('pickup_date', [$firstDate, $lastDate])
                    ->orWhereBetween('dropoff_date', [$firstDate, $lastDate]);
            })->where(function ($query) {
                $query->where('status', '=', 'delivered')
                ->orWhereNull('status');
                    })
         ->get();

        }
        else
        {
             // this block run whenever overlapping dates comes
             $bookingCarStatus = CarBooking::where('carId', '=', $carId)
             ->where(function ($query) use ($firstDate, $lastDate) {
                 $query->whereBetween('pickup_date', [$firstDate, $lastDate])
                       ->orWhereBetween('dropoff_date', [$firstDate, $lastDate])
                       ->orWhere(function ($query) use ($firstDate, $lastDate) {
                           $query->where('pickup_date', '<=', $firstDate)
                                 ->where('dropoff_date', '>=', $lastDate);
                       });
             })->where(function ($query) {
                 $query->whereNull('status')
                       ->orWhere('status', 'delivered');
             })
         ->get();

        }

        $carHoldStatus = CarsBookingDateStatus::where('carId', '=', $carId)
            ->where(function ($query) use ($firstDate, $lastDate) {
                $query->whereBetween('start_date', [$firstDate, $lastDate])
                    ->orWhereBetween('end_date', [$firstDate, $lastDate]);
            })
        ->get();
        if(count($bookingCarStatus) > 0 || count($carHoldStatus) > 0){
            return true;
        } else {
            return false;
        }
    }

    ///////// check car booking status or not ///////
    private function checkBooking($carId, $firstDate, $lastDate,$actionType){

        // $bookingCarStatus = CarBooking::where('carId', '=', $carId)
        //     ->where(function ($query) use ($firstDate, $lastDate) {
        //         $query->whereBetween('start_date', [$firstDate, $lastDate])
        //             ->orWhereBetween('end_date', [$firstDate, $lastDate]);
        //     })
        //     ->get();

        if(strcmp($actionType,'overlapped_date')!==0){

            $bookingCarStatus = CarBooking::where('carId', '=', $carId)
            ->where(function ($query) use ($firstDate, $lastDate) {
                $query->whereBetween('pickup_date', [$firstDate, $lastDate])
                    ->orWhereBetween('dropoff_date', [$firstDate, $lastDate]);
            })->where(function ($query) {
                $query->where('status', '=', 'delivered')
                ->orWhereNull('status');
                })
            ->get();

        }
        else
        {
            // this block run whenever overlapping dates comes

            // $bookingCarStatus = CarBooking::where('carId', '=', $carId)
            //     ->where(function ($query) use ($firstDate, $lastDate) {
            //     $query->where('end_date', '>', $firstDate)
            //     ->where('end_date', '<=', $lastDate);
            //     })->where(function ($query) {
            //     $query->where('status', '=', 'delivered')
            //     ->orWhereNull('status');
            //     })
            //     ->get();
                $bookingCarStatus = CarBooking::where('carId', '=', $carId)
                ->where(function ($query) use ($firstDate, $lastDate) {
                    $query->whereBetween('pickup_date', [$firstDate, $lastDate])
                            ->orWhereBetween('dropoff_date', [$firstDate, $lastDate])
                            ->orWhere(function ($query) use ($firstDate, $lastDate) {
                                $query->where('pickup_date', '<=', $firstDate)
                                    ->where('dropoff_date', '>=', $lastDate);
                            });
                })->where(function ($query) {
                    $query->whereNull('status')
                            ->orWhere('status', 'delivered');
                })
            ->get();

        }


        if(count($bookingCarStatus) > 0){
            return true;
        } else {
            return false;
        }
    }

    ////// next or previous month calendar  //////
    public function ajaxDatesFilter(Request $request)
    {
      if($request->ajax()){
        if(strcmp($request->type,'main_filter')==0){
            $requestData = $request->all();
            $formArray = [
                'start_date' => isset($requestData['start_date']) ? $requestData['start_date'] : null,
                'end_date' =>isset($requestData['end_date']) ? $requestData['end_date'] : null,
                'car_type' => $requestData['car_type'] ?? [],
                'car_name' => $requestData['car_name'] ?? [],
                'partner_id' => $requestData['partner_id'] ?? [],
            ];
            $startDate = isset($formArray['start_date'])?$formArray['start_date']:'';
            $endDate = isset($formArray['end_date'])?$formArray['end_date']:'';
            $car_type = isset($formArray['car_type'])?$formArray['car_type']:'';
            $car_name = isset($formArray['car_name'])?$formArray['car_name']:'';
            $partner = isset($formArray['partner_id'])?$formArray['partner_id']:'';
            $cars = cars::select('id','name','registration_number','transmission','fuel_type','manufacturing_year','car_type','roof_type','price',
            'seats','status','user_id','created_at','updated_at')->where('status', 'NOT LIKE', 'deleted')->
            where(function($query) use($formArray,$car_name,$car_type,$partner){
            if( (isset($formArray['partner_id'] )&& !empty($formArray['partner_id'])) &&
                (isset($formArray['car_name'])&& !empty($formArray['car_name'])) &&
                 (isset($formArray['car_type']) &&  !empty($formArray['car_type'])) &&
                (isset($formArray['start_date']) && !empty($formArray['start_date']) )&&
                 (isset($formArray['end_date'])&& !empty($formArray['end_date']))
            )
            {
                $query->whereIn('name',$formArray['car_name'])->whereIn('car_type',$formArray['car_type'])->whereIn('user_id',$formArray['partner_id']);

            }

            elseif((isset($formArray['car_type'])&& !empty($formArray['car_type'])) && empty($formArray['car_name']))
            {
              $query->whereIn('car_type',$formArray['car_type']);


            }

            elseif((isset($formArray['car_name'])&& !empty($formArray['car_name'])) && empty($formArray['car_type'])){
                 $query->whereIn('name',$formArray['car_name']);


            }
            elseif((isset($formArray['partner_id'])&&!empty($formArray['partner_id'])) ){
                $query->whereIn('user_id',$formArray['partner_id']);


            }

            elseif((isset($formArray['car_type'])&&!empty($formArray['car_type']))&&(isset($formArray['car_name'])&&!empty($formArray['car_name'])) ){
               $query->whereIn('name',$formArray['car_name'])->whereIn('car_type',$formArray['car_type']);


            }

            elseif( ((isset($formArray['start_date']) && !empty($formArray['start_date'])) && empty($formArray['end_date'])) ||
            ((isset($formArray['end_date']) && !empty($formArray['end_date'])) && empty($formArray['start_date']))  ){
            $query;
            }})->orderBy('created_at', 'Desc')->get();
           }
        }

        if($request->ajax()){
         if(strcmp($request->type,'increase')==0){
           $last_date = $request->full_date;
           $list_data = '';
           $date_data = '';
           $startDate = Carbon::parse($last_date);
           $endDate = $startDate->copy()->addDays(30);
           $dates = [];
           $currentDate = $startDate->copy()->addDay();
            while ($currentDate <= $endDate) {
            $dates[] = [
             'day' => $currentDate->day,
             'month' => $currentDate->format('M'),
             'full_date' => $currentDate->toDateString(),
            'day_name' => $currentDate->format('D'),
          ];
          $currentDate->addDay();
          }
        }

        if(strcmp($request->type,'decrease')==0){
             $last_date = $request->full_date;
              $list_data = '';
              $date_data = '';
              $startDate = Carbon::parse($last_date)->subDays(30);
              $endDate = Carbon::parse($last_date);
              $dates = [];
              $currentDate = $startDate->copy();
              while ($currentDate < $endDate) {
                $dates[] = [
                    'day' => $currentDate->day,
                    'month' => $currentDate->format('M'),
                    'full_date' => $currentDate->toDateString(),
                    'day_name' => $currentDate->format('D'),
                ];
                $currentDate->addDay();
            }
        }

        $cars_length=$request->cars_length;
        $requestData = $request->all();
        $userIds = $requestData['userIds'] ?? [];
        $carIds = $requestData['carIds'] ?? [];

        $cars = cars::where('status', 'NOT LIKE', 'deleted')
        ->whereIn('id',$carIds)
        ->orderByDesc('created_at')
        ->get();

        $date_data = '';
        $main_loop = '';
        $carsCount= count($cars);
        $get_partner_ids = [];
        $bookedArr=[];


         foreach ($cars as $car) {
            $get_partner_ids[] = $car['user_id'];
         }

         $get_partner_ids = array_unique($get_partner_ids);
         $carsByPartner = [];


         foreach ($get_partner_ids as $partnerId) {
            $carsByPartner[$partnerId] = [];
             foreach ($cars as $car) {
                if ($car['user_id'] == $partnerId) {
                    $carsByPartner[$partnerId][] = $car;
                }
            }
         }


        foreach ($carsByPartner as $partnerId => $partnerCars){
            foreach ($partnerCars as $car){
            $main_loop .= '<div class="main_loop">';
            $thumbnails = Helper::getFeaturedSetCarPhotoById($car->id);
            $featureImageUrl = asset('images/no_image.svg');
            if (count($thumbnails) > 0) {
                foreach ($thumbnails as $thumbnail) {
                    if (strcmp($thumbnail->featured, 'set') == 0) {
                        $featuredImage = Helper::getPhotoById($thumbnail->imageId);
                        $featureImageUrl = $featuredImage->url;
                    }
                }
            }
           $modifiedImgUrl = $featureImageUrl;
           $partnerName = Helper::getUserMeta($car->user_id, 'company_name') ? Helper::getUserMeta($car->user_id, 'company_name') : 'Not available';
           $defaultImageUrl = asset('images/no_image.svg');
           if (strcmp(Helper::getUserMeta($car->user_id, 'CompanyImageId'), '') !==0) {
               $profileImage = Helper::getPhotoById(Helper::getUserMeta($car->user_id, 'CompanyImageId'));
               if ($profileImage) {
               $defaultImageUrl = $profileImage->url;
               }
           }
          $modifiedUrl = $defaultImageUrl;
           $main_loop .='<div class="car_status_container h-[200px] md:h-[151px]">
           <div class="car_status_main_content">
            <div class="car_status_row relative">
            <div class="flex justify-start sticky w-max left-[28px] sm:left-[14px]">
                <div class="flex justify-start md:mb-[10px]">
                    <div class="car_status_row_sec  md:w-[85px] md:p-[5px] md:bg-[#fff] md:rounded-[17px] left_section car_showcase_main_card md:bg-#fff ">
                        <div class="h-[67px] md:h-[45px] py-[10px] overflow-hidden flex justify-center items-center md:py-[5px] sm:py-1">
                            <img src="'.$modifiedImgUrl.'" alt="car image" class="object-contain h-full">
                        </div>
                    </div>
                </div>
                <div class="car_status_row_sec pl-[15px] right_section md:flex md:flex-row  flex flex-col md:items-start">
                    <div class="car_details_content md:flex-col md:items-start flex justify-start items-center">

                        <div class="car_name_sec">
                            <a href="'.route('agent.car.view',$car->id).'" class="links_item_cta text-base md:text-center font-medium capitalize showcar_title_b hover:underline transition-all duration-300 ease-out   text-purple md:font-bold md:text-sm">
                                '.$car->name.' ('.ucfirst($car->transmission).')
                            </a>
                        </div>
                        <div class="md:block hidden cursor-default ">
                            <p class="py-1 showcase_ct_b text-[14px] font-normal text-textGray500  md:text-sm uppercase sm:text-xs">
                                    '.$car->registration_number.'
                            </p>
                        </div>
                        <div class="car_ragisteration_sec md:hidden pl-2 cursor-default">
                            <p class="py-1 showcase_ct_b text-[14px] font-normal text-textGray500 md:text-sm uppercase">
                                '.$car->registration_number.'
                            </p>
                        </div>

                    </div>

                    <div class="flex items-center mb-[25px] md:mb-[10px] lg:mb-[15px] md:ml-[8px] ">
                        <div class="mr-[8px] h-[36px] md:h-[25px] overflow-hidden">
                            <img class="object-contain  h-full" src="'.$modifiedUrl.'">
                        </div>
                        <h2 class="md:hidden text-base font-medium leading-normal text-black">'.$partnerName.'</h2>
                    </div>
                </div>
            </div>
             <ul class="list selectable" data-car-id="'.$car->id.'" data-user-id="'.$car->user_id.'">';

              foreach ($dates as $date) {

                $fullDate = $date['full_date'];

                if(Helper::isBooked($car->id,$date['full_date']))
                {
                    $additionalBookingData = Helper::getAdditionalBookingsByCarId($car->id, $fullDate);
                    $bookingData = Helper::getBookingDataByCarId($car->id, $fullDate);

                    $key = $fullDate;
                    // $key = $car->id . '_' . $fullDate;
                    $carId=$car->id;
                    $value = [
                        'additional' => [
                            'dropoff_date' => $additionalBookingData->dropoff_date,
                            'pickup_date' => $additionalBookingData->pickup_date,
                            'pickup_time' => $additionalBookingData->pickup_time,
                            'dropoff_time' => $additionalBookingData->dropoff_time,
                            'customer_name' => $additionalBookingData->customer_name,
                            'customer_mobile_country_code' => $additionalBookingData->customer_mobile_country_code,
                            'customer_mobile' => $additionalBookingData->customer_mobile,
                            'pickup_location' => $additionalBookingData->pickup_location,
                            'dropoff_location' => $additionalBookingData->dropoff_location,
                            'bookingId' => $additionalBookingData->bookingId,
                            'booking_owner_id'=>$additionalBookingData->booking_owner_id,
                              'id'=>$additionalBookingData->id

                        ],
                        'booking' => [
                            'bookingId' => $bookingData->bookingId,
                            'pickup_date' => $bookingData->pickup_date,
                            'dropoff_date' => $bookingData->dropoff_date,
                            'customer_name' => $bookingData->customer_name,
                            'customer_mobile_country_code' => $bookingData->customer_mobile_country_code,
                            'customer_mobile' => $bookingData->customer_mobile,
                            'pickup_location' => $bookingData->pickup_location,
                            'dropoff_location' => $bookingData->dropoff_location,
                            'pickup_time' => $bookingData->pickup_time,
                            'dropoff_time' => $bookingData->dropoff_time,
                            'booking_owner_id'=>$bookingData->booking_owner_id,
                              'id'=>$bookingData->id,

                        ]
                    ];

                    $bookedArr[$key][$car->id] = $value;
                }


                $additionalBooking = Helper::getAdditionalBookingsByCarId($car->id, $fullDate);
                $bookingData = Helper::getBookingDataByCarId($car->id, $fullDate);
                $lockedData = Helper::getlockedDataByCarId($car->id, $fullDate);
                $hasAdditionalBookings = Helper::hasAdditionalBookingsByCarId($car->id,$fullDate);
                $overlapLockedOrNot = Helper::overlapLockedOrNot($car->id,$fullDate);

                $isBooked = Helper::isBooked($car->id, $fullDate);
                $isLocked = Helper::isLocked($car->id, $fullDate);

                  $titleAttribute = date("d M Y", strtotime($date['full_date']));
                $main_loop .='<li class="inline-block adj_margin clickable relative"
                  data-date-full_date="'.$date['full_date'].'"
                  data-date-month="'.$date['month'].'" data-date-day="'.$date['day'].'"
                  data-car-id="'.$car->id.'">
                  <a href="javascript:void(0);" ' . ($isBooked ?
                  'data-fancybox data-src="#booking_Details_showcase_popup"'.
                  'data-overlap-bookingId="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['bookingId'] : ''  ). '"' .
                  'data-overlap-pickupdate="' .(array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['pickup_date'] : '' ) . '"' .
                  'data-overlap-dropoffdate="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['dropoff_date'] : '' ). '"' .
                  'data-overlap-pickuptime="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['pickup_time'] : ''  ). '"' .
                  'data-overlap-dropofftime="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['dropoff_time'] : '' ). '"' .
                  'data-overlap-customer-country-code="' .( array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['customer_mobile_country_code'] : ''  ). '"' .
                  'data-overlap-customer-name="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['customer_name'] : '' ). '"' .
                  'data-overlap-customer-number="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['customer_mobile'] : ''  ). '"' .
                  'data-overlap-dropoff-location="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['dropoff_location'] : '' ) . '"' .
                  'data-overlap-pickup-location="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['pickup_location'] : ''  ). '"' .
                  'data-overlap-booking-owner-id="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['booking_owner_id'] : ''  ). '"' .
                  'data-overlap-booked-id="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['id'] : ''  ). '"' .



                  'data-booked-id="' .(array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['bookingId'] : ''  ). '"' .
                  'data-booking-id="' .(array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['id'] : ''  ). '"' .

                  'data-booked-customer-country-code="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['customer_mobile_country_code'] : '' ). '"' .
                  'data-booked-startDate="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['pickup_date'] : '' ). '"' .
                  'data-booked-endDate="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['dropoff_date'] : '' ). '"' .
                  'data-booked-customer-name="' .(array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['customer_name'] : '' ). '"' .

                  'data-booked-customer-number="' .(array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['customer_mobile'] : '' ). '"' .

                  'data-booked-pickup-location="' .(array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['pickup_location'] : '' ). '"' .

                  'data-booked-dropoff-location="' .(array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['dropoff_location'] : '' ). '"' .

                  'data-booked-booking-owner-id="' .(array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['booking_owner_id'] : '' ). '"' .

                  'data-pickupTime="' . (array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['pickup_time'] : '' ). '"' .
                  'data-dropoffTime="' .(array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['dropoff_time'] : '' ). '"' : '') . '
                  ' . ($isLocked ?
                  'data-locked-startdate="' . $lockedData->start_date . '"' .
                  'data-locked-enddate="' . $lockedData->end_date . '"' : '')
                  . '
                  data-fancybox data-src="#open_popup" class="custom_pick_drop_popup inline-flex flex-col items-center justify-center links
                  '.($isBooked ? 'booked adj_status_width' :
                   ($isLocked ? 'locked adj_status_width' : 'activeDate')).'">

                   <div class="'.($hasAdditionalBookings ? 'overlap_booking' :''). ($overlapLockedOrNot ? 'overlap_locked' :'').'">
                   <div class="relative range_active spaces_around_status" title="' . $titleAttribute . '">
                   <div class="mx-auto leading-[0] car_status_container_inner flex items-center cell_dates_day justify-center w-[36px] h-[36px]  md:w-[30px] md:h-[30px] rounded-full
                   '.($isBooked ? '' :
                    ($isLocked ? '' : 'bg-[#E4FFDD]   border border-[#25BE00]')).'">' .($isBooked? '': ($isLocked?'' : '<span class="inline-block date_day ">'.$date['day'].'</span>' )).'
                   </div>
                   </div>
                   <div class="w-full car_status_title spaces_around_status">
                       <span class="text-xs font-normal capitalize title"></span>
                      </div>
                      </div>
                      </a>
                </li>';
              }
             $main_loop .= '</ul>
              </div>
              </div>
            </div>';
            $main_loop .= '</div>';
            }
        }

        foreach ($dates as $date) {
           $date_data.= '<li class="inline-block px-[13px] py-1 custom_calender_dates">
           <p class="text-xs text-black capitalize date_month_name"
            data-date-full_date="'.$date['full_date'].'">'.$date['month'].'</p>
           <p class="lg:text-sm font-normal lg:font-medium text-[17px] leading-4 lg:leading-[14px]">'.$date['day'].'</p>
           <p class="text-xs text-black capitalize">'.$date['day_name'].'</p>
       </li>';
        }
       return response()->json(['success' => true, 'error' => false, 'msg' => 'succeed','main_loop'=>$main_loop,'date_data'=>$date_data,'dates'=>$dates]);
      }
    }

    // public function carbookingtimeout(){
    //     $carHoldStatus = CarsBookingDateStatus::get();
    //     if($carHoldStatus){
    //         foreach ($carHoldStatus as $value) {
    //             $created_at = $value->created_at;
    //             $currentTime = now();
    //             $timeDifference = $currentTime->diffInMinutes($created_at);
    //             if ($timeDifference >= 1) {
    //                 CarsBookingDateStatus::where('carId','=',$value->carId)->delete();
    //             } else {
    //             }
    //         }
    //     }
    // }

    ////// set session for 10 minutes  //////
    public function setTimeCreate(Request $request)
    {

        if ($request->ajax()) {
            if (strcmp($request->action, 'proceed') == 0) {
                $carHoldAvail = CarsBookingDateStatus::where('carId', '=', $carId = $request->carId)
                ->where('start_date', '<=', $firstDate = $request->start_date)
                ->where('end_date', '>=',  $lastDate = $request->last_date)
                ->first();
                if (!$carHoldAvail) {
                    CarsBookingDateStatus::create([
                         'carId'=>$carId = $request->carId,
                         'start_date'=>$firstDate = $request->start_date,
                         'end_date'=>$lastDate = $request->last_date,
                    ]);
                    return response()->json(['success' => true, 'error' => false, 'action' => 'proceed', 'msg' => 'succeed']);
                }
                else{
                    return response()->json(['success' => true, 'error' => false, 'action' => 'notAvailable', 'msg' => 'Your time limit is exceeds, please try again for create booking']);
                }
            }
            if (strcmp($request->action, 'cancel') == 0) {
                $carHoldAvail = CarsBookingDateStatus::where('carId', '=', $carId = $request->carId)
                ->where('start_date', '<=', $firstDate = $request->start_date)
                ->where('end_date', '>=', $lastDate = $request->last_date)
                ->delete();
                return response()->json(['success' => true, 'error' => false, 'action' => 'cancel', 'msg' => 'Cancel booking']);
            }
        }
    }

    public function view($id)
    {
        if (!$id) {
            return redirect()->route('agent.booking.list');
        } else {
            $userId = Auth::user()->id;
            $car_booking = CarBooking::where('id', $id)->first();

            if (!$car_booking || (!$car_booking->booking_owner_id && $car_booking->booking_owner_id !== null)) {
                return redirect()->route('agent.booking.list');
            }

            if(strcmp($userId,$car_booking->booking_owner_id) !== 0){
                return redirect()->route('agent.booking.list');
            }

            $carId = $car_booking->carId;

            $car = cars::find($carId);

            $partner = User::find($car->user_id);

            $booking_payments = booking_payments::where('bookingId', $id)->get();

            return view('agent.bookings.view', compact('car_booking', 'car', 'carId', 'booking_payments', 'partner'));
        }
    }

    public function addPayment(Request $request)
    {
        $amount = $request->amount;
        if ($amount) {
            $numericPart = preg_replace('/[^0-9.]/', '', $amount);
            $trimPriceAmount = round($numericPart);
        }
        else{
            $trimPriceAmount = 0;
        }

        if($request){
            $booking_payment = booking_payments::create([
                'bookingId'=>$request->bookingId,
                'received_refund'=>$request->received_refund,
                'payment_name'=>$request->payment_name,
                'other_payment_name'=>$request->other_payment_name,
                'amount'=>$trimPriceAmount
            ]);

          $bookingPaymentsHtml = '';
           if (strcmp($booking_payment->received_refund, 'received') == 0) {
            $bookingPaymentsHtml .= '<li class="pb-2.5 last:pb-0">
            <div class="flex items-center -mx-3">
                <div class="px-3 w-1/2">
                <p class="sm:text-sm text-base font-normal text-[#666666] capitalize">';

                    if (strcmp($booking_payment->payment_name, 'advance_payment') == 0) {
                        $bookingPaymentsHtml .= 'Advance Payment (' . $booking_payment->created_at->format('d M Y') . ')';
                    } elseif (strcmp($booking_payment->payment_name, 'regular_payment') == 0) {
                        $bookingPaymentsHtml .= 'Regular Payment (' . $booking_payment->created_at->format('d M Y') . ')';
                    } elseif (strcmp($booking_payment->payment_name, 'payment_name') == 0) {
                        $bookingPaymentsHtml .= 'Payment Name (' . $booking_payment->created_at->format('d M Y') . ')';
                    } elseif (strcmp($booking_payment->payment_name, 'security_refund') == 0) {
                        $bookingPaymentsHtml .= 'Security Refund (' . $booking_payment->created_at->format('d M Y') . ')';
                    } elseif (strcmp($booking_payment->payment_name, 'other') == 0) {
                        $bookingPaymentsHtml .= $booking_payment->other_payment_name . ' (' . $booking_payment->created_at->format('d M Y') . ')';
                    }


                $bookingPaymentsHtml .= '</p>
                    </div>
                    <div class="px-3 w-1/2">
                        <p class="sm:text-sm text-right text-base font-normal text-black">+ ₹' . $booking_payment->amount . '</p>
                    </div>
                 </div>
                   </li>';
                }

                elseif (strcmp($booking_payment->received_refund, 'refund') == 0) {
                $bookingPaymentsHtml .= '<li class="pb-2.5 last:pb-0">
                                <div class="flex items-center -mx-3">
                                <div class="px-3 w-1/2">
                                <p class="sm:text-sm text-base font-normal text-[#666666] capitalize">';
                                if (strcmp($booking_payment->payment_name, 'advance_payment') == 0) {
                                    $bookingPaymentsHtml .= 'Advance Payment (' . $booking_payment->created_at->format('d M Y') . ')';
                                } elseif (strcmp($booking_payment->payment_name, 'regular_payment') == 0) {
                                    $bookingPaymentsHtml .= 'Regular Payment (' . $booking_payment->created_at->format('d M Y') . ')';
                                } elseif (strcmp($booking_payment->payment_name, 'payment_name') == 0) {
                                    $bookingPaymentsHtml .= 'Payment Name (' . $booking_payment->created_at->format('d M Y') . ')';
                                } elseif (strcmp($booking_payment->payment_name, 'security_refund') == 0) {
                                    $bookingPaymentsHtml .= 'Security Refund (' . $booking_payment->created_at->format('d M Y') . ')';
                                } elseif (strcmp($booking_payment->payment_name, 'other') == 0) {
                                    $bookingPaymentsHtml .= $booking_payment->other_payment_name. ' (' . $booking_payment->created_at->format('d M Y') . ')';
                                }
                                $bookingPaymentsHtml .= '</p>
                                    </div>
                                     <div class="px-3 w-1/2">
                                        <p class="sm:text-sm text-right text-base font-normal text-black">- ₹' . $booking_payment->amount . '</p>
                                     </div>
                                     </div>
                                 </li>';
                        }

                // Calculate total amount
                $bookingPayments = booking_payments::where('bookingId', '=', $request->bookingId)->get();
                $car_booking = CarBooking::whereId($request->bookingId)->first();
                $totalAmount = 0;
                $totalAmount = $car_booking->advance_booking_amount;
                foreach ($bookingPayments as $payment) {
                    if ($payment->received_refund === 'received') {
                        $totalAmount += $payment->amount;
                    } elseif ($payment->received_refund === 'refund') {
                        $totalAmount -= $payment->amount;
                    }
                }
                if ($booking_payment) {
                    return response()->json([
                        'success' => true,
                        'error' => false,
                        'bookingPaymentsHtml' => $bookingPaymentsHtml,
                        'totalAmount' => $totalAmount,
                    ]);
                }
                else {
                    return response()->json(['success' => false, 'error' => true, 'bookingPaymentsHtml' => '', 'totalAmount' => 0]);
                }
                }
                else{
                    return response()->json(['success' => false, 'error' => true]);
                }
    }

    public function clearAll()
    {

        // $agentId = auth()->user()->id;
        // $sharedCarId = shareCars::where('agent_id', $agentId)->pluck('car_id');
        // $cars = cars::whereIn('id', $sharedCarId)
        //     // ->where('status', '!=', 'deleted')
        //     ->orderBy('created_at', 'desc')
        //     ->get();

        // $partnerIds = $cars->pluck('user_id')->unique();
        // $cars = cars::whereIn('user_id', $partnerIds)->where('status', 'NOT LIKE', 'deleted')->orderBy('id', 'desc')->get();

        // $currentDate = Carbon::now();

        // $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        // $currentDateTime = Carbon::parse($oldcurrentDateTime);

        $agentId = auth()->user()->id;

        $sharedCarId = shareCars::where('agent_id', $agentId)->pluck('car_id');

        $cars = cars::whereIn('id', $sharedCarId)
            // ->where('status', '!=', 'deleted')
            ->orderBy('created_at', 'desc')
            ->get();

        $partnerIds = $cars->pluck('user_id')->unique();

        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);





        $booked_cars = CarBooking::where('booking_owner_id','=',$agentId)
        ->where(function ($query) {
                $query->whereNull('status')->orWhere('status','LIKE','delivered');
        })
        ->orderByRaw("CASE
        WHEN status = 'delivered' THEN dropoff_date
        WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
        ELSE pickup_date
        END ASC")
        ->take(5)
        ->get();


        if(count($booked_cars))
        {
            $result =  $this->collectionOfBookedCars($booked_cars);

            $desktopBookingHtml = $result['desktopBookingHtml'];
            $mobileBookingHtml = $result['mobileBookingHtml'];

            return response()->json(['success' => true, 'error' => false, 'booked_cars'=>$booked_cars, 'desktopBookingHtml'=>$desktopBookingHtml,'mobileBookingHtml'=>$mobileBookingHtml ]);
        }
        else{
            return response()->json(['success' => false, 'error' => true, 'booked_cars'=>$booked_cars,'desktopBookingHtml'=>'','mobileBookingHtml'=>'' ]);
        }
    }

// ////////////// check calander date ////////////////

    public function checkLockedAndBooked(Request $request)
    {

        $carId = $request->carId;
        $firstDate = $request->startDate;
        $lastDate = $request->endDate;
        $actionType=$request->actionType;


        $start_date = Carbon::parse($request->startDate);
        $start_time = $request->pickupTime;
        $end_date = Carbon::parse($request->endDate);
        $end_time = $request->dropoffTime;

        $pickup_date = $start_date->format('Y-m-d') . ' ' . $start_time;
        $dropoff_date = $end_date->format('Y-m-d') . ' ' . $end_time;

        $pickupDate = date('Y-m-d H:i:s', strtotime($pickup_date));
        $dropoffDate = date('Y-m-d H:i:s', strtotime($dropoff_date));

        // dd('all:',$request->all(),'pickupDate:',$pickupDate,'dropoffDate:',$dropoffDate);

        if(strcmp($actionType,'overlapped_date')!==0){

            $bookingCarStatus = CarBooking::where('carId', '=', $carId)
            ->where(function ($query) use ($pickupDate, $dropoffDate) {
                $query->whereBetween('pickup_date', [$pickupDate, $dropoffDate])
                    ->orWhereBetween('dropoff_date', [$pickupDate, $dropoffDate]);
            })->where(function ($query) {
                $query->where('status', '=', 'delivered')
                ->orWhereNull('status');
                    })
        ->get();




        }
        else
        {
            // $bookingCarStatus = CarBooking::where('carId', '=', $carId)->whereNull('status')->orWhere('status', 'delivered')
            //     ->where(function ($query) use ($firstDate, $lastDate) {
            //     $query->where('end_date', '>', $firstDate)
            //     ->where('end_date', '<=', $lastDate);
            //     })
            //     ->get();



        $bookingCarStatus = CarBooking::where('carId', '=', $carId)
            ->where(function ($query) use ($pickupDate, $dropoffDate) {
                $query->whereBetween('pickup_date', [$pickupDate, $dropoffDate])
                      ->orWhereBetween('dropoff_date', [$pickupDate, $dropoffDate])
                      ->orWhere(function ($query) use ($pickupDate, $dropoffDate) {
                          $query->where('pickup_date', '<=', $pickupDate)
                                ->where('dropoff_date', '>=', $dropoffDate);
                      });
            })->where(function ($query) {
                $query->whereNull('status')
                      ->orWhere('status', 'delivered');
            })
        ->get();

        }


            $carHoldStatus = CarsBookingDateStatus::where('carId', '=', $carId)
                ->where(function ($query) use ($firstDate, $lastDate) {
                    $query->whereBetween('start_date', [$firstDate, $lastDate])
                        ->orWhereBetween('end_date', [$firstDate, $lastDate]);
                })
            ->get();


            if(count($bookingCarStatus) > 0 || count($carHoldStatus) > 0){
                return response()->json(['success' => true, 'error' => false]);
            } else {
                return response()->json(['success' => false, 'error' => true]);
        }

    }

    public function getAllBookedAndLockedDates(Request $request){

        $carId = $request->carId;

        $bookingCarDates = CarBooking::select('start_date', 'end_date')->where(function ($query) {
            $query->where('status', '=', 'delivered')
            ->orWhereNull('status');
            })->where('carId', '=', $carId)->get();



        $carHoldStatus = CarsBookingDateStatus::select('start_date', 'end_date')->where('carId', '=', $carId)->get();

        $disableDates = [];

        foreach ($bookingCarDates as $booking) {
            $startDate = Carbon::parse($booking['start_date']);
            $endDate = Carbon::parse($booking['end_date']);
            $disableDates = array_merge($disableDates, $this->addDatesToRange($startDate, $endDate));
        }

        foreach ($carHoldStatus as $status) {
            $startDate = Carbon::parse($status['start_date']);
            $endDate = Carbon::parse($status['end_date']);
            $disableDates = array_merge($disableDates, $this->addDatesToRange($startDate, $endDate));
        }


        if($disableDates){
            return response()->json([ 'disableDates'=>$disableDates,'success' => true, 'error' => false]);
        }
        else{
            return response()->json(['disableDates'=>$disableDates,'success' => false, 'error' => true]);

        }
    }

    // for retriving the all dates with time
    public function getAllBookedAndLockedDatesWithTime(Request $request){

            $carId = $request->carId;
            $bookingId=$request->bookingId;
            $disabledDates=[];

            $bookingCarDates = CarBooking::select('pickup_date', 'dropoff_date')->where(function ($query) {
                $query->where('status', '=', 'delivered')
                ->orWhereNull('status');
                    })->where('carId', '=', $carId)->get();



            // dd('hello:',$bookingCarDates);
            $editbookingdates= CarBooking::select('pickup_date', 'dropoff_date')->where(function ($query) {
                $query->where('status', '=', 'delivered')
                ->orWhereNull('status');
                    })->where('carId', '=', $carId)->whereId($bookingId)->get();



            $carHoldStatus = CarsBookingDateStatus::select('start_date', 'end_date')->where('carId', '=', $carId)->get();

            // echo'<pre>';print_r($carHoldStatus);die;

        $endDates = CarBooking::select('dropoff_date')
                    ->where('carId', $carId)->where(function ($query) {
                        $query->where('status', '=', 'delivered')
                        ->orWhereNull('status');
                            })
                    ->whereRaw('start_date != end_date')
                    ->pluck('dropoff_date')
                    ->map(function ($endDate) {
                        return date('Y-m-d', strtotime($endDate));
                    });




        $overlapDates = CarBooking::select('dropoff_date')
                        ->where('carId', $carId)->where(function ($query) {
                            $query->where('status', '=', 'delivered')
                            ->orWhereNull('status');
                                })
                        ->whereRaw('start_date = end_date')
                        ->pluck('dropoff_date')->map(function ($endDate) {
                            return date('Y-m-d H:i:s', strtotime($endDate));
                        });

        $disableDates = [];
        $editableBookingDates=[];


        foreach ($bookingCarDates as $booking) {
            $startDate = Carbon::parse($booking['pickup_date']);
                $endDate = Carbon::parse($booking['dropoff_date']);
            $disableDates = array_merge($disableDates, $this->addDatesToRange($startDate, $endDate));
        }

        foreach ($editbookingdates as $item) {
            $startDate = Carbon::parse($item['pickup_date']);
            $endDate = Carbon::parse($item['dropoff_date']);
            $editableBookingDates = array_merge($editableBookingDates, $this->addDatesToRange($startDate, $endDate));
        }

        $disableDates= array_diff($disableDates, $editableBookingDates);

            //    dd('bookingCarDates:',$bookingCarDates,'editbookingdates:',$editbookingdates,'endDates:',$endDates,'disableDates',$disableDates);

        $endDates=$endDates->toArray();
        $overlapDates=$overlapDates->toArray();

        // dd('disableDates:',$disableDates,'editableBookingDates',$editableBookingDates,'endDates:',$endDates,'overlapDates:',$overlapDates);

        foreach ($disableDates as $date) {

            $datePart = date('Y-m-d', strtotime($date));
            // $timePart = date('H:i:s', strtotime($date));

            if (!in_array($datePart,$endDates) ) {

                $disabledDates[] = $date;
            }
        }


        // $carHoldStatus = CarsBookingDateStatus::select('start_date', 'end_date')->where('carId', '=', $carId)->get();

        //  echo'<pre>';print_r($disabledDates); print_r($carHoldStatus->toArray());die;


        foreach ($carHoldStatus as $status) {
            $startDate = Carbon::parse($status['start_date']);
            $endDate = Carbon::parse($status['end_date']);
            $disabledDates = array_merge($disabledDates, $this->addDatesToRange($startDate, $endDate));
        }
        // echo'<pre>';print_r($disabledDates);die;


        if($disabledDates){
        return response()->json([ 'disableDates'=>$disabledDates,'success' => true, 'error' => false ]);
        }
        else{
        return response()->json(['disableDates'=>$disabledDates,'success' => false, 'error' => true ]);

        }
    }


    public function statusChange(Request $request)
    {
        $agentId = auth()->user()->id;
        $sharedCarId = shareCars::where('agent_id', $agentId)->pluck('car_id');
        $cars = cars::whereIn('id', $sharedCarId)
            // ->where('status', '!=', 'deleted')
            ->orderBy('created_at', 'desc')
            ->get();
        $partnerIds = $cars->pluck('user_id')->unique();


        date_default_timezone_set('Asia/Kolkata');
        $currentTimeDate = now();

        $checkBookingStatus = CarBooking::whereId($request->bookingId)->first();

        $successMsg = false;
        $errorMsg = false;

        if ($checkBookingStatus)
        {
            if (strcmp($checkBookingStatus->status, 'collected') == 0) {
                $errorMsg = "Already collected, please refresh the page";
            } elseif (strcmp($checkBookingStatus->status, 'delivered') == 0) {
                if (strcmp($request->bookingStatus, 'delivered') == 0) {
                    $errorMsg = "Already delivered, please refresh the page";
                } else {

                    CarBooking::where('id', $request->bookingId)->update([
                        'collected_time' => $currentTimeDate,
                        'status' => $request->bookingStatus,
                    ]);
                    $successMsg = "Successfully confirmed collected";
                }
            } else {
                    CarBooking::where('id', $request->bookingId)->update([
                        'delivered_time' => $currentTimeDate,
                        'status' => $request->bookingStatus,
                    ]);
                    $successMsg = "Successfully confirmed delivered";
            }
        } else {
            $errorMsg = "Booking not found";
        }


        $bookingLength = $request->bookingLength;

        if(strcmp($request->checkStatus, 'list') == 0)
        {
            $currentDate = Carbon::now();

            $cars = cars::where('status', 'NOT LIKE', 'deleted')->orderBy('id', 'desc')->get();

            $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
            $currentDateTime = Carbon::parse($oldcurrentDateTime);

            $booked_cars = CarBooking::where('booking_owner_id','=',$agentId)
            ->where(function ($query) {
                $query->whereNull('status')->orWhere('status','LIKE','delivered');
            })
            ->orderByRaw("CASE
                WHEN status = 'delivered' THEN dropoff_date
                WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
                ELSE pickup_date
            END ASC")
            ->take($bookingLength)
            ->get();

        }
        elseif((strcmp($request->checkStatus, 'desktop_search') == 0) || (strcmp($request->checkStatus, 'mobile_search') == 0) )
        {

            $currentDate = Carbon::now();
            $requestData = $request->all();

            if (isset($requestData['form_data']))
            {
                parse_str($requestData['form_data'], $formArray);
                $start_date = $formArray['start_date'] ?? null;
                $end_date = $formArray['end_date'] ?? null;
                $booking_ids = $formArray['booking_id'] ?? [];
            }

            $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
            $currentDateTime = Carbon::parse($oldcurrentDateTime);

            $booked_cars = CarBooking::where('booking_owner_id','=',$agentId)
            ->where(function ($query) use ($formArray) {
                if (!empty($formArray['start_date']) && !empty($formArray['end_date'])) {
                    if (!empty($formArray['booking_id'])) {
                        $query->whereIn('id', $formArray['booking_id'])
                            ->whereDate('start_date', '>=', $formArray['start_date'])
                            ->whereDate('end_date', '<=', $formArray['end_date']);
                    }
                    else {
                        // Only start date and end date provided
                        $query->whereDate('start_date', '>=', $formArray['start_date'])->whereDate('end_date', '<=', $formArray['end_date']);
                    }
                } elseif (!empty($formArray['start_date']) && empty($formArray['end_date']) && !empty($formArray['booking_id'])) {
                    // Only start date provided with booking IDs
                    $query->whereIn('id', $formArray['booking_id'])->whereDate('start_date', '=', $formArray['start_date']);
                } elseif (!empty($formArray['start_date']) && empty($formArray['end_date']) && empty($formArray['booking_id'])) {
                    // Only start date provided
                    $query->where('start_date', '=', $formArray['start_date']);
                } elseif (empty($formArray['start_date']) && empty($formArray['end_date']) && !empty($formArray['booking_id'])) {
                    $query->whereIn('id', $formArray['booking_id']);
                }
            })
            ->where(function ($query) {
                $query->whereNull('status')->orWhere('status','LIKE','delivered');
            })
            ->orderByRaw("CASE
                WHEN status = 'delivered' THEN dropoff_date
                WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
                ELSE pickup_date
            END ASC")
            ->take($bookingLength)
            ->get();
        }

        if(count($booked_cars))
        {
            $result =  $this->collectionOfBookedCars($booked_cars);

            $desktopBookingHtml = $result['desktopBookingHtml'];
            $mobileBookingHtml = $result['mobileBookingHtml'];

            return response()->json(['success' => true, 'error' => false,'successMsg'=> $successMsg , 'errorMsg'=> $errorMsg,  'booked_cars'=>$booked_cars, 'desktopBookingHtml'=>$desktopBookingHtml,'mobileBookingHtml'=>$mobileBookingHtml ]);
        }
        else
        {
            return response()->json(['success' => false, 'error' => true, 'msg'=>false ,'booked_cars'=>$booked_cars,'desktopBookingHtml'=>'','mobileBookingHtml'=>'' ]);
        }
    }

    protected function addDatesToRange($startDate, $endDate)
    {

         if($startDate== $endDate ){

            $dateRange[] = $startDate->toDateTimeString();
        }
        else{

            $dateRange = [];
            for ($date = $startDate; $date->lt($endDate); $date->addDay()) {
                $dateRange[] = $date->toDateTimeString();
            }
        }
        return $dateRange;
    }

    private function checkFiltersCount($start_date,$end_date,$car_type,$car_name,$partner_id,$transmission)
    {
            $count=0;
            if ($start_date != '' || $end_date != '') $count++;
            if ($car_type != '') $count++;
            if ($car_name != '') $count++;
            if ($partner_id != '') $count++;
            if ($transmission != '') $count++;
            return $count;
    }

    protected function collectionOfBookedCars($booked_cars)
    {

        $desktopBookingHtml="";
        $mobileBookingHtml="";

        foreach($booked_cars as $booked_car)
        {

            date_default_timezone_set('Asia/Kolkata');

            $currentTimeDate = now();

            $combinedStartDateTime = $booked_car->pickup_date;
            // Set the default time zone to Asia/Kolkata

            $carbonStartDateTime = Carbon::parse($combinedStartDateTime);
            $pickupTimeBeforeThirty = $carbonStartDateTime->copy()->subMinutes(30);

            $combinedEndDateTime = $booked_car->dropoff_date;
            $carbonEndDateTime = Carbon::parse($combinedEndDateTime);
            $dropoffTimeBeforeThirty = $carbonEndDateTime->copy()->subMinutes(30);

            // $subtractedStartDateTime = $carbonStartDateTime->copy()->subMinutes(30);
            $formattedStartDateTime = $carbonStartDateTime->format('d M Y h:i A');
            $formattedEndDateTime = $carbonEndDateTime->format('d M Y h:i A');
            $formattedStartTime = $carbonStartDateTime->format('h:i A');
            $formattedEndTime = $carbonEndDateTime->format('h:i A');
            $formattedStartDate = $carbonStartDateTime->format('d M Y');
            $formattedEndDate = $carbonEndDateTime->format('d M Y');
            // $formattedSubtractedEndTime = $subtractedEndTime->format('h:i a');
            $convertedformattedStartDate = Carbon::parse($formattedStartDate);
            $convertedformattedEndDate = Carbon::parse($formattedEndDate);
            // Calculate the date difference
            $dateDifference = $convertedformattedEndDate->diff($convertedformattedStartDate);
            // echo "Date difference: " . $dateDifference->format('%a days');

            // FOR TODAY/TOMORROW
            $PickupDate = $carbonStartDateTime->format('Y-m-d');
            $DropoffDate = $carbonEndDateTime->format('Y-m-d');

            $currentDate =  Carbon::now('Asia/Kolkata')->format('Y-m-d');

            $diffPickAndCurrentDate = strtotime($PickupDate) - strtotime($currentDate);
            $pickupDaysGap = floor($diffPickAndCurrentDate / (60 * 60 * 24));

            // Convert seconds to days and round down
            $diffDropAndCurrentDate = strtotime($DropoffDate) - strtotime($currentDate);
            $dropDaysGap = floor($diffDropAndCurrentDate / (60 * 60 * 24));

            $roundedDays = $dateDifference->days;
            $pickupMoment = Carbon::parse($carbonStartDateTime->format('h:i A'));
            $dropoffMoment = Carbon::parse($carbonEndDateTime->format('h:i A'));

            if ($pickupMoment->lt(Carbon::parse('9:00 AM')) || $dropoffMoment->gt(Carbon::parse('9:00 AM'))) {
                $roundedDays++;
                // echo 'Pickup date is before 9:00 AM: ' . $pickupMoment;
                }
            else {
                $roundedDays = $dateDifference->days;
            }

            $nowDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
            // Parse $nowDateTime into a Carbon object
            $nowDateTimeObject = Carbon::parse($nowDateTime);
            // Parse $carbonStartDateTime into a Carbon object
            $nowCarbonStartDateTime = Carbon::parse($carbonStartDateTime);
            // Parse $carbonEndDateTime into a Carbon object
            $nowCarbonEndDateTime = Carbon::parse($carbonEndDateTime);

            $thumbnails = Helper::getFeaturedSetCarPhotoById($booked_car->carId);
            $carImageUrl = asset('images/no_image.svg');

            foreach($thumbnails as $thumbnail){
                $image = Helper::getPhotoById($thumbnail->imageId);
                $carImageUrl = $image->url;
            }

            $modifiedImgUrl = $carImageUrl;

            $desktopBookingHtml .= '
            <div class="mb-5 bookingLength">
                <div class="booking_car_list_box p-4 rounded-[4px] md:hidden block ';

                if (($currentTimeDate >= $pickupTimeBeforeThirty && strcmp($booked_car->status, 'delivered') !== 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ){
                    $desktopBookingHtml .= 'bg-[#fffbe5]';
                } elseif (($currentTimeDate >= $dropoffTimeBeforeThirty && strcmp($booked_car->status, 'delivered') == 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
                    $desktopBookingHtml .= 'bg-[#fffbe5]';
                } else {
                    $desktopBookingHtml .= 'bg-white';
                }
                $desktopBookingHtml .= '
                " data-id="' . $booked_car->id . '">';

                $customer_name = $booked_car->customer_name;
                $customer_mobile_country_code =  $booked_car->customer_mobile_country_code;
                $customer_mobile = $booked_car->customer_mobile;

                $desktopBookingHtml .='
                   <div class="list_cr_out flex items-center ' . (!empty($customer_name) || !empty($customer_mobile_country_code) || !empty($customer_mobile) ? 'justify-center' : 'justify-start') .'">
                   <div class="list_cr_item">
                        <div class="booking_list_card_top_section" data-id="' . $booked_car->id . '">
                        <div class="flex">
                            <div>
                        <div class="flex items-center flex-wrap gap-[3px]">';

                            if( ($currentTimeDate >= $pickupTimeBeforeThirty && strcmp($booked_car->status, 'delivered') !== 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
                            {
                            $desktopBookingHtml .= '
                                    <div class="flex items-center justify-center mr-[32px] 3xl:mr-[25px]">
                                        <div class="block">
                                            <img src="' . asset('images/time-clock-img.svg') . '" alt="clock" class="w-[19px]">
                                        </div>
                                        <div class="w-full ml-[6px]">
                                                <span class="block text-siteYellow800 text-xs font-normal leading-normal">
                                                    Pickup: '.$formattedStartDate.', '.$formattedStartTime.'
                                                </span>
                                        </div>
                                    </div>

                                    <div class="text-xs ">
                                        <span class="uppercase status_time border border-[#fca728] rounded-[12px]
                                            text-[#fca728] px-[10px] py-[2px] 3xl:py-[1px] bg-[#FFFFFF]">Delivery Awaited
                                        </span>
                                    </div>';
                            }
                            elseif( ($currentTimeDate >= $dropoffTimeBeforeThirty && strcmp($booked_car->status, 'delivered') == 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
                            {
                                $desktopBookingHtml .= '
                                    <div class="flex items-center justify-center mr-[32px] 3xl:mr-[25px]">
                                            <div class="block">
                                                <img src="' . asset('images/time-clock-img.svg') . '" alt="clock" class="w-[19px]">
                                            </div>
                                            <div class="w-full ml-[6px]">
                                                <span class="block text-siteYellow800 text-xs font-normal leading-normal">
                                                    Drop-off: '.$formattedEndDate.', '.$formattedEndTime.'
                                                </span>
                                            </div>
                                    </div>

                                    <div class="text-xs ">
                                        <span class="uppercase status_time border border-[#79CEE9] rounded-[12px]
                                        text-[#79CEE9] px-[10px] py-[2px] 3xl:py-[1px] bg-[#FFFFFF] ">Collection Awaited</span>
                                    </div>';
                            }
                            // Condition for Pickup today
                            elseif (($pickupDaysGap == 0) && ($nowDateTimeObject <= $nowCarbonStartDateTime) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
                                $desktopBookingHtml .= '
                                <div class="flex items-center justify-center mr-[32px] 3xl:mr-[25px]">
                                    <div class="block">
                                        <img src="' . asset('images/time-clock-img.svg') . '" alt="clock" class="w-[19px]">
                                    </div>
                                    <div class="w-full ml-[6px]">
                                        <span class="block text-siteYellow800 text-xs font-normal leading-normal">Pickup:' . $formattedStartTime . '</span>
                                    </div>
                                </div>
                                <div class="text-xs ">
                                    <span class="uppercase status_time border border-[#CE3A3A] rounded-[12px] text-[#CE3A3A] px-[10px] py-[2px] 3xl:py-[1px]">today</span>
                                </div>';
                            } elseif (($pickupDaysGap == 1) && ($nowDateTimeObject <= $nowCarbonStartDateTime) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
                                // Condition for Pickup tomorrow
                                $desktopBookingHtml .= '
                                <div class="flex items-center justify-center mr-[32px] 3xl:mr-[25px]">
                                    <div class="block">
                                        <img src="' . asset('images/time-clock-img.svg') . '" alt="clock" class="w-[19px]">
                                    </div>
                                    <div class="w-full ml-[6px]">
                                        <span class="block text-siteYellow800 text-xs font-normal leading-normal">Pickup: ' . $formattedStartTime . '</span>
                                    </div>
                                </div>
                                <div class="text-xs ">
                                    <span class="uppercase status_time border border-[#5DB47F] rounded-[12px] text-[#5DB47F] px-[10px] py-[2px] 3xl:py-[1px]">tomorrow</span>
                                </div>';
                            } elseif (($dropDaysGap == 0) && ($nowDateTimeObject <= $nowCarbonEndDateTime) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )  {
                                // Condition for Drop-off today
                                $desktopBookingHtml .= '
                                <div class="flex items-center justify-center mr-[32px] 3xl:mr-[25px]">
                                    <div class="block">
                                        <img src="' . asset('images/time-clock-img.svg') . '" alt="clock" class="w-[19px]">
                                    </div>
                                    <div class="w-full ml-[6px]">
                                        <span class="block text-siteYellow800 text-xs font-normal leading-normal">Drop-off: ' . $formattedEndTime . '</span>
                                    </div>
                                </div>
                                <div class="text-xs ">
                                    <span class="uppercase status_time border border-[#CE3A3A] rounded-[12px] text-[#CE3A3A] px-[10px] py-[2px] 3xl:py-[1px]">today</span>
                                </div>';
                            } elseif (($dropDaysGap == 1) && ($nowDateTimeObject <= $nowCarbonEndDateTime) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
                                // Condition for Drop-off tomorrow
                                $desktopBookingHtml .= '
                                    <div class="flex items-center justify-center mr-[32px] 3xl:mr-[25px]">
                                        <div class="block">
                                            <img src="' . asset('images/time-clock-img.svg') . '" alt="clock" class="w-[19px]">
                                        </div>
                                        <div class="w-full ml-[6px]">
                                            <span class="block text-siteYellow800 text-xs font-normal leading-normal">Drop-off: ' . $formattedEndTime . '</span>
                                        </div>
                                    </div>
                                    <div class="text-xs ">
                                        <span class="uppercase status_time border border-[#5DB47F] rounded-[12px] text-[#5DB47F] px-[10px] py-[2px] 3xl:py-[1px]">tomorrow</span>
                                    </div>';
                            }
                            elseif(($pickupDaysGap>1) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
                                $desktopBookingHtml .= '
                                <div class="flex items-center justify-center">
                                        <div class="w-full ml-[6px]">
                                            <span class="block text-[#3D3D3D] text-xs font-normal leading-normal">Pickup: '.$formattedStartDate.', '.$formattedStartTime.'</span>
                                        </div>
                                    </div>';
                            }
                            elseif(($dropDaysGap>1) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
                                $desktopBookingHtml .= '
                                <div class="flex items-center justify-center">
                                        <div class="w-full ml-[6px]">
                                            <span class="block text-[#3D3D3D] text-xs font-normal leading-normal">Drop-off: '.$formattedEndDate.', '.$formattedEndTime.'</span>
                                        </div>
                                    </div>';
                            }
                            elseif((strcmp($booked_car->status, 'collected') == 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
                                $desktopBookingHtml .= '
                                <div class="text-xs ">
                                    <span class="uppercase status_time border border-[#25BE00] rounded-[12px] text-[#25BE00] px-[10px] py-[2px] 3xl:py-[1px]">Booking Completed</span>
                                </div>';
                            }
                            elseif((strcmp($booked_car->status, 'canceled') == 0) ) {
                                $desktopBookingHtml .= '
                                <div class="text-xs ">
                                    <span class="uppercase status_time border border-[#BD0000] rounded-[12px] text-[#BD0000] px-[10px] py-[2px] 3xl:py-[1px]">Booking Canceled</span>
                                </div>';
                            }

                            $desktopBookingHtml .= '
                                </div>
                                </div>
                                </div>
                                    </div>
                                        <div class="flex flex-wrap  booking_list_card_bottom_section mt-[9px]">
                                        <div class="min-w-[150px] max-w-[150px] w-[150px] 3xl:min-w-[135px] 3xl:max-w-[135px] 3xl:w-[135px]">
                                        <div class="image_l_wh">
                                                <img src="' . $modifiedImgUrl . '" alt="cars" class="object-contain max-h-full">
                                        </div>
                                            </div>
                                                <div class="ctc_info_w">
                                                    <div class="mb-[4px]">
                                                        <h4 class="capitalize text-[#2B2B2B] font-medium leading-4 text-[13px]">' . (Helper::getCarNameByCarId($booked_car->carId)) . '</h4>
                                                        <p class="uppercase text-[#666] text-[13px] font-normal">' . Helper::getCarRagisterationNoByCarId($booked_car->carId) . '</p>
                                                    </div>
                                                    <div class="block">
                                                        <a href="' . route('agent.booking.view', $booked_car->id) . '" class="links_item_cta inline-block text-[#2B2B2B] font-medium pb-[1px] leading-4 text-[13px] border-b-2 border-siteYellow">View Booking
                                                            </a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="list_cr_item">
                                            <div class="booking_list_card_top_section">
                                                <div class="block">
                                                    <h4 class="text-[#898376] font-medium leading-4 text-[13px]">Booking Details</h4>
                                                </div>
                                                <div class="flex flex-wrap  mt-[9px]">
                                                    <div class="4xl:min-w-full 4xl:pr-[0px] 3xl:w-[100%] min-w-[165px] max-w-[165px] pr-[20px] 2xl:pr-[0px] 3xl:max-w-none 3xl:pr-[15px]">
                                                        <p class="text-[13px] text-[#666666]">Start</p>
                                                        <h4 class="date_time text-[13px] font-medium text-[#000]">' . $formattedStartDate . '<span class="date">&nbsp;|&nbsp;</span>' . $formattedStartTime . '</h4>
                                                        <p class="text-[13px] font-normal text-[#666666]">' . $booked_car->pickup_location . '</p>
                                                    </div>

                                                    <div class="4xl:min-w-full 4xl:my-[10px] 3xl:w-[100%] my-auto max-w-[42px] min-w-[42px] 3xl:my-[10px] 3xl:max-w-none">
                                                        <div class="inline-block py-1 border-t border-b border-1 border-[#898376] text-[12px] flex justify-center text-center w-[42px]">
                                                            '.$booked_car->number_of_days." days". '
                                                        </div>
                                                    </div>

                                                    <div class="4xl:min-w-full 4xl:pl-[0px] 3xl:w-[100%] max-w-[165px] pl-[20px] 3xl:max-w-none 3xl:pl-[0px]">
                                                        <div>
                                                            <p class="text-[13px] text-[#666666]">End</p>
                                                            <h4 class="date_time text-[13px] font-medium text-[#000]"> ' . $formattedEndDate . '<span class="date">&nbsp;|&nbsp;</span>' . $formattedEndTime . '</h4>
                                                            <p class="text-[13px] font-normal text-[#666666]">' . $booked_car->dropoff_location . '</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';

                                     if(strcmp($booked_car->customer_name,'')!==0 || strcmp($booked_car->customer_mobile_country_code,'')!==0 || strcmp($booked_car->customer_mobile,'')!==0)
                                      {
                                         $desktopBookingHtml .= '
                                        <div class="list_cr_item">

                                            <div class="booking_list_card_top_section">

                                                <div class="block">

                                                    <h4 class="text-[#898376] font-normal leading-4 text-[13px] mb-[2px]">Customer Details</h4>
                                                    <p class="capitalize text-[13px] font-normal text-black">' . $booked_car->customer_name . '</p>
                                                    <p class="text-[13px] font-normal text-black">' .$booked_car->customer_mobile_country_code.'&nbsp;'.$booked_car->customer_mobile . '</p>

                                                    <div class="pt-[10px]">

                                                    <div class="flex">
                                                            <div class="pr-[30px] 2xl:pr-[13px]">
                                                                <a class="inline-block px-6 2xl:px-[18px] py-2.5 text-sm font-normal leading-4 text-black border rounded border-siteYellow bg-siteYellow transition-all duration-300 ease-in-out hover:bg-siteYellow400 leading-none" href="tel:'. $booked_car->customer_mobile . '">CALL</a>
                                                            </div>

                                                            <div class="block">
                                                                <a class="inline-block px-6 2xl:px-[18px] py-2.5 text-sm font-normal leading-4 text-black border rounded border-siteYellow transition-all duration-300 ease-in-out hover:bg-siteYellow400 leading-none" href="https://wa.me/'.ltrim($booked_car->customer_mobile_country_code, '+').$booked_car->customer_mobile.'" target="_blank" >WHATSAPP</a>
                                                            </div>

                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>';
                                }
                              $desktopBookingHtml .= '
                             </div>
                            </div>';

                        // action_section_container
                        if(
                            (($currentTimeDate >= $pickupTimeBeforeThirty && strcmp($booked_car->status, 'delivered') !== 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) ||
                            (($currentTimeDate >= $dropoffTimeBeforeThirty && strcmp($booked_car->status, 'delivered') == 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
                           )
                        {
                            $desktopBookingHtml .= '
                                <div class="action_section_container">
                                    <div class="action_section_inner flex justify-center">
                                        <div class="flex justify-center w-[34%] m-auto bg-[#fffbe5] py-2.5 2xl:px-6 px-10 rounded-md"
                                            style="border-bottom-left-radius: 35px; border-bottom-right-radius: 35px;
                                            clip-path: polygon(0 0, 100% 0, 96% 100%, 4% 100%);">
                                            <div class="w-full">
                                                <div class="flex justify-center -mx-1.5 flex-wrap">
                                                    <!-- 1st button -->
                                                    <div class="flex 2xl:w-full 2xl:mb-3 w-1/2 px-1.5 justify-center">
                                                        <a href="javascript:void(0);" class=" flex justify-center w-full items-center py-1 px-3
                                                            text-xs font-normal leading-4 border rounded ';

                                        if (strcmp($booked_car->status, 'delivered') == 0) {
                                            $desktopBookingHtml .= 'cursor-default bg-[#EBEBE4] border-[#EBEBE4] text-[#AEAEAE]';
                                        } else {
                                            $desktopBookingHtml .= 'booking_action_btn border-siteYellow transition-all duration-300 ease-in-out bg-siteYellow hover:bg-siteYellow400 ';
                                        }


                                        $desktopBookingHtml .= '" data-booking-id="' . $booked_car->id . '"';

                                        if (strcmp($booked_car->status, 'delivered') == 0) {
                                            $desktopBookingHtml .= ' disabled="disabled"';
                                        } else {
                                            $desktopBookingHtml .= ' data-booking-action="confirm_delivery"';
                                        }

                                        $desktopBookingHtml .= '>';

                                        if (strcmp($booked_car->status, 'delivered') == 0) {
                                            $desktopBookingHtml .= '<img src="' . asset('images/blur_confirm_delivery.svg') . '" alt="clock" class="w-[20px] mr-2">';
                                        } else {
                                            $desktopBookingHtml .= '<img src="' . asset('images/confirm_delivery.svg') . '" alt="clock" class="w-[20px] mr-2">';
                                        }

                                        $desktopBookingHtml .= 'CONFIRM DELIVERY
                                                                    </a>
                                                                </div>

                                        <!-- 2nd button -->
                                        <div class="flex 2xl:w-full w-1/2 px-1.5 justify-center">
                                            <a href="javascript:void(0);" class="flex justify-center w-full py-1 px-3
                                                items-center text-xs font-normal leading-4  border rounded ';

                                        if (strcmp($booked_car->status, 'delivered') !== 0) {
                                            $desktopBookingHtml .= 'cursor-default bg-[#EBEBE4] border-[#EBEBE4] text-[#AEAEAE]';
                                        } else {
                                            $desktopBookingHtml .= ' booking_action_btn border-siteYellow transition-all duration-300 ease-in-out bg-siteYellow hover:bg-siteYellow400';
                                        }

                                        $desktopBookingHtml .= '" data-booking-id="' . $booked_car->id . '" ';
                                        if (strcmp($booked_car->status, 'delivered') !== 0) {
                                            $desktopBookingHtml .= ' disabled="disabled"';
                                        } else {
                                            $desktopBookingHtml .= ' data-booking-action="confirm_collection"';
                                        }

                                        $desktopBookingHtml .= '>';

                                        if (strcmp($booked_car->status, 'delivered') !== 0) {
                                            $desktopBookingHtml .= '<img src="' . asset('images/blur_carswithkey3.svg') . '" alt="clock" class="w-[20px] mr-2">';
                                        } else {
                                            $desktopBookingHtml .= '<img src="' . asset('images/carswithkey.svg') . '" alt="clock" class="w-[20px] mr-2">';
                                        }

                                        $desktopBookingHtml .= 'CONFIRM COLLECTION
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
                        }

                    $desktopBookingHtml .= '
            </div>';

            $mobileBookingHtml .= '
            <div class="bookingLength rounded-[8px] p-[14px] md:block hidden mb-[20px]';
                    if(($currentTimeDate >= $pickupTimeBeforeThirty && strcmp($booked_car->status, 'delivered') !== 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
                    {
                        $mobileBookingHtml .= ' bg-[#fffbe5]';
                    }
                    elseif( ($currentTimeDate >= $dropoffTimeBeforeThirty && strcmp($booked_car->status, 'delivered') == 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ){
                        $mobileBookingHtml .= ' bg-[#fffbe5]';
                    }
                    else{
                        $mobileBookingHtml .= ' bg-white';
                    }
                    $mobileBookingHtml .= '   ">';

                        $mobileBookingHtml .= '<div class="flex justify-between items-center gap-[10px]">';

                            if ( ($currentTimeDate >= $pickupTimeBeforeThirty && strcmp($booked_car->status, 'delivered') !== 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
                                    $mobileBookingHtml .= '
                                    <div class="flex items-center flex-wrap gap-[5px]">
                                        <div class="flex items-center justify-center mr-[32px] 3xl:mr-[25px]">
                                            <div class="block">
                                                <img src="' . asset('images/time-clock-img.svg') . '" alt="clock" class="w-[19px]">
                                            </div>
                                            <div class="w-full ml-[6px]">
                                                <span class="block text-siteYellow800 text-[13px] font-normal leading-normal">Pickup: '.$formattedStartDate.', '.$formattedStartTime.'</span>
                                            </div>
                                        </div>
                                        <div class="text-xs">
                                            <span class="uppercase status_time border border-[#fca728] rounded-[12px]
                                            text-[#fca728] px-[10px] py-[2px] 3xl:py-[1px] bg-[#FFFFFF]">Delivery Awaited</span>
                                        </div>
                                    </div>';
                            } elseif ( ($currentTimeDate >= $dropoffTimeBeforeThirty && strcmp($booked_car->status, 'delivered') == 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
                                $mobileBookingHtml .= '
                                    <div class="flex items-center flex-wrap gap-[5px]">
                                        <div class="flex items-center justify-center mr-[32px] 3xl:mr-[25px]">
                                            <div class="block">
                                                <img src="' . asset('images/time-clock-img.svg') . '" alt="clock" class="w-[19px]">
                                            </div>
                                            <div class="w-full text-xs ml-[6px]">
                                                <span class="block text-siteYellow800 text-[13px] font-normal leading-normal">Drop-off: '.$formattedEndDate.', '.$formattedEndTime.'</span>
                                            </div>
                                        </div>
                                        <div class="text-xs">
                                            <span class="uppercase status_time border border-[#79CEE9] rounded-[12px]
                                            text-[#79CEE9] px-[10px] py-[2px] 3xl:py-[1px] bg-[#FFFFFF]">Collection Awaited</span>
                                        </div>
                                    </div>';
                            }

                        elseif (($pickupDaysGap == 0) && ($nowDateTimeObject <= $nowCarbonStartDateTime) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {

                        $mobileBookingHtml .= '
                        <div class="flex items-center flex-wrap gap-[5px]">
                            <div class="flex items-center justify-center mr-[32px] 3xl:mr-[25px]">
                                <div class="block">
                                    <img src="' . asset('images/time-clock-img.svg') . '" alt="clock" class="w-[19px]">
                                </div>
                                <div class="w-full ml-[6px]">
                                    <span class="block text-siteYellow800 text-[13px] font-normal leading-normal">Pickup: ' . $formattedStartTime . '</span>
                                </div>
                            </div>
                            <div class="text-xs">
                                <span class="uppercase status_time border border-[#CE3A3A] rounded-[12px] text-[#CE3A3A] px-[10px] py-[2px] 3xl:py-[1px]">today</span>
                            </div>
                        </div>';
                    } elseif ( ($pickupDaysGap == 1) && ($nowDateTimeObject <= $nowCarbonStartDateTime) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
                                // Condition for Pickup tomorrow
                                $mobileBookingHtml .= '
                            <div class="flex items-center flex-wrap gap-[5px]">
                            <div class="flex items-center justify-center mr-[32px] 3xl:mr-[25px]">
                                <div class="block">
                                    <img src="' . asset('images/time-clock-img.svg') . '" alt="clock" class="w-[19px]">
                                </div>
                                <div class="w-full ml-[6px]">
                                    <span class="block text-siteYellow800 text-[13px] font-normal leading-normal">Pickup: ' . $formattedStartTime . '</span>
                                </div>
                            </div>
                            <div class="text-xs">
                                <span class="uppercase status_time border border-[#5DB47F] rounded-[12px] text-[#5DB47F] px-[10px] py-[2px] 3xl:py-[1px]">tomorrow</span>
                            </div>
                        </div>';
                    } elseif ( ($dropDaysGap == 0) && ($nowDateTimeObject <= $nowCarbonEndDateTime) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
                        $mobileBookingHtml .= '
                        <div class="flex items-center flex-wrap gap-[5px]">
                            <div class="flex items-center justify-center mr-[32px] 3xl:mr-[25px]">
                                <div class="block">
                                    <img src="' . asset('images/time-clock-img.svg') . '" alt="clock" class="w-[19px]">
                                </div>
                                <div class="w-full ml-[6px]">
                                    <span class="block text-siteYellow800 text-[13px] font-normal leading-normal">Pickup: ' . $formattedEndTime . '</span>
                                </div>
                            </div>
                            <div class="text-xs ">
                                <span class="uppercase status_time border border-[#CE3A3A] rounded-[12px] text-[#CE3A3A] px-[10px] py-[2px] 3xl:py-[1px]">today</span>
                            </div>
                        </div>';

                    } elseif ( ($dropDaysGap == 1) && ($nowDateTimeObject <= $nowCarbonEndDateTime) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
                        $mobileBookingHtml .= '
                        <div class="flex items-center flex-wrap gap-[5px]">
                            <div class="flex items-center justify-center mr-[32px] 3xl:mr-[25px]">
                                <div class="block">
                                    <img src="' . asset('images/time-clock-img.svg') . '" alt="clock" class="w-[19px]">
                                </div>
                                <div class="w-full ml-[6px]">
                                    <span class="block text-siteYellow800 text-[13px] font-normal leading-normal">Pickup:  ' . $formattedEndTime . '</span>
                                </div>
                            </div>
                            <div class="text-xs ">
                                <span class="uppercase status_time border border-[#5DB47F] rounded-[12px] text-[#5DB47F] px-[10px] py-[2px] 3xl:py-[1px]">tomorrow</span>
                            </div>
                        </div>';

                    }
                    elseif(($pickupDaysGap>1)&& (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {

                            $mobileBookingHtml .= '
                            <div class="flex items-center justify-center">
                                    <div class="w-full ml-[6px]">
                                        <span class="block text-[#3D3D3D] text-xs font-normal leading-normal">Pickup: '.$formattedStartDate.', '.$formattedStartTime.'</span>
                                    </div>
                                </div>';
                    }
                    elseif(($dropDaysGap>1) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
                        $mobileBookingHtml .= '
                            <div class="flex items-center justify-center">
                                    <div class="w-full ml-[6px]">
                                        <span class="block text-[#3D3D3D] text-xs font-normal leading-normal">Drop-off: '.$formattedEndDate.', '.$formattedEndTime.'</span>
                                    </div>
                            </div>';
                    }
                    elseif((strcmp($booked_car->status, 'collected') == 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
                        $mobileBookingHtml .= '
                            <div class="text-xs ">
                                <span class="uppercase status_time border border-[#25BE00] rounded-[12px] text-[#25BE00] px-[10px] py-[2px] 3xl:py-[1px]">Booking Completed</span>
                            </div>';
                    }
                    elseif( (strcmp($booked_car->status, 'canceled') == 0) ) {
                        $mobileBookingHtml .= '
                                <div class="text-xs ">
                                    <span class="uppercase status_time border border-[#BD0000] rounded-[12px] text-[#BD0000] px-[10px] py-[2px] 3xl:py-[1px]">Booking Canceled</span>
                                </div>';
                    }
                    $mobileBookingHtml .= '
                        <div class="text-right">
                            <p class="text-[#898376] font-normal text-[14px] sm:w-max sm:leading-3">
                                '.$booked_car->number_of_days." days". '
                            </p>
                        </div>

                    </div>
                    <div class="flex items-center my-[10px] sm:my-[13px]">
                        <div class="block">
                            <div class="min-w-[150px] max-w-[150px] w-[150px] 3xl:min-w-[135px] 3xl:max-w-[135px] 3xl:w-[135px]  sm:min-w-[107px] sm:w-[107px]">
                                <div class="image_l_wh sm:w-[107px]">
                                    <img src="' . $modifiedImgUrl . '" alt="cars" class="object-contain max-h-full">
                                </div>
                            </div>
                        </div>
                        <div class="ctc_mid_r pl-[10px] sm:w-full">
                            <div class="mb-[6px]">
                                <h4 class="capitalize text-[#898376] font-normal leading-4 text-[15px]">' . Helper::getCarNameByCarId($booked_car->carId) . '</h4>
                            </div>
                            <div class="block">
                                <h4 class="capitalize text-[#898376] font-normal leading-4 text-[15px] mb-[3px]">Customer: ' . $booked_car->customer_name . '</h4>
                                <p class="text-[#898376] font-normal leading-4 text-[15px] mob_br_sm">Mobile:  ' .$booked_car->customer_mobile_country_code.'&nbsp;'.$booked_car->customer_mobile . '</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="w-1/2 pr-[10px] border-r border-1 border-[#C6C6C6]">
                            <div class="mb-[4px] sm:mb-[0px]">
                                <h5 class="text-[14px] text-black">Start:</h5>
                            </div>
                            <div class="block">
                                <h4 class="text-[#898376] font-normal leading-4 text-[14px] mb-[3px] mob_br_sm">' . $formattedStartDate . ', <br>' . $formattedStartTime . '</h4>
                                <p class="text-[#898376] font-normal leading-4 text-[14px]">' . $booked_car->pickup_location . '</p>
                            </div>
                        </div>
                        <div class="pl-[25px] w-1/2 sm:pl-[15px]">
                            <div class="mb-[4px] sm:mb-[0px]">
                                <h5 class="text-[14px] text-black">End:</h5>
                            </div>
                            <div class="">
                                <h4 class="text-[#898376] font-normal leading-4 text-[14px] mb-[3px] mob_br_sm">' . $formattedEndDate . ', <br>' . $formattedEndTime . '</h4>
                                <p class="text-[#898376] font-normal leading-4 text-[14px]">' . $booked_car->dropoff_location . '</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-[18px] sm:mt-[12px] afclr">
                        <div class="flex">
                            <div class="block">
                                <h4 class="text-[#898376] font-normal text-[15px]">Contact Customer</h4>
                            </div>
                            <div class="pl-[55px] pr-[25px] sm:pl-[15px] sm:pr-[15px]">
                                <a href="tel:'. $booked_car->customer_mobile . '" class="text-[#700D4A] font-bold text-[15px]">CALL</a>
                            </div>
                            <div class="text-left">
                                <a href="https://wa.me/' .ltrim($booked_car->customer_mobile_country_code, '+'). $booked_car->customer_mobile . '" class="text-[#700D4A] font-bold text-[15px]">WHATSAPP</a>
                            </div>
                        </div>
                        <div class="block mt-[2px]">
                            <a href="' . route('agent.booking.view', $booked_car->id) . '" class="links_item_cta inline-flex items-center text-[#2B2B2B] font-medium leading-4 text-[14px]">View Booking <img src="' . asset('images/arrow-booking.svg') . '" alt="arro" class="ml-[9px] w-[24px]"></a>
                        </div>
                    </div>';

                    if (
                        (($currentTimeDate >= $pickupTimeBeforeThirty && strcmp($booked_car->status, 'delivered') !== 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) ||
                        (($currentTimeDate >= $dropoffTimeBeforeThirty && strcmp($booked_car->status, 'delivered') == 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
                       )
                    {
                        $mobileBookingHtml .= '
                        <!-- action buttons -->
                        <div class="action_section_container my-5">
                            <div class="action_section_inner flex justify-center">

                                <div class="flex justify-center w-[100%]  m-auto  bg-[#FFF]  rounded-md" >
                                    <div class="w-full">
                                    <div class="flex justify-center -mx-1.5 flex-wrap bg-[#fffbe5]">

                                    <!-- 1st button -->
                                    <div class="flex 2xl:w-full 2xl:mb-3 w-1/2  px-1.5  justify-center">
                                        <a href="javascript:void(0);" class="flex justify-center py-1 px-3
                                        w-full items-center  text-xs font-normal leading-4  border rounded ';

                                    if (strcmp($booked_car->status, 'delivered') == 0) {
                                        $mobileBookingHtml .= 'cursor-default bg-[#EBEBE4] border-[#EBEBE4] text-[#AEAEAE]';
                                    } else {
                                        $mobileBookingHtml .= 'booking_action_btn border-siteYellow transition-all duration-300 ease-in-out bg-siteYellow hover:bg-siteYellow400';
                                    }

                                    $mobileBookingHtml .= '" data-booking-id="' . $booked_car->id . '"';

                                    if (strcmp($booked_car->status, 'delivered') == 0) {
                                        $mobileBookingHtml .= ' disabled="disabled"';
                                    } else {
                                        $mobileBookingHtml .= ' data-booking-action="confirm_delivery"';
                                    }

                                    $mobileBookingHtml .= '>';

                                    if (strcmp($booked_car->status, 'delivered') == 0) {
                                        $mobileBookingHtml .= '<img src="' . asset('images/blur_confirm_delivery.svg') . '" alt="clock" class="w-[20px] mr-2">';
                                    } else {
                                        $mobileBookingHtml .= '<img src="' . asset('images/confirm_delivery.svg') . '" alt="clock" class="w-[20px] mr-2">';
                                    }

                                    $mobileBookingHtml .= 'CONFIRM DELIVERY
                                        </a>
                                    </div>


                                    <div class="flex 2xl:w-full w-1/2  px-1.5 justify-center">
                                        <a href="javascript:void(0);" class="flex py-1 px-3
                                            justify-center w-full items-center text-xs font-normal leading-4 border rounded ';

                                    if (strcmp($booked_car->status, 'delivered') !== 0) {
                                        $mobileBookingHtml .= 'cursor-default bg-[#EBEBE4] border-[#EBEBE4] text-[#AEAEAE]';
                                    } else {
                                        $mobileBookingHtml .= ' booking_action_btn border-siteYellow transition-all duration-300 ease-in-out bg-siteYellow hover:bg-siteYellow400';
                                    }

                                    $mobileBookingHtml .= '" data-booking-id="' . $booked_car->id . '"';
                                    if (strcmp($booked_car->status, 'delivered') !== 0) {
                                        $mobileBookingHtml .= ' disabled="disabled"';
                                    } else {
                                        $mobileBookingHtml .= ' data-booking-action="confirm_collection"';
                                    }

                                    $mobileBookingHtml .= '>';

                                    if (strcmp($booked_car->status, 'delivered') !== 0) {
                                        $mobileBookingHtml .= '<img src="' . asset('images/blur_carswithkey3.svg') . '" alt="clock" class="w-[20px] mr-2">';
                                    } else {
                                        $mobileBookingHtml .= '<img src="' . asset('images/carswithkey.svg') . '" alt="clock" class="w-[20px] mr-2">';
                                    }

                                    $mobileBookingHtml .= 'CONFIRM COLLECTION
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        </div>';
                    }

                $mobileBookingHtml .=
                '
            </div>';

        }

        $result = array(
            'desktopBookingHtml' => $desktopBookingHtml,
            'mobileBookingHtml' => $mobileBookingHtml
        );

        // Return the array
        return $result;

    }

    public function viewStatusChange(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');

        $currentTimeDate = now();

        $checkBookingStatus = CarBooking::whereId($request->bookingId)->first();

        $successMsg = false;
        $errorMsg = false;
        $goback = false;

        if ($checkBookingStatus)
        {
            if (strcmp($checkBookingStatus->status, 'collected') == 0) {
                $errorMsg = "Already collected, please refresh the page";
            } elseif (strcmp($checkBookingStatus->status, 'delivered') == 0) {
                if (strcmp($request->bookingStatus, 'delivered') == 0) {
                    $errorMsg = "Already delivered, please refresh the page";
                } else {

                    CarBooking::where('id', $request->bookingId)->update([
                        'collected_time' => $currentTimeDate,
                        'status' => $request->bookingStatus,
                    ]);
                    $successMsg = "Successfully confirmed collected";
                    $goback = true;
                }
            } else {
                    CarBooking::where('id', $request->bookingId)->update([
                        'delivered_time' => $currentTimeDate,
                        'status' => $request->bookingStatus,
                    ]);
                    $successMsg = "Successfully confirmed delivered";
            }
        } else {
            $errorMsg = "Booking not found";
        }


            return response()->json(['success' => true, 'error' => false, 'msg'=>true,'successMsg'=>$successMsg , 'errorMsg'=>$errorMsg, 'goback' => $goback ]);
    }

////////////////////////////////////////////// ALL BOOKINGS ///////////////////////////////////////////////

    ////// allBookings list start here  //////
    public function allBookings()
    {
        $agentId = auth()->user()->id;

        $sharedCarId = shareCars::where('agent_id', $agentId)->pluck('car_id');

        $cars = cars::whereIn('id', $sharedCarId)
            // ->where('status', '!=', 'deleted')
            ->orderBy('created_at', 'desc')
            ->get();

        $partnerIds = $cars->pluck('user_id')->unique();

        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);

        $countBookedCars = CarBooking::where('booking_owner_id','=',$agentId)
            ->orderByRaw("CASE
                WHEN status = 'delivered' THEN dropoff_date
                WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
                ELSE pickup_date
            END ASC")
            ->count();


        $booked_cars = CarBooking::where('booking_owner_id','=',$agentId)
            ->orderByRaw("CASE
                WHEN status = 'delivered' THEN dropoff_date
                WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
                ELSE pickup_date
            END ASC")
        ->take(5)
        ->get();

         $allBookedCars = CarBooking::where('booking_owner_id','=',$agentId)
            ->orderByRaw("CASE
                WHEN status = 'delivered' THEN dropoff_date
                WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
                ELSE pickup_date
            END ASC")
        ->get();


        return view('agent.bookings.all-bookings', compact('booked_cars','allBookedCars','countBookedCars'));
    }

    ////// loadmore list start here  //////
    public function allLoadMoreBookings(Request $request)
    {
        $agentId = auth()->user()->id;

        $sharedCarId = shareCars::where('agent_id', $agentId)->pluck('car_id');

        $cars = cars::whereIn('id', $sharedCarId)
            // ->where('status', '!=', 'deleted')
            ->orderBy('created_at', 'desc')
            ->get();

        $partnerIds = $cars->pluck('user_id')->unique();

        $test = $request->checkLoadMore;
        date_default_timezone_set('Asia/Kolkata');

        if(strcmp($request->checkLoadMore, 'list') == 0)
        {
            $currentDate = Carbon::now();
            $offset = $request->input('offset', 0);

            $cars = cars::where('status', 'NOT LIKE', 'deleted')->orderBy('id', 'desc')->get();

            $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
            $currentDateTime = Carbon::parse($oldcurrentDateTime);

            $booked_cars = CarBooking::where('booking_owner_id','=',$agentId)
                ->orderByRaw("CASE
                    WHEN status = 'delivered' THEN dropoff_date
                    WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
                    ELSE pickup_date
                END ASC")
            ->skip($offset)
            ->take(5)
            ->get();
        }
        elseif((strcmp($request->checkLoadMore, 'desktop_search') == 0) || (strcmp($request->checkLoadMore, 'mobile_search') == 0) )
        {

            $customerName = isset($request->customerName) ? $request->customerName : null;
            // $countryCode = isset($request->countryCode) ? $request->countryCode : null;
            $customerMobile = isset($request->customerMobile) ? $request->customerMobile : null;

            $offset = $request->input('offset', 0);

            $currentDate = Carbon::now();
            $requestData = $request->all();

            if (isset($requestData['form_data']))
            {
                parse_str($requestData['form_data'], $formArray);
                $start_date = $formArray['start_date'] ?? null;
                $end_date = $formArray['end_date'] ?? null;
                // $booking_ids = $formArray['booking_id'] ?? [];

                // for booking id give empty
                $formArray['booking_id']=[];
            }

            $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
            $currentDateTime = Carbon::parse($oldcurrentDateTime);
            $booked_cars = CarBooking::where('booking_owner_id','=',$agentId)
            ->where(function ($query) use ($customerName, $customerMobile) {
                if ($customerName) {
                    $query->where('customer_name', '=', $customerName);
                }

                if ($customerMobile) {
                    $query->where('customer_mobile', '=', $customerMobile);
                }
            })
            ->where(function ($query) use ($formArray) {
                if (!empty($formArray['start_date']) && !empty($formArray['end_date'])) {
                    if (!empty($formArray['booking_id'])) {
                        $query->whereIn('id', $formArray['booking_id'])
                            ->whereDate('start_date', '>=', $formArray['start_date'])
                            ->whereDate('end_date', '<=', $formArray['end_date']);
                    }
                    else {
                        // Only start date and end date provided
                        $query->whereDate('start_date', '>=', $formArray['start_date'])->whereDate('end_date', '<=', $formArray['end_date']);
                    }
                } elseif (!empty($formArray['start_date']) && empty($formArray['end_date']) && !empty($formArray['booking_id'])) {
                    // Only start date provided with booking IDs
                    $query->whereIn('id', $formArray['booking_id'])->whereDate('start_date', '=', $formArray['start_date']);
                } elseif (!empty($formArray['start_date']) && empty($formArray['end_date']) && empty($formArray['booking_id'])) {
                    // Only start date provided
                    $query->where('start_date', '=', $formArray['start_date']);
                } elseif (empty($formArray['start_date']) && empty($formArray['end_date']) && !empty($formArray['booking_id'])) {
                    $query->whereIn('id', $formArray['booking_id']);
                }
            })
            ->orderByRaw("CASE
                WHEN status = 'delivered' THEN dropoff_date
                WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
                ELSE pickup_date
            END ASC")
            ->skip($offset)
            ->take(5)
            ->get();

        }
        elseif((strcmp($request->checkLoadMore, 'autocomplete_search') == 0) )
        {

            $customerName = isset($request->customerName) ? $request->customerName : null;
            // $countryCode = isset($request->countryCode) ? $request->countryCode : null;
            $customerMobile = isset($request->customerMobile) ? $request->customerMobile : null;
            // $countryCode = isset($request->countryCode) ? $request->countryCode : null;

            $offset = $request->input('offset', 0);

            $currentDate = Carbon::now();

            $requestData = $request->all();
            $formArray = [];

            $user_id = auth()->user()->id;

                if (isset($requestData['form_data']))
                {
                    parse_str($requestData['form_data'], $formArray);
                    $start_date = $formArray['start_date'] ?? null;
                    $end_date = $formArray['end_date'] ?? null;
                    $booking_ids = $formArray['booking_id'] ?? [];
                }

                $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
                $currentDateTime = Carbon::parse($oldcurrentDateTime);

                $booked_cars = CarBooking::where('booking_owner_id','=',$agentId)
                ->where(function ($query) use ($customerName, $customerMobile) {
                    if ($customerName) {
                        $query->where('customer_name', '=', $customerName);
                    }
                    if ($customerMobile) {
                        $query->where('customer_mobile', '=', $customerMobile);
                    }
                    // if ($countryCode) {
                    //     $query->where('customer_mobile_country_code', '=', $countryCode);
                    // }
                })
                ->where(function ($query) use ($formArray) {
                    if (!empty($formArray['start_date']) && !empty($formArray['end_date'])) {
                        if (!empty($formArray['booking_id'])) {
                            $query->whereIn('id', $formArray['booking_id'])
                                ->whereDate('start_date', '>=', $formArray['start_date'])
                                ->whereDate('end_date', '<=', $formArray['end_date']);
                        }
                        else {
                            // Only start date and end date provided
                            $query->whereDate('start_date', '>=', $formArray['start_date'])->whereDate('end_date', '<=', $formArray['end_date']);
                        }
                    } elseif (!empty($formArray['start_date']) && empty($formArray['end_date']) && !empty($formArray['booking_id'])) {
                        // Only start date provided with booking IDs
                        $query->whereIn('id', $formArray['booking_id'])->whereDate('start_date', '=', $formArray['start_date']);
                    } elseif (!empty($formArray['start_date']) && empty($formArray['end_date']) && empty($formArray['booking_id'])) {
                        // Only start date provided
                        $query->where('start_date', '=', $formArray['start_date']);
                    } elseif (empty($formArray['start_date']) && empty($formArray['end_date']) && !empty($formArray['booking_id'])) {
                        $query->whereIn('id', $formArray['booking_id']);
                    }
                })
            ->orderByRaw("CASE
                WHEN status = 'delivered' THEN dropoff_date
                WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
                ELSE pickup_date END ASC")
                ->skip($offset)
                ->take(5)
                ->get();
        }

        if(count($booked_cars))
        {

            $result =  $this->collectionOfBookedCars($booked_cars);

            $desktopBookingHtml = $result['desktopBookingHtml'];
            $mobileBookingHtml = $result['mobileBookingHtml'];

            return response()->json(['success' => true, 'error' => false, 'booked_cars'=> $booked_cars, 'desktopBookingHtml'=>$desktopBookingHtml, 'mobileBookingHtml'=> $mobileBookingHtml,
            'test'=> $test, 'offset'=> $offset
         ]);

        }
        else
        {
            return response()->json(['success' => false, 'error' => true, 'booked_cars'=>'', 'desktopBookingHtml'=>'','mobileBookingHtml'=>'',
            'test'=>$test, 'offset'=>$offset
            ]);
        }

    }

    ///// booking allListAjaxFilter filter //////
    public function allListAjaxFilter(Request $request)
    {
        $customerName = isset($request->customerName) ? $request->customerName : null;

        $customerMobile = isset($request->customerMobile) ? $request->customerMobile : null;

        $agentId = auth()->user()->id;
        $sharedCarId = shareCars::where('agent_id', $agentId)->pluck('car_id');
        $cars = cars::whereIn('id', $sharedCarId)
            // ->where('status', '!=', 'deleted')
            ->orderBy('created_at', 'desc')
            ->get();
        $partnerIds = $cars->pluck('user_id')->unique();


       $currentDate = Carbon::now();
       $requestData = $request->all();

        if (isset($requestData['form_data']))
        {
            parse_str($requestData['form_data'], $formArray);
            $start_date = $formArray['start_date'] ?? null;
            $end_date = $formArray['end_date'] ?? null;
            $formArray['booking_id']=[];
        }

        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);

        $booked_cars = CarBooking::where('booking_owner_id','=',$agentId)
        ->where(function ($query) use ($customerName, $customerMobile) {
            if ($customerName) {
                $query->where('customer_name', '=', $customerName);
            }

            if ($customerMobile) {
                $query->where('customer_mobile', '=', $customerMobile);
            }
        })

        ->where(function ($query) use ($formArray) {
            if (!empty($formArray['start_date']) && !empty($formArray['end_date'])) {
                if (!empty($formArray['booking_id'])) {
                    $query->whereIn('id', $formArray['booking_id'])
                        ->whereDate('start_date', '>=', $formArray['start_date'])
                        ->whereDate('end_date', '<=', $formArray['end_date']);
                }
                 else {
                    // Only start date and end date provided
                    $query->whereDate('start_date', '>=', $formArray['start_date'])->whereDate('end_date', '<=', $formArray['end_date']);
                }
            } elseif (!empty($formArray['start_date']) && empty($formArray['end_date']) && !empty($formArray['booking_id'])) {
                // Only start date provided with booking IDs
                $query->whereIn('id', $formArray['booking_id'])->whereDate('start_date', '=', $formArray['start_date']);
            } elseif (!empty($formArray['start_date']) && empty($formArray['end_date']) && empty($formArray['booking_id'])) {
                // Only start date provided
                $query->where('start_date', '=', $formArray['start_date']);
            } elseif (empty($formArray['start_date']) && empty($formArray['end_date']) && !empty($formArray['booking_id'])) {
                $query->whereIn('id', $formArray['booking_id']);
            }
        })->orderByRaw("CASE
                WHEN status = 'delivered' THEN dropoff_date
                WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
                ELSE pickup_date
            END ASC")
        ->take(5)
        ->get();


        // dd(count($booked_cars));

        if(count($booked_cars))
        {
            $desktopBookingHtml="";
            $mobileBookingHtml="";

            $result =  $this->collectionOfBookedCars($booked_cars);

            $desktopBookingHtml = $result['desktopBookingHtml'];
            $mobileBookingHtml = $result['mobileBookingHtml'];

            return response()->json(['success' => true, 'error' => false, 'booked_cars'=>$booked_cars, 'desktopBookingHtml'=>$desktopBookingHtml,'mobileBookingHtml'=>$mobileBookingHtml ]);
        }
        else{
            return response()->json(['success' => false, 'error' => true, 'booked_cars'=> $booked_cars,'desktopBookingHtml'=>'','mobileBookingHtml'=>'' ]);
        }
    }



    // checked only booking dates
    public function checkBooked(Request $request)
    {
        $carId = $request->carId;
        $firstDate = $request->startDate;
        $lastDate = $request->endDate;
        $actionType=$request->actionType;


        $start_date = Carbon::parse($request->startDate);
        $start_time = $request->pickupTime;
        $end_date = Carbon::parse($request->endDate);
        $end_time = $request->dropoffTime;

        $pickup_date = $start_date->format('Y-m-d') . ' ' . $start_time;
        $dropoff_date = $end_date->format('Y-m-d') . ' ' . $end_time;

        $pickupDate = date('Y-m-d H:i:s', strtotime($pickup_date));
        $dropoffDate = date('Y-m-d H:i:s', strtotime($dropoff_date));

        if(strcmp($actionType,'overlapped_date')!==0){
            $bookingCarStatus = CarBooking::where('carId', '=', $carId)
            ->where(function ($query) use ($pickupDate, $dropoffDate) {
                $query->whereBetween('pickup_date', [$pickupDate, $dropoffDate])
                    ->orWhereBetween('dropoff_date', [$pickupDate, $dropoffDate]);
            })->where(function ($query) {
                $query->where('status', '=', 'delivered')
                ->orWhereNull('status');
                    })
        ->get();

        }
        else
        {
            $bookingCarStatus = CarBooking::where('carId', '=', $carId)
            ->where(function ($query) use ($pickupDate, $dropoffDate) {
                $query->whereBetween('pickup_date', [$pickupDate, $dropoffDate])
                      ->orWhereBetween('dropoff_date', [$pickupDate, $dropoffDate])
                      ->orWhere(function ($query) use ($pickupDate, $dropoffDate) {
                          $query->where('pickup_date', '<=', $pickupDate)
                                ->where('dropoff_date', '>=', $dropoffDate);
                      });
            })->where(function ($query) {
                $query->whereNull('status')
                      ->orWhere('status', 'delivered');
            })
        ->get();
        }

         if(count($bookingCarStatus) > 0){
                return response()->json(['success' => true, 'error' => false]);
            } else {
                return response()->json(['success' => false, 'error' => true,'msg'=>'']);
            }

    }


    ///// checked and lockedDates with time ////////
    public function checkLockedAndBookedDates(Request $request)
    {

        $carId = $request->carId;
        $bookingId = $request->bookingId;
        $firstDate = $request->startDate;
        $lastDate = $request->endDate;
        $actionType=$request->actionType;

        $start_date = Carbon::parse($request->startDate);
        $start_time = $request->pickupTime;
        $end_date = Carbon::parse($request->endDate);
        $end_time = $request->dropoffTime;

        $pickup_date = $start_date->format('Y-m-d') . ' ' . $start_time;
        $dropoff_date = $end_date->format('Y-m-d') . ' ' . $end_time;

        $pickupDate = date('Y-m-d H:i:s', strtotime($pickup_date));
        $dropoffDate = date('Y-m-d H:i:s', strtotime($dropoff_date));

        $isDateBooked=CarBooking::where('carId', '=', $carId)->where('id','!=',$bookingId)
        ->where(function ($query) use ($pickupDate, $dropoffDate) {
            $query->whereBetween('pickup_date', [$pickupDate, $dropoffDate])
                  ->orWhereBetween('dropoff_date', [$pickupDate, $dropoffDate])
                  ->orWhere(function ($query) use ($pickupDate, $dropoffDate) {
                      $query->where('pickup_date', '<=', $pickupDate)
                            ->where('dropoff_date', '>=', $dropoffDate);
                  });
         })->where(function ($query) {
            $query->whereNull('status')
                    ->orWhere('status', 'delivered');
            })
        ->exists();

        if(!$isDateBooked){
            return response()->json(['success' => false, 'error' => true,'msg'=>'','dropoffDate'=>$dropoffDate,'pickupDate'=>$pickupDate]);
        }
          else{
                $bookingCarStatus = CarBooking::where('carId', '=', $carId)
                ->where(function ($query) use ($pickupDate, $dropoffDate) {
                    $query->whereBetween('pickup_date', [$pickupDate, $dropoffDate])
                        ->orWhereBetween('dropoff_date', [$pickupDate, $dropoffDate]);
                })->where(function ($query) {
                    $query->where('status', '=', 'delivered')
                    ->orWhereNull('status');
                        })
                ->get();

                $carHoldStatus = CarsBookingDateStatus::where('carId', '=', $carId)
                    ->where(function ($query) use ($firstDate, $lastDate) {
                        $query->whereBetween('start_date', [$firstDate, $lastDate])
                            ->orWhereBetween('end_date', [$firstDate, $lastDate]);
                    })
                ->get();

                if(count($bookingCarStatus) > 0 || count($carHoldStatus) > 0){
                    $msg = 'Please select a valid date, Either dates are Locked or Booked !!';
                    return response()->json(['success' => true, 'error' => false,'msg'=>$msg]);
                } else {
                    return response()->json(['success' => false, 'error' => true]);
                }

          }

    }

    ////// allClearAll start here  //////
    public function allClearAll()
    {
        $agentId = auth()->user()->id;
        $sharedCarId = shareCars::where('agent_id', $agentId)->pluck('car_id');
        $cars = cars::whereIn('id', $sharedCarId)
            // ->where('status', '!=', 'deleted')
            ->orderBy('created_at', 'desc')
            ->get();
        $partnerIds = $cars->pluck('user_id')->unique();

        $currentDate = Carbon::now();
        $cars = cars::whereIn('user_id', $partnerIds)->where('status', 'NOT LIKE', 'deleted')->orderBy('id', 'desc')->get();

        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);

        $booked_cars = CarBooking::where('booking_owner_id','=',$agentId)
            ->orderByRaw("CASE
                WHEN status = 'delivered' THEN dropoff_date
                WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
                ELSE pickup_date
            END ASC")
        ->take(5)
        ->get();

        if(count($booked_cars))
        {
            $result =  $this->collectionOfBookedCars($booked_cars);
            $desktopBookingHtml = $result['desktopBookingHtml'];
            $mobileBookingHtml = $result['mobileBookingHtml'];
            return response()->json(['success' => true, 'error' => false, 'booked_cars'=>$booked_cars, 'desktopBookingHtml'=>$desktopBookingHtml,'mobileBookingHtml'=>$mobileBookingHtml ]);
        }
        else{
            return response()->json(['success' => false, 'error' => true, 'booked_cars'=>$booked_cars,'desktopBookingHtml'=>'','mobileBookingHtml'=>'' ]);
        }
    }

    ////// allStatusChange start here  //////
    public function allStatusChange(Request $request)
    {
        $agentId = auth()->user()->id;
        $sharedCarId = shareCars::where('agent_id', $agentId)->pluck('car_id');
        $cars = cars::whereIn('id', $sharedCarId)
            // ->where('status', '!=', 'deleted')
            ->orderBy('created_at', 'desc')
            ->get();
        $partnerIds = $cars->pluck('user_id')->unique();

        date_default_timezone_set('Asia/Kolkata');
        $currentTimeDate = now();

        $checkBookingStatus = CarBooking::whereId($request->bookingId)->first();

        $successMsg = false;
        $errorMsg = false;

        if ($checkBookingStatus)
        {
            if (strcmp($checkBookingStatus->status, 'collected') == 0) {
                $errorMsg = "Already collected, please refresh the page";
            } elseif (strcmp($checkBookingStatus->status, 'delivered') == 0) {
                if (strcmp($request->bookingStatus, 'delivered') == 0) {
                    $errorMsg = "Already delivered, please refresh the page";
                } else {

                    CarBooking::where('id', $request->bookingId)->update([
                        'collected_time' => $currentTimeDate,
                        'status' => $request->bookingStatus,
                    ]);
                    $successMsg = "Successfully confirmed collected";
                }
            } else {
                    CarBooking::where('id', $request->bookingId)->update([
                        'delivered_time' => $currentTimeDate,
                        'status' => $request->bookingStatus,
                    ]);
                    $successMsg = "Successfully confirmed delivered";
            }
        } else {
            $errorMsg = "Booking not found";
        }


        $bookingLength = $request->bookingLength;

        if(strcmp($request->checkStatus, 'list') == 0)
        {
            $currentDate = Carbon::now();

            $cars = cars::where('status', 'NOT LIKE', 'deleted')->orderBy('id', 'desc')->get();

            $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
            $currentDateTime = Carbon::parse($oldcurrentDateTime);

            $booked_cars =CarBooking::where('booking_owner_id','=',$agentId)
            ->orderByRaw("CASE
                WHEN status = 'delivered' THEN dropoff_date
                WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
                ELSE pickup_date
            END ASC")
            ->take($bookingLength)
            ->get();
        }
        elseif((strcmp($request->checkStatus, 'desktop_search') == 0) || (strcmp($request->checkStatus, 'mobile_search') == 0) )
        {

            $currentDate = Carbon::now();
            $requestData = $request->all();

            if (isset($requestData['form_data']))
            {
                parse_str($requestData['form_data'], $formArray);
                $start_date = $formArray['start_date'] ?? null;
                $end_date = $formArray['end_date'] ?? null;
                $booking_ids = $formArray['booking_id'] ?? [];
            }

            $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
            $currentDateTime = Carbon::parse($oldcurrentDateTime);

            $booked_cars = CarBooking::where('booking_owner_id','=',$agentId)
            ->where(function ($query) use ($formArray) {
                if (!empty($formArray['start_date']) && !empty($formArray['end_date'])) {
                    if (!empty($formArray['booking_id'])) {
                        $query->whereIn('id', $formArray['booking_id'])
                            ->whereDate('start_date', '>=', $formArray['start_date'])
                            ->whereDate('end_date', '<=', $formArray['end_date']);
                    }
                    else {
                        // Only start date and end date provided
                        $query->whereDate('start_date', '>=', $formArray['start_date'])->whereDate('end_date', '<=', $formArray['end_date']);
                    }
                } elseif (!empty($formArray['start_date']) && empty($formArray['end_date']) && !empty($formArray['booking_id'])) {
                    // Only start date provided with booking IDs
                    $query->whereIn('id', $formArray['booking_id'])->whereDate('start_date', '=', $formArray['start_date']);
                } elseif (!empty($formArray['start_date']) && empty($formArray['end_date']) && empty($formArray['booking_id'])) {
                    // Only start date provided
                    $query->where('start_date', '=', $formArray['start_date']);
                } elseif (empty($formArray['start_date']) && empty($formArray['end_date']) && !empty($formArray['booking_id'])) {
                    $query->whereIn('id', $formArray['booking_id']);
                }
            })
            ->orderByRaw("CASE
                WHEN status = 'delivered' THEN dropoff_date
                WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
                ELSE pickup_date
            END ASC")
            ->take($bookingLength)
            ->get();
        }

        if(count($booked_cars))
        {
            $result =  $this->collectionOfBookedCars($booked_cars);

            $desktopBookingHtml = $result['desktopBookingHtml'];
            $mobileBookingHtml = $result['mobileBookingHtml'];

            return response()->json(['success' => true, 'error' => false,'successMsg'=> $successMsg , 'errorMsg'=> $errorMsg,  'booked_cars'=>$booked_cars, 'desktopBookingHtml'=>$desktopBookingHtml,'mobileBookingHtml'=>$mobileBookingHtml ]);
        }
        else
        {
            return response()->json(['success' => false, 'error' => true, 'msg'=>false ,'booked_cars'=>$booked_cars,'desktopBookingHtml'=>'','mobileBookingHtml'=>'' ]);
        }
    }


    public function updateBookingDates(Request $request)
    {
        $start_date = Carbon::parse($request->carDetails['start_date']);
        $start_time = $request->start_time;
        $end_date = Carbon::parse($request->carDetails['end_date']);
        $end_time = $request->end_time;

        $pickup_date = $start_date->format('Y-m-d') . ' ' . $start_time;
        $dropoff_date = $end_date->format('Y-m-d') . ' ' . $end_time;

        $carId=$request->carDetails['carId'];
        $id=$request->carDetails['bookingId'];

        if($id){
            $carBookingUpdate= CarBooking::whereId($id)->update([
            'start_date' => date('Y-m-d H:i:s', strtotime($start_date)),
            'pickup_date' => date('Y-m-d H:i:s', strtotime($pickup_date)),
            'end_date' => date('Y-m-d H:i:s', strtotime($end_date)),
            'dropoff_date' => date('Y-m-d H:i:s', strtotime($dropoff_date)),
            'pickup_time' => $start_time,
            'dropoff_time' => $end_time,
            ]);
            return response()->json(['success' => true, 'error' => false, 'msg'=>'booking dates are updated','pickup_date'=>$pickup_date,'dropoff_date'=>$dropoff_date ]);

        }else{
            return response()->json(['success' => false, 'error' => true, 'msg'=>'carId is invalid' ]);
        }
    }

    // cancel bookings
    public function bookingCancel(Request $request)
    {
        $carId= $request->carId;
        $bookingId= $request->bookingId;
        if($bookingId){
            $CancelBooking= CarBooking::whereId($bookingId)->update(['status' => 'canceled']);
                if($CancelBooking){
                    return response()->json(['success' => true, 'error' => false, 'msg'=>'booking canceled' ]);
                }
                else{
                    return response()->json(['success' => false, 'error' => true, 'msg'=>'error occured']);
                }
        }else{
            return response()->json(['success' => false, 'error' => true, 'msg'=>'invalid id']);
        }
    }


    public function getLocation(Request $request){

        $searchTerm = $request->input('search');

        // dd('search term',$searchTerm);

         $locationArr = [

            "Top Locations"=>
            [
                [
                    "id"=> "1",
                    "location"=> "Anjuna"
                ],
                [
                    "id"=> "2",
                    "location"=> "Baga"
                ],
                [
                    "id"=> "3",
                    "location"=> "Calangute"
                ],
                [
                    "id"=>"4",
                    "location"=> "Candolim"
                ],
                [
                    "id"=>"5",
                    "location"=> "Madagaon Bus Stand"
                ],
                [
                    "id"=>"6",
                    "location"=> "Panjim"
                ],
                [
                    "id"=>"7",
                    "location"=> "Railway Station (Madagaon Railway Station)"
                ],
                [
                    "id"=>"8",
                    "location"=> "Madagaon Railway Station"
                ]
                ,
                [
                    "id"=>"9",
                    "location"=> "Vasco Railway Station"
                ]
            ],
            "All Locations"=> [
                [
                    "id"=> "10",
                    "location"=> "Anjuna"
                ],
                [
                    "id"=>"11",
                    "location"=> "Agacaim"
                ],
                [
                    "id"=>"12",
                    "location"=> "Agonda"
                ],
                [
                    "id"=>"13",
                    "location"=> "Aldona"
                ],
                [
                    "id"=>"14",
                    "location"=> "Aquem"
                ],
                [
                    "id"=>"15",
                    "location"=> "Arambol Beach"
                ],
                [
                    "id"=>"16",
                    "location"=> "Arpora"
                ],
                [
                    "id"=>"17",
                    "location"=> "Ashwem Beach"
                ],
                [
                    "id"=>"18",
                    "location"=> "Assagao"
                ],
                [
                    "id"=>"19",
                    "location"=> "Assonora"
                ],
                [
                    "id"=>"20",
                    "location"=> "Baga"
                ],
                [
                    "id"=>"21",
                    "location"=> "Bambolim"
                ],
                [
                    "id"=>"22",
                    "location"=> "Bardez"
                ],
                [
                    "id"=>"23",
                    "location"=> "Bastora"
                ],
                [
                    "id"=>"24",
                    "location"=> "Benaulim"
                ],
                [
                    "id"=>"25",
                    "location"=> "Betal Betim"
                ],
                [
                    "id"=>"26",
                    "location"=> "Betim"
                ],
                [
                    "id"=>"27",
                    "location"=> "Betul"
                ],
                [
                    "id"=>"28",
                    "location"=> "Bicholim"
                ],
                [
                    "id"=>"29",
                    "location"=> "Borim"
                ],
                [
                    "id"=>"30",
                    "location"=> "Calangute"
                ],
                [
                    "id"=>"31",
                    "location"=> "Candolim"
                ],
                [
                    "id"=>"32",
                    "location"=> "Cabderam"
                ],
                [
                    "id"=>"33",
                    "location"=> "Camurlim"
                ],
                [
                    "id"=>"34",
                    "location"=> "Canacona"
                ],
                [
                    "id"=>"35",
                    "location"=> "Canaguinim Beach"
                ],
                [
                    "id"=>"36",
                    "location"=> "Canca"
                ],
                [
                    "id"=>"37",
                    "location"=> "Charpora Fort"
                ],
                [
                    "id"=>"38",
                    "location"=> "Chimbel"
                ],
                [
                    "id"=>"39",
                    "location"=> "Chogam Road"
                ],
                [
                    "id"=>"40",
                    "location"=> "Chopdem"
                ],
                [
                    "id"=>"41",
                    "location"=> "Chorao Island"
                ],
                [
                    "id"=>"42",
                    "location"=> "Cola"
                ],
                [
                    "id"=>"43",
                    "location"=> "Colva"
                ],
                [
                    "id"=>"44",
                    "location"=> "Cortalim"
                ],
                [
                    "id"=>"45",
                    "location"=> "Covale"
                ],
                [
                    "id"=>"46",
                    "location"=> "Covelossim"
                ],
                [
                    "id"=>"47",
                    "location"=> "Curchorem"
                ],
                [
                    "id"=>"48",
                    "location"=> "Dabolim"
                ],
                [
                    "id"=>"49",
                    "location"=> "Dabolim Airport"
                ],
                [
                    "id"=>"50",
                    "location"=> "Divar Island"
                ],
                [
                    "id"=>"51",
                    "location"=> "Dona Paula"
                ],
                [
                    "id"=>"52",
                    "location"=> "Dudhsagar"
                ],
                [
                    "id"=>"53",
                    "location"=> "Fort Aguada"
                ],
                [
                    "id"=>"54",
                    "location"=> "Goa Velha"
                ],
                [
                    "id"=>"55",
                    "location"=> "Galgibaga Beach Goa"
                ],
                [
                    "id"=>"56",
                    "location"=> "Goa Chitra Museum"
                ],
                [
                    "id"=>"57",
                    "location"=> "Karmali Railway Station"
                ],
                [
                    "id"=>"58",
                    "location"=> "Kundai"
                ],
                [
                    "id"=>"59",
                    "location"=> "Loliem"
                ],
                [
                    "id"=>"60",
                    "location"=> "Madagaon Railway Station"
                ],
                [
                    "id"=>"61",
                    "location"=> "Majorda"
                ],
                [
                    "id"=>"62",
                    "location"=> "Mandrem Beach"
                ],
                [
                    "id"=>"63",
                    "location"=> "Mapusa"
                ],
                [
                    "id"=>"64",
                    "location"=> "Marbela Beach"
                ],
                [
                    "id"=>"65",
                    "location"=> "Mardol"
                ],
                [
                    "id"=>"66",
                    "location"=> "Mayem"
                ],
                [
                    "id"=>"67",
                    "location"=> "Miramar"
                ],
                [
                    "id"=>"68",
                    "location"=> "Mobor"
                ],
                [
                    "id"=>"69",
                    "location"=> "Molem"
                ],
                [
                    "id"=>"70",
                    "location"=> "Mopa"
                ],
                [
                    "id"=>"71",
                    "location"=> "Morjim"
                ],
                [
                    "id"=>"72",
                    "location"=> "Madagaon Bus Stand"
                ],
                [
                    "id"=>"73",
                    "location"=> "Nagao"
                ],
                [
                    "id"=>"74",
                    "location"=> "Navelim"
                ],
                [
                    "id"=>"75",
                    "location"=> "Nerul"
                ],
                [
                    "id"=>"76",
                    "location"=> "Nuvem"
                ],
                [
                    "id"=>"77",
                    "location"=> "Old Goa"
                ],
                [
                    "id"=>"78",
                    "location"=> "Ozran Beach"
                ],
                [
                    "id"=>"79",
                    "location"=> "Panjim"
                ],
                [
                    "id"=>"80",
                    "location"=> "Palivem"
                ],
                [
                    "id"=>"81",
                    "location"=> "Palolem"
                ],
                [
                    "id"=>"82",
                    "location"=> "Parcem"
                ],
                [
                    "id"=>"83",
                    "location"=> "Park Hayat Goa"
                ],
                [
                    "id"=>"84",
                    "location"=> "Parra"
                ],
                [
                    "id"=>"85",
                    "location"=> "Penha De Franca"
                ],
                [
                    "id"=>"86",
                    "location"=> "Pernem"
                ],
                [
                    "id"=>"87",
                    "location"=> "Pilar"
                ],
                [
                    "id"=>"88",
                    "location"=> "Pilern"
                ],
                [
                    "id"=>"89",
                    "location"=> "Poinguinim"
                ],
                [
                    "id"=>"90",
                    "location"=> "Polem"
                ],
                [
                    "id"=>"91",
                    "location"=> "Ponda"
                ],
                [
                    "id"=>"92",
                    "location"=> "Porvorim"
                ],
                [
                    "id"=>"93",
                    "location"=> "Quepem"
                ],
                [
                    "id"=>"94",
                    "location"=> "Querim"
                ],
                [
                    "id"=>"95",
                    "location"=> "Raia"
                ],
                [
                    "id"=>"96",
                    "location"=> "Raibandar"
                ],
                [
                    "id"=>"97",
                    "location"=> "Rajbag Beach"
                ],
                [
                    "id"=>"98",
                    "location"=> "Revona"
                ],
                [
                    "id"=>"99",
                    "location"=> "Revora"
                ],
                [
                    "id"=>"100",
                    "location"=> "Railway Station (Madagaon Railway Station)"
                ],
                [
                    "id"=>"101",
                    "location"=> "Railway Station (Vasco Railway Station)"
                ],
                [
                    "id"=>"102",
                    "location"=> "Saligaon"
                ],
                [
                    "id"=>"103",
                    "location"=> "Sangolda"
                ],
                [
                    "id"=>"104",
                    "location"=> "Sanguem"
                ],
                [
                    "id"=>"105",
                    "location"=> "Sanquelim"
                ],
                [
                    "id"=>"106",
                    "location"=> "Santa Cruz"
                ],
                [
                    "id"=>"107",
                    "location"=> "Satari"
                ],
                [
                    "id"=>"108",
                    "location"=> "Shiroda"
                ],
                [
                    "id"=>"109",
                    "location"=> "Siolim"
                ],
                [
                    "id"=>"110",
                    "location"=> "Siridao"
                ],
                [
                    "id"=>"111",
                    "location"=> "Taleigao"
                ],
                [
                    "id"=>"112",
                    "location"=> "Thivim Railway Station"
                ],
                [
                    "id"=>"113",
                    "location"=> "Tiracol"
                ],
                [
                    "id"=>"114",
                    "location"=> "Tiswadi"
                ],
                [
                    "id"=>"115",
                    "location"=> "Torxem"
                ],
                [
                    "id"=>"116",
                    "location"=> "Vagator"
                ],
                [
                    "id"=>"117",
                    "location"=> "Vainguinim Beach"
                ],
                [
                    "id"=>"118",
                    "location"=> "Valpoi"
                ],
                [
                    "id"=>"119",
                    "location"=> "Varca"
                ],
                [
                    "id"=>"120",
                    "location"=> "Vasco"
                ],
                [
                    "id"=>"121",
                    "location"=> "Verem"
                ],
                [
                    "id"=>"122",
                    "location"=> "Verna"
                ],
                [
                    "id"=>"123",
                    "location"=> "Vasco Railway Station"
                ]
            ],
            "Other"=> [
                [
                    "id"=>"124",
                    "location"=> "Other"
                ],
            ],

        ];


        if ($searchTerm) {

            foreach ($locationArr as $key => $locations) {
                $filteredLocations = array_filter($locations, function($location) use ($searchTerm) {
                    return stripos($location['location'], $searchTerm) !== false;
                });



                if(!empty($filteredLocations)){

                    $locationArr[$key] = array_values($filteredLocations);
                }else{

                    // dd('filteredLocations is empty');
                    $filteredLocations = array_filter($locations, function($location) {
                         return stripos($location['location'], 'other') !== false;
                    });

                     $locationArr[$key] = array_values($filteredLocations);
                }
            }
        }

         return response()->json(['locations'=>$locationArr]);

    }

}

?>
