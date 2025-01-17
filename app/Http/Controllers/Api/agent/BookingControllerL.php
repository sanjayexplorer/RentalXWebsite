<?php
namespace App\Http\Controllers\Api\agent;
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
use App\Models\Drivers;
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
class BookingControllerL extends Controller
{
    public function calendar(Request $request){

    $filtersCount = 0;

    $limit = $request->input('limit', 3);

    $offset = $request->input('offset', 0);

    $requestData = $request->all();

    // Fetching the request parameters for filtering

    $car_type = $requestData['car_type'] ?? '';

    $transmission = $requestData['transmission'] ?? '';

    $filterRequest = $requestData['filterRequest'] ?? '';

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

    if ($filterRequest){
        $car_type = isset($requestData['car_type']) ? $requestData['car_type'] : '';
        $transmission = isset($requestData['transmission']) ? $requestData['transmission'] : '';
        $formArray = [
            'car_type' => $requestData['car_type'] ?? [],
            'transmission' => $requestData['transmission'] ?? [],
        ];

        if ($filterRequest) {
            // Initialize filter array with car_type and transmission
            $car_type = isset($requestData['car_type']) ? $requestData['car_type'] : '';
            $transmission = isset($requestData['transmission']) ? $requestData['transmission'] : '';
            $formArray = [
                'car_type' => $requestData['car_type'] ?? [],
                'transmission' => $requestData['transmission'] ?? [],
            ];
            $cars = cars::select('cars.id','cars.name','cars.registration_number','cars.transmission','cars.fuel_type',
            'cars.manufacturing_year','cars.car_type','cars.price','cars.seats','cars.status',
            'cars.user_id','cars.created_at','cars.updated_at')
            ->whereIn('cars.id',$sharedCarId)
            ->where('cars.status','NOT LIKE','deleted')
            ->where(function($query) use($formArray,$userIds,$sharedCarId){

                if (!empty($formArray['car_type']) && (empty($formArray['transmission']))) {
                    // echo 'only car_type';
                    $query->whereIn('cars.car_type', $formArray['car_type']);
                    // dd($query);
                }

                if (!empty($formArray['transmission']) && (empty($formArray['car_type']))) {
                    // echo 'only transmission';
                    $query->whereIn('cars.transmission', $formArray['transmission']);
                    // dd($query);
                }

                // check with car_type with transmission
                if (!empty($formArray['transmission']) && !empty($formArray['car_type'])) {
                    // echo 'car_type with transmission';
                    $query->whereIn('cars.transmission', $formArray['transmission'])->whereIn('cars.car_type', $formArray['car_type']);
                    // dd($query);
                }
                // check with car_type with transmission
            })
                ->orderBy('cars.created_at', 'DESC')
                ->take(10) // You can change this to dynamically take based on pagination
                ->get();
               $filtersCount= $this->checkFiltersCount($car_type,$transmission);

        }

    }else{
         // Default query if no filters are provided
         $cars = cars::whereIn('id',$sharedCarId)->where('status', 'NOT LIKE', 'deleted')->orderByDesc('created_at')
         ->offset($offset)
         ->limit($limit)->get();
    }

        // Getting the car ids to fetch related thumbnails
        $carIds = $cars->pluck('id')->toArray();

        // Optimized thumbnail query
        $thumbnails = carImages::whereIn('carId', $carIds)
            ->where('featured', 'set')
            ->get()
            ->keyBy('carId');

        // Attach the feature image URLs to the cars
        $cars = $cars->map(function ($car) use ($thumbnails) {
            $thumbnail = $thumbnails->get($car->id);
            $featuredImage = $thumbnail ? Helper::getPhotoById($thumbnail->imageId) : null;
            $car->featureImageUrl = $featuredImage ? $featuredImage->url : null;
            return $car;
        });

        // Parsing and formatting startDate and endDate from the request
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        // Fetch bookings related to the cars within the provided date range
        $bookings = CarBooking::whereIn('carId', $carIds)
        ->where(function ($query) {
            $query->where('status', '=', 'delivered')
                ->orWhereNull('status');
        })
        ->orderByDesc('created_at')
        ->get();

        // Get total car count for pagination or display
        $totalCars = cars::where('status', '!=', 'deleted')
          ->whereIn('id', $carIds)
            ->where('user_id','!=',$agentId)
            ->count();

        // Return the response with the filtered cars, bookings, and total count
        return response()->json([
            'success' => true,
            'error' => false,
            'cars' => $cars->makeVisible(['featureImageUrl']),
            'bookings' => $bookings,
            'totalCars' => $totalCars,
            'startDate'=>$startDate,
            'car_type'=>$car_type,
            'transmission'=>$transmission,
            'filtersCount'=>$filtersCount
        ], 200);

}
    private function checkFiltersCount($car_type,$transmission)
    {
        $count=0;
        if ($car_type != '') $count++;
        if ($transmission != '') $count++;
        return $count;
    }

