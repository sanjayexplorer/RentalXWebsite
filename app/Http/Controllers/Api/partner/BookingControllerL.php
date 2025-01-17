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
class BookingControllerL extends Controller
{
    public function calendar(Request $request)
    {
        $filtersCount = 0;
        
        $userId = auth()->user()->id;

        $limit = $request->input('limit', 3);

        $offset = $request->input('offset', 0);

        $requestData = $request->all();

        // Fetching the request parameters for filtering
        $car_type = $requestData['car_type'] ?? '';
        $transmission = $requestData['transmission'] ?? '';
        $filterRequest = $requestData['filterRequest'] ?? '';

        if ($filterRequest) {
            // Initialize filter array with car_type and transmission
            $car_type = isset($requestData['car_type']) ? $requestData['car_type'] : '';
            $transmission = isset($requestData['transmission']) ? $requestData['transmission'] : '';
            $formArray = [
                'car_type' => $requestData['car_type'] ?? [],
                'transmission' => $requestData['transmission'] ?? [],
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
                ->take(10) 
                ->get();
            $filtersCount= $this->checkFiltersCount($car_type,$transmission);

        } else {

            $cars = cars::where('status', '!=', 'deleted')
                ->where('user_id', $userId)
                ->orderByDesc('created_at')
                ->offset($offset)
                ->limit($limit)
                ->get();
        }

        $carIds = $cars->pluck('id')->toArray();

        $thumbnails = carImages::whereIn('carId', $carIds)
            ->where('featured', 'set')
            ->get()
            ->keyBy('carId');

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
    
        // $bookings = CarBooking::whereIn('carId', $carIds)
        // ->where(function ($query) use ($startDate, $endDate) {
        //     $query->whereBetween('start_date', [$startDate, $endDate])
        //           ->orWhereBetween('end_date', [$startDate, $endDate]);
        // })
        // ->where(function ($query) {
        //     $query->where('status', 'delivered')
        //           ->orWhereNull('status');
        // })
        // ->orderByDesc('created_at')
        // ->get();

        $bookings = CarBooking::whereIn('carId', $carIds)
        ->where(function ($query) {
            $query->where('status', '=', 'delivered')
                ->orWhereNull('status');
        })
        ->orderByDesc('created_at')
        ->get();

        $totalCars = cars::where('status', '!=', 'deleted')
            ->where('user_id', $userId)
            ->count();

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
    

        $plate_type = carsMeta::where([['carId', '=', $carDetails->id], ['meta_key', 'LIKE', 'plate_type']])->first();

        $responseData = [
            'carDetails' => $carDetails,
            'plate_type'=>$plate_type,
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

    public function checkLockedAndBooked(Request $request){

        $carId = $request->carId;
        $firstDate = $request->start_date;
        $lastDate = $request->end_date;
        $actionType=$request->actionType;


        $start_date = Carbon::parse($request->start_date);
        $start_time = $request->pickupTime;
        $end_date = Carbon::parse($request->end_date);
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
                return response()->json(['success' => true, 'error' => false]);
            } else {
                return response()->json(['success' => false, 'error' => true]);
        }

    }

}