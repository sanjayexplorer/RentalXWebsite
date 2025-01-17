<?php
namespace App\Http\Controllers\Api\partner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Auth;
use Helper;
use App\Models\User;
use App\Models\cars;
use App\Models\Drivers;
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
use Mail;
class BookingController extends Controller
{

    protected function getBookedCarsQuery(){

        date_default_timezone_set('Asia/Kolkata');

        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');

        $currentDateTime = Carbon::parse($oldcurrentDateTime);
        $user_id = auth()->user()->id;

        return CarBooking::where('user_id', $user_id)
            ->where(function ($query) {
                $query->whereNull('status')->orWhere('status', 'LIKE', 'delivered');
            })
            ->orderByRaw("CASE
                WHEN status = 'delivered' THEN dropoff_date
                WHEN '$currentDateTime' >= pickup_date AND status != 'delivered' THEN dropoff_date
                ELSE pickup_date
            END ASC");

    }

    protected function getBookedCarsWithFilter($request)
    {

        date_default_timezone_set('Asia/Kolkata');

        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');

        $currentDateTime = Carbon::parse($oldcurrentDateTime);

        $user_id = auth()->user()->id;

        $requestData = $request->all();

        $formArray = [];

        if (isset($requestData['form_data']))
        {
            parse_str($requestData['form_data'], $formArray);
            $start_date = $formArray['start_date'] ?? null;
            $end_date = $formArray['end_date'] ?? null;
            $formArray['booking_id'] = [];
        }

        $customerName = isset($request->customerName) ? $request->customerName : null;
        $customerMobile = isset($request->customerMobile) ? $request->customerMobile : null;

        return CarBooking::where('user_id', $user_id)

        ->where(function ($query) use ($customerName, $customerMobile) {
            if ($customerName) {
                $query->where('customer_name', '=', $customerName);
            }
            if ($customerMobile) {
                $query->where('customer_mobile', '=', $customerMobile);
            }
        })->where(function ($query) use ($formArray) {
            if (!empty($formArray['start_date']) && !empty($formArray['end_date'])) {
            if (!empty($formArray['booking_id'])) {
                $query->whereIn('id', $formArray['booking_id'])
                    ->whereDate('start_date', '>=', $formArray['start_date'])
                    ->whereDate('end_date', '<=', $formArray['end_date']);
            }
            else{
                    $query->whereDate('start_date', '>=', $formArray['start_date'])->whereDate('end_date', '<=', $formArray['end_date']);
                }
             } elseif (!empty($formArray['start_date']) && empty($formArray['end_date']) && !empty($formArray['booking_id'])) {
                $query->whereIn('id', $formArray['booking_id'])->whereDate('start_date', '=', $formArray['start_date']);
             } elseif (!empty($formArray['start_date']) && empty($formArray['end_date']) && empty($formArray['booking_id'])) {

                $query->where('start_date', '=', $formArray['start_date']);
             } elseif (empty($formArray['start_date']) && empty($formArray['end_date']) && !empty($formArray['booking_id'])) {
                $query->whereIn('id', $formArray['booking_id']);
          }
        })
        ->where(function ($query) {
            $query->whereNull('status')->orWhere('status', 'LIKE', 'delivered');
        })
        ->orderByRaw("CASE
            WHEN status = 'delivered' THEN dropoff_date
            WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
            ELSE pickup_date
        END ASC");
    }

    protected function getAllBookedCarsQuery(){
        date_default_timezone_set('Asia/Kolkata');
        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);
        $user_id = auth()->user()->id;
        return CarBooking::where('user_id', $user_id)
            ->orderByRaw("CASE WHEN status = 'delivered' THEN dropoff_date
                WHEN '$currentDateTime' >= pickup_date AND status != 'delivered' THEN dropoff_date
                ELSE pickup_date
            END ASC");
    }

    protected function getAllBookedCarsWithFilter($request){
        date_default_timezone_set('Asia/Kolkata');
        $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
        $currentDateTime = Carbon::parse($oldcurrentDateTime);

        $user_id = auth()->user()->id;

        $requestData = $request->all();
        $formArray = [];

        if (isset($requestData['form_data']))
        {
            parse_str($requestData['form_data'], $formArray);
            $start_date = $formArray['start_date'] ?? null;
            $end_date = $formArray['end_date'] ?? null;
            $formArray['booking_id'] = [];
        }

        $customerName = isset($request->customerName) ? $request->customerName : null;
        $customerMobile = isset($request->customerMobile) ? $request->customerMobile : null;

        return CarBooking::where('user_id', $user_id)
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
                else
                {

                    $query->whereDate('start_date', '>=', $formArray['start_date'])->whereDate('end_date', '<=', $formArray['end_date']);
                }
            } elseif (!empty($formArray['start_date']) && empty($formArray['end_date']) && !empty($formArray['booking_id'])) {

                $query->whereIn('id', $formArray['booking_id'])->whereDate('start_date', '=', $formArray['start_date']);
            } elseif (!empty($formArray['start_date']) && empty($formArray['end_date']) && empty($formArray['booking_id'])) {

                $query->where('start_date', '=', $formArray['start_date']);
            } elseif (empty($formArray['start_date']) && empty($formArray['end_date']) && !empty($formArray['booking_id'])) {
                $query->whereIn('id', $formArray['booking_id']);
            }
        })
        ->orderByRaw("CASE
            WHEN status = 'delivered' THEN dropoff_date
            WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
            ELSE pickup_date
        END ASC");
    }

