<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [App\Http\Controllers\Api\auth\AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/logout', [App\Http\Controllers\Api\auth\AuthController::class, 'logout']);

    // bookings partner start
    Route::post('/car/booking/calendar',[App\Http\Controllers\Api\partner\BookingControllerL::class,'calendar']);
    Route::post('/car/booking/calender/checkLockedAndBooked', [App\Http\Controllers\Api\partner\BookingControllerL::class, 'checkLockedAndBooked']);
    Route::post('/car/booking/calendar/booked-locked-dates',[App\Http\Controllers\Api\partner\BookingController::class,'getAllBookedAndLockedDates']);
    Route::post('/car/booking/calender/booked-locked-dates-time',[App\Http\Controllers\Api\partner\BookingController::class,'getAllBookedAndLockedDatesWithTime']);
    Route::post('/car/booking/calender/check-locked-booked-dates', [App\Http\Controllers\Api\partner\BookingController::class, 'checkLockedAndBookedDates']);
    Route::post('/car/booking/date/update',[App\Http\Controllers\Api\partner\BookingController::class,'updateBookingDates']);
    Route::post('/car/booking/calendar/post', [App\Http\Controllers\Api\partner\BookingController::class, 'calendarPost']);
    Route::post('/car/booking/details', [App\Http\Controllers\Api\partner\BookingController::class, 'getBookingDetails']);
    Route::post('/car/details/{carId}/', [App\Http\Controllers\Api\partner\BookingControllerL::class, 'getCarDetails']);
    Route::post('/car/booking/edit/{id}/post', [App\Http\Controllers\Api\partner\BookingController::class, 'editPost']);
    Route::post('/car/booking/edit/{id}',[App\Http\Controllers\Api\partner\BookingController::class,'edit']);
    Route::post('/car/booking/locations', [App\Http\Controllers\Api\partner\BookingController::class, 'getLocation']);
    Route::post('/car/booking/active-list',[App\Http\Controllers\Api\partner\BookingController::class,'list']);
    Route::post('/car/booking/loadMoreBookings',[App\Http\Controllers\Api\partner\BookingController::class,'loadMoreBookings']);
    Route::post('/car/booking/update/status', [App\Http\Controllers\Api\partner\BookingController::class, 'statusChange']);
    Route::post('/car/booking/view/{id}', [App\Http\Controllers\Api\partner\BookingController::class, 'view']);
    Route::post('/car/booking/postFeaturedSetCarPhotoById/{carId}', [App\Http\Controllers\Api\partner\BookingController::class, 'getFeaturedSetCarPhotoById']);
    Route::post('/car/booking/postPhotoById/{imageId}', [App\Http\Controllers\Api\partner\BookingController::class, 'getPhotoById']);
    Route::post('/car/booking/view/addPayment/post', [App\Http\Controllers\Api\partner\BookingController::class, 'addPayment']);
    Route::post('/car/booking/all-bookings',[App\Http\Controllers\Api\partner\BookingController::class,'allBookings']);
    Route::post('/car/booking/allLoadMoreBookings',[App\Http\Controllers\Api\partner\BookingController::class,'allLoadMoreBookings']);
    Route::post('/booking/cancel', [App\Http\Controllers\Api\partner\BookingController::class, 'bookingCancel']);
    Route::post('/car/booking/listAjaxFilter',[App\Http\Controllers\Api\partner\BookingController::class,'listAjaxFilter']);
    Route::post('/car/booking/all/allListAjaxFilter',[App\Http\Controllers\Api\partner\BookingController::class,'allListAjaxFilter']);
    
    // SearchController
    Route::post('/car/booking/list/autocomplete', [App\Http\Controllers\Api\partner\searchController::class,'autocomplete']);
    Route::post('/car/booking/list/search', [App\Http\Controllers\Api\partner\searchController::class,'search']);
    Route::post('/car/booking/list/search/searchCustomerAndMob', [App\Http\Controllers\Api\partner\searchController::class,'searchCustomerAndMob']);
    Route::post('/car/booking/list/search/allSearchCustomerAndMob', [App\Http\Controllers\Api\partner\searchController::class,'allSearchCustomerAndMob']);
    // bookings partner end


    // cars partner start
    Route::post('/car/list',[App\Http\Controllers\Api\partner\CarsController::class,'list']);
    Route::post('/car/loadMoreCars',[App\Http\Controllers\Api\partner\CarsController::class,'loadMoreCars']);
    Route::post('/car/add',[App\Http\Controllers\Api\partner\CarsController::class,'add']);
    Route::post('/car/view/{id}',[App\Http\Controllers\Api\partner\CarsController::class,'view']);
    Route::post('/car/imageUrl/{id}',[App\Http\Controllers\Api\partner\CarsController::class,'imageUrl']);
    Route::post('/car/edit/{id}',[App\Http\Controllers\Api\partner\CarsController::class,'edit']);
    Route::post('/car/delete/{id}',[App\Http\Controllers\Api\partner\CarsController::class,'delete']);
    Route::post('/car/update/{id}',[App\Http\Controllers\Api\partner\CarsController::class,'update']);
    Route::post('/car/add/post',[App\Http\Controllers\Api\partner\CarsController::class,'store']);
    Route::post('/add/car/images',[App\Http\Controllers\Api\partner\CarsController::class,'uploadImage']);
    Route::post('/add/car/images/delete/{id}',[App\Http\Controllers\Api\partner\CarsController::class,'uploadImageDelete']);
    Route::post('/filter/cars',[App\Http\Controllers\Api\partner\CarsController::class,'ajaxFilter']);
    Route::post('/car/thumbnails/{id}',[App\Http\Controllers\Api\partner\CarsController::class,'fetchThumbnails']);
    // cars partner end

    // agent partner start
    Route::post('/agent/list',[App\Http\Controllers\Api\partner\AgentController::class,'list']);
    Route::post('/agent/list/ajax',[App\Http\Controllers\Api\partner\AgentController::class,'getAgentListAjax']);
    Route::post('/invite/agent/search',[App\Http\Controllers\Api\partner\AgentController::class,'agentSearch']);
    Route::post('/agent/share/cars/{agentId}', [App\Http\Controllers\Api\partner\AgentController::class,'selectShareCars']);
    Route::post('/share/cars', [App\Http\Controllers\Api\partner\AgentController::class,'shareCars']);
    Route::post('/unshare/cars', [App\Http\Controllers\Api\partner\AgentController::class,'unshareCars']);
    Route::post('/agent/preview/{id}',[App\Http\Controllers\Api\partner\AgentController::class,'preview']);
    Route::post('/agent/exit',[App\Http\Controllers\Api\partner\AgentController::class,'exit']);
    // agent partner end

    // leads partner start
    Route::post('/leads', [App\Http\Controllers\Api\partner\LeadsController::class, 'index']);
    Route::post('/leads/list/ajax',[App\Http\Controllers\Api\partner\LeadsController::class,'getLeadsListAjax']);
    Route::post('/leads/status/update/{id}',[App\Http\Controllers\Api\partner\LeadsController::class,'statusChange']);
    Route::post('/leads/add',[App\Http\Controllers\Api\partner\LeadsController::class,'add']);
    Route::post('/leads/add/post',[App\Http\Controllers\Api\partner\LeadsController::class,'addPost']);
    Route::post('/leads/view/{id}',[App\Http\Controllers\Api\partner\LeadsController::class,'view']);
    // leads partner start

    // Users partner start
    Route::post('/users/list',[App\Http\Controllers\Api\partner\UserController::class,'index']);
    Route::post('/users/list/ajax',[App\Http\Controllers\Api\partner\UserController::class,'getUserListAjax']);
    Route::post('/user/add',[App\Http\Controllers\Api\partner\UserController::class,'store']);
    Route::post('/user/edit/{id}',[App\Http\Controllers\Api\partner\UserController::class,'edit']);
    Route::post('/user/update/{id}',[App\Http\Controllers\Api\partner\UserController::class,'update']);
    Route::post('/user/delete/{id}',[App\Http\Controllers\Api\partner\UserController::class,'delete']);
    Route::post('/user/view/{id}',[App\Http\Controllers\Api\partner\UserController::class,'view']);
    // Users partner end

    // profile partner start
    Route::post('/view/profile', [App\Http\Controllers\Api\partner\ProfileController::class, 'profile']);
    Route::post('/edit/profile',[App\Http\Controllers\Api\partner\ProfileController::class,'EditProfile']);
    Route::post('/profile/update/{id}',[App\Http\Controllers\Api\partner\ProfileController::class,'ProfileUpdate']);
    Route::post('/profile/photo/update/{id}',[App\Http\Controllers\Api\partner\ProfileController::class,'ProfilePhotoUpdate']);
    Route::post('/profile/photo/remove',[App\Http\Controllers\Api\partner\ProfileController::class,'ProfilePhotoRemove']);
    // profile partner end

  ///////////////////////////////// AGENT API ///////////////////////////////////////////

   // Agent booking start
   Route::post('/agent/car/booking/calendar',[App\Http\Controllers\Api\agent\BookingControllerL::class,'calendar']);
   Route::post('/agent/car/booking/calender/checkLockedAndBooked', [App\Http\Controllers\Api\agent\BookingControllerL::class, 'checkLockedAndBooked']);
   Route::post('/agent/car/booking/calendar/booked-locked-dates',[App\Http\Controllers\Api\agent\BookingControllerL::class,'getAllBookedAndLockedDates']);
   Route::post('/agent/car/booking/calender/booked-locked-dates-time',[App\Http\Controllers\Api\agent\BookingControllerL::class,'getAllBookedAndLockedDatesWithTime']);
   Route::post('/agent/car/booking/calender/check-locked-booked-dates', [App\Http\Controllers\Api\agent\BookingControllerL::class, 'checkLockedAndBookedDates']);
   Route::post('/agent/car/booking/date/update',[App\Http\Controllers\Api\agent\BookingControllerL::class,'updateBookingDates']);
   Route::post('/agent/car/booking/calendar/post', [App\Http\Controllers\Api\agent\BookingControllerL::class, 'calendarPost']);
   Route::post('/agent/car/booking/details', [App\Http\Controllers\Api\agent\BookingControllerL::class, 'getBookingDetails']);
   Route::post('/agent/car/details/{carId}/', [App\Http\Controllers\Api\agent\BookingControllerL::class, 'getCarDetails']);
   Route::post('/agent/car/booking/edit/{id}/post', [App\Http\Controllers\Api\agent\BookingControllerL::class, 'editPost']);
   Route::post('/agent/car/booking/edit/{id}',[App\Http\Controllers\Api\agent\BookingControllerL::class,'edit']);
   Route::post('/agent/car/booking/locations', [App\Http\Controllers\Api\agent\BookingControllerL::class, 'getLocation']);

   Route::post('/agent/car/booking/active-list',[App\Http\Controllers\Api\agent\BookingController::class,'list']);
   Route::post('/agent/car/booking/loadMoreBookings',[App\Http\Controllers\Api\agent\BookingController::class,'loadMoreBookings']);
   Route::post('/agent/car/booking/update/status', [App\Http\Controllers\Api\agent\BookingController::class, 'statusChange']);
   Route::post('/agent/car/booking/view/{id}',[App\Http\Controllers\Api\agent\BookingController::class,'view']);
   Route::post('/agent/car/booking/getFeaturedSetCarPhotoById/{carId}', [App\Http\Controllers\Api\agent\BookingController::class, 'getFeaturedSetCarPhotoById']);
   Route::post('/agent/car/booking/getPhotoById/{imageId}', [App\Http\Controllers\Api\agent\BookingController::class, 'getPhotoById']);
   Route::post('/agent/car/booking/view/addPayment',[App\Http\Controllers\Api\agent\BookingController::class,'addPayment']);
   Route::post('/agent/car/booking/all-bookings',[App\Http\Controllers\Api\agent\BookingController::class,'allBookings']);
   Route::post('/agent/car/booking/allLoadMoreBookings', [App\Http\Controllers\Api\agent\BookingController::class, 'allLoadMoreBookings']);
   Route::post('/agent/booking/cancel', [App\Http\Controllers\Api\agent\BookingController::class, 'bookingCancel']);
   Route::post('/agent/car/booking/listAjaxFilter',[App\Http\Controllers\Api\agent\BookingController::class,'listAjaxFilter']);
   Route::post('/agent/car/booking/all/allListAjaxFilter',[App\Http\Controllers\Api\agent\BookingController::class,'allListAjaxFilter']);
   
  
   // SearchController
   Route::post('/agent/car/booking/list/autocomplete', [App\Http\Controllers\Api\agent\searchController::class,'autocomplete']);
   Route::post('/agent/car/booking/list/search', [App\Http\Controllers\Api\agent\searchController::class,'search']);
   Route::post('/agent/car/booking/list/search/searchCustomerAndMob', [App\Http\Controllers\Api\agent\searchController::class,'searchCustomerAndMob']);
   Route::post('/agent/car/booking/list/search/allSearchCustomerAndMob', [App\Http\Controllers\Api\agent\searchController::class,'allSearchCustomerAndMob']);
  // Agent booking end

  // cars agent start
  Route::post('/agent/car/list',[App\Http\Controllers\Api\agent\CarsController::class,'list']);
  Route::post('/agent/car/loadMoreCars',[App\Http\Controllers\Api\agent\CarsController::class,'loadMoreCars']);
  Route::post('/agent/car/add',[App\Http\Controllers\Api\agent\CarsController::class,'add']);
  Route::post('/agent/car/view/{id}',[App\Http\Controllers\Api\agent\CarsController::class,'view']);
  Route::post('/agent/car/imageUrl/{id}',[App\Http\Controllers\Api\agent\CarsController::class,'imageUrl']);
  Route::post('/agent/car/edit/{id}',[App\Http\Controllers\Api\agent\CarsController::class,'edit']);
  Route::post('/agent/car/delete/{id}',[App\Http\Controllers\Api\agent\CarsController::class,'delete']);
  Route::post('/agent/car/update/{id}',[App\Http\Controllers\Api\agent\CarsController::class,'update']);
  Route::post('/agent/car/add/post',[App\Http\Controllers\Api\agent\CarsController::class,'store']);
  Route::post('/agent/add/car/images',[App\Http\Controllers\Api\agent\CarsController::class,'uploadImage']);
  Route::post('/agent/add/car/images/delete/{id}',[App\Http\Controllers\Api\agent\CarsController::class,'uploadImageDelete']);
  Route::post('/agent/filter/cars',[App\Http\Controllers\Api\agent\CarsController::class,'ajaxFilter']);
  Route::post('/agent/car/thumbnails/{id}',[App\Http\Controllers\Api\agent\CarsController::class,'fetchThumbnails']);
  Route::post('/agent/car/list/ajax/ajaxShowCars',[App\Http\Controllers\Api\agent\CarsController::class,'ajaxShowCars']);
  // cars agent end

  // profile agent start
  Route::post('/agent/view/profile', [App\Http\Controllers\Api\agent\ProfileController::class, 'profile']);
  Route::post('/agent/edit/profile',[App\Http\Controllers\Api\agent\ProfileController::class,'EditProfile']);
  Route::post('/agent/profile/update/{id}',[App\Http\Controllers\Api\agent\ProfileController::class,'ProfileUpdate']);
  Route::post('/agent/profile/photo/update/{id}',[App\Http\Controllers\Api\agent\ProfileController::class,'ProfilePhotoUpdate']);
  Route::post('/agent/profile/photo/remove',[App\Http\Controllers\Api\agent\ProfileController::class,'ProfilePhotoRemove']);
  // profile agent end

  // partners
  Route::post('/agent/partner/list',[App\Http\Controllers\Api\agent\PartnerController::class,'list']);
  Route::post('/agent/partner/list/ajax',[App\Http\Controllers\Api\agent\PartnerController::class,'getPartnerListAjax']);
  Route::post('/agent/partner/preview/{id}',[App\Http\Controllers\Api\agent\PartnerController::class,'preview']);
  Route::post('/agent/partner/exit', [App\Http\Controllers\Api\agent\PartnerController::class, 'exit']);

});
