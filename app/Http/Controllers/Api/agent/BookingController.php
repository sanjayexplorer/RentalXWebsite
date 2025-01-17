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

class BookingController extends Controller
{
   
    private function checkFiltersCount($car_type,$transmission)
    {
        $count=0;
        if ($car_type != '') $count++;
        if ($transmission != '') $count++;
        return $count;
    }

    private function sanitizeAmount($amount)
    {
        $trimmed = preg_replace('/[₹,]+/', '', $amount);
        return is_numeric($trimmed) ? $trimmed : 0;
    }

    private function checkBookingStatus($carId, $pickupDate, $dropoffDate,$actionType)
    {

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

   /////////////////////////////////////////////////////////////////

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

    // protected function getBookedCarsWithFilter($request)
    // { 
    //     date_default_timezone_set('Asia/Kolkata');
    //     $oldcurrentDateTime = Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
    //     $currentDateTime = Carbon::parse($oldcurrentDateTime);

    //     $customerName = isset($request->customerName) ? $request->customerName : null;
    //     // $countryCode = isset($request->countryCode) ? $request->countryCode : null;
    //     $customerMobile = isset($request->customerMobile) ? $request->customerMobile : null;

    //     $agentId = auth()->user()->id;
        
    //     $requestData = $request->all();

    //     if (isset($requestData['form_data']))
    //     {
    //         parse_str($requestData['form_data'], $formArray);
    //         $start_date = $formArray['start_date'] ?? null;
    //         $end_date = $formArray['end_date'] ?? null;
    //         // $booking_ids = $formArray['booking_id'] ?? [];
    //         // for booking id give empty
    //         $formArray['booking_id'] = [];
    //     }

    //     return CarBooking::where('booking_owner_id','=',$agentId)->where(function ($query) use ($customerName, $customerMobile) {
    //             if ($customerName) {
    //                 $query->where('customer_name', '=', $customerName);
    //             }

    //             if ($customerMobile) {
    //                 $query->where('customer_mobile', '=', $customerMobile);
    //             }
    //         })
    //         ->where(function ($query) use ($formArray) {
    //             if (!empty($formArray['start_date']) && !empty($formArray['end_date'])) {
    //                 if (!empty($formArray['booking_id'])) {
    //                     $query->whereIn('id', $formArray['booking_id'])
    //                         ->whereDate('start_date', '>=', $formArray['start_date'])
    //                         ->whereDate('end_date', '<=', $formArray['end_date']);
    //                 }
    //                 else {
    //                     // Only start date and end date provided
    //                     $query->whereDate('start_date', '>=', $formArray['start_date'])->whereDate('end_date', '<=', $formArray['end_date']);
    //                 }
    //             } elseif (!empty($formArray['start_date']) && empty($formArray['end_date']) && !empty($formArray['booking_id'])) {
    //                 // Only start date provided with booking IDs
    //                 $query->whereIn('id', $formArray['booking_id'])->whereDate('start_date', '=', $formArray['start_date']);
    //             } elseif (!empty($formArray['start_date']) && empty($formArray['end_date']) && empty($formArray['booking_id'])) {
    //                 // Only start date provided
    //                 $query->where('start_date', '=', $formArray['start_date']);
    //             } elseif (empty($formArray['start_date']) && empty($formArray['end_date']) && !empty($formArray['booking_id'])) {
    //                 $query->whereIn('id', $formArray['booking_id']);
    //             }
    //         })
    //         ->where(function ($query) {
    //             $query->whereNull('status')->orWhere('status','LIKE','delivered');
    //         })
    //         ->orderByRaw("CASE
    //             WHEN status = 'delivered' THEN dropoff_date
    //             WHEN '$currentDateTime' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
    //             ELSE pickup_date
    //         END ASC");

    // }

    public function list(Request $request)
    {

        // dd("agent active LIst");
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

            return response()->json([
                'success' => true,
                'error' => false,
                'booked_cars' => $bookedCarsWithImages,
                'allBookedCars' => $allBookedCars,
                'countBookedCars' => $countBookedCars,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function loadMoreBookings(Request $request)
    {
        // dd($request->toArray());

            $offset = $request->input('offset', 0);

            $booked_cars = $this->getBookedCarsQuery()
            ->skip($offset)
            ->take(5)
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


        if(count($booked_cars)){
            return response()->json(['success' => true, 'error' => false, 'booked_cars'=> $bookedCarsWithImages,  'offset'=> $offset]);
        }

        else{
            return response()->json(['success' => false, 'error' => true, 'booked_cars'=>'',  'offset'=>$offset]);
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
        $agentId = auth()->user()->id;

        $sharedCarId = shareCars::where('agent_id', $agentId)->pluck('car_id');
        $cars = cars::whereIn('id', $sharedCarId)
            // ->where('status', '!=', 'deleted')
            ->orderBy('created_at', 'desc')
            ->get();

        date_default_timezone_set('Asia/Kolkata');
        $currentTimeDate = now();

  
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



        if(count($booked_cars))
        {


            return response()->json(['success' => true, 'error' => false,'successMsg'=> $successMsg , 'errorMsg'=> $errorMsg,  'booked_cars'=> $booked_cars  ]);

        }
        else
        {
            return response()->json(['success' => false, 'error' => true, 'msg'=>false ,'booked_cars'=> $booked_cars,'desktopBookingHtml'=>'','mobileBookingHtml'=>'' ]);
        }

    }

    public function view(Request $request, $id)
    {

        if (!$id) 
        {
        return response()->json(['success' => false, 'error' => true]);
        } 
        else 
        {
                        
            $userId = Auth::user()->id;
            $UserMetas=UserMetas::where('userId','=',$userId)->get();
            $car_booking = CarBooking::where('id', $id)->first();

            if (!$car_booking || (!$car_booking->booking_owner_id && $car_booking->booking_owner_id !== null)) {
                return response()->json(['success' => false, 'error' => true]);

            }
    
            if(strcmp($userId,$car_booking->booking_owner_id) !== 0){
                return response()->json(['success' => false, 'error' => true]);

            }

            $carId = $car_booking->carId;

            $car = cars::find($carId);

            $partner = User::find($car->user_id);

            $booking_payments = booking_payments::where('bookingId', $id)->get();
                  

            // For Single Agent Image
            $thumbnails = Helper::getFeaturedSetCarPhotoById($carId);
            $carImageUrl = asset('public/images/no_image.svg');


            foreach($thumbnails as $thumbnail){
                $image = Helper::getPhotoById($thumbnail->imageId);
                $carImageUrl = $image->url;
            }

                $modifiedImgUrl = $carImageUrl;

            // Convert to response:
            return response()->json([
                // 'drivers' => $drivers,
                'UserMetas' => $UserMetas,
                'car_booking' => $car_booking,
                'car' => $car,
                'carId' => $carId,
                'booking_payments' => $booking_payments,
                'partner' => $partner,
                // 'assignedDriver' => $assignedDriver,
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
        // Validate request
        $request->validate([
            'bookingId' => 'required|integer|exists:car_bookings,id',
        ]);

        $bookingId = $request->bookingId;

        try {
            // Update booking status
            $CancelBooking = CarBooking::where('id', $bookingId)->update(['status' => 'canceled']);

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
            // Handle unexpected exceptions
            return response()->json([
                'success' => false,
                'error' => true,
                'msg' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }
    }

    ///// booking list filter //////
    public function listAjaxFilter(Request $request)
    {
        try {
            $booked_cars = $this->getBookedCarsWithFilter($request)
                // ->take(5)
                ->get();

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

            if ($booked_cars->isNotEmpty()) {
                return response()->json(['success' => true, 'error' => false, 'booked_cars' => $bookedCarsWithImages]);
            } else {
                return response()->json(['success' => false, 'error' => true, 'booked_cars' => '', 'message' => 'No Booking found']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => true, 'message' => $e->getMessage()]);
        }
    }


    protected function getBookedCarsWithFilter($request)
    {
        date_default_timezone_set('Asia/Kolkata');
        $currentDateTime = Carbon::now('Asia/Kolkata');

        $customerName = $request->input('customerName', null);
        $customerMobile = $request->input('customerMobile', null);

        $agentId = auth()->user()->id;

        $requestData = $request->input('form_data', []);
        parse_str($requestData, $formArray);

        $start_date = $formArray['start_date'] ?? null;
        $end_date = $formArray['end_date'] ?? null;

        return CarBooking::where('booking_owner_id', '=', $agentId)
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
                    } else {
                        $query->whereDate('start_date', '>=', $formArray['start_date'])
                            ->whereDate('end_date', '<=', $formArray['end_date']);
                    }
                } elseif (!empty($formArray['start_date']) && empty($formArray['end_date']) && !empty($formArray['booking_id'])) {
                    $query->whereIn('id', $formArray['booking_id'])
                        ->whereDate('start_date', '=', $formArray['start_date']);
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
                WHEN '{$currentDateTime}' >= pickup_date AND status NOT LIKE 'delivered' THEN dropoff_date
                ELSE pickup_date
            END ASC");
    }


//////////////////////////////////////ALL BOOKINGS//////////////////////////////

    ////// allBookings list start here  //////
    public function allBookings(Request $request)
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

        return response()->json([
            'success' => true,
            'error' => false,
            'booked_cars' => $bookedCarsWithImages,
            'allBookedCars' => $allBookedCars,
            'countBookedCars' => $countBookedCars,
        ], 200);

        // return view('agent.bookings.all-bookings', compact('booked_cars','allBookedCars','countBookedCars'));
    }

    ////// loadmore list start here  //////
    public function allLoadMoreBookings(Request $request)
    {
        // dd("allLoadMorebBookings");
        $agentId = auth()->user()->id;

        $sharedCarId = shareCars::where('agent_id', $agentId)->pluck('car_id');

        $cars = cars::whereIn('id', $sharedCarId)
            // ->where('status', '!=', 'deleted')
            ->orderBy('created_at', 'desc')
            ->get();

        $partnerIds = $cars->pluck('user_id')->unique();

        // $test = $request->checkLoadMore;
        date_default_timezone_set('Asia/Kolkata');




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

            if(count($booked_cars)){
            return response()->json(['success' => true, 'error' => false, 'booked_cars'=> $bookedCarsWithImages,  'offset'=> $offset]);
        }

        else{
            return response()->json(['success' => false, 'error' => true, 'booked_cars'=>'',  'offset'=>$offset]);
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
        // ->take(5)
        ->get();


        // Attach Booked Cars With Images
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



        // dd(count($booked_cars));

        if(count($booked_cars))
        {
            return response()->json(['success' => true, 'error' => false, 'booked_cars' => $bookedCarsWithImages]);
        }
        else{
            return response()->json(['success' => false, 'error' => true, 'booked_cars'=> '' ]);
        }
    }



}