    public static function getFeaturedSetCarPhotoById(Request $request ,$carId){
       $thumbnails = carImages::where('carId', '=', $carId)->where('featured', 'set')->first();
       return response()->json(['thumbnails' => $thumbnails], 200);
    }


    public static function getPhotoById(Request $request ,$imageId){
        $image =  master_images::whereId($imageId)->first();
        $carImageUrl = $image->url;
        $modifiedImgUrl = $carImageUrl;
        return response()->json(['modifiedImgUrl' => $modifiedImgUrl], 200);
    }

    public function list(Request $request)
    {
        try {
            $allBookedCars = $this->getBookedCarsQuery()->get();
            $booked_cars = $this->getBookedCarsQuery()->take(5)->get();
            $countBookedCars = $this->getBookedCarsQuery()->count();

            date_default_timezone_set('Asia/Kolkata');
            $currentDateTime = Carbon::now();

            foreach ($allBookedCars as $value) {
                $pickup_date_time = $value->pickup_date;
                $dropoff_date_time = $value->dropoff_date;
                $carbonPickupDateTime = Carbon::parse($pickup_date_time);
                $carbonDropoffDateTime = Carbon::parse($dropoff_date_time);

                $pickup_date_time_after_oneday = $carbonPickupDateTime->copy()->addDay();
                $dropoff_date_time_after_oneday = $carbonDropoffDateTime->copy()->addDay();

                if ($pickup_date_time_after_oneday->lt($currentDateTime) && $dropoff_date_time_after_oneday->lt($currentDateTime)) {
                    CarBooking::whereId($value->id)->update([
                        'status' => 'collected',
                        'delivered_time' => $dropoff_date_time_after_oneday,
                        'collected_time' => $dropoff_date_time_after_oneday,
                    ]);
                } else {
                    if ($pickup_date_time_after_oneday->lt($currentDateTime) && $value->status === null) {
                        CarBooking::whereId($value->id)->update([
                            'status' => 'delivered',
                            'delivered_time' => $pickup_date_time_after_oneday,
                        ]);
                    }

                    if ($dropoff_date_time_after_oneday->lt($currentDateTime) && strcmp($value->status, 'delivered') == 0) {
                        CarBooking::whereId($value->id)->update([
                            'status' => 'collected',
                            'collected_time' => $dropoff_date_time_after_oneday,
                        ]);
                    }
                }
            }



            // Attach images to the booked cars
            $bookedCarsWithImages = $booked_cars->map(function ($car) {
            // Fetch the featured image for the car
            $thumbnail = carImages::where('carId', $car->carId)
                ->where('featured', 'set')
                ->first();

            $car->imageUrl = $thumbnail
                ? optional(master_images::find($thumbnail->imageId))->url
                : '';

            // Fetch the company name of the booking owner
            $companyMeta = UserMetas::where([
                ['userId', '=', $car->booking_owner_id],
                ['meta_key', 'LIKE', 'company_name']
            ])->first();

            $car->companyName = $companyMeta ? $companyMeta->meta_value : '';

            // Replace car_name if carId is not 0
            if ($car->carId != 0) {
                $carDetails = cars::where('id', $car->carId)->first();
                $car->car_name = $carDetails ? $carDetails->name : $car->name;
            }

            return $car;
        });

            return response()->json([
                'success' => true,
                'error' => false,
                'booked_cars' => $bookedCarsWithImages,
                'countBookedCars' => $countBookedCars,
                'allBookedCars' => $allBookedCars,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function loadMoreBookings(Request $request)
    {

            $offset = $request->input('offset', 0);

            $booked_cars = $this->getBookedCarsQuery()
            ->skip($offset)
            ->take(5)
            ->get();


            // Attach images to the booked cars
            $bookedCarsWithImages = $booked_cars->map(function ($car) {
            // Fetch the featured image for the car
            $thumbnail = carImages::where('carId', $car->carId)
                ->where('featured', 'set')
                ->first();

            $car->imageUrl = $thumbnail
                ? optional(master_images::find($thumbnail->imageId))->url
                : '';

            // Fetch the company name of the booking owner
            $companyMeta = UserMetas::where([
                ['userId', '=', $car->booking_owner_id],
                ['meta_key', 'LIKE', 'company_name']
            ])->first();

            $car->companyName = $companyMeta ? $companyMeta->meta_value : '';

            // Replace car_name if carId is not 0
            if ($car->carId != 0) {
                $carDetails = cars::where('id', $car->carId)->first();
                $car->car_name = $carDetails ? $carDetails->name : $car->name;
            }

            return $car;
        });


        if(count($booked_cars)){
            return response()->json(['success' => true, 'error' => false, 'booked_cars'=> $bookedCarsWithImages,  'offset'=> $offset]);
        }

        else{
            return response()->json(['success' => false, 'error' => true, 'booked_cars'=>'',  'offset'=>$offset]);
        }

    }

    public function view(Request $request,$id)
    {

        if (!$id) {
            return response()->json(['success' => false, 'error' => true]);

        }
        else
        {

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



            // For Single Agent Image
            $thumbnails = Helper::getFeaturedSetCarPhotoById($carId);
            // $carImageUrl = asset('public/images/no_image.svg');
            $carImageUrl ='';


            foreach($thumbnails as $thumbnail){
                $image = Helper::getPhotoById($thumbnail->imageId);
                $carImageUrl = $image->url;
            }

                $modifiedImgUrl = $carImageUrl;

            // Convert to response:
            return response()->json([
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

   
    public function addPayment(Request $request)
    {
        $amount = $request->amount;

        // Trim and process the amount
        if ($amount) {
            $numericPart = preg_replace('/[^0-9.]/', '', $amount);
            $trimPriceAmount = round($numericPart);
        } else {
            $trimPriceAmount = 0;
        }

        // Proceed only if the request has required data
        if ($request) {
            $bookingPayment = booking_payments::create([
                'bookingId' => $request->bookingId,
                'received_refund' => $request->received_refund,
                'payment_name' => $request->payment_name,
                'other_payment_name' => $request->other_payment_name,
                'amount' => $trimPriceAmount,
            ]);

            // Fetch updated payments for the booking
            $bookingPayments = booking_payments::where('bookingId', $request->bookingId)->get();
            $carBooking = CarBooking::find($request->bookingId);

            $totalAmount = $carBooking->advance_booking_amount;
            $processedPayments = [];

            // Calculate total amount and prepare payments data
            foreach ($bookingPayments as $payment) {
                if ($payment->received_refund === 'received') {
                    $totalAmount += $payment->amount;
                } elseif ($payment->received_refund === 'refund') {
                    $totalAmount -= $payment->amount;
                }

                $processedPayments[] = [
                    'id' => $payment->id,
                    'payment_name' => $payment->payment_name === 'other' ? $payment->other_payment_name : $payment->payment_name,
                    'amount' =>$payment->amount,
                    'received_refund' => $payment->received_refund,
                    'created_at' => $payment->created_at,
                ];

                // $processedPayments[] = booking_payments::whereId($payment->id)->get();
            }

            return response()->json([
                'success' => true,
                'error' => false,
                'bookingPayments' => $processedPayments,
                'totalAmount' => $totalAmount,
            ]);
        } else {
            return response()->json(['success' => false, 'error' => true]);
        }
    }


    public function bookingCancel(Request $request)
    {

        // return response()->json([
        //     'success' => true,
        //     'error' => false,
        //     'request' => $request->toArray()
        // ], 200);


        // Validate request
        $request->validate([
            'bookingId' => 'required|integer|exists:car_bookings,id',
        ]);

        $bookingId = $request->bookingId;
        // return response()->json([
        //     'success' => true,
        //     'error' => false,
        //     'bookingId' => $bookingId
        // ], 200);


        try {
            // Update booking status
            $CancelBooking = CarBooking::where('id', $bookingId)
                ->update(['status' => 'canceled']);

            if ($CancelBooking) {
                return response()->json([
                    'success' => true,
                    'error' => false,
                    'msg' => 'Booking canceled successfully.'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'error' => true,
                'msg' => 'Failed to cancel booking. Please try again.'
            ], 500);
        } catch (\Exception $e) {
            // Catch unexpected exceptions
            return response()->json([
                'success' => false,
                'error' => true,
                'msg' => 'An unexpected error occurred. Please try again later.',
            ], 500);
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

    public function calendar(Request $request)
    {
        $userId = auth()->user()->id;

        $limit = $request->input('limit', 3);
        $offset = $request->input('offset', 0);

        $requestData = $request->all();

        // Fetching the request parameters for filtering
        // $car_type = $request->input('car_type', '');
        // $transmission = $request->input('transmission', '');

        $car_type = $requestData['car_type'] ?? '';
        $transmission = $requestData['transmission'] ?? '';

        $filterRequest = $requestData['filterRequest'] ?? '';

        if ($filterRequest) {
            // Initialize filter array with car_type and transmission
            // $formArray = [
            //     'car_type' => $car_type ? (is_array($car_type) ? $car_type : explode(',', $car_type)) : [],
            //     'transmission' => $transmission ? (is_array($transmission) ? $transmission : explode(',', $transmission)) : [],
            // ];

            $car_type = isset($requestData['car_type']) ? $requestData['car_type'] : '';
            $transmission = isset($requestData['transmission']) ? $requestData['transmission'] : '';

            $formArray = [
                'car_type'   => $requestData['car_type'] ?? [],
                'transmission'   => $requestData['transmission'] ?? [],
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
            )->where('cars.user_id', '=', $userId)->join('users', function ($join) {
                $join->on('users.id', '=', 'cars.user_id')->where('users.status', 'NOT LIKE', 'deleted');
            })->where('cars.status', 'NOT LIKE', 'deleted')->where(function ($query) use ($formArray, $userId) {


                // if (!empty($formArray['car_type'])) {
                //     $query->whereIn('cars.car_type', $formArray['car_type']);
                // }

                // if (!empty($formArray['transmission'])) {
                //     $query->whereIn('cars.transmission', $formArray['transmission']);
                // }


                if (!empty ($formArray['car_type'])  && (empty ($formArray['transmission']))) {
                    // echo 'only car_type';
                    $query->whereIn('cars.car_type', $formArray['car_type']);
                    // dd($query);
                }

                if (!empty ($formArray['transmission'])  && (empty ($formArray['car_type']))) {
                    // echo 'only transmission';
                    $query->whereIn('cars.transmission', $formArray['transmission']);
                    // dd($query);
                }

                // check with car_type with transmission
                if (!empty ($formArray['transmission']) && !empty ($formArray['car_type'])) {
                    // echo 'car_type with transmission';
                    $query->whereIn('cars.transmission', $formArray['transmission'])->whereIn('cars.car_type', $formArray['car_type']);
                    // dd($query);
                }
                // check with car_type with transmission
            })
            ->orderBy('cars.created_at', 'DESC')
            ->take(10) // You can change this to dynamically take based on pagination
            ->get();
        } else {
            // Default query if no filters are provided
            $cars = cars::where('status', '!=', 'deleted')
                ->where('user_id', $userId)
                ->orderByDesc('created_at')
                ->offset($offset)
                ->limit($limit)
                ->get();
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

        // Fetch bookings related to the cars
        $bookings = CarBooking::whereIn('carId', $carIds)
            ->where(function ($query) {
                $query->where('status', '=', 'delivered')
                    ->orWhereNull('status');
            })
            ->orderByDesc('created_at')
            ->get();

        // Get total car count for pagination or display
        $totalCars = cars::where('status', '!=', 'deleted')
            ->where('user_id', $userId)
            ->count();

        // Return the response with the filtered cars, bookings, and total count
        return response()->json([
            'success' => true,
            'error' => false,
            'cars' => $cars->makeVisible(['featureImageUrl']),
            'bookings' => $bookings,
            'totalCars' => $totalCars,
        ], 200);
    }


    public function calendarPost(Request $request)
    {

        if (strcmp($request->booking_type, 'normal') == 0) {
                $carId = $request->carId;
        }

        if (strcmp($request->booking_type, 'external') == 0) {
            $carId = 0;
        }

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

        if (strcmp($request->booking_type, 'external') == 0) {

            $per_day_rental_charges = $request->per_day_rental_charges;

            $total_booking_amount = $per_day_rental_charges * $roundedDays;

            $car_name = $request->car_name;

            $userId = auth()->user()->id;

            $registration_number = $request->registration_number;
        }

        $pickup_location = ($request->pickup_location == 'Other') ? $request->other_pickup_location : $request->pickup_location;
        $dropoff_location = ($request->dropoff_location == 'Other') ? $request->other_dropoff_location : $request->dropoff_location;

        $booking = CarBooking::create([
            'carId' => $carId,
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

        $carBooking = CarBooking::whereId($request->bookingId)->first();

        return response()->json(['success' => true, 'error' => false, 'carBooking'=> $carBooking, 'msg' => 'Customer details fetched...' ]);

    }

    public function edit(Request $request,$id){

        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'Booking ID is required.'
            ], 400);
        }

        $userId = auth()->user()->id;

        $car_booking = CarBooking::where('id', $id)->first();

        if (!$car_booking) {
            return response()->json([
                'success' => false,
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
                'message' => 'Car not found.'
            ], 404);
        }

        $partner = User::find($car_booking->user_id);

        if (!$partner) {
            return response()->json([
                'success' => false,
                'message' => 'Partner not found.'
            ], 404);
        }

        if ($userId != $car_booking->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }


        $drivers = Drivers::where('userId', '=', $userId)->get();

        return response()->json([
            'success' => true,
            'car_booking' => $car_booking,
            'car' => $car,
            'featuredImageUrl'=>$featuredImageUrl,
            'carId' => $carId,
            'drivers' => $drivers,
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
        // $trim_agent_commission = $this->sanitizeAmount($request->agent_commission);
        // $trim_agent_commission_received = $this->sanitizeAmount($request->agent_commission_received);

        // Handle pickup and dropoff locations
        // $pickup_location = ($request->pickup_location == 'Other') ? $request->other_pickup_location : $request->pickup_location;
        // $dropoff_location = ($request->dropoff_location == 'Other') ? $request->other_dropoff_location : $request->dropoff_location;

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
            'driver_id'=> $request->driver_id,
            'booking_remarks' => $request->booking_remarks,
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

    private function sanitizeAmount($amount)
    {
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

    public function getLocation(Request $request){
        $userId = Auth()->user()->id;
        $drivers = Drivers::where('userId', '=', $userId)->get();
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

         return response()->json(['locations'=>$locationArr,'drivers'=>$drivers]);

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

            return response()->json(['success' => true, 'error' => false, 'msg'=>'booking dates are updated','pickup_date'=>$pickup_date,'dropoff_date'=>$dropoff_date ]);

        }else{

            return response()->json(['success' => false, 'error' => true, 'msg'=>'carId is invalid' ]);

        }

    }

    public function updateBookingCalculation(Request $request)
    {

        $start_date = Carbon::parse($request->pickupDate);
        $start_time = $request->startTime;
        $end_date = Carbon::parse($request->dropOffDate);
        $end_time = $request->endTime;


        $pickup_date = $start_date->format('Y-m-d') . ' ' . $start_time;
        $dropoff_date = $end_date->format('Y-m-d') . ' ' . $end_time;

        $carId=$request->carId;
        $id=$request->currentBookingId;

        if($id){

            $carBookingUpdate= CarBooking::whereId($id)->update([

            ]);

            return response()->json(['success' => true, 'error' => false, 'msg'=>'calculations are updated' ]);

        }else{

            return response()->json(['success' => false, 'error' => true, 'msg'=>'carId is invalid' ]);

        }

    }


    public function statusChange(Request $request)
    {

        // dd('$request',$request);
        // echo "<pre>";
        // print_r($request->all());
        // echo "</pre>";



        // die;

        // dd("Status Change");

        date_default_timezone_set('Asia/Kolkata');
        $currentTimeDate = now();

        $currentDate = Carbon::now();

        $checkBookingStatus = CarBooking::where('id',$request->bookingId)->first();


        // return response()->json(['success' => false, 'error' => true, 'msg'=>false ,'bookingIdNew'=>$request->bookingId,'request'=> $request->toArray(),'checkBookingStatus' =>$checkBookingStatus]);

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

        // if(strcmp($request->checkStatus, 'list') == 0)
        // {

            $booked_cars = $this->getBookedCarsQuery()
            ->take($bookingLength)
            ->get();

        // }
        // elseif((strcmp($request->checkStatus, 'desktop_search') == 0) || (strcmp($request->checkStatus, 'mobile_search') == 0) )
        // {
        //     $booked_cars = $this->getBookedCarsWithFilter($request)
        //     ->take($bookingLength)
        //     ->get();
        // }
        // return response()->json(['success' => false, 'error' => true, 'msg'=>false ,'request'=> $booked_cars]);

        // dd($booked_car);

        if(count($booked_cars))
        {
            // $result =  $this->collectionOfBookedCars($booked_cars);
            // $desktopBookingHtml = $result['desktopBookingHtml'];
            // $mobileBookingHtml = $result['mobileBookingHtml'];

            return response()->json(['success' => true, 'error' => false,'successMsg'=> $successMsg , 'errorMsg'=> $errorMsg,  'booked_cars'=> $booked_cars  ]);

        }
        else
        {
            return response()->json(['success' => false, 'error' => true, 'msg'=>false ,'booked_cars'=> $booked_cars,'desktopBookingHtml'=>'','mobileBookingHtml'=>'' ]);
        }

    }

    ///// booking list filter //////
    public function listAjaxFilter(Request $request)
    {
        $currentDate = Carbon::now();

        $booked_cars = $this->getBookedCarsWithFilter($request)
        // ->take(5)
        ->get();

        // Attach images to the booked cars
        $bookedCarsWithImages = $booked_cars->map(function ($car) {
            $thumbnail = carImages::where('carId', $car->carId)
                    ->where('featured', 'set')
                    ->first();

                $car->imageUrl = $thumbnail
                    ? optional(master_images::find($thumbnail->imageId))->url
                    : '';

            // Replace car_name if carId is not 0
            if ($car->carId != 0) {
                $carDetails = cars::where('id', $car->carId)->first();
                $car->car_name = $carDetails ? $carDetails->name : $car->name;
            }    

                return $car;
            });

        if(count($booked_cars))
        {
            return response()->json(['success' => true, 'error' => false, 'booked_cars'=>$bookedCarsWithImages]);
        }
        else
        {
            return response()->json(['success' => false, 'error' => true, 'booked_cars'=>'',  ]);
        }
    }

/////////////////////////////////////////// ALL BOOKINGS //////////////////////////////


    ////// allBookings list start here  //////
    public function allBookings(Request $request)
    {
        $countBookedCars = $this->getAllBookedCarsQuery()
        ->count();

        $booked_cars = $this->getAllBookedCarsQuery()
        ->take(5)
        ->get();

        $allBookedCars = $this->getAllBookedCarsQuery()
        ->get();

        // Attach images to the booked cars
        $bookedCarsWithImages = $booked_cars->map(function ($car) {
            // Fetch the featured image for the car
            $thumbnail = carImages::where('carId', $car->carId)
                ->where('featured', 'set')
                ->first();

            $car->imageUrl = $thumbnail
                ? optional(master_images::find($thumbnail->imageId))->url
                : '';

            // Fetch the company name of the booking owner
            $companyMeta = UserMetas::where([
                ['userId', '=', $car->booking_owner_id],
                ['meta_key', 'LIKE', 'company_name']
            ])->first();

            $car->companyName = $companyMeta ? $companyMeta->meta_value : '';

            // Replace car_name if carId is not 0
            if ($car->carId != 0) {
                $carDetails = cars::where('id', $car->carId)->first();
                $car->car_name = $carDetails ? $carDetails->name : $car->name;
            }

            return $car;
        });

        return response()->json([
                'success' => true,
                'error' => false,
                'booked_cars' => $bookedCarsWithImages,
                'countBookedCars' => $countBookedCars,
                'allBookedCars' => $allBookedCars,
            ], 200);

        // return view('partner.bookings.all-bookings', compact('booked_cars','allBookedCars','countBookedCars'));
    }

    ////// loadmore list start here  //////
    public function allLoadMoreBookings(Request $request)
    {
        $currentDate = Carbon::now();

        $test = $request->checkLoadMore;

        $offset = $request->input('offset', 0);

            $booked_cars = $this->getAllBookedCarsQuery()
            ->skip($offset)
            ->take(5)
            ->get();


            // Attach images to the booked cars
            $bookedCarsWithImages = $booked_cars->map(function ($car) {
            // Fetch the featured image for the car
            $thumbnail = carImages::where('carId', $car->carId)
                ->where('featured', 'set')
                ->first();

            $car->imageUrl = $thumbnail
                ? optional(master_images::find($thumbnail->imageId))->url
                : '';

            // Fetch the company name of the booking owner
            $companyMeta = UserMetas::where([
                ['userId', '=', $car->booking_owner_id],
                ['meta_key', 'LIKE', 'company_name']
            ])->first();

            $car->companyName = $companyMeta ? $companyMeta->meta_value : '';

            // Replace car_name if carId is not 0
            if ($car->carId != 0) {
                $carDetails = cars::where('id', $car->carId)->first();
                $car->car_name = $carDetails ? $carDetails->name : $car->name;
            }

            return $car;
        });


        if(count($booked_cars)){
            return response()->json(['success' => true, 'error' => false, 'booked_cars'=> $bookedCarsWithImages,  'offset'=> $offset]);
        }

        else{
            return response()->json(['success' => false, 'error' => true, 'booked_cars'=>'',  'offset'=>$offset]);
        }




    }

    ///// booking list filter //////
    public function allListAjaxFilter(Request $request)
    {
        // echo "<pre>";
        // print_r($request->toArray());
        // echo "</pre>";
        // die;

        $currentDate = Carbon::now();
       
        $booked_cars = $this->getAllBookedCarsWithFilter($request)
        // ->take(5)
        ->get();

        // Attach images to the booked cars
        $bookedCarsWithImages = $booked_cars->map(function ($car) {
            $thumbnail = carImages::where('carId', $car->carId)
                    ->where('featured', 'set')
                    ->first();

                $car->imageUrl = $thumbnail
                    ? optional(master_images::find($thumbnail->imageId))->url
                    : '';
                    
                // Replace car_name if carId is not 0
                if ($car->carId != 0) {
                    $carDetails = cars::where('id', $car->carId)->first();
                    $car->car_name = $carDetails ? $carDetails->name : $car->name;
                }

                return $car;
            });

        if(count($booked_cars))
        {
            return response()->json(['success' => true, 'error' => false, 'booked_cars'=>$bookedCarsWithImages]);
        }
        else
        {
            return response()->json(['success' => false, 'error' => true, 'booked_cars'=> '']);
        }
    }
 

}
