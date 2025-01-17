<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {return redirect('/login');});
Route::group(['middleware'=> 'guest'],function(){
Route::get('/login',[App\Http\Controllers\Auth\LoginController::class,'index'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login.post');
Route::get('/otp',[App\Http\Controllers\Auth\LoginController::class,'otp'])->name('otp');
Route::post('/otp/check', [App\Http\Controllers\Auth\LoginController::class, 'otpCredential'])->name('otp.post');
Route::get('/profile',[App\Http\Controllers\Auth\LoginController::class,'profile'])->name('profile');
Route::post('/profile/post', [App\Http\Controllers\Auth\LoginController::class, 'profilePost'])->name('profile.post');

});

Route::group(
[
    'prefix'=>'agent','as'=>'agent.',
        'middleware' => [
            'auth','isAgent'
        ],
],
function () {
    // agent dashboard
    // Route::get('/dashboard',[App\Http\Controllers\agent\DashboardController::class,'index'])->name('dashboard');
    // partners
    // Route::get('/partner/list',[App\Http\Controllers\agent\PartnerController::class,'list'])->name('partner.list');
    // Route::get('/partner/list/ajax',[App\Http\Controllers\agent\PartnerController::class,'getPartnerListAjax'])->name('partner.list.ajax');
    // Route::get('/partner/add',[App\Http\Controllers\agent\PartnerController::class,'add'])->name('partner.add');
    // Route::post('/partner/add',[App\Http\Controllers\agent\PartnerController::class,'create'])->name('partner.add.post');
    // Route::get('/partner/edit/{id}',[App\Http\Controllers\agent\PartnerController::class,'edit'])->name('partner.edit');
    // Route::post('/partner/update/{id}',[App\Http\Controllers\agent\PartnerController::class,'update'])->name('partner.edit.post');
    // Route::post('/partner/delete{id}',[App\Http\Controllers\agent\PartnerController::class,'delete'])->name('partner.delete');
    // Route::post('/partner/delete/all',[App\Http\Controllers\agent\PartnerController::class,'deleteAll'])->name('partner.delete.all');
    // Route::get('/partner/view/{id}',[App\Http\Controllers\agent\PartnerController::class,'view'])->name('partner.view');
    // Route::post('/partner/photo',[App\Http\Controllers\agent\PartnerController::class,'photoUpload'])->name('partner.photo');
    // Route::post('/partner/uploadCompanyLogo{id}',[App\Http\Controllers\agent\PartnerController::class,'uploadCompanyLogo'])->name('partner.company.logo');

    //LeadsController
    Route::get('/leads/list',[App\Http\Controllers\agent\LeadsController::class,'list'])->name('leads.list');
    Route::get('/leads/list/ajax',[App\Http\Controllers\agent\LeadsController::class,'getUserListAjax'])->name('leads.list.ajax');
    Route::post('/leads/status/update/{id}',[App\Http\Controllers\agent\LeadsController::class,'statusChange'])->name('leads.status.update');
    Route::get('/leads/add',[App\Http\Controllers\agent\LeadsController::class,'add'])->name('leads.add');
    Route::post('/leads/add/post',[App\Http\Controllers\agent\LeadsController::class,'addPost'])->name('leads.add.post');

    Route::get('/leads/view/{id}',[App\Http\Controllers\agent\LeadsController::class,'view'])->name('leads.view');


    // cars
    Route::get('/car/list',[App\Http\Controllers\agent\CarsController::class,'list'])->name('car.list');
    Route::post('/car/list/ajax/ajaxShowCars',[App\Http\Controllers\agent\CarsController::class,'ajaxShowCars'])->name('car.ajaxShowCars');
    Route::get('/car/loadMoreCars',[App\Http\Controllers\agent\CarsController::class,'loadMoreCars'])->name('cars.load-more');
    Route::post('/partner/search',[App\Http\Controllers\agent\CarsController::class,'partner_search'])->name('partner.search');
    Route::get('/car/add',[App\Http\Controllers\agent\CarsController::class,'add'])->name('car.add');
    Route::post('/add/post',[App\Http\Controllers\agent\CarsController::class,'store'])->name('car.add.post');
    Route::get('/car/view/{id}',[App\Http\Controllers\agent\CarsController::class,'view'])->name('car.view');
    Route::get('/car/edit/{id}',[App\Http\Controllers\agent\CarsController::class,'edit'])->name('car.edit');
    Route::post('/car/update{id}',[App\Http\Controllers\agent\CarsController::class,'update'])->name('car.edit.post');
    Route::post('/add/car/images',[App\Http\Controllers\agent\CarsController::class,'uploadImage'])->name('upload.car.images');
    Route::post('/add/car/images/delete/{id}',[App\Http\Controllers\agent\CarsController::class,'uploadImageDelete'])->name('upload.car.images.delete');
    Route::post('/car/delete/{id}',[App\Http\Controllers\agent\CarsController::class,'delete'])->name('car.delete');
    Route::post('/car/add/color',[App\Http\Controllers\agent\CarsController::class,'ajaxColor'])->name('car.ajaxColor');
    Route::post('/car/add/getColorOptions',[App\Http\Controllers\agent\CarsController::class,'getColorOptions'])->name('car.getColorOptions');
    Route::post('/car/add/deleteColor',[App\Http\Controllers\agent\CarsController::class,'deleteColor'])->name('car.deleteColor');
  

    // booking
    Route::get('/car/booking/active-list',[App\Http\Controllers\agent\BookingController::class,'list'])->name('booking.list');
    Route::post('/car/booking/list/ajax',[App\Http\Controllers\agent\BookingController::class,'listAjaxFilter'])->name('booking.list.filter');
    Route::get('car/booking/calendar',[App\Http\Controllers\agent\BookingController::class,'calendar'])->name('booking.calendar');
    Route::get('car/booking/calendar/loadmore',[App\Http\Controllers\agent\BookingController::class,'loadMoreList'])->name('booking.calendar.load.more');
    Route::post('/car/booking/calendar/post/',[App\Http\Controllers\agent\BookingController::class,'calendarPost'])->name('booking.calendar.post');
    Route::get('/car/booking/add',[App\Http\Controllers\agent\BookingController::class,'add'])->name('booking.add');
    Route::post('/car/booking/add/post/{id}',[App\Http\Controllers\agent\BookingController::class,'addPost'])->name('booking.add.post');
    Route::get('/car/booking/detail/{id}',[App\Http\Controllers\agent\BookingController::class,'detail'])->name('booking.detail');
    Route::get('/car/booking/view/{id}',[App\Http\Controllers\agent\BookingController::class,'view'])->name('booking.view');
    Route::get('car/booking/edit/{id}',[App\Http\Controllers\agent\BookingController::class,'edit'])->name('edit.customer.details');
    Route::post('/car/booking/edit/{id}',[App\Http\Controllers\agent\BookingController::class,'editPost'])->name('edit.customer.details.post');
    Route::post('/car/booking/filter',[App\Http\Controllers\agent\BookingController::class,'ajaxDatesFilter'])->name('booking.filter');
    Route::post('/car/booking/set/time',[App\Http\Controllers\agent\BookingController::class,'setTimeCreate'])->name('booking.set.time');
    Route::post('/car/booking/delete/locked/status',[App\Http\Controllers\agent\BookingController::class,'deleteLockedDates'])->name('booking.delete.locked.status');
    Route::get('/car/booking/loadMoreBookings',[App\Http\Controllers\agent\BookingController::class,'loadMoreBookings'])->name('loadMoreBookings');
    Route::post('/car/booking/view/addPayment',[App\Http\Controllers\agent\BookingController::class,'addPayment'])->name('addPayment');
    Route::post('/car/booking/calender/checkLockedAndBooked',[App\Http\Controllers\agent\BookingController::class,'checkLockedAndBooked'])->name('checkLockedAndBooked');
    Route::post('/car/booking/calender/checkBooked',[App\Http\Controllers\agent\BookingController::class,'checkBooked'])->name('checkBooked');

    Route::post('/car/booking/calender/getAllBookedAndLocked',[App\Http\Controllers\agent\BookingController::class,'getAllBookedAndLockedDates'])->name('getAllBookedAndLockedDates');
   
    // checked booked and locked dates with time  
    Route::post('/car/booking/calender/checkLockedAndBookedDates',[App\Http\Controllers\agent\BookingController::class,'checkLockedAndBookedDates'])->name('checkLockedAndBookedDates');

    Route::post('/car/booking/calender/getAllBookedAndLockedWithTime',[App\Http\Controllers\agent\BookingController::class,'getAllBookedAndLockedDatesWithTime'])->name('getAllBookedAndLockedWithTime');

    Route::post('/car/booking/date/update',[App\Http\Controllers\agent\BookingController::class,'updateBookingDates'])->name('edit.booking.dates'); 
   
     // cancel booking
    Route::post('/booking/cancel', [App\Http\Controllers\agent\BookingController::class, 'bookingCancel'])->name('booking.cancel');
    // location json
    Route::post('booking/location', [App\Http\Controllers\agent\BookingController::class, 'getLocation'])->name('location.json');



    Route::get('/car/booking/list/clearAll',[App\Http\Controllers\agent\BookingController::class,'clearAll'])->name('clearAll');
    Route::post('/car/booking/update/status', [App\Http\Controllers\agent\BookingController::class, 'statusChange'])->name('change.booking.status');
    Route::post('/car/booking/update/view/status', [App\Http\Controllers\agent\BookingController::class, 'viewStatusChange'])->name('view.change.booking.status');

    Route::get('/car/booking/all-bookings',[App\Http\Controllers\agent\BookingController::class,'allBookings'])->name('booking.allBookings');
    Route::get('/car/booking/allLoadMoreBookings',[App\Http\Controllers\agent\BookingController::class,'allLoadMoreBookings'])->name('allLoadMoreBookings');
    Route::post('/car/booking/all/list/ajax',[App\Http\Controllers\agent\BookingController::class,'allListAjaxFilter'])->name('all.booking.list.filter');
    Route::get('/car/booking/all/list/clearAll',[App\Http\Controllers\agent\BookingController::class,'allClearAll'])->name('allClearAll');
    Route::post('/car/booking/all/update/status', [App\Http\Controllers\agent\BookingController::class, 'allStatusChange'])->name('all.change.booking.status');
   

    // searchController
    Route::get('/car/booking/list/autocomplete', [App\Http\Controllers\agent\searchController::class,'autocomplete'])->name('autocomplete');
    Route::get('/car/booking/list/search', [App\Http\Controllers\agent\searchController::class,'search'])->name('search');
    Route::get('/car/booking/list/autocompleteCustomerAndMob', [App\Http\Controllers\agent\searchController::class,'autocompleteCustomerAndMob'])->name('autocompleteCustomerAndMob');
    Route::get('/car/booking/list/search/searchCustomerAndMob', [App\Http\Controllers\agent\searchController::class,'searchCustomerAndMob'])->name('searchCustomerAndMob');
    Route::post('/car/booking/update/search/status', [App\Http\Controllers\agent\searchController::class,'searchStatusChange'])->name('search.change.booking.status');

    Route::get('/car/booking/list/all/autocompleteCustomerAndMob', [App\Http\Controllers\agent\searchController::class,'allAutocompleteCustomerAndMob'])->name('allAutocompleteCustomerAndMob');
    Route::get('/car/booking/list/search/all/searchCustomerAndMob', [App\Http\Controllers\agent\searchController::class,'allSearchCustomerAndMob'])->name('allSearchCustomerAndMob');
    Route::post('/car/booking/update/search/all/status', [App\Http\Controllers\agent\searchController::class,'allSearchStatusChange'])->name('all.search.change.booking.status');

    // partners
    Route::get('/partner/list',[App\Http\Controllers\agent\PartnerController::class,'list'])->name('partner.list');
    Route::get('/partner/list/ajax',[App\Http\Controllers\agent\PartnerController::class,'getPartnerListAjax'])->name('partner.list.ajax');

    Route::get('/partner/preview/{id}',[App\Http\Controllers\agent\PartnerController::class,'preview'])->name('partner.preview');
    Route::post('/partner/exit',[App\Http\Controllers\agent\PartnerController::class,'exit'])->name('partner.exit');


    Route::get('/view/profile',[App\Http\Controllers\agent\ProfileController::class,'profile'])->name('profile');
    Route::get('/edit/profile',[App\Http\Controllers\agent\ProfileController::class,'EditProfile'])->name('edit.profile');
    Route::post('/profile/update/{id}',[App\Http\Controllers\agent\ProfileController::class,'ProfileUpdate'])->name('profile.update');
    Route::post('/profile/photo/update{id}',[App\Http\Controllers\agent\ProfileController::class,'ProfilePhotoUpdate'])->name('profile.photo.update');
    Route::post('/profile/photo/remove',[App\Http\Controllers\agent\ProfileController::class,'ProfilePhotoRemove'])->name('profile.photo.remove');
    // logout
    Route::post('/logout',[App\Http\Controllers\agent\DashboardController::class,'logout'])->name('logout');

});


Route::group(
    [
    'prefix'=>'partner','as'=>'partner.',
    'middleware' => [
    'auth','isPartner'
    ],
    ],
  function() {

    // leads

    Route::get('/leads/list',[App\Http\Controllers\partner\LeadsController::class,'list'])->name('leads.list');
    Route::get('/leads/list/ajax',[App\Http\Controllers\partner\LeadsController::class,'getUserListAjax'])->name('leads.list.ajax');
    Route::post('/leads/status/update/{id}',[App\Http\Controllers\partner\LeadsController::class,'statusChange'])->name('leads.status.update');
    Route::get('/leads/add',[App\Http\Controllers\partner\LeadsController::class,'add'])->name('leads.add');
    Route::post('/leads/add/post',[App\Http\Controllers\partner\LeadsController::class,'addPost'])->name('leads.add.post');

    Route::get('/leads/view/{id}',[App\Http\Controllers\partner\LeadsController::class,'view'])->name('leads.view');



// partner dashboard
Route::get('/complete/profile', [App\Http\Controllers\Auth\LoginController::class, 'showCompleteProfileForm'])->name('complete.profile.show');
Route::post('/complete/profile/update/{id}', [App\Http\Controllers\Auth\LoginController::class, 'updateCompleteProfile'])->name('complete.profile.update');
Route::post('/complete/profile/photo/update/{id}', [App\Http\Controllers\Auth\LoginController::class, 'updateCompleteProfilePhoto'])->name('complete.profile.photo.update');
Route::post('/complete/profile/photo/remove/{id}', [App\Http\Controllers\Auth\LoginController::class, 'removeCompleteProfilePhoto'])->name('complete.profile.photo.remove');


// Route::get('/dashboard',[App\Http\Controllers\partner\DashboardController::class,'index'])->name('dashboard');
Route::get('/view/profile',[App\Http\Controllers\partner\ProfileController::class,'profile'])->name('profile');
Route::get('/edit/profile',[App\Http\Controllers\partner\ProfileController::class,'EditProfile'])->name('edit.profile');
Route::post('/profile/update/{id}',[App\Http\Controllers\partner\ProfileController::class,'ProfileUpdate'])->name('profile.update');
Route::post('/profile/photo/update{id}',[App\Http\Controllers\partner\ProfileController::class,'ProfilePhotoUpdate'])->name('profile.photo.update');
Route::post('/profile/photo/remove',[App\Http\Controllers\partner\ProfileController::class,'ProfilePhotoRemove'])->name('profile.photo.remove');
Route::post('/logout',[App\Http\Controllers\partner\DashboardController::class,'logout'])->name('logout');

// cars
Route::get('/car/list',[App\Http\Controllers\partner\CarsController::class,'list'])->name('car.list');
Route::get('/car/loadMoreCars',[App\Http\Controllers\partner\CarsController::class,'loadMoreCars'])->name('cars.load-more');
Route::get('/car/add',[App\Http\Controllers\partner\CarsController::class,'add'])->name('car.add');
Route::get('/car/view/{id}',[App\Http\Controllers\partner\CarsController::class,'view'])->name('car.view');
Route::get('/car/edit/{id}',[App\Http\Controllers\partner\CarsController::class,'edit'])->name('car.edit');
Route::post('/car/delete/{id}',[App\Http\Controllers\partner\CarsController::class,'delete'])->name('car.delete');
Route::post('/car/update{id}',[App\Http\Controllers\partner\CarsController::class,'update'])->name('car.edit.post');
Route::post('/add/post',[App\Http\Controllers\partner\CarsController::class,'store'])->name('car.add.post');
Route::post('/add/car/images',[App\Http\Controllers\partner\CarsController::class,'uploadImage'])->name('upload.car.images');
Route::post('/add/car/images/delete/{id}',[App\Http\Controllers\partner\CarsController::class,'uploadImageDelete'])->name('upload.car.images.delete');
Route::post('/filter/cars',[App\Http\Controllers\partner\CarsController::class,'ajaxFilter'])->name('car.ajax.filters');
Route::post('/car/add/color',[App\Http\Controllers\partner\CarsController::class,'ajaxColor'])->name('car.ajaxColor');
Route::post('/car/add/getColorOptions',[App\Http\Controllers\partner\CarsController::class,'getColorOptions'])->name('car.getColorOptions');
Route::post('/car/add/deleteColor',[App\Http\Controllers\partner\CarsController::class,'deleteColor'])->name('car.deleteColor');


// partners
Route::get('/agent/list',[App\Http\Controllers\partner\AgentController::class,'list'])->name('agent.list');
Route::get('/agent/list/ajax',[App\Http\Controllers\partner\AgentController::class,'getAgentListAjax'])->name('agent.list.ajax');
Route::get('/agent/add',[App\Http\Controllers\partner\AgentController::class,'add'])->name('agent.add');
Route::post('/invite/agent/search', [App\Http\Controllers\partner\AgentController::class,'agentSearch'])->name('invite.agent.search');
Route::get('agent/share/cars/{agentId}', [App\Http\Controllers\partner\AgentController::class,'selectShareCars'])->name('agent.share.cars');
Route::post('/share/cars', [App\Http\Controllers\partner\AgentController::class,'shareCars'])->name('share.cars');
Route::post('/unshare/cars', [App\Http\Controllers\partner\AgentController::class,'unshareCars'])->name('unshare.cars');
Route::get('/agent/preview/{id}',[App\Http\Controllers\partner\AgentController::class,'preview'])->name('agent.preview');
Route::post('/agent/exit',[App\Http\Controllers\partner\AgentController::class,'exit'])->name('agent.exit');

// booking
Route::get('/car/booking/active-list',[App\Http\Controllers\partner\BookingController::class,'list'])->name('booking.list');
Route::post('/car/booking/list/ajax',[App\Http\Controllers\partner\BookingController::class,'listAjaxFilter'])->name('booking.list.filter');
Route::get('car/booking/calendar',[App\Http\Controllers\partner\BookingController::class,'calendar'])->name('booking.calendar');

Route::get('/car/external/booking/add',[App\Http\Controllers\partner\BookingController::class,'ExternalBooking'])->name('external.booking.add');

Route::get('car/booking/calendar2',[App\Http\Controllers\partner\BookingController2::class,'calendar2'])->name('booking.calendar2');

Route::get('car/booking/calendar/loadmore',[App\Http\Controllers\partner\BookingController::class,'loadMoreList'])->name('booking.calendar.load.more');
Route::post('/car/booking/calendar/post/',[App\Http\Controllers\partner\BookingController::class,'calendarPost'])->name('booking.calendar.post');
Route::get('/car/booking/add',[App\Http\Controllers\partner\BookingController::class,'add'])->name('booking.add');
Route::post('/car/booking/add/post/{id}',[App\Http\Controllers\partner\BookingController::class,'addPost'])->name('booking.add.post');
Route::get('/car/booking/detail/{id}',[App\Http\Controllers\partner\BookingController::class,'detail'])->name('booking.detail');
Route::get('/car/booking/view/{id}',[App\Http\Controllers\partner\BookingController::class,'view'])->name('booking.view');
Route::get('/car/booking/edit/{id}',[App\Http\Controllers\partner\BookingController::class,'edit'])->name('edit.customer.details');

Route::post('/car/booking/edit/{id}',[App\Http\Controllers\partner\BookingController::class,'editPost'])->name('edit.customer.details');
Route::post('/car/booking/date/update',[App\Http\Controllers\partner\BookingController::class,'updateBookingDates'])->name('edit.booking.dates');
Route::post('/car/booking/filter',[App\Http\Controllers\partner\BookingController::class,'ajaxDatesFilter'])->name('booking.filter');
Route::post('/car/booking/set/time',[App\Http\Controllers\partner\BookingController::class,'setTimeCreate'])->name('booking.set.time');
Route::post('/car/booking/delete/locked/status',[App\Http\Controllers\partner\BookingController::class,'deleteLockedDates'])->name('booking.delete.locked.status');
Route::get('/car/booking/loadMoreBookings',[App\Http\Controllers\partner\BookingController::class,'loadMoreBookings'])->name('loadMoreBookings');
Route::post('/car/booking/view/addPayment',[App\Http\Controllers\partner\BookingController::class,'addPayment'])->name('addPayment');
Route::post('/car/booking/calender/checkLockedAndBooked',[App\Http\Controllers\partner\BookingController::class,'checkLockedAndBooked'])->name('checkLockedAndBooked');
Route::post('/car/booking/calender/checkLockedAndBookedDates',[App\Http\Controllers\partner\BookingController::class,'checkLockedAndBookedDates'])->name('checkLockedAndBookedDates');

Route::post('/car/booking/calender/checkBooked',[App\Http\Controllers\partner\BookingController::class,'checkBooked'])->name('checkBooked');

Route::post('/car/booking/calender/getAllBookedAndLocked',[App\Http\Controllers\partner\BookingController::class,'getAllBookedAndLockedDates'])->name('getAllBookedAndLockedDates');
Route::get('/car/booking/list/clearAll',[App\Http\Controllers\partner\BookingController::class,'clearAll'])->name('clearAll');
Route::post('/car/booking/update/status', [App\Http\Controllers\partner\BookingController::class, 'statusChange'])->name('change.booking.status');
Route::post('/car/booking/update/view/status', [App\Http\Controllers\agent\BookingController::class, 'viewStatusChange'])->name('view.change.booking.status');
Route::post('/car/booking/calender/getAllBookedAndLockedWithTime',[App\Http\Controllers\partner\BookingController::class,'getAllBookedAndLockedDatesWithTime'])->name('getAllBookedAndLockedWithTime');
Route::get('/car/booking/all-bookings',[App\Http\Controllers\partner\BookingController::class,'allBookings'])->name('booking.allBookings');
Route::get('/car/booking/allLoadMoreBookings',[App\Http\Controllers\partner\BookingController::class,'allLoadMoreBookings'])->name('allLoadMoreBookings');
Route::post('/car/booking/all/list/ajax',[App\Http\Controllers\partner\BookingController::class,'allListAjaxFilter'])->name('all.booking.list.filter');
Route::get('/car/booking/all/list/clearAll',[App\Http\Controllers\partner\BookingController::class,'allClearAll'])->name('allClearAll');
Route::post('/car/booking/all/update/status', [App\Http\Controllers\partner\BookingController::class, 'allStatusChange'])->name('all.change.booking.status');

// location json
Route::post('booking/location', [App\Http\Controllers\partner\BookingController::class, 'getLocation'])->name('location.json');


// calculations
Route::post('/car/booking/calculation/update',[App\Http\Controllers\partner\BookingController::class,'updateBookingCalculation'])->name('edit.booking.calculation');
// cancel booking
Route::post('/booking/cancel', [App\Http\Controllers\partner\BookingController::class, 'bookingCancel'])->name('booking.cancel');
//

// searchController
Route::get('/car/booking/list/autocomplete', [App\Http\Controllers\partner\searchController::class,'autocomplete'])->name('autocomplete');
Route::get('/car/booking/list/search', [App\Http\Controllers\partner\searchController::class,'search'])->name('search');
Route::get('/car/booking/list/autocompleteCustomerAndMob', [App\Http\Controllers\partner\searchController::class,'autocompleteCustomerAndMob'])->name('autocompleteCustomerAndMob');
Route::get('/car/booking/list/search/searchCustomerAndMob', [App\Http\Controllers\partner\searchController::class,'searchCustomerAndMob'])->name('searchCustomerAndMob');
Route::post('/car/booking/update/search/status', [App\Http\Controllers\partner\searchController::class,'searchStatusChange'])->name('search.change.booking.status');

Route::get('/car/booking/list/all/autocompleteCustomerAndMob', [App\Http\Controllers\partner\searchController::class,'allAutocompleteCustomerAndMob'])->name('allAutocompleteCustomerAndMob');
Route::get('/car/booking/list/search/all/searchCustomerAndMob', [App\Http\Controllers\partner\searchController::class,'allSearchCustomerAndMob'])->name('allSearchCustomerAndMob');
Route::post('/car/booking/update/search/all/status', [App\Http\Controllers\partner\searchController::class,'allSearchStatusChange'])->name('all.search.change.booking.status');


  ///////-------------- users---------------/////////
  Route::get('/users/list',[App\Http\Controllers\partner\UserController::class,'index'])->name('users.list');
  Route::get('/users/list/ajax',[App\Http\Controllers\partner\UserController::class,'getUserListAjax'])->name('users.list.ajax');
  Route::get('/user/add',[App\Http\Controllers\partner\UserController::class,'add'])->name('users.add');
  Route::post('/user/add',[App\Http\Controllers\partner\UserController::class,'store'])->name('users.add.post');
  Route::get('/user/edit/{id}',[App\Http\Controllers\partner\UserController::class,'edit'])->name('users.edit');
  Route::post('/user/update/{id}',[App\Http\Controllers\partner\UserController::class,'update'])->name('users.edit.post');

  Route::post('/user/delete/{id}',[App\Http\Controllers\partner\UserController::class,'delete'])->name('users.delete');
  Route::get('/user/view/{id}',[App\Http\Controllers\partner\UserController::class,'view'])->name('users.view');

});


Route::group(
    [
        'prefix'=>'admin','as'=>'admin.',
            'middleware' => [
                'auth','isAdmin'
            ],
    ],
    function () {
        // admin dashboard
        // users
        Route::get('/users/list',[App\Http\Controllers\admin\UserController::class,'index'])->name('users.list');
        Route::get('/users/list/ajax',[App\Http\Controllers\admin\UserController::class,'getUserListAjax'])->name('users.list.ajax');
        Route::get('/user/add',[App\Http\Controllers\admin\UserController::class,'add'])->name('users.add');
        Route::post('/user/add',[App\Http\Controllers\admin\UserController::class,'store'])->name('users.add.post');
        Route::get('/user/edit/{id}',[App\Http\Controllers\admin\UserController::class,'edit'])->name('users.edit');
        Route::post('/user/status/update/{id}',[App\Http\Controllers\admin\UserController::class,'statusChange'])->name('users.status.update');
        Route::post('/user/update/{id}',[App\Http\Controllers\admin\UserController::class,'update'])->name('users.edit.post');
        Route::post('/user/uploadCompanyLogo{id}',[App\Http\Controllers\admin\UserController::class,'uploadCompanyLogo'])->name('user.company.logo');
        Route::post('/user/delete/{id}',[App\Http\Controllers\admin\UserController::class,'delete'])->name('users.delete');
        Route::get('/user/view/{id}',[App\Http\Controllers\admin\UserController::class,'view'])->name('users.view');

        // logout
        Route::post('/logout',[App\Http\Controllers\admin\UserController::class,'logout'])->name('logout');

        // settings
        Route::get('/user/settings/profile/view',[App\Http\Controllers\admin\SettingsController::class,'index'])->name('users.settings.profile.view');
        Route::post('/user/settings/profile/edit/{id}',[App\Http\Controllers\admin\SettingsController::class,'edit'])->name('users.settings.profile.edit');
        //search controller for admin
        Route::get('/user/list/autocomplete', [App\Http\Controllers\admin\SearchController::class,'autocomplete'])->name('autocomplete');
        Route::get('/user/list/search', [App\Http\Controllers\admin\SearchController::class,'search'])->name('search');

    });
    