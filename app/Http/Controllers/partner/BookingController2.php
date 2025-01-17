<?php
namespace App\Http\Controllers\partner;
use App\Http\Controllers\Controller;
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
use Carbon\Carbon;
use Intervention\Image\Facades\Image;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;

class BookingController2 extends Controller
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


    //bookings list
    public function list()
    {
        date_default_timezone_set('Asia/Kolkata');
        $user_id = auth()->user()->id;
        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);

        $countBookedCars = CarBooking::where('user_id', $user_id)
        ->where(function ($query) {
                $query->whereNull('status')->orWhere('status','LIKE','delivered');
        })
        ->orderByRaw("CASE
            WHEN status = 'delivered' THEN dropoff_date
            WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
            ELSE pickup_date
        END ASC")
        ->count();

        $booked_cars = CarBooking::where('user_id', $user_id)
        ->where(function ($query) {
                $query->whereNull('status')->orWhere('status','LIKE','delivered');
        })
        ->orderByRaw("CASE
            WHEN status = 'delivered' THEN dropoff_date
            WHEN '$currentDateTime' >= pickup_date AND status != 'delivered' THEN dropoff_date
            ELSE pickup_date
            END ASC")
        ->take(5)
        ->get();


        $allBookedCars = CarBooking::where('user_id', $user_id)
        ->where(function ($query) {
                $query->whereNull('status')->orWhere('status','LIKE','delivered');
        })
        ->orderByRaw("CASE
            WHEN status = 'delivered' THEN dropoff_date
            WHEN '$currentDateTime' >= pickup_date AND status != 'delivered' THEN dropoff_date
            ELSE pickup_date
            END ASC")
        // ->take(5)
        ->get();



        return view('partner.bookings.list', compact('booked_cars','allBookedCars','countBookedCars'));
    }


    ////// loadmore list start here  //////
    public function loadMoreBookings(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');

        $user_id = auth()->user()->id;

        $test = $request->checkLoadMore;

        if(strcmp($request->checkLoadMore, 'list') === 0)
        {
            $currentDate = Carbon::now();
            $offset = $request->input('offset', 0);


            $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
            $currentDateTime = Carbon::parse($oldcurrentDateTime);

            $booked_cars = CarBooking::where('user_id', $user_id)
            ->where(function ($query) {
                $query->whereNull('status')->orWhere('status','LIKE','delivered');
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
        elseif((strcmp($request->checkLoadMore, 'desktop_search') === 0) || (strcmp($request->checkLoadMore, 'mobile_search') === 0))
        {

            // echo "<pre>";
            // print_r($request->toArray());
            // die;

            $customerName = isset($request->customerName) ? $request->customerName : null;
            // $countryCode = isset($request->countryCode) ? $request->countryCode : null;
            $customerMobile = isset($request->customerMobile) ? $request->customerMobile : null;

            // echo "<pre>";
            // echo "customerName".$customerName;
            // echo "customerMobile".$customerMobile;
            // // print_r($request->toArray());
            // die;

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

            // echo "<pre>";
            // echo "customerName".$customerName;
            // echo "customerMobile".$customerMobile;
            // // print_r($request->toArray());
            // die;

            // dd('$booking_id:',$formArray['booking_id'],'start_date:',$formArray['start_date'],'end_date:',$formArray['end_date']);

            $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
            $currentDateTime = Carbon::parse($oldcurrentDateTime);

            $booked_cars = CarBooking::where('user_id', $user_id)
            ->where(function ($query) use ($customerName, $customerMobile) {
                if ($customerName) {
                    $query->where('customer_name', '=', $customerName);
                }

                if ($customerMobile) {
                    $query->where('customer_mobile', '=', $customerMobile);
                }
                // dd("3");
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
                    // dd('only booking id',$formArray['booking_id']);
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
            ->skip($offset)
            ->take(5)
            ->get();

            // echo "<pre>";
            // echo "offset".$offset;
            // print_r($booked_cars->toArray());
            // die;
        }
        elseif((strcmp($request->checkLoadMore, 'autocomplete_search') === 0) )
        {
            // echo "<pre>";
            // print_r($request->toArray());
            // die;

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

                $booked_cars = CarBooking::where('user_id', $user_id)
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


                    // dd("3");
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
                END ASC")
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

    public function calendar2(Request $request)
    {
        $userId = auth()->user()->id;

        // dd($request->all());

        $car_type = '';
        $car_name = '';

        $requestData = $request->all();

        $start_date = $requestData['start_date'] ?? '';
        $end_date = $requestData['end_date'] ?? '';
        $car_type = $requestData['car_type'] ?? '';
        $car_name = $requestData['car_name'] ?? '';

        if ($requestData) {

            $start_date = isset($requestData['start_date']) ? $requestData['start_date'] : '';
            $end_date = isset($requestData['end_date']) ? $requestData['end_date'] : '';
            $car_type = isset($requestData['car_type']) ? $requestData['car_type'] : '';
            $car_name = isset($requestData['car_name']) ? $requestData['car_name'] : '';

            $formArray = [
                'start_date' => $requestData['start_date'] ?? null,
                'end_date'   => $requestData['end_date'] ?? null,
                'car_type'   => $requestData['car_type'] ?? [],
                'car_name'   => $requestData['car_name'] ?? [],
            ];
        $cars = cars::select(
        'cars.id',
        'cars.name',
        'cars.registration_number',
        'cars.transmission',
        'cars.fuel_type',
        'cars.manufacturing_year',
        'cars.car_type',
        'cars.price',
        'cars.seats',
        'cars.status',
        'cars.user_id',
        'cars.created_at',
        'cars.updated_at'
      )->where('cars.user_id','=',$userId)->join('users', function ($join) {
        $join->on('users.id', '=', 'cars.user_id')->where('users.status', 'NOT LIKE', 'deleted');
      })->where('cars.status', 'NOT LIKE', 'deleted')->where(function ($query) use ($formArray, $userId) {
        if (!empty($formArray['car_name'])) {
            $query->whereIn('cars.name', $formArray['car_name']);
        }

        if (!empty($formArray['car_type']) && empty($formArray['car_name'])) {
            $query->whereIn('cars.car_type', $formArray['car_type']);
        }

        if (!empty($formArray['car_type']) && !empty($formArray['car_name'])) {
            $query->whereIn('cars.name', $formArray['car_name'])->whereIn('cars.car_type', $formArray['car_type']);
        }

        if (!empty($formArray['start_date'])) {
            if (empty($formArray['end_date'])) {
                $query->whereNotExists(function ($subQuery) use ($formArray) {
                    $subQuery->select(DB::raw(1))
                        ->from('car_bookings')
                        ->whereRaw('cars.id = car_bookings.carId')
                        ->where(function ($query) use ($formArray) {
                            $query->where('car_bookings.start_date', '<=', $formArray['start_date'])
                                ->where('car_bookings.end_date', '>=', $formArray['start_date']);
                        });
                });
            } else {
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
        } else {
            $query->whereIn('cars.user_id', [$userId]);
        }
        })->orderBy('cars.created_at', 'DESC')->get();

        }
        else
        {
           $cars = cars::where('status', '!=', 'deleted')->where('user_id', $userId)->orderByDesc('created_at')->get();
        }
        $startDate='';
        $endDate='';

        $allCarsFilter = User::join('cars', 'users.id', '=', 'cars.user_id')
        ->where('users.id', '=', auth()->user()->id)
        ->where('users.status', 'NOT LIKE', 'inactive')
        ->where('cars.status', 'NOT LIKE', 'deleted')
        ->whereHas('roles', function ($q) {
        $q->where('name', 'LIKE', 'partner');
        })
        ->with('roles')
        ->select('cars.*')
        ->orderBy('cars.created_at', 'desc')
        ->distinct()
        ->get();


        if((isset($formArray['start_date'])&&
        !empty($formArray['start_date']))&&
        (isset($formArray['end_date'])&&
        !empty($formArray['end_date']))){

        $dates = [];
        $startDate = new Carbon( $formArray['start_date']);
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

        elseif((isset($formArray['start_date']) && !empty($formArray['start_date']))){
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

        elseif((isset($formArray['end_date']) && !empty($formArray['end_date'])) && !empty($formArray['start_date'])){
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

          $filtersCount= $this->checkFiltersCount($start_date,$end_date,$car_type,$car_name);


     return view('partner.bookings.calendar2', compact('dates','cars','allCarsFilter','startDate','endDate','start_date','end_date','car_name','car_type','filtersCount'));
    }


    ////// calendar post for get booking details  //////
    public function calendarPost(Request $request)
    {
        $carId=[];
        $carIds=[];
        $fullDates=[];
        $carId = $request['carDetails']['carId'];
        $firstDate= $request['carDetails']['start_date'];
        $pickupTime = isset($request['start_time']) ? $request['start_time'] : '';
        $lastDate= $request['carDetails']['end_date'];
        $dropoffTime = isset($request['end_time']) ? $request['end_time'] : '';
        $actionType=$request->action_type;
        $flag = $this->checkBookingStatus($carId, $firstDate, $lastDate,$actionType);
        if ($flag) {
        return response()->json(['success' => false, 'error' => true, 'msg' => 'Please select a valid date, Either dates are Locked or Booked !!' ]);
        }
        else{
            $car = cars::whereId($carId)->where('status','NOT LIKE','deleted')->first();
            $userId = $car->user_id;
            if($car){
            Session::put('PartebookingStarted','yes');
            CarsBookingDateStatus::create([
                'carId'=>$carId,
                'start_date'=>$firstDate,
                'end_date'=>$lastDate,
            ]);
            return response()->json(['success' => true, 'error' => false, 'msg' => 'succeed','url'=>route('partner.booking.add',compact('firstDate','lastDate','carId','pickupTime','dropoffTime','userId') )]);
            }
            else{
            return response()->json(['success' => false, 'error' => true, 'msg' => 'car not available' ]);
            }
        }
    }

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
        $userId = auth()->user()->id;

        $uniqueCarIds = $request->carIds;


        $cars = cars::where('status', '!=', 'deleted')
        ->where('user_id',$userId)
        ->whereNotIn('id', $uniqueCarIds)
        ->orderBy('created_at', 'DESC')
        ->paginate(3, ['cars.*']);


         $date_data = '';
         $main_loop = '';
         $carsCount= count($cars);

         foreach ($cars as $car){
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
           if (strcmp(Helper::getUserMeta($car->user_id, 'CompanyImageId'), '') != 0) {
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
                            <div class="h-[67px] md:h-[45px] py-[10px] flex justify-center items-center overflow-hidden md:py-[5px] sm:py-1">
                                <img src="'.$modifiedImgUrl.'" alt="car image" class="object-contain  h-full">
                            </div>
                        </div>
                    </div>

                    <div class="car_status_row_sec pl-[15px] right_section md:flex md:flex-row  flex flex-col md:items-start">
                        <div class="car_details_content md:flex-col md:items-start flex justify-start items-center">

                            <div class="car_name_sec">
                                <a href="'.route('partner.car.view',$car->id).'" class="text-base md:text-center font-medium capitalize showcar_title_b hover:underline transition-all duration-300 ease-out   text-purple md:font-bold md:text-sm">
                                    '.$car->name.'
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
                $titleAttribute = date("d M Y", strtotime($date['full_date']));
                  $main_loop .='<li class="inline-block adj_margin clickable relative"
                    data-date-full_date="'.$date['full_date'].'"
                    data-date-month="'.$date['month'].'" data-date-day="'.$date['day'].'"
                    data-car-id="'.$car->id.'">
                    <a href="javascript:void(0);" ' . (Helper::isBooked($car->id, $date['full_date']) ?
                    'data-fancybox data-src="#booking_Details_showcase_popup"'.
                    'data-bookingId="' . Helper::getBookingDataByCarId($car->id, $date['full_date'])->bookingId . '"' .
                    'data-overlap-pickupdate="' . Helper::getAdditionalBookingsByCarId($car->id,$date['full_date'])->pickup_date . '"' .
                    'data-overlap-dropoffdate="' . Helper::getAdditionalBookingsByCarId($car->id,$date['full_date'])->dropoff_date . '"' .
                    'data-overlap-pickuptime="' . Helper::getAdditionalBookingsByCarId($car->id,$date['full_date'])->pickup_time . '"' .
                    'data-overlap-dropofftime="' . Helper::getAdditionalBookingsByCarId($car->id,$date['full_date'])->dropoff_time . '"' .
                    'data-booked-startDate="' . Helper::getBookingDataByCarId($car->id, $date['full_date'])->start_date . '"' .
                    'data-booked-endDate="' . Helper::getBookingDataByCarId($car->id, $date['full_date'])->end_date . '"' .
                    'data-booked-customer-country-code="' . Helper::getBookingDataByCarId($car->id, $date['full_date'])->customer_mobile_country_code . '"' .
                    'data-overlap-customer-country-code="' . Helper::getAdditionalBookingsByCarId($car->id, $date['full_date'])->customer_mobile_country_code . '"' .
                    'data-booked-customer-name="' . Helper::getBookingDataByCarId($car->id, $date['full_date'])->customer_name . '"' .
                    'data-overlap-customer-name="' . Helper::getAdditionalBookingsByCarId($car->id, $date['full_date'])->customer_name . '"' .
                    'data-booked-customer-number="' . Helper::getBookingDataByCarId($car->id, $date['full_date'])->customer_mobile . '"' .
                    'data-overlap-customer-number="' . Helper::getAdditionalBookingsByCarId($car->id, $date['full_date'])->customer_mobile . '"' .
                    'data-booked-pickup-location="' . Helper::getBookingDataByCarId($car->id, $date['full_date'])->pickup_location . '"' .
                    'data-overlap-pickup-location="' . Helper::getAdditionalBookingsByCarId($car->id, $date['full_date'])->pickup_location . '"' .
                    'data-booked-dropoff-location="' . Helper::getBookingDataByCarId($car->id, $date['full_date'])->dropoff_location . '"' .
                    'data-overlap-dropoff-location="' . Helper::getAdditionalBookingsByCarId($car->id, $date['full_date'])->dropoff_location . '"' .
                    'data-pickupTime="' . Helper::getBookingDataByCarId($car->id, $date['full_date'])->pickup_time . '"' .
                    'data-dropoffTime="' . Helper::getBookingDataByCarId($car->id, $date['full_date'])->dropoff_time . '"' : '') . '
                    ' . (Helper::islocked($car->id, $date['full_date']) ?
                    'data-locked-startdate="' . Helper::getLockedDataByCarId($car->id, $date['full_date'])->start_date . '"' .
                    'data-locked-enddate="' . Helper::getLockedDataByCarId($car->id, $date['full_date'])->end_date . '"' : '')
                    . '
                    data-fancybox data-src="#open_popup" class="custom_pick_drop_popup inline-flex flex-col items-center justify-center links
                    '.(Helper::isBooked($car->id, $date['full_date']) ? 'booked adj_status_width' :
                     (Helper::isLocked($car->id, $date['full_date']) ? 'locked adj_status_width' : 'activeDate')).'">

                     <div class="'.(Helper::hasAdditionalBookingsByCarId($car->id, $date['full_date']) ? 'overlap_booking' :''). (Helper::overlapLockedOrNot($car->id, $date['full_date']) ? 'overlap_locked' :'').'">
                     <div class="relative range_active spaces_around_status" title="' . $titleAttribute . '">
                     <div class="mx-auto leading-[0] car_status_container_inner flex items-center cell_dates_day justify-center w-[36px] h-[36px]  md:w-[30px] md:h-[30px] rounded-full
                     '.(Helper::isBooked($car->id, $date['full_date']) ? '' :
                      (Helper::isLocked($car->id, $date['full_date']) ? '' : 'bg-[#E4FFDD]   border border-[#25BE00]')).'">' .(Helper::isBooked($car->id, $date['full_date'])? '': (Helper::isLocked($car->id, $date['full_date'])?'' : '<span class="inline-block date_day ">'.$date['day'].'</span>' )).'
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
        return response()->json([ 'data' => $main_loop,'next' => $start + 3]);
   }

    ///// booking list filter //////
    public function listAjaxFilter(Request $request)
    {

        // echo "<pre>";
        // print_r($request->toArray());
        // print_r($request->customerName);
        // die;

        $customerName = isset($request->customerName) ? $request->customerName : null;
        // $countryCode = isset($request->countryCode) ? $request->countryCode : null;
        $customerMobile = isset($request->customerMobile) ? $request->customerMobile : null;

        // echo "customerName: " . $customerName;
        // echo "customerMobile: " . $customerMobile;
        // die;

       $user_id = auth()->user()->id;

       $currentDate = Carbon::now();
       $requestData = $request->all();

        if (isset($requestData['form_data']))
        {
            parse_str($requestData['form_data'], $formArray);
            $start_date = $formArray['start_date'] ?? null;
            $end_date = $formArray['end_date'] ?? null;
            // $booking_ids = $formArray['booking_id'] ?? [];
            $formArray['booking_id']=[];
        }

        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);

        $booked_cars = CarBooking::where('user_id', $user_id)
        ->where(function ($query) use ($customerName, $customerMobile) {
            if ($customerName) {
                $query->where('customer_name', '=', $customerName);
            }

            if ($customerMobile) {
                $query->where('customer_mobile', '=', $customerMobile);
            }
            // dd("3");
        })
        // ->where('end_date', '>', $currentDate)
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
        ->take(5)
        ->get();

        if(count($booked_cars)){

            $result =  $this->collectionOfBookedCars($booked_cars);

            $desktopBookingHtml = $result['desktopBookingHtml'];
            $mobileBookingHtml = $result['mobileBookingHtml'];

            return response()->json(['success' => true, 'error' => false, 'booked_cars'=>$booked_cars, 'desktopBookingHtml'=>$desktopBookingHtml,'mobileBookingHtml'=>$mobileBookingHtml ]);
        }
        else{
        return response()->json(['success' => false, 'error' => true, 'booked_cars'=>$booked_cars, 'desktopBookingHtml'=>'','mobileBookingHtml'=>'' ]);
        }
    }

    public function add(Request $request)
    {
        $carId = $request->input('carId');
        $firstDate = $request->input('firstDate');
        $pickupTime = $request->input('pickupTime');
        $lastDate = $request->input('lastDate');
        $dropoffTime = $request->input('dropoffTime');
        if (session()->has('PartebookingStarted')) {
            session()->forget('PartebookingStarted');
            $car = cars::where('id', $carId)->where('status', '!=', 'deleted')->first();
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
            return view('partner.bookings.add', compact('car', 'carId','userId','firstDate', 'lastDate', 'pickupTime', 'dropoffTime', 'bookingStatusId','perDayRentalCharges','totalDayRentalCharges','diffInDays'));
        } else {
             return redirect()->route('partner.booking.calendar')->with('error', 'Invalid car selection or booking status.');
        }
        }
        else {

            $bookingStatus = CarsBookingDateStatus::where('carId', '=', $carId)
            ->where('start_date', '<=', $firstDate)
            ->where('end_date', '>=', $lastDate)
            ->delete();
            return redirect()->route('partner.booking.calendar')->with('error', 'Time has been expired.');
        }
    }

    ////// add customer booking for customer  //////
    public function addPost(Request $request, $carId)
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

        $pickup_location = ($request->pickup_location == 'other') ? $request->other_pickup_location : $request->pickup_location;
        $dropoff_location = ($request->dropoff_location == 'other') ? $request->other_dropoff_location : $request->dropoff_location;

        $user = User::where('status','NOT LIKE','inactive')->find($userId);
        if (!$user) {
            abort(404);
        }

        $cars = CarBooking::create([
            'carId' => $carId,
            'user_id' => $userId,
            'customer_name' => $request->customer_name,
            'customer_mobile' => $request->customer_mobile,
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
        ]);

        $maxBookingId = CarBooking::where('user_id', $user->id)
         ->max('bookingId');

        $newBookingId = ($maxBookingId !== null) ? ($maxBookingId + 1) : 1001;

        CarBooking::whereId($cars->id)->update([
            'bookingId' => $newBookingId,
        ]);

        $deleted = CarsBookingDateStatus::where('carId','=',$carId)->where('start_date','<=',$start_date)->
        where('end_date','>=',$end_date)->delete();
        $bookingId = $cars->id;

        return redirect()->route('partner.booking.view', $bookingId)->with('success', 'You successfully created your booking');
    }

    /////// this will give filters count  //////
    private function checkFiltersCount($start_date,$end_date,$car_type,$car_name){
        $count=0;

        if ($start_date != '' || $end_date != '') $count++;
        if ($car_type != '') $count++;
        if ($car_name != '') $count++;


        return $count;

    }

    ////// check car booking available or not  //////
    private function checkBookingStatus($carId, $firstDate, $lastDate,$actionType){

        // $bookingCarStatus = CarBooking::where('carId', '=', $carId)
        //     ->where(function ($query) use ($firstDate, $lastDate) {
        //         $query->whereBetween('start_date', [$firstDate, $lastDate])
        //             ->orWhereBetween('end_date', [$firstDate, $lastDate]);
        //     })
        //     ->get();

        if(strcmp($actionType,'overlapped_date')!=0){

            $bookingCarStatus = CarBooking::where('carId', '=', $carId)
            ->where(function ($query) use ($firstDate, $lastDate) {
                $query->whereBetween('start_date', [$firstDate, $lastDate])
                    ->orWhereBetween('end_date', [$firstDate, $lastDate]);
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
                $query->where('end_date', '>', $firstDate)
                ->where('end_date', '<=', $lastDate);
                })->where(function ($query) {
                $query->where('status', '=', 'delivered')
                ->orWhereNull('status');
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

    public function deleteLockedDates(Request $request)
    {
        $carId = $request->carId;
        $firstDate = $request->start_date;
        $lastDate = $request->last_date;
        if ($carId !== null && $firstDate !== null && $lastDate !== null)
        {
            $deleted = CarsBookingDateStatus::where('carId','=',$carId)->where('start_date','<=',$firstDate)->
            where('end_date','>=',$lastDate)->delete();
                if ($deleted) {
                    return response()->json(['success' => true, 'error' => false, 'msg' => 'Deletion succeeded']);
            }
        }
    }

    ////// next or previous month calendar  //////
    public function ajaxDatesFilter(Request $request)
    {
        $userId = auth()->user()->id;

        if($request->ajax()){
            if(strcmp($request->type,'main_filter')==0){
                $requestData = $request->all();

                $formArray = [
                    'start_date' => $requestData['start_date'] ?? null,
                    'end_date'   => $requestData['end_date'] ?? null,
                    'car_type'   => $requestData['car_type'] ?? [],
                    'car_name'   => $requestData['car_name'] ?? [],
                ];

            $cars = cars::select(
            'cars.id',
            'cars.name',
            'cars.registration_number',
            'cars.transmission',
            'cars.fuel_type',
            'cars.manufacturing_year',
            'cars.car_type',
            'cars.price',
            'cars.seats',
            'cars.status',
            'cars.user_id',
            'cars.created_at',
            'cars.updated_at'
          )->join('users', function ($join) {
            $join->on('users.id', '=', 'cars.user_id')->where('users.status', 'NOT LIKE', 'deleted');
          })->where('cars.status', 'NOT LIKE', 'deleted')->where(function ($query) use ($formArray, $userId) {
            if (!empty($formArray['car_name'])) {
                $query->whereIn('cars.name', $formArray['car_name']);
            }

            if (!empty($formArray['car_type']) && empty($formArray['car_name'])) {
                $query->whereIn('cars.car_type', $formArray['car_type']);
            }

            if (!empty($formArray['car_type']) && !empty($formArray['car_name'])) {
                $query->whereIn('cars.name', $formArray['car_name'])->whereIn('cars.car_type', $formArray['car_type']);
            }

            if (!empty($formArray['start_date'])) {
                if (empty($formArray['end_date'])) {
                    $query->whereNotExists(function ($subQuery) use ($formArray) {
                        $subQuery->select(DB::raw(1))
                            ->from('car_bookings')
                            ->whereRaw('cars.id = car_bookings.carId')
                            ->where(function ($query) use ($formArray) {
                                $query->where('car_bookings.start_date', '<=', $formArray['start_date'])
                                    ->where('car_bookings.end_date', '>=', $formArray['start_date']);
                            });
                    });
                } else {
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
            } else {
                $query->whereIn('cars.user_id', [$userId]);
            }
            })->orderBy('cars.created_at', 'DESC')->get();

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
            $carIds = $requestData['carIds'] ?? [];
            $cars = cars::where('status', '!=', 'deleted')->where('user_id', $userId)->orderByDesc('created_at')->get();
            $date_data = '';
            $main_loop = '';

            $carsCount= count($cars);
              foreach ($cars as $car){
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
               if (strcmp(Helper::getUserMeta($car->user_id, 'CompanyImageId'), '') != 0) {
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
                            <div class="h-[67px] md:h-[45px] py-[10px] flex justify-center items-center overflow-hidden md:py-[5px] sm:py-1">
                                <img src="'.$modifiedImgUrl.'" alt="car image" class="object-contain h-full">
                            </div>
                        </div>
                    </div>
                    <div class="car_status_row_sec pl-[15px] right_section md:flex md:flex-row  flex flex-col md:items-start">
                        <div class="car_details_content md:flex-col md:items-start flex justify-start items-center">

                            <div class="car_name_sec">
                                <a href="'.route('partner.car.view',$car->id).'" class="text-base md:text-center font-medium capitalize showcar_title_b hover:underline transition-all duration-300 ease-out   text-purple md:font-bold md:text-sm">
                                    '.$car->name.'
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

                      $titleAttribute = date("d M Y", strtotime($date['full_date']));
                      $main_loop .='<li class="inline-block adj_margin clickable relative"
                        data-date-full_date="'.$date['full_date'].'"
                        data-date-month="'.$date['month'].'" data-date-day="'.$date['day'].'"
                        data-car-id="'.$car->id.'">
                        <a href="javascript:void(0);" ' . (Helper::isBooked($car->id, $date['full_date']) ?
                        'data-fancybox data-src="#booking_Details_showcase_popup"'.
                        'data-bookingId="' . Helper::getBookingDataByCarId($car->id, $date['full_date'])->bookingId . '"' .
                        'data-overlap-pickupdate="' . Helper::getAdditionalBookingsByCarId($car->id,$date['full_date'])->pickup_date . '"' .
                        'data-overlap-dropoffdate="' . Helper::getAdditionalBookingsByCarId($car->id,$date['full_date'])->dropoff_date . '"' .
                        'data-overlap-pickuptime="' . Helper::getAdditionalBookingsByCarId($car->id,$date['full_date'])->pickup_time . '"' .
                        'data-overlap-dropofftime="' . Helper::getAdditionalBookingsByCarId($car->id,$date['full_date'])->dropoff_time . '"' .
                        'data-booked-startDate="' . Helper::getBookingDataByCarId($car->id, $date['full_date'])->start_date . '"' .
                        'data-booked-endDate="' . Helper::getBookingDataByCarId($car->id, $date['full_date'])->end_date . '"' .
                        'data-booked-customer-country-code="' . Helper::getBookingDataByCarId($car->id, $date['full_date'])->customer_mobile_country_code . '"' .
                        'data-overlap-customer-country-code="' . Helper::getAdditionalBookingsByCarId($car->id, $date['full_date'])->customer_mobile_country_code . '"' .
                        'data-booked-customer-name="' . Helper::getBookingDataByCarId($car->id, $date['full_date'])->customer_name . '"' .
                        'data-overlap-customer-name="' . Helper::getAdditionalBookingsByCarId($car->id, $date['full_date'])->customer_name . '"' .
                        'data-booked-customer-number="' . Helper::getBookingDataByCarId($car->id, $date['full_date'])->customer_mobile . '"' .
                        'data-overlap-customer-number="' . Helper::getAdditionalBookingsByCarId($car->id, $date['full_date'])->customer_mobile . '"' .
                        'data-booked-pickup-location="' . Helper::getBookingDataByCarId($car->id, $date['full_date'])->pickup_location . '"' .
                        'data-overlap-pickup-location="' . Helper::getAdditionalBookingsByCarId($car->id, $date['full_date'])->pickup_location . '"' .
                        'data-booked-dropoff-location="' . Helper::getBookingDataByCarId($car->id, $date['full_date'])->dropoff_location . '"' .
                        'data-overlap-dropoff-location="' . Helper::getAdditionalBookingsByCarId($car->id, $date['full_date'])->dropoff_location . '"' .
                        'data-pickupTime="' . Helper::getBookingDataByCarId($car->id, $date['full_date'])->pickup_time . '"' .
                        'data-dropoffTime="' . Helper::getBookingDataByCarId($car->id, $date['full_date'])->dropoff_time . '"' : '') . '
                        ' . (Helper::islocked($car->id, $date['full_date']) ?
                        'data-locked-startdate="' . Helper::getLockedDataByCarId($car->id, $date['full_date'])->start_date . '"' .
                        'data-locked-enddate="' . Helper::getLockedDataByCarId($car->id, $date['full_date'])->end_date . '"' : '')
                        . '
                        data-fancybox data-src="#open_popup" class="custom_pick_drop_popup inline-flex flex-col items-center justify-center links
                        '.(Helper::isBooked($car->id, $date['full_date']) ? 'booked adj_status_width' :
                         (Helper::isLocked($car->id, $date['full_date']) ? 'locked adj_status_width' : 'activeDate')).'">

                        <div class="'.(Helper::hasAdditionalBookingsByCarId($car->id, $date['full_date']) ? 'overlap_booking' :'').(Helper::overlapLockedOrNot($car->id, $date['full_date']) ? 'overlap_locked' :'').'">
                        <div class="relative range_active spaces_around_status" title="' . $titleAttribute . '">
                        <div class="mx-auto leading-[0] car_status_container_inner flex items-center cell_dates_day justify-center w-[36px] h-[36px]  md:w-[30px] md:h-[30px] rounded-full
                        '.(Helper::isBooked($car->id, $date['full_date']) ? '' :
                        (Helper::isLocked($car->id, $date['full_date']) ? '' : 'bg-[#E4FFDD]   border border-[#25BE00]')).'">' .(Helper::isBooked($car->id, $date['full_date'])? '': (Helper::isLocked($car->id, $date['full_date'])?'' : '<span class="inline-block date_day ">'.$date['day'].'</span>' )).'
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

    ////// set session for 10 minutes  //////
    public function setTimeCreate(Request $request)
    {
      if ($request->ajax()) {
        if (strcmp($request->action, 'proceed') == 0) {
            $carHoldAvail = CarsBookingDateStatus::where('carId', '=', $carId = $request->carId)
                ->where('start_date', '<=', $firstDate = $request->start_date)
                ->where('end_date', '>=',  $lastDate = $request->last_date)
                ->first();
            if ($carHoldAvail) {
                $carHoldAvail = CarsBookingDateStatus::where('carId', '=', $carId = $request->carId)
                ->where('start_date', '<=', $firstDate = $request->start_date)
                ->where('end_date', '>=', $lastDate = $request->last_date)
                ->delete();
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
            return redirect()->route('partner.booking.list');
        } else {
            $userId = auth()->user()->id;

            $car_booking = CarBooking::where('id', $id)->first();

            if (!$car_booking) {
                return redirect()->route('partner.booking.list');
            }

            $carId = $car_booking->carId;

            $car = cars::find($carId);

            if (!$car) {
                return redirect()->route('partner.booking.list');
            }

            $partner = User::find($car_booking->user_id);

            if (!$partner) {
                return redirect()->route('partner.booking.list');
            }

            if ($userId != $car_booking->user_id) {
                return redirect()->route('partner.booking.list');
            }

            $booking_payments = booking_payments::where('bookingId', $id)->get();

            return view('partner.bookings.view', compact('car_booking', 'car', 'carId', 'booking_payments', 'partner'));
        }
    }


    public function edit($id) {
        if (!$id) {
            return redirect()->route('partner.booking.list');
        } else {
            $userId = auth()->user()->id;

            $car_booking = CarBooking::where('id', $id)->first();

            if (!$car_booking) {
                return redirect()->route('partner.booking.list');
            }

            $carId = $car_booking->carId;

            $car = cars::find($carId);

            if (!$car) {
                return redirect()->route('partner.booking.list');
            }

            $partner = User::find($car_booking->user_id);

            if (!$partner) {
                return redirect()->route('partner.booking.list');
            }

            if ($userId != $car_booking->user_id) {
                return redirect()->route('partner.booking.list');
            }

            return view('partner.bookings.edit', compact('car_booking','car','carId'));
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
        $user_id = auth()->user()->id;
        $currentDate = Carbon::now();

        $cars = cars::where('status','NOT LIKE','deleted')->orderBy('id', 'desc')->get();

        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);

        $booked_cars = CarBooking::where('user_id', $user_id)
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

    public function statusChange(Request $request)
    {
        $user_id = auth()->user()->id;

        date_default_timezone_set('Asia/Kolkata');
        $currentTimeDate = now();
        // echo "<pre>";
        // print_r($request->toArray());
        // echo $request->collected_time;
        // die;


        $checkBookingStatus = CarBooking::whereId($request->bookingId)->first();

        $successMsg = false;
        $errorMsg = false;

        if ($checkBookingStatus)
        {
            if (strcmp($checkBookingStatus->status, 'collected') === 0) {
                $errorMsg = "Already collected, please refresh the page";
            } elseif (strcmp($checkBookingStatus->status, 'delivered') === 0) {
                if (strcmp($request->bookingStatus, 'delivered') === 0) {
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

        if(strcmp($request->checkStatus, 'list') === 0)
        {
            $currentDate = Carbon::now();

            $cars = cars::where('status','NOT LIKE','deleted')->orderBy('id', 'desc')->get();

            $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
            $currentDateTime = Carbon::parse($oldcurrentDateTime);

            $booked_cars = CarBooking::where('user_id', $user_id)
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
        elseif((strcmp($request->checkStatus, 'desktop_search') === 0) || (strcmp($request->checkStatus, 'mobile_search') === 0) )
        {
            $customerName = isset($request->customerName) ? $request->customerName : null;
            // $countryCode = isset($request->countryCode) ? $request->countryCode : null;
            $customerMobile = isset($request->customerMobile) ? $request->customerMobile : null;

            // echo "<pre>";
            // print_r($request->toArray());
            // die;
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

            $booked_cars = CarBooking::where('user_id', $user_id)
            ->where(function ($query) use ($customerName, $customerMobile) {
                if ($customerName) {
                    $query->where('customer_name', '=', $customerName);
                }

                if ($customerMobile) {
                    $query->where('customer_mobile', '=', $customerMobile);
                }
                // dd("3");
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
            END ASC")
            ->take($bookingLength)
            ->get();

            // echo "<pre>";
            // echo "offset".$offset;
            // print_r($booked_cars->toArray());
            // die;
        }


        if(count($booked_cars))
        {
            $result =  $this->collectionOfBookedCars($booked_cars);

            $desktopBookingHtml = $result['desktopBookingHtml'];
            $mobileBookingHtml = $result['mobileBookingHtml'];

            return response()->json(['success' => true, 'error' => false,'successMsg'=>$successMsg , 'errorMsg'=>$errorMsg,  'booked_cars'=>$booked_cars, 'desktopBookingHtml'=>$desktopBookingHtml,'mobileBookingHtml'=>$mobileBookingHtml ]);
        }
        else
        {
            return response()->json(['success' => false, 'error' => true, 'msg'=>false ,'booked_cars'=>$booked_cars,'desktopBookingHtml'=>'','mobileBookingHtml'=>'' ]);
        }
    }


    public function checkLockedAndBooked(Request $request)
    {
        // dd('other');

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

        // dd($firstDate,$lastDate);
        if(strcmp($actionType,'overlapped_date')!=0){
            // dd('under');
            $bookingCarStatus = CarBooking::where('carId', '=', $carId)
            ->where(function ($query) use ($pickupDate, $dropoffDate) {
                $query->whereBetween('pickup_date', [$pickupDate, $dropoffDate])
                    ->orWhereBetween('dropoff_date', [$pickupDate, $dropoffDate]);
            })->where(function ($query) {
                $query->where('status', '=', 'delivered')
                ->orWhereNull('status');
                    })
        ->get();

        // echo'<pre>';print_r($bookingCarStatus);die;
        // dd($bookingCarStatus);

        }
        else
        {
        // dd($firstDate,$lastDate);

            // dd('under the overlap');
            // $bookingCarStatus = CarBooking::where('carId', '=', $carId)
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

        // dd('overlapped:',count($bookingCarStatus));
        // echo'<pre>';print_r($bookingCarStatus->toArray());die;


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
                return response()->json(['success' => false, 'error' => true,'msg'=>'fffffff']);
            }

    }

    public function getAllBookedAndLockedDates(Request $request)
    {
        $carId = $request->carId;

        $bookingCarDates = CarBooking::select('start_date', 'end_date')->where('carId', '=', $carId)->where(function ($query) {
            $query->where('status', '=', 'delivered')
            ->orWhereNull('status');
                })->get();

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
            return response()->json([ 'disableDates'=>$disableDates,'success' => true, 'error' => false ,'msg'=>'']);
         }
         else{
            return response()->json(['disableDates'=>$disableDates,'success' => false, 'error' => true ,'msg'=>'']);

         }
    }


    public function checkLockedAndBookedDates(Request $request)
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


        // dd('pickup_date:',$pickup_date,'dropoff_date:',$dropoff_date,'start_time',$start_time,'end_time',$end_time);

            $isDateBooked= CarBooking::where('carId','=',$carId)->where('pickup_date','<=',$pickupDate)->orWhere('dropoff_date','>=',$pickupDate)->where(function ($query) {
                $query->where('status', '=', 'delivered')
                ->orWhereNull('status');
                    })->first();



          if($isDateBooked){

            return response()->json(['success' => false, 'error' => true,'msg'=>'','dropoffDate'=>$dropoffDate,'pickupDate'=>$pickupDate]);

          }
          else
          {
            // dd('from else:');

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

    //    $check = CarBooking::where('carId', '=', $carId)
    //     ->where('pickup_date', '>',$dropoffDate)->get();
    //     dd($check);


    }

    // for retriving the all dates with time
    public function getAllBookedAndLockedDatesWithTime(Request $request){
        $carId = $request->carId;
        $bookingId=$request->bookingId;
        $disabledDates=[];

        $bookingCarDates = CarBooking::select('pickup_date', 'dropoff_date')->where('carId', '=', $carId)->where(function ($query) {
            $query->where('status', '=', 'delivered')
            ->orWhereNull('status');
                })->get();

        // dd('hello:',$bookingCarDates);
        $editbookingdates= CarBooking::select('pickup_date', 'dropoff_date')->where('carId', '=', $carId)->whereId($bookingId)->where(function ($query) {
            $query->where('status', '=', 'delivered')
            ->orWhereNull('status');
                })->get();



        $carHoldStatus = CarsBookingDateStatus::select('start_date', 'end_date')->where('carId', '=', $carId)->get();

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


        foreach ($carHoldStatus as $status) {
            $startDate = Carbon::parse($status['start_date']);
            $endDate = Carbon::parse($status['end_date']);
            $disabledDates = array_merge($disabledDates, $this->addDatesToRange($startDate, $endDate));
        }


        if($disabledDates){
        return response()->json([ 'disableDates'=>$disabledDates,'success' => true, 'error' => false ]);
        }
        else{
        return response()->json(['disableDates'=>$disabledDates,'success' => false, 'error' => true ]);

        }
    }


    protected function addDatesToRange($startDate, $endDate)
    {
        // dd('startDate:',$startDate,'$endDate:',$endDate);
        $dateRange = [];

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $dateRange[] = $date->toDateTimeString();
        }

        return $dateRange;
    }

    protected function collectionOfBookedCars($booked_cars)
    {
        // dd('booked_cars:',$booked_cars);
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

            if ($pickupMoment->lt(Carbon::parse('9:00 AM'))||$dropoffMoment->gt(Carbon::parse('9:00 AM'))) {
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
                } elseif ( ($currentTimeDate >= $dropoffTimeBeforeThirty && strcmp($booked_car->status, 'delivered') == 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
                    $desktopBookingHtml .= 'bg-[#fffbe5]';
                } else {
                    $desktopBookingHtml .= 'bg-white';
                }
                $desktopBookingHtml .= '
                " data-id="' . $booked_car->id . '">';

                $desktopBookingHtml .='
                <div class="list_cr_out flex justify-center items-center ">
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
                                                        <a href="' . route('partner.booking.view', $booked_car->id) . '" class="inline-block text-[#2B2B2B] font-medium pb-[1px] leading-4 text-[13px] border-b-2 border-siteYellow">View Booking
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
                                                        <div class="inline-block py-1 border-t border-b border-1 border-[#898376] text-[12px] flex justify-center items-center w-[42px]">
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
                                        </div>
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
                                        </div>
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
                                                        <a href="javascript:void(0)" class=" flex justify-center w-full items-center py-1 px-3
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
                                            <a href="javascript:void(0)" class="flex justify-center w-full py-1 px-3
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
                    if(($currentTimeDate >= $pickupTimeBeforeThirty && strcmp($booked_car->status, 'delivered') !== 0) && (strcmp($booked_car->status, 'collected') !== 0)  && (strcmp($booked_car->status, 'canceled') !== 0))
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
                            <a href="' . route('partner.booking.view', $booked_car->id) . '" class="inline-flex items-center text-[#2B2B2B] font-medium leading-4 text-[14px]">View Booking <img src="' . asset('images/arrow-booking.svg') . '" alt="arro" class="ml-[9px] w-[24px]"></a>
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
                                        <a href="javascript:void(0)" class="flex justify-center py-1 px-3
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
                                        <a href="javascript:void(0)" class="flex py-1 px-3
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
        // echo "<pre>";
        // print_r($request->toArray());
        // echo $request->collected_time;
        // die;

        $checkBookingStatus = CarBooking::whereId($request->bookingId)->first();

        $successMsg = false;
        $errorMsg = false;
        $goback = false;

        if ($checkBookingStatus)
        {
            if (strcmp($checkBookingStatus->status, 'collected') === 0) {

                $errorMsg = "Already collected, please refresh the page";

            } elseif (strcmp($checkBookingStatus->status, 'delivered') === 0) {

                if (strcmp($request->bookingStatus, 'delivered') === 0) {
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
        }
        else {
            $errorMsg = "Booking not found";
        }


            return response()->json(['success' => true, 'error' => false, 'msg'=>true,'successMsg'=>$successMsg , 'errorMsg'=>$errorMsg, 'goback' => $goback ]);
    }

/////////////////////////////////////// ALL BOOKINGS /////////////////////////////////////////////////

    ////// allBookings list start here  //////
    public function allBookings()
    {
        date_default_timezone_set('Asia/Kolkata');

        $user_id = auth()->user()->id;

        // $countBookedCars = CarBooking::where('user_id', $user_id)->count();

        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);


        $countBookedCars = CarBooking::where('user_id', $user_id)
        ->orderByRaw("CASE
            WHEN status = 'delivered' THEN dropoff_date
            WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
            ELSE pickup_date
        END ASC")
        ->count();

        $booked_cars = CarBooking::where('user_id', $user_id)
        ->orderByRaw("CASE
        WHEN status = 'delivered' THEN dropoff_date
        WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
        ELSE pickup_date
        END ASC")
        ->take(5)
        ->get();

        $allBookedCars = CarBooking::where('user_id', $user_id)
        ->orderByRaw("CASE
        WHEN status = 'delivered' THEN dropoff_date
        WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
        ELSE pickup_date
        END ASC")
        ->get();

        return view('partner.bookings.all-bookings', compact('booked_cars','allBookedCars','countBookedCars'));
    }

    ////// loadmore list start here  //////
    public function allLoadMoreBookings(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');

        $user_id = auth()->user()->id;

        $test = $request->checkLoadMore;

        if(strcmp($request->checkLoadMore, 'list') === 0)
        {
            $currentDate = Carbon::now();
            $offset = $request->input('offset', 0);


            $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
            $currentDateTime = Carbon::parse($oldcurrentDateTime);

            $booked_cars = CarBooking::where('user_id', $user_id)
            ->orderByRaw("CASE
            WHEN status = 'delivered' THEN dropoff_date
            WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
            ELSE pickup_date
            END ASC")
            ->skip($offset)
            ->take(5)
            ->get();

        }
        elseif((strcmp($request->checkLoadMore, 'desktop_search') === 0) || (strcmp($request->checkLoadMore, 'mobile_search') === 0))
        {

            // echo "<pre>";
            // print_r($request->toArray());
            // die;

            $customerName = isset($request->customerName) ? $request->customerName : null;
            // $countryCode = isset($request->countryCode) ? $request->countryCode : null;
            $customerMobile = isset($request->customerMobile) ? $request->customerMobile : null;

            // echo "<pre>";
            // echo "customerName".$customerName;
            // echo "customerMobile".$customerMobile;
            // // print_r($request->toArray());
            // die;

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

            // echo "<pre>";
            // echo "customerName".$customerName;
            // echo "customerMobile".$customerMobile;
            // // print_r($request->toArray());
            // die;

            // dd('$booking_id:',$formArray['booking_id'],'start_date:',$formArray['start_date'],'end_date:',$formArray['end_date']);

            $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
            $currentDateTime = Carbon::parse($oldcurrentDateTime);

            $booked_cars = CarBooking::where('user_id', $user_id)
            ->where(function ($query) use ($customerName, $customerMobile) {
                if ($customerName) {
                    $query->where('customer_name', '=', $customerName);
                }

                if ($customerMobile) {
                    $query->where('customer_mobile', '=', $customerMobile);
                }
                // dd("3");
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
                    // dd('only booking id',$formArray['booking_id']);
                    $query->whereIn('id', $formArray['booking_id']);
                }
            })
            // ->where(function ($query) use ($currentDateTime) {
            // $query->where('pickup_date', '>=', $currentDateTime)
            //     ->orWhere('dropoff_date', '>=', $currentDateTime)
            //     ->orWhere(function ($query) {
            //         $query->where('status', 'NOT LIKE', 'collected')
            //             ->orWhereNull('status');
            //     });

            // })
            ->orderByRaw("CASE
            WHEN status = 'delivered' THEN dropoff_date
            WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
            ELSE pickup_date
            END ASC")
            ->skip($offset)
            ->take(5)
            ->get();

            // echo "<pre>";
            // echo "offset".$offset;
            // print_r($booked_cars->toArray());
            // die;
        }
        elseif((strcmp($request->checkLoadMore, 'autocomplete_search') === 0) )
        {
            // echo "<pre>";
            // print_r($request->toArray());
            // die;

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

                $booked_cars = CarBooking::where('user_id', $user_id)
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
                // ->where(function ($query) use ($currentDateTime) {
                // $query->where('pickup_date', '>=', $currentDateTime)
                //     ->orWhere('dropoff_date', '>=', $currentDateTime)
                //     ->orWhere(function ($query) {
                //         $query->where('status', 'NOT LIKE', 'collected')
                //             ->orWhereNull('status');
                //     });

                // })
                ->orderByRaw("CASE
                WHEN status = 'delivered' THEN dropoff_date
                WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
                ELSE pickup_date
                END ASC")
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

    ///// booking list filter //////
    public function allListAjaxFilter(Request $request)
    {

        // echo "<pre>";
        // print_r($request->toArray());
        // print_r($request->customerName);
        // die;

        $customerName = isset($request->customerName) ? $request->customerName : null;
        // $countryCode = isset($request->countryCode) ? $request->countryCode : null;
        $customerMobile = isset($request->customerMobile) ? $request->customerMobile : null;

        // echo "customerName: " . $customerName;
        // echo "customerMobile: " . $customerMobile;
        // die;

       $user_id = auth()->user()->id;

       $currentDate = Carbon::now();
       $requestData = $request->all();

        if (isset($requestData['form_data']))
        {
            parse_str($requestData['form_data'], $formArray);
            $start_date = $formArray['start_date'] ?? null;
            $end_date = $formArray['end_date'] ?? null;
            // $booking_ids = $formArray['booking_id'] ?? [];
            $formArray['booking_id']=[];
        }

        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);

        $booked_cars = CarBooking::where('user_id', $user_id)
        ->where(function ($query) use ($customerName, $customerMobile) {
            if ($customerName) {
                $query->where('customer_name', '=', $customerName);
            }

            if ($customerMobile) {
                $query->where('customer_mobile', '=', $customerMobile);
            }
            // dd("3");
        })
        // ->where('end_date', '>', $currentDate)
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
        ->take(5)
        ->get();

        if(count($booked_cars)){

            $result =  $this->collectionOfBookedCars($booked_cars);

            $desktopBookingHtml = $result['desktopBookingHtml'];
            $mobileBookingHtml = $result['mobileBookingHtml'];

            return response()->json(['success' => true, 'error' => false, 'booked_cars'=>$booked_cars, 'desktopBookingHtml'=>$desktopBookingHtml,'mobileBookingHtml'=>$mobileBookingHtml ]);
        }
        else{
        return response()->json(['success' => false, 'error' => true, 'booked_cars'=> $booked_cars, 'desktopBookingHtml'=>'','mobileBookingHtml'=>'' ]);
        }
    }

    ////// allClearAll start here  //////
    public function allClearAll()
    {
        $user_id = auth()->user()->id;
        $currentDate = Carbon::now();

        $cars = cars::where('status','NOT LIKE','deleted')->orderBy('id', 'desc')->get();

        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);

        $booked_cars = CarBooking::where('user_id', $user_id)
        // ->where(function ($query) use ($currentDateTime) {
        //     $query->where('pickup_date', '>=', $currentDateTime)
        //         ->orWhere('dropoff_date', '>=', $currentDateTime)
        //         ->orWhere(function ($query) {
        //             $query->where('status', 'NOT LIKE', 'collected')
        //                 ->orWhereNull('status');
        //         });

        // })
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
        $user_id = auth()->user()->id;

        date_default_timezone_set('Asia/Kolkata');
        $currentTimeDate = now();
        // echo "<pre>";
        // print_r($request->toArray());
        // echo $request->collected_time;
        // die;


        $checkBookingStatus = CarBooking::whereId($request->bookingId)->first();

        $successMsg = false;
        $errorMsg = false;

        if ($checkBookingStatus)
        {
            if (strcmp($checkBookingStatus->status, 'collected') === 0) {
                $errorMsg = "Already collected, please refresh the page";
            } elseif (strcmp($checkBookingStatus->status, 'delivered') === 0) {
                if (strcmp($request->bookingStatus, 'delivered') === 0) {
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

        if(strcmp($request->checkStatus, 'list') === 0)
        {
            $currentDate = Carbon::now();

            $cars = cars::where('status','NOT LIKE','deleted')->orderBy('id', 'desc')->get();

            $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
            $currentDateTime = Carbon::parse($oldcurrentDateTime);

            $booked_cars = CarBooking::where('user_id', $user_id)
            // ->where(function ($query) use ($currentDateTime) {
            //     $query->where('pickup_date', '>=', $currentDateTime)
            //         ->orWhere('dropoff_date', '>=', $currentDateTime)
            //         ->orWhere(function ($query) {
            //             $query->where('status', 'NOT LIKE', 'collected')
            //                 ->orWhereNull('status');
            //         });

            //     })
            ->orderByRaw("CASE
            WHEN status = 'delivered' THEN dropoff_date
            WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
            ELSE pickup_date
            END ASC")
            ->take($bookingLength)
            ->get();

        }
        elseif((strcmp($request->checkStatus, 'desktop_search') === 0) || (strcmp($request->checkStatus, 'mobile_search') === 0) )
        {
            $customerName = isset($request->customerName) ? $request->customerName : null;
            // $countryCode = isset($request->countryCode) ? $request->countryCode : null;
            $customerMobile = isset($request->customerMobile) ? $request->customerMobile : null;

            // echo "<pre>";
            // print_r($request->toArray());
            // die;
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

            $booked_cars = CarBooking::where('user_id', $user_id)
            ->where(function ($query) use ($customerName, $customerMobile) {
                if ($customerName) {
                    $query->where('customer_name', '=', $customerName);
                }

                if ($customerMobile) {
                    $query->where('customer_mobile', '=', $customerMobile);
                }
                // dd("3");
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
            // ->where(function ($query) use ($currentDateTime) {
            //     $query->where('pickup_date', '>=', $currentDateTime)
            //         ->orWhere('dropoff_date', '>=', $currentDateTime)
            //         ->orWhere(function ($query) {
            //             $query->where('status', 'NOT LIKE', 'collected')
            //                 ->orWhereNull('status');
            //         });
            // })
            ->orderByRaw("CASE
            WHEN status = 'delivered' THEN dropoff_date
            WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
            ELSE pickup_date
            END ASC")
            ->take($bookingLength)
            ->get();

            // echo "<pre>";
            // echo "offset".$offset;
            // print_r($booked_cars->toArray());
            // die;
        }



        if(count($booked_cars))
        {
            $result =  $this->collectionOfBookedCars($booked_cars);

            $desktopBookingHtml = $result['desktopBookingHtml'];
            $mobileBookingHtml = $result['mobileBookingHtml'];

            return response()->json(['success' => true, 'error' => false,'successMsg'=>$successMsg , 'errorMsg'=>$errorMsg,  'booked_cars'=>$booked_cars, 'desktopBookingHtml'=>$desktopBookingHtml,'mobileBookingHtml'=>$mobileBookingHtml ]);
        }
        else
        {
            return response()->json(['success' => false, 'error' => true, 'msg'=>false ,'booked_cars'=>$booked_cars,'desktopBookingHtml'=>'','mobileBookingHtml'=>'' ]);
        }
    }

    public function editPost(Request $request ,$id){
        // echo "<pre>";
        // print_r($request->all());
        // die;
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

            $pickup_location = ($request->pickup_location == 'other') ? $request->other_pickup_location : $request->pickup_location;
            $dropoff_location = ($request->dropoff_location == 'other') ? $request->other_dropoff_location : $request->dropoff_location;
            $userId = auth()->user()->id;
            CarBooking::whereId($id)->update([
            'carId' => $request->carId,
            'user_id' => $userId,
            'customer_name' => $request->customer_name,
            'customer_mobile' => $request->customer_mobile,
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
        ]);
        $bookingId = $id;
        return redirect()->route('partner.booking.view', $bookingId)->with('success', 'You successfully created your booking');
    }

    public function updateBookingDates(Request $request){

        // echo "hello";
        // die;
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

    public function updateBookingCalculation(Request $request){


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

            ]);

            return response()->json(['success' => true, 'error' => false, 'msg'=>'calculations are updated' ]);

        }else{

            return response()->json(['success' => false, 'error' => true, 'msg'=>'carId is invalid' ]);

        }


    }


    // cancel bookings
    public function bookingCancel(Request $request){
        // dd('booking cancel',$request->all());

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



}
