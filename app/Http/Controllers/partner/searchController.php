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
use App\Models\Agents;
use App\Models\UserMetas;
use App\Models\carsMeta;
use App\Models\carImages;
use App\Models\CarBooking;
use App\Models\CarsBookingDateStatus;
use App\Models\master_images;
use Carbon\Carbon;
use Intervention\Image\Facades\Image;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
class searchController extends Controller{

    public function __construct()
    {
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

    public function autocomplete(Request $request)
    {

        $userId = auth()->user()->id;
        $query = $request->input('search');

        $carsByName = cars::select("name as value", "registration_number", "id", \DB::raw("'CARS' as header, '1' as priority"))
            ->where('name', 'LIKE', $query . '%')
            ->where('status', '!=', 'deleted')
            ->where('user_id', '=', $userId)
            ->get();

        $carsPartialByName = cars::select("name as value", "registration_number", "id",  \DB::raw("'CARS' as header, '1' as priority"))
            ->where('name', 'LIKE', '%' . $query . '%')
            ->where('status', '!=', 'deleted')
            ->where('user_id', '=', $userId)
            ->get();

        $results = $carsByName
        ->merge($carsPartialByName)
        ->toArray();

        $customComparison = function ($a, $b) use ($query) {
        $similarityA = similar_text(strtolower($query), strtolower($a['value']));
        $similarityB = similar_text(strtolower($query), strtolower($b['value']));
        return $similarityB <=> $similarityA;
        };

        usort($results, $customComparison);
        return response()->json($results);

    }

    public function search(Request $request)
    {
        // echo "<pre>";
        // print_r($request->toArray());
        // die;

        $id = $request->input('id');
        $name = $request->input('name');
        $header = $request->input('header');

        $query = $request->input('name');


        if($header=='CARS')
        {

            if(!$id)
            {
                return redirect()->route('partner.car.list');
            }
            else
            {
                $car =  cars::whereId($id)->where('status','NOT LIKE','deleted')->first();
                return view('partner.cars.view',compact('car'));
            }
        }
        else
        {
            // echo "something went wrong";
            return view('partner.cars.list');
        }

    }

    public function autocompleteCustomerAndMob(Request $request)
    {
        $user_id = auth()->user()->id;

        $query = $request->input('search');

        date_default_timezone_set('Asia/Kolkata');
        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);
        $currentDate = Carbon::now();

        $custAndMobByName = CarBooking::select("customer_name", "customer_mobile","registration_number","status","customer_mobile_country_code","id", \DB::raw("'1' as priority"))
        ->where('user_id', $user_id)
        ->where(function ($q) use ($query, $currentDate) {
                $q->where('customer_name', 'LIKE', $query . '%')
                    ->orWhere('customer_mobile', 'LIKE', $query . '%')
                    ->orWhere('registration_number', 'LIKE', $query . '%')
                    ;
        })
        ->where(function ($query) {
            $query->whereNull('status')->orWhere('status','LIKE','delivered');
        })
        // ->where('customer_name', 'LIKE', $query . '%')
        // ->orWhere('customer_mobile', 'LIKE', $query . '%')
        // ->orWhere('registration_number', 'LIKE', $query . '%')
        ->orderByRaw("CASE
            WHEN status = 'delivered' THEN dropoff_date
            WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
            ELSE pickup_date
            END ASC")
        // ->where('status', 'NOT LIKE', 'collected')
        ->get();

        // Search for customer names and mobile numbers that contain the query anywhere
        $custAndMobPartiallyByName = CarBooking::select("customer_name", "customer_mobile","registration_number","status","customer_mobile_country_code", "id", \DB::raw("'2' as priority"))
        ->where('user_id', $user_id)
        ->where(function ($q) use ($query, $currentDate) {
            $q->where('customer_name', 'LIKE', '%' . $query . '%')
                ->orWhere('customer_mobile', 'LIKE', '%' . $query . '%')
                ->orWhere('registration_number', 'LIKE','%'. $query . '%') ;
        })
        ->where(function ($query) {
            $query->whereNull('status')->orWhere('status','LIKE','delivered');
        })
        // ->where('customer_name', 'LIKE', '%' . $query . '%')
        // ->orWhere('customer_mobile', 'LIKE', '%' . $query . '%')
        // ->orWhere('registration_number', 'LIKE', $query . '%')
        ->orderByRaw("CASE
        WHEN status = 'delivered' THEN dropoff_date
        WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
        ELSE pickup_date
        END ASC")
        // ->where('status', 'NOT LIKE', 'collected')
        ->get();

        // print_r($custAndMobByName->toArray());
        // die;
        // print_r($custAndMobByName->toArray());

        // Custom comparison function for sorting results based on similarity to the query
        $customComparison = function ($a, $b) use ($query) {
            // Calculate similarity between the query and the values
            $similarityA = similar_text(strtolower($query), strtolower($a['customer_name'] . ' ' . $a['customer_mobile']));
            $similarityB = similar_text(strtolower($query), strtolower($b['customer_name'] . ' ' . $b['customer_mobile']));

            // Sort in descending order based on similarity
            return $similarityB <=> $similarityA;
        };

        // Combine and sort the results using the custom comparison function
        $results = array_merge($custAndMobByName->toArray(), $custAndMobPartiallyByName->toArray());
        usort($results, $customComparison);


        // Return the sorted results as JSON
        return response()->json($results);
    }

    public function searchCustomerAndMob(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);

        $customerName = $request->input('customerName');
        $customerMobile = $request->input('customerMobile');
        $countryCode = $request->input('countryCode');

        $requestData = $request->all();
        $formArray = [];

        $user_id = auth()->user()->id;

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
            ->where(function ($query) use ($customerName, $customerMobile, $countryCode) {
                if ($customerName) {
                    $query->where('customer_name', '=', $customerName);
                }

                if ($customerMobile) {
                    $query->where('customer_mobile', '=', $customerMobile);
                }

                if ($countryCode) {
                    $query->where('customer_mobile_country_code', '=', $countryCode);
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
            END ASC")
            ->take(5)
            ->get();


        if (count($booked_cars))
        {
            $result =  $this->collectionOfBookedCars($booked_cars);

            $desktopBookingHtml = $result['desktopBookingHtml'];
            $mobileBookingHtml = $result['mobileBookingHtml'];

            return response()->json(['success' => true, 'error' => false,'booked_cars'=> $booked_cars, 'desktopBookingHtml' => $desktopBookingHtml,'desktopBookingHtml' => $desktopBookingHtml, 'mobileBookingHtml' => $mobileBookingHtml]);
        }
        else {
            return response()->json(['success' => false, 'error' => true,'booked_cars'=>'', 'desktopBookingHtml' => '', 'mobileBookingHtml' => '']);
        }
    }

    public function searchStatusChange(Request $request)
    {
        $user_id = auth()->user()->id;

        date_default_timezone_set('Asia/Kolkata');
        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);

        $currentTimeDate = now();

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
        }
        else
        {
            $errorMsg = "Booking not found";
        }


        $bookingLength = $request->bookingLength;

        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);

        $booked_cars = CarBooking::whereIn('id', $request->bookingIds)
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


        if(count($booked_cars))
        {
            $result =  $this->collectionOfBookedCars($booked_cars);

            $desktopBookingHtml = $result['desktopBookingHtml'];
            $mobileBookingHtml = $result['mobileBookingHtml'];

            return response()->json(['success' => true, 'error' => false,'successMsg'=>$successMsg , 'errorMsg'=>$errorMsg,  'booked_cars'=>$booked_cars, 'desktopBookingHtml'=>$desktopBookingHtml,'mobileBookingHtml'=>$mobileBookingHtml ]);
        }
        else
        {
            return response()->json(['success' => false, 'error' => true, 'msg'=>false , 'booked_cars'=>$booked_cars,'desktopBookingHtml'=>'','mobileBookingHtml'=>'' ]);
        }
    }

    protected function collectionOfBookedCars($booked_cars)
    {
        $desktopBookingHtml="";
        $mobileBookingHtml="";

        foreach($booked_cars as $booked_car)
        {
            $car_name = '';
            $registration_number =  '';

            if(strcmp($booked_car->carId,'0')!==0){
                $car_name = Helper::getCarNameByCarId($booked_car->carId);
            }else{
                $car_name = $booked_car->car_name;
            }

            if(strcmp($booked_car->carId,'0')!==0){
                $registration_number = Helper::getCarRagisterationNoByCarId($booked_car->carId);
            }
            else{
                $registration_number = $booked_car->registration_number;
            }

            date_default_timezone_set('Asia/Kolkata');
            $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
            $currentDateTime = Carbon::parse($oldcurrentDateTime);

            $currentTimeDate = now();

            $combinedStartDateTime = $booked_car->pickup_date;

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

                if (($currentTimeDate >= $pickupTimeBeforeThirty && strcmp($booked_car->status, 'delivered') !== 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
                    $desktopBookingHtml .= 'bg-[#fffbe5]';
                } elseif (($currentTimeDate >= $dropoffTimeBeforeThirty && strcmp($booked_car->status, 'delivered') == 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
                    $desktopBookingHtml .= 'bg-[#fffbe5]';
                } else {
                    $desktopBookingHtml .= 'bg-white';
                }
                    $desktopBookingHtml .= '
                " data-id="' . $booked_car->id . '" >';

                $desktopBookingHtml .='
                <div class="list_cr_out flex justify-center items-center ">
                            <div class="list_cr_item">
                        <div class="booking_list_card_top_section" data-id="' . $booked_car->id . '">';
                        if($booked_car->booking_owner_id)
                        {
                            $agentCompanyName = ucwords(Helper::getUserMeta($booked_car->booking_owner_id, 'company_name'));
                            $desktopBookingHtml .=' <div class="flex pb-2"><p> Booked By '.$agentCompanyName.'</p></div>';
                        }
                        $desktopBookingHtml .='
                        <div class="flex">
                            <div>
                        <div class="flex items-center  flex-wrap gap-[3px]">';

                            if(($currentTimeDate >= $pickupTimeBeforeThirty && strcmp($booked_car->status, 'delivered') !== 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
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
                            elseif(($currentTimeDate >= $dropoffTimeBeforeThirty && strcmp($booked_car->status, 'delivered') == 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
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
                            } elseif (($dropDaysGap == 0) && ($nowDateTimeObject <= $nowCarbonEndDateTime) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
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
                                        <div class="image_l_wh ">
                                                <img src="' . $modifiedImgUrl . '" alt="cars" class="object-contain max-h-full">
                                        </div>
                                            </div>
                                                <div class="ctc_info_w">
                                                    <div class="mb-[4px]">
                                                        <h4 class="capitalize text-[#2B2B2B] font-medium leading-4 text-[13px]">' . $car_name . '</h4>
                                                        <p class="uppercase text-[#666] text-[13px] font-normal">' . $registration_number . '</p>
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
                        if (
                            (($currentTimeDate >= $pickupTimeBeforeThirty && strcmp($booked_car->status, 'delivered') !== 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) ||
                            (($currentTimeDate >= $dropoffTimeBeforeThirty && strcmp($booked_car->status, 'delivered') == 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0)  )
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
                                                            text-xs font-normal leading-4  border rounded ';

                                        if (strcmp($booked_car->status, 'delivered') == 0) {
                                            $desktopBookingHtml .= 'cursor-default bg-[#EBEBE4] border-[#EBEBE4] text-[#AEAEAE]';
                                        } else {
                                            $desktopBookingHtml .= 'search_booking_action_btn border-siteYellow transition-all duration-300 ease-in-out bg-siteYellow hover:bg-siteYellow400 ';
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
                                                items-center text-xs font-normal leading-4 border rounded';

                                        if (strcmp($booked_car->status, 'delivered') !== 0) {
                                            $desktopBookingHtml .= 'cursor-default bg-[#EBEBE4] border-[#EBEBE4] text-[#AEAEAE]';
                                        } else {
                                            $desktopBookingHtml .= ' search_booking_action_btn border-siteYellow transition-all duration-300 ease-in-out bg-siteYellow hover:bg-siteYellow400';
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
                    elseif(($currentTimeDate >= $dropoffTimeBeforeThirty && strcmp($booked_car->status, 'delivered') == 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
                        $mobileBookingHtml .= ' bg-[#fffbe5]';
                    }
                    else{
                        $mobileBookingHtml .= ' bg-white';
                    }
                    $mobileBookingHtml .= '   ">';

                    if($booked_car->booking_owner_id)
                    {
                        $agentCompanyName = ucwords(Helper::getUserMeta($booked_car->booking_owner_id, 'company_name'));
                        $mobileBookingHtml .=' <div class="flex pb-2"><p> Booked By '.$agentCompanyName.'</p></div>';

                    }

                        $mobileBookingHtml .= '<div class="flex justify-between items-center gap-[10px]">';

                            if (($currentTimeDate >= $pickupTimeBeforeThirty && strcmp($booked_car->status, 'delivered') !== 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
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
                                            <span class="uppercase status_time border border-[#fca728] rounded-[12px]
                                            text-[#fca728] px-[10px] py-[2px] 3xl:py-[1px] bg-[#FFFFFF]">Delivery Awaited</span>
                                        </div>
                                    </div>';
                            } elseif (($currentTimeDate >= $dropoffTimeBeforeThirty && strcmp($booked_car->status, 'delivered') == 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
                                $mobileBookingHtml .= '
                                    <div class="flex items-center flex-wrap gap-[5px]">
                                        <div class="flex items-center justify-center mr-[32px] 3xl:mr-[25px]">
                                            <div class="block">
                                                <img src="' . asset('images/time-clock-img.svg') . '" alt="clock" class="w-[19px]">
                                            </div>
                                            <div class="w-full text-xs ml-[6px]">
                                                <span class="block text-siteYellow800 text-[13px] font-normal leading-normal">Drop-off: ' . $formattedEndTime . '</span>
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

                    } elseif (($dropDaysGap == 1) && ($nowDateTimeObject <= $nowCarbonEndDateTime) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {
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
                    elseif(($pickupDaysGap>1) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) {

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
                        <div class="text-xs">
                            <span class="uppercase status_time border border-[#25BE00] rounded-[12px]
                            text-[#25BE00] px-[10px] py-[2px] 3xl:py-[1px] bg-[#FFFFFF]">Booking Completed</span>
                        </div>';
                    }
                    elseif( (strcmp($booked_car->status, 'canceled') == 0) ) {
                        $mobileBookingHtml .= '
                        <div class="text-xs">
                            <span class="uppercase status_time border border-[#BD0000] rounded-[12px]
                            text-[#BD0000] px-[10px] py-[2px] 3xl:py-[1px] bg-[#FFFFFF]">Booking Canceled</span>
                        </div>';
                    }

                    $mobileBookingHtml .= '
                        <div class="text-right flex items-center">
                            <p class="text-[#898376] font-normal text-[14px] sm:leading-3 sm:w-max sm:leading-3">
                                '.$booked_car->number_of_days." days". '
                            </p>
                        </div>

                    </div>
                    <div class="flex items-center my-[10px] sm:my-[13px]">
                        <div class="block">
                            <div class="min-w-[150px] max-w-[150px] w-[150px] 3xl:min-w-[135px] 3xl:max-w-[135px] 3xl:w-[135px] sm:min-w-[107px] sm:w-[107px]">
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
                                <a href="https://wa.me/'.ltrim($booked_car->customer_mobile_country_code, '+'). $booked_car->customer_mobile . '" class="text-[#700D4A] font-bold text-[15px]">WHATSAPP</a>
                            </div>
                        </div>
                        <div class="block mt-[2px]">
                            <a href="' . route('agent.booking.view', $booked_car->id) . '" class=" links_item_cta inline-flex items-center text-[#2B2B2B] font-medium leading-4 text-[14px]">View Booking <img src="' . asset('images/arrow-booking.svg') . '" alt="arro" class="ml-[9px] w-[24px]"></a>
                        </div>
                    </div>';

                        if(
                            (($currentTimeDate >= $pickupTimeBeforeThirty && strcmp($booked_car->status, 'delivered') !== 0) && (strcmp($booked_car->status, 'collected') !== 0) ) ||
                            (($currentTimeDate >= $dropoffTimeBeforeThirty && strcmp($booked_car->status, 'delivered') == 0) && (strcmp($booked_car->status, 'collected') !== 0) )
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
                                        $mobileBookingHtml .= ' search_booking_action_btn border-siteYellow transition-all duration-300 ease-in-out bg-siteYellow hover:bg-siteYellow400';
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
                                        $mobileBookingHtml .= ' search_booking_action_btn border-siteYellow transition-all duration-300 ease-in-out bg-siteYellow hover:bg-siteYellow400';
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

        return $result;
    }

//////////////////////////////////////////////  ALL BOOKED CARS ///////////////////////////////////////////

    public function allAutocompleteCustomerAndMob(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);

        $user_id = auth()->user()->id;
        $currentDate = Carbon::now();

        // Get the search query from the request
        $query = $request->input('search');

        // Search for customer names and mobile numbers that start with the query
        $custAndMobByName = CarBooking::select("customer_name", "customer_mobile","customer_mobile_country_code", "id", \DB::raw("'1' as priority"))
        ->where('user_id', $user_id)
        // ->where('customer_name', 'LIKE', $query . '%')
        // ->orWhere('customer_mobile', 'LIKE', $query . '%')
        ->where(function ($q) use ($query, $currentDate) {
                $q->where('customer_name', 'LIKE', $query . '%')
                    ->orWhere('customer_mobile', 'LIKE', $query . '%')
                    ->orWhere('registration_number', 'LIKE', $query . '%')
                    ;
        })
        ->orderByRaw("CASE
        WHEN status = 'delivered' THEN dropoff_date
        WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
        ELSE pickup_date
        END ASC")
        ->get();


        // Search for customer names and mobile numbers that contain the query anywhere
        $custAndMobPartiallyByName = CarBooking::select("customer_name", "customer_mobile","customer_mobile_country_code", "id", \DB::raw("'2' as priority"))
        ->where('user_id', $user_id)
        // ->where('customer_name', 'LIKE', '%' . $query . '%')
        // ->orWhere('customer_mobile', 'LIKE', '%' . $query . '%')
        ->where(function ($q) use ($query, $currentDate) {
            $q->where('customer_name', 'LIKE', '%' . $query . '%')
                ->orWhere('customer_mobile', 'LIKE', '%' . $query . '%')
                ->orWhere('registration_number', 'LIKE', '%'. $query . '%') ;
        })
        ->orderByRaw("CASE
        WHEN status = 'delivered' THEN dropoff_date
        WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
        ELSE pickup_date
        END ASC")
        ->get();

        // Custom comparison function for sorting results based on similarity to the query
        $customComparison = function ($a, $b) use ($query) {
            // Calculate similarity between the query and the values
            $similarityA = similar_text(strtolower($query), strtolower($a['customer_name'] . ' ' . $a['customer_mobile']));
            $similarityB = similar_text(strtolower($query), strtolower($b['customer_name'] . ' ' . $b['customer_mobile']));

            // Sort in descending order based on similarity
            return $similarityB <=> $similarityA;
        };

        // Combine and sort the results using the custom comparison function
        $results = array_merge($custAndMobByName->toArray(), $custAndMobPartiallyByName->toArray());
        usort($results, $customComparison);

        // Return the sorted results as JSON
        return response()->json($results);
    }

    public function allSearchCustomerAndMob(Request $request)
    {
        date_default_timezone_set('Asia/Kolkata');
        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);

        $customerName = $request->input('customerName');
        $customerMobile = $request->input('customerMobile');
        $countryCode = $request->input('countryCode');

        $requestData = $request->all();
        $formArray = [];

        $user_id = auth()->user()->id;

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
            ->where(function ($query) use ($customerName, $customerMobile, $countryCode) {
                if ($customerName) {
                    $query->where('customer_name', '=', $customerName);
                }

                if ($customerMobile) {
                    $query->where('customer_mobile', '=', $customerMobile);
                }

                if ($countryCode) {
                    $query->where('customer_mobile_country_code', '=', $countryCode);
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
            ->take(5)
            ->get();


        if (count($booked_cars))
        {
            $result =  $this->collectionOfBookedCars($booked_cars);

            $desktopBookingHtml = $result['desktopBookingHtml'];
            $mobileBookingHtml = $result['mobileBookingHtml'];

            return response()->json(['success' => true, 'error' => false,'booked_cars'=> $booked_cars, 'desktopBookingHtml' => $desktopBookingHtml,'desktopBookingHtml' => $desktopBookingHtml, 'mobileBookingHtml' => $mobileBookingHtml]);
        }
        else {
            return response()->json(['success' => false, 'error' => true,'booked_cars'=>'', 'desktopBookingHtml' => '', 'mobileBookingHtml' => '']);
        }
    }

    public function allSearchStatusChange(Request $request)
    {
        $user_id = auth()->user()->id;



        date_default_timezone_set('Asia/Kolkata');
        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);

        $currentTimeDate = now();

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
        }
        else
        {
            $errorMsg = "Booking not found";
        }


        $bookingLength = $request->bookingLength;

        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);

        $booked_cars = CarBooking::whereIn('id', $request->bookingIds)

        ->orderByRaw("CASE
        WHEN status = 'delivered' THEN dropoff_date
        WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
        ELSE pickup_date
        END ASC")
        ->take($bookingLength)
        ->get();


        if(count($booked_cars))
        {
            $result =  $this->collectionOfBookedCars($booked_cars);

            $desktopBookingHtml = $result['desktopBookingHtml'];
            $mobileBookingHtml = $result['mobileBookingHtml'];

            return response()->json(['success' => true, 'error' => false,'successMsg'=>$successMsg , 'errorMsg'=>$errorMsg,  'booked_cars'=>$booked_cars, 'desktopBookingHtml'=>$desktopBookingHtml,'mobileBookingHtml'=>$mobileBookingHtml ]);
        }
        else
        {
            return response()->json(['success' => false, 'error' => true, 'msg'=>false , 'booked_cars'=>$booked_cars,'desktopBookingHtml'=>'','mobileBookingHtml'=>'' ]);
        }
    }

}
?>
