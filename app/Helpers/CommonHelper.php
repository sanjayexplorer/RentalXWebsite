<?php
namespace App\Helpers;
use App\Models\UserMetas;
use App\Models\master_images;
use App\Models\carImages;
use App\Models\carsMeta;
use App\Models\cars;
use App\Models\CarBooking;
use App\Models\bookingMeta;
use App\Models\booking_payments;
use App\Models\CarsBookingDateStatus;
use DB;
class CommonHelper
{
   public static function getUserMeta($userId,$userMetaKey){
        $UserMetas=UserMetas::where([['userId','=',$userId],['meta_key','LIKE',$userMetaKey]])->first();
        return ($UserMetas)?$UserMetas->meta_value:'';
   }

   public static function getPhotoById($imageId)
   {
       return master_images::whereId($imageId)->first();
   }

    public static function getCarPhotoById($carId)
    {
        $carPhoto=carImages::where('carId','=',$carId)->get();
        return $carPhoto;
    }

    public static function getFeaturedSetCarPhotoById($carId)
    {
        $carPhoto=carImages::where('carId','=',$carId)->where('featured','set')->get();
        return $carPhoto;
    }

    public static function getCarMeta($carId,$carMetaKey){
        $CarMetas=carsMeta::where([['carId','=',$carId],['meta_key','LIKE',$carMetaKey]])->first();
        return ($CarMetas)?$CarMetas->meta_value:'';
   }

   public static function getCarStatusByCarId($carId){
        $car=cars::where('id','=',$carId)->where('status','NOT LIKE','deleted')->first();
        return($car)?$car->status:'';
   }

   public static function isBooked($carId,$date){
    $carBooking = CarBooking::where('carId', '=', $carId)
    ->where('start_date', '<=', $date)
    ->where('end_date', '>=', $date)
    ->where(function ($query) {
        $query->where('status', '=', 'delivered')
            ->orWhereNull('status');
    })
    ->first();

    return ($carBooking)?true:false;
   }

   public static function isLocked($carId,$date)
   {
    $carBooking = CarsBookingDateStatus::where('carId','=',$carId)->where('start_date','<=',$date)->where('end_date','>=',$date)->first();
    return ($carBooking)?true:false;
   }

   public static function overlapLockedOrNot($carId,$date)
   {

    $carBooking = CarBooking::where('carId','=',$carId)->where(function ($query) {
        $query->where('status', '=', 'delivered')
            ->orWhereNull('status');
    })->where('start_date','<=',$date)->where('end_date','>=',$date)->first();
    $lockedDates = CarsBookingDateStatus::where('carId','=',$carId)->where('start_date','<=',$date)->where('end_date','>=',$date)->first();

    return ($carBooking && $lockedDates)?true:false;
   }



    public static function getIsLocked($carId, $date)
    {
    $carBooking = CarsBookingDateStatus::where('carid', '=', $carId)->where('start_date', '<=', $date)->where('end_date', '>=', $date)->first();

    return $carBooking;
    }

    public static function getUserRoleName($userRole){
        if ($userRole) {
            $roleName = $userRole->name;
            return $roleName;
        }
    }

    public static function getCarNameByCarId($id)
    {
        $getCarName = cars::where('id', $id)->pluck('name')->first();

        return $getCarName;
    }

    public static function getCarRagisterationNoByCarId($id)
    {
        $getCarRagisteration = cars::where('id', $id)->pluck('registration_number')->first();

        return $getCarRagisteration;
    }

    public static function carBookingLockedCreate($carId,$firstDate,$lastDate)
    {
        if($carId&&$firstDate&&$lastDate){
            $carBooking=CarsBookingDateStatus::create([
                'carId'=>$carId,
                'start_date'=>$firstDate,
                'end_date'=>$lastDate,
            ]);
        }
    }

    public static function getBookingMeta($bookingId,$bookingMetaKey){
        $bookingMetas=bookingMeta::where([['bookingId','=',$bookingId],['meta_key','LIKE',$bookingMetaKey]])->first();
        return ($bookingMetas)?$bookingMetas->meta_value:'';
    }



    public static function getBookingMetaByCarId($carId, $bookingMetaKey){
    $carBooking = CarBooking::where('carId', $carId)->first();
    if ($carBooking) {
        $bookingId = $carBooking->id;
        $bookingMeta = BookingMeta::where([
            ['bookingId', '=', $bookingId],
            ['meta_key', 'LIKE', $bookingMetaKey]
        ])->first();
        return $bookingMeta ? $bookingMeta->meta_value : '';
    }
    return '';
    }


    public static function getBookingDataByCarId($carId,$date){
    $carBooking = CarBooking::where('carId', $carId)->where('start_date','<=',$date)->where('end_date','>=',$date)
    ->where(function ($query) {
            $query->where('status', '=', 'delivered')
            ->orWhereNull('status');
    })
    ->first();
        return $carBooking ? $carBooking : '';
    }

    public static function hasAdditionalBookingsByCarId($carId, $date) {
        $matchingBookingsCount = CarBooking::where('carId', $carId)->where(function ($query) {
            $query->where('status', '=', 'delivered')
            ->orWhereNull('status');
            })
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->count();
        return $matchingBookingsCount > 1;
    }


    public static function getAdditionalBookingsByCarId($carId, $date) {
        $matchingBookings = CarBooking::where('carId', $carId)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)->where(function ($query) {
            $query->where('status', '=', 'delivered')
            ->orWhereNull('status');
                }) ->latest('created_at')
            ->first();
            return $matchingBookings ? $matchingBookings : '';
           
    }


    public static function getBookingByCarId($carId){
        $carBooking = CarBooking::where('carId', $carId)->where(function ($query) {
            $query->where('status', '=', 'delivered')
                ->orWhereNull('status');
        })->first();
        return $carBooking ? $carBooking :false;
    }

    public static function getlockedDataByCarId($carId,$date){
        $lockedDates = CarsBookingDateStatus::where('carId', $carId)->where('start_date','<=',$date)->where('end_date','>=',$date)->first();
            return $lockedDates ? $lockedDates : '';
    }

    public static function getBookingPayments($bookingId,$bookingMetaKey){
        $bookingPaymentsMetas = booking_payments::where([['bookingId','=',$bookingId],['meta_key','LIKE',$bookingMetaKey]])->first();
        return ($bookingPaymentsMetas)?$bookingPaymentsMetas->meta_value:'';
    }

    public static function getUserMetaByCarId($carId, $userMetaKey){
            $car = cars::where('id', $carId)->first();
            if ($car) {
                $userId = $car->user_id;
                $userMeta = UserMetas::where([
                    ['userId', '=', $userId],
                    ['meta_key', 'LIKE', $userMetaKey]
                ])->first();
                return $userMeta ? $userMeta->meta_value : '';
            }
            return '';
        }
}
?>