    public function getCarDetails(Request $request, $carId)
    {
        if (!$carId || !is_numeric($carId)) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Car ID is required and must be a valid number.',
            ], 400); 
        }
    
        $carDetails = cars::where('id', $carId)
            ->where('status', '<>', 'deleted')
            ->orderByDesc('created_at')
            ->first();

        if (!$carDetails) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Car not found.',
            ], 404); 
        }
    
        $thumbnail = carImages::where('carId', $carDetails->id)
            ->where('featured', 'set')
            ->first(); 

        $partnerMetas = UserMetas::where('userId', $carDetails->user_id)
            ->where('status', '<>', 'inactive')
            ->get();
        $plate_type = carsMeta::where([['carId', '=', $carDetails->id], ['meta_key', 'LIKE', 'plate_type']])->first();

    
        $responseData = [
            'carDetails' => $carDetails,
            'plate_type'=>$plate_type,
            'partnerMetas' => $partnerMetas,
        ];

        if ($thumbnail) {
            $featuredImage = Helper::getPhotoById($thumbnail->imageId);
            $responseData['featuredImageUrl'] = $featuredImage ? $featuredImage->url : null;
        }
    
        return response()->json([
            'success' => true,
            'error' => false,
            'data' => $responseData,
        ], 200);
    }
    
    
    public function calendarPost(Request $request){
        $carId = $request->carId;
        $firstDate = $request->start_date;
        $pickupTime = $request->start_time;
        $lastDate = $request->end_date;
        $dropoffTime = $request->end_time;
        $actionType = $request->action_type;

        $carbonFirstDate = Carbon::parse($firstDate);
        $carbonLastDate = Carbon::parse($lastDate);

        $pickup_date = $carbonFirstDate->format('Y-m-d') . ' ' . $pickupTime;
        $dropoff_date = $carbonLastDate->format('Y-m-d') . ' ' . $dropoffTime;

        $pickupDate = date('Y-m-d H:i:s', strtotime($pickup_date));
        $dropoffDate = date('Y-m-d H:i:s', strtotime($dropoff_date));

        $convertedformattedStartDate = Carbon::parse($pickupDate);
        $convertedformattedEndDate = Carbon::parse($dropoffDate);

        if($request->isEditPage){
            $roundedDays = $request->number_of_days;
        }
        else{
            // Calculate the initial day difference
            $roundedDays = $convertedformattedStartDate->diffInDays($convertedformattedEndDate);

            if($roundedDays === 0){
            $roundedDays = 1;
            }

            // Check if pickup time is before 9:00 AM, then count the full day
            if (Carbon::parse($pickupTime)->lt(Carbon::parse('09:00 AM'))) {
                $roundedDays++;
            }

            // If dropoff time is after 9:00 AM on the last day, include that as a full day
            if (Carbon::parse($dropoffTime)->gt(Carbon::parse('09:00 AM'))) {
                $roundedDays++;
            }
        }
        
        if (strcmp($request->booking_type, 'normal') == 0) {

            $flag = $this->checkBookingStatus($carId, $pickupDate, $dropoffDate, $actionType);

            if ($flag) {
                return response()->json([
                    'success' => false,
                    'error' => true,
                    'msg' => 'Please select a valid date, Either dates are Locked or Booked !!'
                ]);
            }

            $car = cars::whereId($carId)->where('status', 'NOT LIKE', 'deleted')->first();

            if (!$car) {
                return response()->json(['success' => false, 'error' => true, 'msg' => 'car not available']);
            }

            $per_day_rental_charges = $car->price;

            $total_booking_amount = $per_day_rental_charges * $roundedDays;

            $userId = $car->user_id;

            Session::put('PartebookingStarted', 'yes');

            CarsBookingDateStatus::create([
                'carId' => $carId,
                'start_date' => $firstDate,
                'end_date' => $lastDate,
            ]);

            $car_name = $car->name;

            $registration_number = $car->registration_number;
        }

        $pickup_location = ($request->pickup_location == 'Other') ? $request->other_pickup_location : $request->pickup_location;
        $dropoff_location = ($request->dropoff_location == 'Other') ? $request->other_dropoff_location : $request->dropoff_location;

        $booking = CarBooking::create([
            'carId' => $carId,
            'booking_owner_id'=>auth()->user()->id,
            'user_id' => $userId,
            'car_name' => $car_name,
            'registration_number' => $registration_number,
            'start_date' => date('Y-m-d H:i:s', strtotime($carbonFirstDate)),
            'pickup_date' => date('Y-m-d H:i:s', strtotime($pickupDate)),
            'end_date' => date('Y-m-d H:i:s', strtotime($carbonLastDate)),
            'dropoff_date' => date('Y-m-d H:i:s', strtotime($dropoffDate)),
            'pickup_time' => $pickupTime,
            'dropoff_time' => $dropoffTime,
            'pickup_location' => $pickup_location,
            'dropoff_location' => $dropoff_location,
            'bookingId' => 1001,
            'per_day_rental_charges' => $per_day_rental_charges,
            'number_of_days' => $roundedDays,
            'total_booking_amount' => $total_booking_amount,
            'booking_type' => $request->booking_type,
        ]);

        if (strcmp($request->booking_type, 'normal') == 0) {
            CarsBookingDateStatus::where('carId', '=', $carId)
            ->where('start_date', '<=', $carbonFirstDate)
            ->where('end_date', '>=', $carbonLastDate)
            ->delete();
        }

        $maxBookingId = CarBooking::where('user_id', $userId)->max('bookingId');
        $newBookingId = ($maxBookingId !== null) ? ($maxBookingId + 1) : 1001;

        CarBooking::whereId($booking->id)->update(['bookingId' => $newBookingId]);
        $new = CarBooking::whereId($booking->id)->first();
        return response()->json([
            'success' => true,
            'error' => false,
            'msg' => 'succeed',
            'new'=>$new,
            'bookingId' => $booking->id
        ]);
    }

    public function getBookingDetails(Request $request){
        $isPartnerBooking = false;
        $carBooking = CarBooking::whereId($request->bookingId)->first();
        if($carBooking->booking_owner_id != null){
            $isPartnerBooking = true;
        }
        return response()->json(['success' => true, 'error' => false, 'carBooking'=> $carBooking, 'isPartnerBooking'=>$isPartnerBooking,'msg' => 'Customer details fetched...' ]);
    }


    public function edit(Request $requset,$id){
        $userId = auth()->user()->id;


        if (!$id) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Booking ID is required.'
            ], 400);
        }

        $car_booking = CarBooking::where('id', $id)->first();

        if (!$car_booking) {
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => 'Booking not found.'
            ], 404);
        }

        $carId = $car_booking->carId;

        $car = cars::find($carId);

          $thumbnail = carImages::where('carId', $carId)
          ->where('featured', 'set')
          ->first();

          if ($thumbnail) {
              $featuredImage = Helper::getPhotoById($thumbnail->imageId);
              $featuredImageUrl = $featuredImage ? $featuredImage->url : null;
          } else {
              $featuredImageUrl = null;
          }
          if (!$car) {
              return response()->json([
                  'success' => false,
                  'error' => true,
                  'message' => 'Car not found.'
              ], 404);
          }

      $partner = User::find($car->user_id);

      $partnerMetas = UserMetas::where('userId', $partner->id)->where('status', 'NOT LIKE', 'inactive')->get();

      $agentMetas = UserMetas::where('userId', $userId)->where('status', 'NOT LIKE', 'inactive')->get();

      $drivers = Drivers::where('userId', '=', $car_booking->user_id)->get();

      $booking_payments = booking_payments::where('bookingId', $id)->get();


      return response()->json([
          'success' => true,
          'error' => false,
          'car_booking' => $car_booking,
          'car' => $car,
          'featuredImageUrl'=>$featuredImageUrl,
          'carId' => $carId,
          'drivers' => $drivers,
          'partner'=> $partner,
          'partnerMetas'=>$partnerMetas,
          'agentMetas'=>$agentMetas,
          'booking_payments:'=>$booking_payments,
      ], 200);

    }

    public function editPost(Request $request, $id)
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
            return response()->json([
                'success' => false,
                'error' => true,
                'message' => $validator->errors(),
            ], 400);
        }


        // Format and process dates
        $pickup_date = Carbon::parse($request->start_date)->format('Y-m-d') . ' ' . $request->start_time;
        $dropoff_date = Carbon::parse($request->end_date)->format('Y-m-d') . ' ' . $request->end_time;

        // Prepare trimmed values
        $trimAdvanceAmount = $this->sanitizeAmount($request->advance_booking_amount);
        $trim_per_day_rental_charges = $this->sanitizeAmount($request->per_day_rental_charges);
        $trim_pickup_charges = $this->sanitizeAmount($request->pickup_charges);
        $trim_dropoff_charges = $this->sanitizeAmount($request->dropoff_charges);
        $trim_discount = $this->sanitizeAmount($request->discount);
        $trim_total_booking_amount = $this->sanitizeAmount($request->total_booking_amount);
        $trim_refundable_security_deposit = $this->sanitizeAmount($request->refundable_security_deposit);
        $trim_due_at_delivery = $this->sanitizeAmount($request->due_at_delivery);

        $trim_agent_commission = $this->sanitizeAmount($request->agent_commission);
        $trim_agent_commission_received = $this->sanitizeAmount($request->agent_commission_received);

        // Update booking
        $booking = CarBooking::whereId($id)->update([
            'customer_name' => $request->customer_name,
            'customer_mobile' => $request->customer_mobile,
            'customer_mobile_country_code' => $request->customer_mobile_country_code,
            'alt_customer_mobile' => $request->alt_customer_mobile,
            'alt_customer_mobile_country_code' => $request->alt_customer_mobile_country_code,
            'customer_email' => $request->customer_email,
            'customer_city' => $request->customer_city,
            'pickup_location' => $request->pickup_location,
            'dropoff_location' => $request->dropoff_location,
            'start_date' => $request->start_date,
            'pickup_date' => $pickup_date,
            'end_date' => $request->end_date,
            'dropoff_date' => $dropoff_date,
            'pickup_time' => $request->pickup_time,
            'dropoff_time' => $request->dropoff_time,
            'advance_booking_amount' => $trimAdvanceAmount,
            'per_day_rental_charges' => $trim_per_day_rental_charges,
            'number_of_days' => $request->number_of_days,
            'pickup_charges' => $trim_pickup_charges,
            'dropoff_charges' => $trim_dropoff_charges,
            'discount' => $trim_discount,
            'total_booking_amount' => $trim_total_booking_amount,
            'refundable_security_deposit' => $trim_refundable_security_deposit,
            'due_at_delivery' => $trim_due_at_delivery,
            'booking_remarks' => $request->booking_remarks,
            'agent_commission'=>$trim_agent_commission,
            'agent_commission_received'=>$trim_agent_commission_received,
        ]);

        if ($booking) {
            return response()->json([
                'success' => true,
                'error' => false,
                'datas' => $request->all(),
                'msg' => 'You successfully updated your booking.'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'error' => true,
                 'id:'=>$id,
                'datas' => $request->all(),
                'msg' => 'Failed to update the booking.'
            ], 500);
        }
    }

    private function sanitizeAmount($amount){
        $trimmed = preg_replace('/[₹,]+/', '', $amount);
        return is_numeric($trimmed) ? $trimmed : 0;
    }

    private function checkBookingStatus($carId, $pickupDate, $dropoffDate,$actionType){

        if(strcmp($actionType,'overlapped_date')!=0){

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

        else{
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
            })->get();
        }

        $carHoldStatus = CarsBookingDateStatus::where('carId', '=', $carId)
            ->where(function ($query) use ($pickupDate, $dropoffDate) {
                $query->whereBetween('start_date', [$pickupDate, $dropoffDate])
                ->orWhereBetween('end_date', [$pickupDate, $dropoffDate]);
            })
        ->get();

        if(count($bookingCarStatus) > 0 || count($carHoldStatus) > 0){
            return true;
        } else {
            return false;
        }
    }

    public function view(Request $requset,$id)
    {

        if (!$id) {
            return response()->json(['success' => false, 'error' => true]);
        }
        else{
            $assignedDriver='';

            $userId = auth()->user()->id;

            $car_booking = CarBooking::where('id', $id)->first();


            if (!$car_booking) {
                return response()->json(['success' => false, 'error' => true]);
            }

            $carId = $car_booking->carId;

            $car = cars::find($carId);

            $partner = User::find($car_booking->user_id);

            $UserMetas=UserMetas::where('userId','=',$userId)->get();

            if (!$partner) {
                return response()->json(['success' => false, 'error' => true]);
            }

            if ($userId != $car_booking->user_id) {
                return response()->json(['success' => false, 'error' => true]);
            }

            if (strcmp($car_booking->driver_id, '') != 0) {
                $assignedDriver = Drivers::whereId($car_booking->driver_id)->first();
            }

            $booking_payments = booking_payments::where('bookingId', $id)->get();

            $drivers= Drivers::where('userId','=',$userId)->get();

            $thumbnails = Helper::getFeaturedSetCarPhotoById($carId);
            $carImageUrl = asset('public/images/no_image.svg');


            foreach($thumbnails as $thumbnail){
                $image = Helper::getPhotoById($thumbnail->imageId);
                $carImageUrl = $image->url;
            }

                $modifiedImgUrl = $carImageUrl;

 
            return response()->json([
                'success' => true,
                'error' => false,
                'drivers' => $drivers,
                'UserMetas' => $UserMetas,
                'car_booking' => $car_booking,
                'car' => $car,
                'carId' => $carId,
                'booking_payments' => $booking_payments,
                'partner' => $partner,
                'assignedDriver' => $assignedDriver,
                'modifiedUrl' => $modifiedImgUrl
            ]);

        }
    }

    public function getAllBookedAndLockedDates(Request $request)
    {
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
            return response()->json([ 'disableDates'=>$disableDates,'success' => true, 'error' => false ,'msg'=>'disable dates found']);
         }
         else{
            return response()->json(['disableDates'=>$disableDates,'success' => false, 'error' => true ,'msg'=>'disable dates not found']);
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

    public function getLocation(Request $request){
        $userId = Auth()->user()->id;
        $drivers = Drivers::orderByDesc('created_at')->get();
        $searchTerm = $request->input('search');

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
                }
                else{

                    $filteredLocations = array_filter($locations, function($location) {
                         return stripos($location['location'], 'other') !== false;
                    });

                    $locationArr[$key] = array_values($filteredLocations);
                }
            }
        }

         return response()->json([ 
            'success' => true,
            'error' => false,
            'locations'=>$locationArr,
            'drivers'=>$drivers
        ]);

    }


    public function checkLockedAndBooked(Request $request){

        $carId = $request->carId;
        $firstDate = $request->start_date;
        $lastDate = $request->end_date;
        $actionType=$request->actionType;


        $start_date = Carbon::parse($request->start_date);
        $start_time = $request->start_time;
        $end_date = Carbon::parse($request->end_date);
        $end_time = $request->end_time;

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

        else{


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
            return response()->json(['success' => true, 'error' => false,]);
        } else {
            return response()->json(['success' => false, 'error' => true,]);
         }

    }

    public function getAllBookedAndLockedDatesWithTime(Request $request){
        $carId = $request->carId;
        $bookingId=$request->bookingId;
        $disabledDates=[];

        $bookingCarDates = CarBooking::select('pickup_date', 'dropoff_date')->where('carId', '=', $carId)->where(function ($query) {
        $query->where('status', '=', 'delivered')
            ->orWhereNull('status');
        })->get();

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

       $endDates=$endDates->toArray();
       $overlapDates=$overlapDates->toArray();

        foreach ($disableDates as $date) {

        $datePart = date('Y-m-d', strtotime($date));

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

    public function checkLockedAndBookedDates(Request $request)
    {
        $carId = $request->carId;
        $firstDate = $request->pickupDate;
        $lastDate = $request->dropOffDate;
        $actionType = 'normal';

        $start_date = Carbon::parse($request->pickupDate);
        $start_time = $request->startTime;
        $end_date = Carbon::parse($request->dropOffDate);
        $end_time = $request->endTime;
        $pickup_date = $start_date->format('Y-m-d') . ' ' . $start_time;
        $dropoff_date = $end_date->format('Y-m-d') . ' ' . $end_time;
        $pickupDate = date('Y-m-d H:i:s', strtotime($pickup_date));
        $dropoffDate = date('Y-m-d H:i:s', strtotime($dropoff_date));
        $bookingId=$request->bookingId;
       
        $bookingN=CarBooking::whereId($bookingId)->first();
       
        if(strcmp($bookingN->booking_type,'external')!=0)
        {
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
        }

        else
        {
            $isDateBooked = true;

        }

        if(!$isDateBooked){
            return response()->json(['success' => false, 'error' => true,'msg'=>'','dropoffDate'=>$dropoffDate,'pickupDate'=>$pickupDate]);
        }

        else
        {

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

            if (strcmp($bookingN->booking_type, 'external') == 0) {
                $bookingCarStatus = [];
                $carHoldStatus = [];
            }
                if(count($bookingCarStatus) > 0 || count($carHoldStatus) > 0){
                    $msg = 'Please select a valid date, Either dates are Locked or Booked !!';
                    return response()->json(['success' => true, 'error' => false,'msg'=>$msg]);
                } else {
                    $msg = 'something went wrong';
                    return response()->json(['success' => false, 'error' => true]);
                }

          }
    }

    public function updateBookingDates(Request $request)
    {
        $start_date = Carbon::parse($request->pickupDate);
        $start_time = $request->startTime;
        $end_date = Carbon::parse($request->dropOffDate);
        $end_time = $request->endTime;
        $pickup_date = $start_date->format('Y-m-d') . ' ' . $start_time;
        $dropoff_date = $end_date->format('Y-m-d') . ' ' . $end_time;

        $id=$request->bookingId;

        if($id){

            $carBookingUpdate= CarBooking::whereId($id)->update([
            'start_date' => date('Y-m-d H:i:s', strtotime($start_date)),
            'pickup_date' => date('Y-m-d H:i:s', strtotime($pickup_date)),
            'end_date' => date('Y-m-d H:i:s', strtotime($end_date)),
            'dropoff_date' => date('Y-m-d H:i:s', strtotime($dropoff_date)),
            'pickup_time' => $start_time,
            'dropoff_time' => $end_time,
            ]);

            return response()->json([
                'success' => true,
                'error' => false,
                'msg'=>'booking dates are updated','pickup_date'=>$pickup_date,
                'dropoff_date'=>$dropoff_date
            ]);

        }else{

            return response()->json([
                'success' => false,
                'error' => true,
                'msg'=>'carId is invalid'
            ]);

        }

    }

}