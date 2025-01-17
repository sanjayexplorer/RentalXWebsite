@extends('layouts.agent')
@section('title', 'Booking Details')
@section('content')
<style>
.edit_booking.active::after { content: ''; position: absolute; width: 0; height: 0; left: 0; width: 0; height: 0; border-left: 15px solid transparent; border-right: 15px solid transparent; border-bottom: 25px solid #fff; z-index: 5; right: 0; margin: 0 auto; top: 100%; }
.edit_booking.active::before { content: ''; position: absolute; top: -25px; left: -15px; border-left: 15px solid transparent; border-right: 15px solid transparent; border-bottom: 25px solid #000; /* Change the border color as needed */ z-index: 0; right: 0; margin: 0 auto; top: 100%; width: 0; height: 0; left: 0; }
.edit_booking_cta{ position: absolute; margin: 0 auto; /* top: 50px; */ /* left: 50%; */ transform: translateX(-50%); }
/* .edit_booking_cta{ width: 350px; }
.edit_booking_popup .edit_booking_cta{ display: block; position: absolute; left: 0; right: 0; margin: 0 auto; left: 50%; transform: translateX(-50%); }  */
/* .edit_booking::after { content: ''; position: absolute; width: 0; height: 0; left: 0; border-left: 15px solid transparent; border-right: 15px solid transparent; border-bottom: 25px solid #fff; z-index: 5; right: 0; margin: 0 auto; top: 100%; } */
.right_dashboard_section > .right_dashboard_section_inner { padding-top: 0px; }
.header_bar {display: none;}
.add_payment_fancybox{ border-radius: 10px; max-width: 500px; }
.add_payment_fancybox{ max-width: 432px; }
.add_payment_fancybox{ max-height:600px; }
.rupee_price_field{ position: relative; }
.head_title{margin-bottom: 25px;}
.rupee_price_field .rupee_icon {display: flex;align-items: center;}
  span.rupee_icon {position: absolute !important; top: 0; bottom: 0; margin: auto; left: 9px; height: 100%; text-align: center; font-size: 16px; color: #000; font-weight: 500; }
.add_payment_fancybox .fancybox-close-small { color: currentColor; padding: 3px; right: 15px; top: 17px; }
.customer_popup_container{ display: none; position: fixed; padding: 20px; left: 50%; top: 50%; transform: translate(-50%, -50%);  background-color: #fff; border-radius: 8px; }
.customer_popup_container .popup_container_inner{ border: 1px solid #8A8A8A; border-radius: 10px; padding: 10px; }
.active.customer_popup_container{ display: block; }
/* body.customer_popup { overflow: hidden; } */
.partner_popup_container{ display: none; position: fixed; padding: 20px; left: 50%; top: 50%; transform: translate(-50%, -50%); background-color: #fff; border-radius: 8px; }
.partner_popup_container .popup_container_inner{ border: 1px solid #8A8A8A; border-radius: 10px; padding: 15px; }
.active.partner_popup_container{ display: block; }
/* body.partner_popup { overflow: hidden; } */
.fancybox-can-pan .fancybox-content, .fancybox-can-swipe .fancybox-content{ cursor: default; }
.sub_heading{ padding: 5px;}
/* scroller  */ /* Styles for the scrollbar track */
.collection_of_sub_heading::-webkit-scrollbar-track { /* -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);  Adds a subtle shadow inside the scrollbar track */ border-radius: 10px;  /* Rounds the corners of the scrollbar track */ background-color: #F5F5F5;  /* Sets the background color of the scrollbar track */ }
/* Styles for the scrollbar itself */
.collection_of_sub_heading::-webkit-scrollbar { border-radius: 10px; width: 10px;  /* Sets the width of the scrollbar */ background-color: #F5F5F5;  /* Sets the background color of the entire scrollbar */ }
/* Styles for the scrollbar thumb (the draggable part) */
.collection_of_sub_heading::-webkit-scrollbar-thumb { border-radius: 10px;  /* Rounds the corners of the scrollbar thumb */ /* -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);  Adds a subtle shadow inside the scrollbar thumb */ background: #CCCCCC;  /* Sets the background color of the scrollbar thumb */ } /* end */
/* .customer_popup_container{ } */
@media screen and (max-width: 992px){
  .main_header_container{ display: none; }
  }
</style>

@php
$combinedStartDateTime = $car_booking->pickup_date;
$carbonStartDateTime = \Carbon\Carbon::parse($combinedStartDateTime);

$combinedEndDateTime = $car_booking->dropoff_date;
$carbonEndDateTime = \Carbon\Carbon::parse($combinedEndDateTime);

$formattedStartTime = $carbonStartDateTime->format('h:i A');
$formattedEndTime = $carbonEndDateTime->format('h:i A');

$formattedStartDate = $carbonStartDateTime->format('d M Y');
$formattedEndDate = $carbonEndDateTime->format('d M Y');

$f_formattedStartDate = $carbonStartDateTime->format('Y-m-d');
$f_formattedEndDate = $carbonEndDateTime->format('Y-m-d');
$dateDifference = $carbonEndDateTime->diff($carbonStartDateTime);
@endphp

@php
    $shortNamePartner = Helper::getUserMetaByCarId($carId, 'partner_short_name');
    $modifiedImgUrl = '';
    $uniqueBookingId = $car_booking->bookingId;
    $carName = ucwords(Helper::getCarNameByCarId($carId));
    $pickupLocation = ucfirst($car_booking->pickup_location);
    $dropOffLocation = ucfirst($car_booking->dropoff_location);
    $carbonFirstDate =  \Carbon\Carbon::parse($f_formattedStartDate);
    $carbonLastDate =  \Carbon\Carbon::parse($f_formattedEndDate);
    $diffInDays =  $carbonLastDate->diffInDays($carbonFirstDate);
    $rentalCharges = $car_booking->per_day_rental_charges;
    $rentalCharges = empty($rentalCharges) ? 0 : $rentalCharges;
    $numberOfDays = floatVal($car_booking->number_of_days);
    $totalDayRentalCharges = $rentalCharges * $numberOfDays;
    $pickupCharges = $car_booking->pickup_charges;
    $dropCharges =  $car_booking->dropoff_charges;
    $pickupCharges = is_numeric($pickupCharges) ? $pickupCharges : 0;
    $dropCharges = is_numeric($dropCharges) ? $dropCharges : 0;
    $discount=  is_numeric($car_booking->discount) ? $car_booking->discount : 0;
    $advanceAmt = $car_booking->advance_booking_amount;
    $dueAtDelivery = $car_booking->due_at_delivery;
    $dueAtDelivery = is_numeric($dueAtDelivery) ? $dueAtDelivery : 0;
    $securityDeposit = $car_booking->refundable_security_deposit;
    $securityDeposit = is_numeric($securityDeposit) ? $securityDeposit : 0;
    $total_payable_at_delivery = $dueAtDelivery;
    $bookingManagerName = Helper::getUserMetaByCarId($carId, 'primary_manager');
    $bookingManagerPhone = Helper::getUserMetaByCarId($carId, 'primary_manager_mobile');
    $primary_manager_country_code = Helper::getUserMetaByCarId($carId, 'primary_manager_country_code');
    $concatenatedbookingManagerPhone = $primary_manager_country_code . ' ' . $bookingManagerPhone;
    $bookingAssistantName = Helper::getUserMetaByCarId($carId, 'secondary_manager');
    $bookingAssistantPhone = Helper::getUserMetaByCarId($carId, 'secondary_manager_mobile');
    $secondary_manager_country_code = Helper::getUserMetaByCarId($carId, 'secondary_manager_country_code');
    $concatenatedbookingAssistantPhone = $secondary_manager_country_code . ' ' . $bookingAssistantPhone;
    $shortNamePartner = Helper::getUserMetaByCarId($carId, 'partner_short_name');
    $partnerCompanyName = ucwords(Helper::getUserMetaByCarId($carId, 'company_name'));
    $agentCompanyName   = ucwords(Helper::getUserMeta(auth()->user()->id, 'company_name'));
    $bookingData = Helper::getBookingByCarId($carId);
    $bookingRemarks = $car_booking->booking_remarks ? $car_booking->booking_remarks : '';
    $customer_name = ucfirst($car_booking->customer_name);
    $customer_mobile_country_code = $car_booking->customer_mobile_country_code;
    $customer_mobile = $car_booking->customer_mobile;
    $concatenatedNumber = $customer_mobile_country_code . ' ' . $customer_mobile;


    $ownerName = ucwords(Helper::getUserMeta(auth()->user()->id, 'owner_name'));
    $user_mobile_country_code = Helper::getUserMeta(auth()->user()->id, 'user_mobile_country_code');
    $ownerMobile = auth()->user()->mobile;
    $concatenatedOwnNumber = $user_mobile_country_code . ' ' . $ownerMobile;

    $agent_commission = $car_booking->agent_commission;
    $agent_commission = is_numeric($agent_commission) ? $agent_commission : 0;
    $agent_commission_received = $car_booking->agent_commission_received;
    $agent_commission_received = is_numeric($agent_commission_received) ? $agent_commission_received : 0;
    $registration_number = Helper::getCarRagisterationNoByCarId($carId);

    // Customer message
    $customer_message = "Booking No -".$shortNamePartner."#$uniqueBookingId\n\n"
        . "*Your Booking Details*\n"
        . "Car Name - $carName ({$numberOfDays} Days)\n"
        . "Car Registration Number - $registration_number\n"
        . "Pickup Date & Time - $formattedStartDate at $formattedStartTime\n"
        . "Drop Off Date & Time - $formattedEndDate at $formattedEndTime\n"
        . "Pickup Location - $pickupLocation\n"
        . "Drop Off Location - $dropOffLocation\n"
        . "\n*Rental Summary*\n"
        . "Car Rental - ₹" . number_format($rentalCharges) . " * " . $numberOfDays . " Days = ₹" . number_format($totalDayRentalCharges)."\n"
        . "Pickup Charges - ₹" . number_format($pickupCharges)."\n"
        . "Drop Charges - ₹" . number_format($dropCharges)."\n";
        if($discount)
        {
          $customer_message .="Discount - ₹" . number_format($discount)."\n";

        }
        if($advanceAmt){
           $customer_message .="Advance Booking Amt (Received) - ₹" . number_format($advanceAmt) . "\n"
        . "------\n";
        }

          $customer_message .=
          "Due Car rental charges - ₹" . number_format(($totalDayRentalCharges + $pickupCharges + $dropCharges - $discount) - $advanceAmt)."\n" .
          "Security Deposit (Payable during delivery) - ₹" . number_format($securityDeposit) . "\n" .
          "Total Payable at delivery - (₹" . number_format(($totalDayRentalCharges + $pickupCharges + $dropCharges - $discount) - $advanceAmt) . " + ₹" . number_format($securityDeposit) . ") ₹" . number_format(($totalDayRentalCharges + $pickupCharges + $dropCharges - $discount) - $advanceAmt + $securityDeposit) . "\n".
          "------\n";

        if($bookingManagerPhone || $bookingAssistantPhone)
        {
          $customer_message .="\n*Booking Manager*\n"
                            . "Booking Manager Details\n";
        }

        if($bookingManagerPhone)
        {
          $customer_message .=  "Booking Manager Name - $bookingManagerName\n"
                                ."Booking Manager Phone - $concatenatedbookingManagerPhone\n";
        }

        if($bookingAssistantPhone)
        {
            $customer_message .= "Booking Assistant Name - $bookingAssistantName\n"
                                ."Booking Assistant Phone - $concatenatedbookingAssistantPhone\n";
        }

        if($bookingManagerPhone || $bookingAssistantPhone)
        {
          $customer_message .="---------------------------\n";
        }


       $customer_message .=  "\n*Notes/Policies*\n"
        . "-As govt. Policies guest are not allowed to take self drive cars outside Goa borders. Heavy fine will be imposed if you take car outside Goa border without permission of booking manager.\n"

        . "-All cars come with unlimited km's facility. You can drive as much as you want within Goa.\n"

        . "-The guest will re-fuel the car from the nearest petrol pump and be advised to drop the car off with the same amount of fuel that we provided during the delivery of the car. If you return car with extra fuel then company wont be liable to pay for that extra fuel.\n"

        . "-The security refundable amount is payable at the time of car pickup and will be returned to you during car Dropoff time by booking manager.\n"

        . "-All the remaining booking amount needs to be paid during the time of car pickup/checkin time.\n"

        . "-Drink and Drive is strictly prohibited in Goa and govt. is very strict about that due to past incidents that happened in Goa. So it is advisable to drive carefully and return the car in the same condition.\n"

        . "-We will make a video of the car during the delivery and take pictures and you are also advised to do the same so there will be no hassle during the return of the car.\n"

        . "-Customer have to take care of all parking charges own at hotels, airport and commercial parkings.\n"

        . "-In case if you are coming at Dabolim Airport, you have to come outside Airport as Dabolim Airport authority doesn’t allow self drive cars at Departures gate.\n"

        . "-The guest will be asked to provide 1 original ID each car that will be returned during drop off of the car..\n"

        . "-Due to flight/train delays/cancellation if you are taking/dropping car during off timings( 12- 7.00 am ) then there will be extra 700 rs for waiting charge or early morning drop charge.\n"

        . "-If guest want to extend the car booking then inform booking manager 24 hours prior. Only after approval your booking will be extended depending on car availability.\n"

        . "\n"

        . "Thanks & Regards\n"
        . $agentCompanyName."\n"
        . $ownerName."\n"
        . $concatenatedOwnNumber."\n";


    $customer_encodedMessage = urlencode($customer_message);

    // PARTNER MESSAGE
    $partner_message = "Booking No -".$shortNamePartner."#$uniqueBookingId\n\n"
        . "Car Name - $carName ({$numberOfDays} Days)\n"
        . "Car Registration Number - $registration_number\n"
        . "Pickup Date & Time - $formattedStartDate at $formattedStartTime\n"
        . "Drop Off Date & Time - $formattedEndDate at $formattedEndTime\n"
        . "Pickup Location - $pickupLocation\n"
        . "Drop Off Location - $dropOffLocation\n"
        . "\n*Rental Summary*\n"
        . "Car Rental - ₹" . number_format($rentalCharges) . " * " . $numberOfDays . " Days = ₹" . number_format($totalDayRentalCharges)."\n"
        . "Pickup Charges - ₹" . number_format($pickupCharges)."\n"
        . "Drop Charges - ₹" . number_format($dropCharges)."\n";
        if($discount){
        $partner_message .="Discount - ₹" . number_format($discount)."\n";
        }

        if($advanceAmt){
           $partner_message .="Advance Booking Amt (Received) - ₹" . number_format($advanceAmt) . "\n"
        . "------\n";
        }

          $partner_message .=
          "$agentCompanyName Commission - ₹" . number_format($agent_commission) . "\n".
          "$agentCompanyName Commission received - ₹" . number_format($agent_commission_received) . "\n";
          if ($agent_commission > $agent_commission_received) {
              $partner_message .= "$partnerCompanyName to $agentCompanyName - ₹" . number_format($agent_commission - $agent_commission_received) ."\n------\n";
          } else if ($agent_commission < $agent_commission_received) {
              $partner_message .= "$agentCompanyName to $partnerCompanyName - ₹" . number_format($agent_commission_received - $agent_commission) . "\n------\n";
          }

          $partner_message .=
          "Due Car rental charges - ₹" . number_format(($totalDayRentalCharges + $pickupCharges + $dropCharges - $discount) - $advanceAmt)."\n" .
          "Security Deposit (Payable during delivery) - ₹" . number_format($securityDeposit) . "\n" .
          "Total Payable at delivery - (₹" . number_format(($totalDayRentalCharges + $pickupCharges + $dropCharges - $discount) - $advanceAmt) . " + ₹" . number_format($securityDeposit) . ") ₹" . number_format(($totalDayRentalCharges + $pickupCharges + $dropCharges - $discount) - $advanceAmt + $securityDeposit) . "\n";

          if(strcmp($customer_name,'')!==0 && strcmp($concatenatedNumber,'')!==0){
          $partner_message .=
          "------\n".
          "\n*Customer Details*\n" .
          "Customer Name - $customer_name\n" .
          "Customer Phone - $concatenatedNumber\n";
          }

        if ($bookingRemarks) {
            $partner_message .=
             "\n*Internal Notes*\n" .
             "$bookingRemarks";
        }

        $partner_encodedMessage = urlencode($partner_message);

@endphp

@php
    $thumbnails = Helper::getFeaturedSetCarPhotoById($carId);
    $carImageUrl = asset('images/no_image.svg');
@endphp

@foreach($thumbnails as $thumbnail)
  @php
    $image = Helper::getPhotoById($thumbnail->imageId);
    $carImageUrl = $image->url;
  @endphp
@endforeach

@php
    $modifiedImgUrl = $carImageUrl;
@endphp

@php
  date_default_timezone_set('Asia/Kolkata');
  $combinedStartDateTime = $car_booking->pickup_date;
  $carbonStartDateTime = \Carbon\Carbon::parse($combinedStartDateTime);
  $combinedEndDateTime = $car_booking->dropoff_date;
  $carbonEndDateTime = \Carbon\Carbon::parse($combinedEndDateTime);
  $startDate = $carbonStartDateTime instanceof \Carbon\Carbon ? $carbonStartDateTime->format('d M Y') :
  $carbonStartDateTime;
  $startTime = $carbonStartDateTime instanceof \Carbon\Carbon ? $carbonStartDateTime->format('h:i A') : '';
  $endDate = $carbonEndDateTime instanceof \Carbon\Carbon ? $carbonEndDateTime->format('d M Y') : $carbonEndDateTime;
  $endTime = $carbonEndDateTime instanceof \Carbon\Carbon ? $carbonEndDateTime->format('h:i A') : '';
  $daysDifference = $carbonStartDateTime instanceof \Carbon\Carbon && $carbonEndDateTime instanceof \Carbon\Carbon
  ? $carbonEndDateTime->diffInDays($carbonStartDateTime): 0;
  $formattedStartDateTime = $carbonStartDateTime->format('d M Y, h:i A');
  $formattedEndDateTime = $carbonEndDateTime->format('d M Y, h:i A');
  $formattedStartDate = $carbonStartDateTime->format('d M Y');
  $formattedEndDate = $carbonEndDateTime->format('d M Y');
  $formattedStartTime = $carbonStartDateTime->format('h:i A');
  $formattedEndTime = $carbonEndDateTime->format('h:i A');
  $currentTimeDate = now();
  $pickupTimeBeforeThirty = $carbonStartDateTime->copy()->subMinutes(30);
  $dropoffTimeBeforeThirty = $carbonEndDateTime->copy()->subMinutes(30);
@endphp

@if (Session::has('success'))
    <script>
        Swal.fire({
            title: "{{ Session::get('success') }}",
            icon: 'success',
            showCancelButton: false,
            confirmButtonText: 'OK',
            customClass: {
                popup: 'popup_updated',
                title: 'popup_title',
                actions: 'btn_confirmation',
            },
        }).then((result) => {
            if (result.isConfirmed) {
            }
        });
    </script>
    @endif
    @if (Session::has('errorMessage'))
    <script>
        Swal.fire({
            title: "{{ Session::get('errorMessage') }}",
            icon: 'error',
            showCancelButton: false,
            confirmButtonText: 'OK',
            customClass: {
                popup: 'popup_updated',
                title: 'popup_title',
                actions: 'btn_confirmation',
            },
        }).then((result) => {
            if (result.isConfirmed) {
            }
        });
    </script>
@endif
<div class="dashboard_ct_section">
    <!-- 1st Part -->
    <div class="lg:flex hidden lg:bg-white lg:mb-[0px] lg:px-[17px] lg:py-[15px] mb-[20px] justify-start items-center">
      <a href="{{ route('agent.booking.list') }}" class="links_item_cta inline-block p-2 mr-2 border border-[#C8C0C0] rounded-md hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
          <img class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
      </a>
      <span class="inline-block text-xl font-normal leading-normal align-middle text-black800">Booking Details</span>
    </div>

    <div class="pt-[42px] px-[36px] px-7 lg:px-[17px] lg:pt-[25px] lg:pb-[100px]  xl:py-10 bg-[#F6F6F6] pb-[100px]  min-h-screen">
      <div class="booking_head_sec">
        <div class="flex items-center mb-[36px] lg:mb-[20px] justify-between">

            <div class="lg:hidden flex flex-col">
              <div class="back-button">
                  <a href="{{ route('agent.booking.list') }}" class="links_item_cta inline-flex py-1 px-2 border border-[#C8C0C0] rounded-md bg-[#fff] hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
                      <img  class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
                      <span class="inline-block text-base leading-normal align-middle text-black800 ml-[10px] font-medium">
                          Bookings
                      </span>
                  </a>
              </div>
              <span class="inline-block text-[26px] font-normal leading-normal align-middle text-black800">
                Booking Details
              </span>
            </div>

            @if(strcmp($car_booking->status,'canceled')!==0 && strcmp($car_booking->status,'collected')!==0)
              <div class=" flex justify-end items-center lg:w-full w-1/2 relative">
                  <div class="flex justify-end mr-[15px] xl:mr-[10px] lg:mr-[15px] ">
                      <a href="{{route('agent.edit.customer.details',$car_booking->id)}}" id="edit_booking" class="links_item_cta relative inline-flex edit_booking rounded-[4px] items-center uppercase text-black text-base md:text-sm font-normal border border-siteYellow bg-siteYellow px-[20px] py-2.5 md:px-[15px] hover:bg-siteYellow400 transition-all duration-300 ease-in-out">
                          edit booking
                      </a>
                        {{-- pop up  --}}
                        <div class="flex justify-center hidden bg-white py-5 rounded-[10px] md:py-3 sm:top-[55px] md:top-[50px] top-[60px] edit_booking_cta sm:w-[330px] md:left-[50%] lg:left-[60%] left-[50%] w-[350px] border border-[#B1B1B1]  custom-shadow  mt-4">
                          <div class="w-full px-[14px] py-10">
                            <div class=" mb-2">
                              <a href="{{route('agent.edit.customer.details',$car_booking->id)}}"  class=" links_item_cta inline-flex text-[#000000]  rounded-[4px] items-center justify-center
                                  text-center w-full px-5 py-3 text-[14px] font-normal leading-tight transition-all duration-300 border border-siteYellow
                                  rounded-[4px] cursor-pointer bg-siteYellow  md:px-[20px] md:py-[14px] sm:text-sm sm:py-[12px]
                                  transition-all duration-500 ease-0 hover:bg-[#d7b50a]  disabled:opacity-40 disabled:cursor-not-allowed uppercase">
                                      edit customer details
                              </a>
                            </div>
                            <div class="pt-2">
                              <a href="javascript:void(0);"  data-src="#open_popup" class=" inline-flex text-[#000000]  rounded-[4px] items-center justify-center edit_date_and_time
                                  text-center w-full px-5 py-3 text-[14px] font-normal leading-tight transition-all duration-300 border border-siteYellow
                                  rounded-[4px] cursor-pointer bg-siteYellow  md:px-[20px] md:py-[14px] sm:text-sm sm:py-[12px]
                                  transition-all duration-500 ease-0 hover:bg-[#d7b50a]  disabled:opacity-40 disabled:cursor-not-allowed uppercase">
                                    Edit Date & Time
                              </a>
                            </div>

                          </div>
                        </div>
                  </div>
                  <div class="flex justify-end ml-[15px]">
                    <a href="javascript:void(0);" id="cancelBookingBtn" class="inline-flex rounded-[4px] items-center uppercase text-[#BD0000] text-base md:text-sm font-normal border border-transparent bg-[#FFD3D3] px-[20px] py-2.5 md:px-[15px] hover:bg-[#ffe4e4] hover:border hover:border-[#BD0000]   transition-all duration-300 ease-in-out">
                        cancel booking
                    </a>
                  </div>

              </div>
            @endif
        </div>
      </div>

      <!-- ///////////////////////////////////////////////////////////// -->
      <div class="max-w-[686px] lg:max-w-[100%]">
        <div class="bg-white  rounded-[10px] mb-14">
          <div class="px-4 pt-5">
            <div class="flex justify-center items-center">
              <div class="w-[90%]">
                <div class="flex items-center flex-wrap gap-[5px]">
                  @php
                    // FOR TODAY/TOMORROW
                    $PickupDate = $carbonStartDateTime->format('Y-m-d');
                    $DropoffDate = $carbonEndDateTime->format('Y-m-d');

                    $currentDate = \Carbon\Carbon::now('Asia/Kolkata')->format('Y-m-d');

                    $diffPickAndCurrentDate = strtotime($PickupDate) - strtotime($currentDate);
                    $pickupDaysGap = floor($diffPickAndCurrentDate / (60 * 60 * 24));

                    // Convert seconds to days and round down
                    $diffDropAndCurrentDate = strtotime($DropoffDate) - strtotime($currentDate);
                    $dropDaysGap = floor($diffDropAndCurrentDate / (60 * 60 * 24));

                    $nowDateTime = \Carbon\Carbon::now('Asia/Kolkata')->format('d M Y, h:i A');

                    $nowDateTimeObject = \Carbon\Carbon::parse($nowDateTime);
                    $nowCarbonStartDateTime = \Carbon\Carbon::parse($carbonStartDateTime);
                    $nowCarbonEndDateTime = \Carbon\Carbon::parse($carbonEndDateTime);

                  @endphp

                  @if($currentTimeDate >= $pickupTimeBeforeThirty && (strcmp($car_booking->status, 'delivered') !== 0) && (strcmp($car_booking->status, 'collected') !== 0)  && (strcmp($car_booking->status, 'canceled') !== 0) )
                  <div class="flex items-center justify-center mr-[32px] 3xl:mr-[15px]">
                      <div class="block">
                          <img src="{{asset('images/time-clock-img.svg')}}" alt="clock" class="w-[19px]">
                      </div>
                      <div class="w-full ml-[6px]">
                              <span class="block text-siteYellow800 text-xs font-normal leading-normal">
                                  Pickup: {{$formattedStartDate}}, {{$formattedStartTime}}
                              </span>
                      </div>
                  </div>

                  <div class="text-xs ">
                      <span class="uppercase status_time border border-[#fca728] rounded-[12px]
                      text-[#fca728] px-[10px] py-[2px] 3xl:py-[1px] bg-[#FFFFFF]">Delivery Awaited
                      </span>
                  </div>

              @elseif($currentTimeDate >= $dropoffTimeBeforeThirty && (strcmp($car_booking->status, 'delivered') == 0) && (strcmp($car_booking->status, 'collected') !== 0) && (strcmp($car_booking->status, 'canceled') !== 0) )
                  <div class="flex items-center justify-center mr-[32px] 3xl:mr-[15px]">
                          <div class="block">
                              <img src="{{asset('images/time-clock-img.svg')}}" alt="clock" class="w-[19px]">
                          </div>
                          <div class="w-full ml-[6px]">
                              <span class="block text-siteYellow800 text-xs font-normal leading-normal">
                                  Drop-off: {{$formattedEndDate}}, {{$formattedEndTime}}
                              </span>
                          </div>
                  </div>

                  <div class="text-xs ">
                      <span class="uppercase status_time border border-[#79CEE9] rounded-[12px]
                      text-[#79CEE9] px-[10px] py-[2px] 3xl:py-[1px] bg-[#FFFFFF] ">Collection Awaited</span>
                  </div>

                    <!-- pickup  -->
                    @elseif(($pickupDaysGap == 0) && ($nowDateTimeObject <= $nowCarbonStartDateTime) && (strcmp($car_booking->status, 'collected') !== 0) && (strcmp($car_booking->status, 'canceled') !== 0) )
                        <div class="flex items-center justify-center">
                        <div class="block">
                            <img src="{{asset('images/time-clock-img.svg')}}" alt="clock" class="w-[19px]">
                            </div>
                                <div class="w-full ml-[6px]">
                                    <span class="block text-siteYellow800 text-xs font-normal leading-normal">Pickup: {{$formattedStartTime}}</span>
                                </div>
                            </div>

                        <div class="text-xs ml-[32px] 3xl:ml-[25px]">
                            <span class="uppercase status_time border border-[#CE3A3A] rounded-[12px] text-[#CE3A3A] px-[10px] py-[2px] 3xl:py-[1px]">today</span>
                        </div>

                    @elseif(($pickupDaysGap == 1) && ($nowDateTimeObject <= $nowCarbonStartDateTime) && (strcmp($car_booking->status, 'collected') !== 0) && (strcmp($car_booking->status, 'canceled') !== 0))
                                    <div class="flex items-center justify-center">
                                        <div class="block">
                                            <img src="{{asset('images/time-clock-img.svg')}}" alt="clock" class="w-[19px]">
                                        </div>

                                        <div class="w-full ml-[6px]">
                                            <span class="block text-siteYellow800 text-xs font-normal leading-normal">Pickup: {{$formattedStartTime}}</span>
                                        </div>
                                    </div>

                                  <div class="text-xs ml-[32px] 3xl:ml-[25px]">
                                      <span class="uppercase status_time border border-[#5DB47F] rounded-[12px] text-[#5DB47F] px-[10px] py-[2px] 3xl:py-[1px]">Tomorrow</span>
                                </div>
                     <!-- drop-off -->
                      @elseif(($dropDaysGap == 0) && ($nowDateTimeObject <= $nowCarbonEndDateTime) && (strcmp($car_booking->status, 'collected') !== 0) && (strcmp($car_booking->status, 'canceled') !== 0))
                                    <div class="flex items-center justify-center">
                                        <div class="block">
                                            <img src="{{asset('images/time-clock-img.svg')}}" alt="clock" class="w-[19px]">
                                        </div>
                                        <div class="w-full ml-[6px]">
                                            <span class="block text-siteYellow800 text-xs font-normal leading-normal">Drop-off: {{$formattedEndTime}}</span>
                                        </div>
                                    </div>

                                    <div class="text-xs ml-[32px] 3xl:ml-[25px]">
                                        <span class="uppercase status_time border border-[#CE3A3A] rounded-[12px] text-[#CE3A3A] px-[10px] py-[2px] 3xl:py-[1px]">today</span>
                                    </div>

                      @elseif(($dropDaysGap == 1) && ($nowDateTimeObject <= $nowCarbonEndDateTime) && (strcmp($car_booking->status, 'collected') !== 0) && (strcmp($car_booking->status, 'canceled') !== 0) )
                                    <div class="flex items-center justify-center">
                                        <div class="block">
                                            <img src="{{asset('images/time-clock-img.svg')}}" alt="clock" class="w-[19px]">
                                        </div>

                                        <div class="w-full ml-[6px]">
                                            <span class="block text-siteYellow800 text-xs font-normal leading-normal">Drop-off: {{$formattedEndTime}}</span>
                                        </div>
                                    </div>

                                    <div class="text-xs ml-[32px] 3xl:ml-[25px]">
                                        <span class="uppercase status_time border border-[#5DB47F] rounded-[12px] text-[#5DB47F] px-[10px] py-[2px] 3xl:py-[1px]">Tomorrow</span>
                                    </div>
                    @elseif(($pickupDaysGap>1) && (strcmp($car_booking->status, 'collected') !== 0) && (strcmp($car_booking->status, 'canceled') !== 0) )
                                    <div class="flex items-center justify-center">
                                        <div class="w-full ml-[6px]">
                                            <span class="block text-[#3D3D3D] text-xs font-normal leading-normal">Pickup: {{$formattedStartDate}}, {{$formattedStartTime}}</span>
                                        </div>
                                    </div>
                    @elseif(($dropDaysGap>1) && (strcmp($car_booking->status, 'collected') !== 0) && (strcmp($car_booking->status, 'canceled') !== 0) )
                            <div class="flex items-center justify-center">
                                        <div class="w-full ml-[6px]">
                                            <span class="block text-[#3D3D3D] text-xs font-normal leading-normal">Drop-off: {{$formattedEndDate}}, {{$formattedEndTime}}</span>
                                        </div>
                           </div>

                    @elseif((strcmp($car_booking->status, 'collected') == 0)  && (strcmp($car_booking->status, 'canceled') !== 0) )
                          <div class="text-xs ">
                              <span class="uppercase status_time border border-[#25BE00] rounded-[12px]
                              text-[#25BE00] px-[10px] py-[2px] 3xl:py-[1px] bg-[#FFFFFF] ">Booking Completed
                              </span>
                          </div>
                    @elseif( (strcmp($car_booking->status, 'canceled') == 0) )
                          <div class="text-xs ">
                              <span class="uppercase status_time border border-[#BD0000] rounded-[12px]
                              text-[#BD0000] px-[10px] py-[2px] 3xl:py-[1px] bg-[#FFFFFF] ">Booking Canceled
                              </span>
                          </div>
                    @endif

                    </div>
              </div>

              <!-- <div class="w-[10%] text-right flex justify-end">
                <div class="w-5 h-5 inline-block">
                  <img class="w-5 h-5 block" src="{{asset('images/check_right_icon.svg')}}" alt="img">
                </div>
              </div> -->

            </div>
          </div>
          <div class=" md:px-8 px-10 pb-9 pt-3 ">
            <div class="flex">
              <div class="flex justify-center w-[45%] md:w-[50%] booking_main_inner_left min-w-[174px] sm:min-w-[149px] md:pr-[20px]">
                <div class="max-h-[200px] min-w-[174px] sm:max-h-[137px] sm:min-w-[131px] sm:max-w-[131px]">
                  <div class="flex flex-col items-center justify-center w-full h-full overflow-hidden  car_image_container">
                    @if($modifiedImgUrl)
                    <img src="{{$modifiedImgUrl}}" alt="creta" class="object-contain max-w-full max-h-full">
                    @endif
                  </div>
                </div>
              </div>

              <div class="w-[55%] md:w-[50%] flex text-left booking_main_inner_right md:pl-[0px] pl-[15px] items-center">
                <div class="car_details ">
                 <div class="md:pl-0 pl-10">
                  <h3 class="text-base font-medium leading-4 text-[#2B2B2B] pb-1 capitalize">
                  {{$car->name}}</h3>
                   <p class="text-sm font-normal leading-none text-[#666666] pb-1.5 last:pb-0 uppercase">
                  {{$car->registration_number}}</p>
                  @if(strcmp($car_booking->customer_name,'')!==0 && strcmp($car_booking->customer_mobile_country_code,'')!==0 && strcmp($car_booking->customer_mobile,'')!==0)
                  <div class="md:py-5 py-8">
                    <div class="flex items-center ">
                      <div class="w-4 h-4">
                        <svg class="block w-4 h-4" width="10" height="11" viewBox="0 0 10 11" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M4.6875 0C5.2832 0 5.83008 0.14649 6.32812 0.43946C6.83594 0.73243 7.23633 1.13282 7.5293 1.64063C7.82227 2.13868 7.9688 2.68555 7.9688 3.28125C7.9688 3.83789 7.83691 4.35547 7.57324 4.83399C7.31934 5.3125 6.96777 5.70313 6.51855 6.00586C7.0752 6.25 7.56836 6.58692 7.998 7.01661C8.4375 7.44629 8.7744 7.93946 9.0088 8.4961C9.2529 9.0723 9.375 9.6777 9.375 10.3125H8.4375C8.4375 9.6289 8.2666 9.0039 7.9248 8.4375C7.59277 7.86133 7.13867 7.40723 6.5625 7.0752C5.99609 6.7334 5.37109 6.5625 4.6875 6.5625C4.00391 6.5625 3.37402 6.7334 2.79785 7.0752C2.23145 7.40723 1.77734 7.86133 1.43555 8.4375C1.10352 9.0039 0.9375 9.6289 0.9375 10.3125H0C0 9.6777 0.12207 9.0723 0.36621 8.4961C0.60059 7.93946 0.93262 7.44629 1.3623 7.01661C1.80176 6.58692 2.2998 6.25 2.85645 6.00586C2.40723 5.70313 2.05078 5.3125 1.78711 4.83399C1.5332 4.35547 1.40625 3.83789 1.40625 3.28125C1.40625 2.68555 1.55273 2.13868 1.8457 1.64063C2.13867 1.13282 2.53418 0.73243 3.03223 0.43946C3.54004 0.14649 4.0918 0 4.6875 0ZM4.6875 0.9375C4.25781 0.9375 3.8623 1.04493 3.50098 1.25977C3.14941 1.46485 2.86621 1.74805 2.65137 2.10938C2.44629 2.46094 2.34375 2.85157 2.34375 3.28125C2.34375 3.71094 2.44629 4.10645 2.65137 4.46778C2.86621 4.81934 3.14941 5.10254 3.50098 5.31739C3.8623 5.52247 4.25781 5.625 4.6875 5.625C5.11719 5.625 5.50781 5.52247 5.85938 5.31739C6.2207 5.10254 6.50391 4.81934 6.70898 4.46778C6.92383 4.10645 7.03125 3.71094 7.03125 3.28125C7.03125 2.85157 6.92383 2.46094 6.70898 2.10938C6.50391 1.74805 6.2207 1.46485 5.85938 1.25977C5.50781 1.04493 5.11719 0.9375 4.6875 0.9375Z"
                            fill="black" />
                        </svg>
                      </div>
                      <div class="pl-2.5">
                        <p class="text-[#898376] text-normal text-base font-normal sm:text-sm">Customer: {{ucwords($car_booking->customer_name)}}</p>
                      </div>
                    </div>
                    <div class="flex items-center ">
                      <div class="w-4 h-4">
                        <svg class="block w-4 h-4" width="13" height="13" viewBox="0 0 13 13" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M2.66429 0.0625C2.91819 0.0625 3.14769 0.145499 3.35276 0.311499L3.39671 0.340799L5.31565 2.3184L5.30101 2.333C5.50608 2.5283 5.60862 2.7676 5.60862 3.0508C5.60862 3.334 5.51097 3.5732 5.31565 3.7686L4.37815 4.7061C4.71019 5.4678 5.14476 6.1025 5.68187 6.6104C6.23851 7.1377 6.87815 7.5772 7.60081 7.9287L8.53831 6.9912C8.73362 6.7959 8.97776 6.6982 9.27073 6.6982C9.56367 6.6982 9.80787 6.7959 10.0032 6.9912L10.0178 7.0205L11.9221 8.9248C12.1174 9.1201 12.2151 9.3643 12.2151 9.6572C12.2151 9.9404 12.1174 10.1797 11.9221 10.375L10.4426 11.8545C10.218 12.0498 9.95918 12.1768 9.66628 12.2354C9.38308 12.2939 9.09984 12.2744 8.81664 12.1768H8.80198C6.70237 11.3564 4.92992 10.2236 3.48461 8.7783C2.6057 7.8994 1.83909 6.9082 1.18479 5.8047C0.716055 5.0039 0.349835 4.2227 0.0861669 3.4609V3.4463C-0.0114891 3.1729 -0.0261372 2.8945 0.0422218 2.6113C0.110581 2.3184 0.257066 2.0693 0.481675 1.8643L0.467025 1.8496L1.94651 0.326199L1.97581 0.311499C2.18089 0.145499 2.41038 0.0625 2.66429 0.0625ZM2.66429 1C2.63499 1 2.60081 1.0147 2.56175 1.0439L1.11155 2.5234C1.03343 2.5918 0.979725 2.6895 0.950425 2.8164C0.921125 2.9336 0.926015 3.041 0.965075 3.1387C1.20921 3.832 1.55101 4.5596 1.99046 5.3213C2.60569 6.3662 3.32347 7.2988 4.14378 8.1191C5.4719 9.4473 7.12718 10.502 9.1096 11.2832C9.38307 11.3711 9.62228 11.3272 9.82738 11.1514L11.2922 9.6865C11.3118 9.667 11.3215 9.6572 11.3215 9.6572C11.3215 9.6475 11.3118 9.6328 11.2922 9.6133L9.32938 7.6504C9.30008 7.6211 9.2805 7.6064 9.27073 7.6064C9.26097 7.6064 9.24144 7.6211 9.21214 7.6504L7.80589 9.0566L7.14671 8.749C6.83421 8.5928 6.53147 8.417 6.23851 8.2217C5.82835 7.958 5.46214 7.6797 5.13987 7.3867L5.03733 7.2988C4.7053 6.9766 4.3928 6.6006 4.09983 6.1709C3.89476 5.8584 3.70922 5.5313 3.5432 5.1895L3.36741 4.7793L3.26487 4.4863L3.48461 4.2813L4.65647 3.1094C4.68577 3.0801 4.69554 3.0459 4.68577 3.0068L2.76683 1.0439C2.72776 1.0147 2.69358 1 2.66429 1Z"
                            fill="black" />
                        </svg>
                      </div>
                <div class="pl-2.5">
              <p class="text-[#898376] text-normal text-base font-normal sm:text-sm">
              Mobile: {{$car_booking->customer_mobile_country_code}}&nbsp;{{$car_booking->customer_mobile}}</p>
                </div>
                </div>
                @if(strcmp($car_booking->alt_customer_mobile_country_code,'')!==0 && strcmp($car_booking->alt_customer_mobile,'')!==0)
                    <div class="flex items-center ">
                      <div class="w-4 h-4">
                        <svg class="block w-4 h-4" width="13" height="13" viewBox="0 0 13 13" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M2.66429 0.0625C2.91819 0.0625 3.14769 0.145499 3.35276 0.311499L3.39671 0.340799L5.31565 2.3184L5.30101 2.333C5.50608 2.5283 5.60862 2.7676 5.60862 3.0508C5.60862 3.334 5.51097 3.5732 5.31565 3.7686L4.37815 4.7061C4.71019 5.4678 5.14476 6.1025 5.68187 6.6104C6.23851 7.1377 6.87815 7.5772 7.60081 7.9287L8.53831 6.9912C8.73362 6.7959 8.97776 6.6982 9.27073 6.6982C9.56367 6.6982 9.80787 6.7959 10.0032 6.9912L10.0178 7.0205L11.9221 8.9248C12.1174 9.1201 12.2151 9.3643 12.2151 9.6572C12.2151 9.9404 12.1174 10.1797 11.9221 10.375L10.4426 11.8545C10.218 12.0498 9.95918 12.1768 9.66628 12.2354C9.38308 12.2939 9.09984 12.2744 8.81664 12.1768H8.80198C6.70237 11.3564 4.92992 10.2236 3.48461 8.7783C2.6057 7.8994 1.83909 6.9082 1.18479 5.8047C0.716055 5.0039 0.349835 4.2227 0.0861669 3.4609V3.4463C-0.0114891 3.1729 -0.0261372 2.8945 0.0422218 2.6113C0.110581 2.3184 0.257066 2.0693 0.481675 1.8643L0.467025 1.8496L1.94651 0.326199L1.97581 0.311499C2.18089 0.145499 2.41038 0.0625 2.66429 0.0625ZM2.66429 1C2.63499 1 2.60081 1.0147 2.56175 1.0439L1.11155 2.5234C1.03343 2.5918 0.979725 2.6895 0.950425 2.8164C0.921125 2.9336 0.926015 3.041 0.965075 3.1387C1.20921 3.832 1.55101 4.5596 1.99046 5.3213C2.60569 6.3662 3.32347 7.2988 4.14378 8.1191C5.4719 9.4473 7.12718 10.502 9.1096 11.2832C9.38307 11.3711 9.62228 11.3272 9.82738 11.1514L11.2922 9.6865C11.3118 9.667 11.3215 9.6572 11.3215 9.6572C11.3215 9.6475 11.3118 9.6328 11.2922 9.6133L9.32938 7.6504C9.30008 7.6211 9.2805 7.6064 9.27073 7.6064C9.26097 7.6064 9.24144 7.6211 9.21214 7.6504L7.80589 9.0566L7.14671 8.749C6.83421 8.5928 6.53147 8.417 6.23851 8.2217C5.82835 7.958 5.46214 7.6797 5.13987 7.3867L5.03733 7.2988C4.7053 6.9766 4.3928 6.6006 4.09983 6.1709C3.89476 5.8584 3.70922 5.5313 3.5432 5.1895L3.36741 4.7793L3.26487 4.4863L3.48461 4.2813L4.65647 3.1094C4.68577 3.0801 4.69554 3.0459 4.68577 3.0068L2.76683 1.0439C2.72776 1.0147 2.69358 1 2.66429 1Z"
                            fill="black" />
                        </svg>
                      </div>
                      <div class="pl-2.5">

                        <p class="text-[#898376] text-normal text-base font-normal sm:text-sm">Alternate Mobile:  {{$car_booking->alt_customer_mobile_country_code}}&nbsp;{{$car_booking->alt_customer_mobile}}</p>
                      </div>
                    </div>
                  @endif
                  </div>
                  @endif
                </div>
                </div>
              </div>
            </div>
            @if(strcmp($car_booking->customer_name,'')!==0 && strcmp($car_booking->customer_mobile_country_code,'')!==0 && strcmp($car_booking->customer_mobile,'')!==0)
            <div class="pt-3">
              <p class="text-black text-sm font-normal pb-2.5 text-center">Contact Customer</p>
              <div class="flex -mx-2.5 flex-wrap">
                <div class="md:w-full w-1/2 px-2.5 md:mb-3 mb-0">
                  <a href="tel:{{$car_booking->customer_mobile}}"
                    class="w-full bg-[#088CDE] rounded py-2 px-4 flex items-center justify-center text-center text-white text-sm font-normal  uppercase">

                    <img class="w-[14px] h-[14px] block mr-2" src="{{asset('images/coll_icon_b.png')}}" alt="call">

                    call</a>
                </div>

                <div class="md:w-full w-1/2 px-2.5">
                  <a href="https://wa.me/{{ ltrim($car_booking->customer_mobile_country_code, '+') }}{{ $car_booking->customer_mobile }}" target="_blank"
                    class="w-full bg-[#00C62B] rounded py-2 px-4 flex items-center justify-center text-center text-white text-sm font-normal uppercase customer_encodedMessage">
                    <img class="w-[14px] h-[14px] block mr-2" src="{{asset('images/w_booking_icon.png')}}" alt="whatsapp">
                    Whatsapp
                  </a>
                </div>
              </div>
              </a>
            </div>
            @endif
            <div class="mt-5 overflow-hidden form_outer">
              <div class="relative flex border border-[#898376] rounded-[10px] form_inner sm:bg-[#F6F6F6]">
                <div class="relative w-1/2 py-[16px] px-3 text-center form_inner_left sm:py-[10px]  left_part">
                  <h4 class="sm:text-[13px] text-[14px] text-[#898376] font-medium text-black mb-[5px] capitalize">
                    FROM:</h4>
                  <div class="block">
                    <h4 class="text-black font-medium leading-4 text-[14px] sm:text-[13px] mb-[3px]">
                      {{$formattedStartDate}} | {{$formattedStartTime}}
                      </h4>
                      @if(strcmp($car_booking->pickup_location,'')!==0)
                     <p class="text-[#898376] font-normal leading-4 text-[14px] sm:text-[13px]">Pickup: {{$car_booking->pickup_location}}</p>
                    @endif
                  </div>
                </div>
                <div class="w-1/2 py-[16px] px-3 text-center right_part py-[10px] form_inner_right sm:py-[10px]">
                  <h4 class="sm:text-[13px] text-[14px] font-medium text-black mb-[5px] capitalize text-[#898376]">
                    TO:</h4>
                  <div class="block">
                    <h4 class="text-black font-medium leading-4 text-[14px] sm:text-[13px] mb-[3px]">
                      {{$formattedEndDate}} | {{$formattedEndTime}}
                    </h4>
                    @if(strcmp($car_booking->dropoff_location,'')!==0)
                    <p class="text-[#898376] font-normal leading-4 text-[14px] sm:text-[13px]">Drop: {{$car_booking->dropoff_location}}
                    @endif

                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!--Send Booking Confirmation-->
        <div>
          <h3 class="text-xl text-normal text-[#241A00] mb-5">Send Booking Confirmation</h3>
            <div class="bg-white rounded-[10px] mb-14">
              <div class="md:px-8 px-10 pb-9 pt-9">
                <div class="py-3">
                  <div class="flex -mx-2.5 flex-wrap">
                   @if( strcmp($car_booking->customer_mobile_country_code,'')!==0 || strcmp($car_booking->customer_mobile,'')!==0)
                    <div class="md:w-full w-1/2 px-2.5  md:mb-3 mb-0">
                      <a href="javascript:void(0);" target="_blank" id="customer_popup" class="transition-all duration-500 ease-0 w-full rounded border border-[#088cde] hover:bg-[#088CDE] hover:text-[#fff] py-2 px-4 flex items-center justify-center text-center text-[#088cde] text-sm font-normal uppercase">
                        Send To Customer
                      </a>
                    </div>
                    @endif
                    <div class="md:w-full w-1/2 px-2.5">
                      <a href="javascript:void(0);" target="_blank" id="partner_popup"
                        class="transition-all duration-500 ease-0 w-full border border-[#088cde] hover:bg-[#088CDE] hover:text-[#fff] rounded py-2 px-4 flex items-center justify-center text-center text-[#088cde] text-sm font-normal uppercase">
                        Send To Partner</a>
                    </div>

                  </div>
                </div>
              </div>
            </div>
        </div>

        <!-- confirm delivery / confirm collection -->
        @if(($currentTimeDate >= $pickupTimeBeforeThirty && strcmp($car_booking->status, 'delivered') !== 0) &&
            (strcmp($car_booking->status, 'canceled') !== 0) && (strcmp($car_booking->status, 'collected') !== 0) ||
            ($currentTimeDate >= $dropoffTimeBeforeThirty && strcmp($car_booking->status, 'delivered') == 0) &&
            (strcmp($car_booking->status, 'canceled') !== 0)  && (strcmp($car_booking->status, 'collected') !== 0)
          )
          <div>
            <h3 class="text-xl text-normal text-[#241A00] mb-5">Confirm Delivery/Collection</h3>
              <div class="bg-white rounded-[10px] mb-14">
                <div class="md:px-8 px-10 pb-9 pt-9">
                  <div class="py-3">
                    <div class="flex -mx-2.5 flex-wrap">

                      <div class="md:w-full w-1/2 px-2.5  md:mb-3 mb-0">
                        <a href="javascript:void(0);" class="w-full rounded border py-2 px-4 flex items-center
                        justify-center text-center  text-sm font-normal uppercase
                        @if(strcmp($car_booking->status, 'delivered') == 0)
                          cursor-default
                          bg-[#EBEBE4] border-[#EBEBE4] text-[#AEAEAE]
                        @else
                          booking_action_btn
                          border-siteYellow transition-all duration-300
                          ease-in-out bg-siteYellow hover:bg-siteYellow400
                        @endif
                        "
                          data-booking-id="{{$car_booking->id}}"
                          @if(strcmp($car_booking->status, 'delivered') == 0)
                            disabled="disabled"
                          @else
                            data-booking-action="confirm_delivery"
                          @endif
                          >
                            @if(strcmp($car_booking->status, 'delivered') == 0)
                                <img src="{{asset('images/blur_confirm_delivery.svg')}}" alt="clock" class="w-[20px] mr-2">
                            @else
                                <img src="{{asset('images/confirm_delivery.svg')}}" alt="clock" class="w-[20px] mr-2">
                            @endif
                            CONFIRM DELIVERY
                        </a>
                      </div>

                      <div class="md:w-full w-1/2 px-2.5">
                        <a href="javascript:void(0);"  class="w-full border rounded py-2 px-4 flex items-center justify-center text-center
                         text-sm font-normal uppercase
                          @if(strcmp($car_booking->status, 'delivered') !== 0)
                            cursor-default
                            bg-[#EBEBE4] border-[#EBEBE4] text-[#AEAEAE]
                          @else
                            booking_action_btn
                            border-siteYellow transition-all duration-300
                            ease-in-out bg-siteYellow hover:bg-siteYellow400
                          @endif
                          "
                            data-booking-id="{{$car_booking->id}}"
                          @if(strcmp($car_booking->status, 'delivered') !== 0)
                            disabled="disabled"
                          @else
                            data-booking-action="confirm_collection"
                          @endif
                          >
                            @if(strcmp($car_booking->status, 'delivered') !== 0)
                              <img src="{{asset('images/blur_carswithkey3.svg')}}" alt="clock" class="w-[20px] mr-2">
                            @else
                              <img src="{{asset('images/carswithkey.svg')}}" alt="clock" class="w-[20px] mr-2">
                            @endif
                            CONFIRM COLLECTION
                        </a>
                      </div>

                    </div>
                  </div>
                </div>
              </div>
          </div>
        @endif

        <!-- Rental Summary -->
        <div class="mb-[30px]">
          <h3 class="text-xl text-normal text-[#241A00] mb-5">Rental Summary</h3>
          <div class="bg-white  rounded-[10px]">
            <div class="md:px-8 px-10 py-12 ">
              <h4 class="text-lg font-medium  text-[#241A00]">Payments</h4>
              <div class="py-2.5">

                <ul id="rental_summary" class="payment_box_sec py-4 border-t border-b border-[#E1D6BF]">

                      <li class="pb-2.5 last:pb-0">
                        <div class="flex items-center -mx-3 ">
                          <div class="px-3 w-[50%]">
                            <p class=" sm:text-sm text-base font-normal text-[#666666] ">Car Rental: {{number_format($rentalCharges)}} * {{$numberOfDays}} Days

                            </p>
                          </div>
                          <div class="px-3 w-[50%]">
                            <p class="sm:text-sm text-right text-base font-normal text-black"> ₹{{number_format($totalDayRentalCharges)}}
                            </p>
                          </div>
                        </div>
                      </li>

                      <li class="pb-2.5 last:pb-0">
                        <div class="flex items-center -mx-3 ">
                          <div class="px-3 w-[50%]">
                            <p class=" sm:text-sm text-base font-normal text-[#666666] ">
                              Pickup Charges:
                            </p>
                          </div>
                          <div class="px-3 w-[50%]">
                            <p class="sm:text-sm text-right text-base font-normal text-black">
                              ₹{{number_format($pickupCharges, 0, '.', ',')}}
                            </p>
                          </div>
                        </div>
                      </li>

                      <li class="pb-2.5 last:pb-0">
                        <div class="flex items-center -mx-3 ">
                          <div class="px-3 w-1/2">
                            <p class=" sm:text-sm text-base font-normal text-[#666666] ">
                              Drop Charges:
                            </p>
                          </div>
                          <div class="px-3 w-1/2">
                            <p class="sm:text-sm text-right text-base font-normal text-black">₹{{number_format($dropCharges, 0, '.', ',')}}
                            </p>
                          </div>
                        </div>
                      </li>

                      <li class="pb-2.5 last:pb-0">
                        <div class="flex items-center -mx-3 ">
                          <div class="px-3 w-1/2">
                            <p class=" sm:text-sm text-base font-normal text-[#666666] ">
                              Discount:
                            </p>
                          </div>
                          <div class="px-3 w-1/2">
                            <p class="sm:text-sm text-right text-base font-normal text-black"><span>-</span>&nbsp;₹{{number_format($discount, 0, '.', ',')}}
                            </p>
                          </div>
                        </div>
                      </li>

                      <li class="pb-2.5 last:pb-0">
                        <div class="flex items-center -mx-3 ">
                          <div class="px-3 w-[50%]">
                            <p class=" sm:text-sm text-base font-normal text-[#666666] ">Advance Booking Amt:
                              (Received)
                            </p>
                          </div>
                          <div class="px-3 w-[50%]">
                            <p class="sm:text-sm text-right text-base font-normal text-black">₹{{number_format($advanceAmt, 0, '.', ',')}}
                            </p>
                          </div>
                        </div>
                      </li>

                      <li class="pb-2.5 last:pb-0">
                        <div class="flex items-center -mx-3 ">
                          <div class="px-3 w-[50%]">
                            <p class=" sm:text-sm text-base font-normal text-[#666666] ">Due at Delivery:  (Car rental charges)
                            </p>
                          </div>
                          <div class="px-3 w-[50%]">
                            <p class="sm:text-sm text-right text-base font-normal text-black">₹{{number_format(($totalDayRentalCharges+$pickupCharges+$dropCharges-$discount)-$advanceAmt, 0, '.', ',')}}
                            </p>
                          </div>
                        </div>
                      </li>

                      <li class="pb-2.5 last:pb-0">
                        <div class="flex items-center -mx-3 ">
                          <div class="px-3 w-[50%]">
                            <p class=" sm:text-sm text-base font-normal text-[#666666] ">Security Deposit: (Payable during delivery)
                            </p>
                          </div>
                          <div class="px-3 w-[50%]">
                            <p class="sm:text-sm text-right text-base font-normal text-black">₹{{number_format($securityDeposit, 0, '.', ',')}}
                            </p>
                          </div>
                        </div>
                      </li>

                      <li class="pb-2.5 last:pb-0">

                        <div class="flex items-center -mx-3 ">
                          <div class="px-3 w-[50%]">
                            <p class=" sm:text-sm text-base font-normal text-[#666666] ">Total Payable at
                              delivery: (₹{{number_format(($totalDayRentalCharges+$pickupCharges+$dropCharges-$discount)-$advanceAmt, 0, '.', ',')}} + ₹{{number_format($securityDeposit, 0, '.', ',') }})
                            </p>
                          </div>
                          <div class="px-3 w-[50%]">
                            <p class="sm:text-sm text-right text-base font-normal text-black">₹{{number_format($total_payable_at_delivery, 0, '.', ',')}}
                            </p>
                          </div>
                        </div>
                      </li>
                </ul>

              </div>
              <div class="flex items-center -mx-3 ">
                <div class="px-3 w-1/2">
                  <p class="text-lg font-medium  text-[#000]">Total:</p>
                </div>
                <div class="px-3 w-1/2">
                  <p id="total_amount_edit" class="text-lg font-medium text-[#000] text-right">₹{{number_format(($totalDayRentalCharges+$pickupCharges+$dropCharges-$discount)-$advanceAmt+$securityDeposit, 0, '.', ',')}}</p>
              </div>
              </div>
            </div>
          </div>
        </div>

      <!-- Booking Remarks -->
      @if($bookingRemarks)
        <div class=" mb-5">
          <h3 class="text-xl text-normal text-[#241A00] mb-5">Internal Notes</h3>
          <div class="bg-white  rounded-[10px]">
            <div class="md:px-8 px-10 py-3 ">
              {{-- <h4 class="text-lg font-medium  text-[#241A00]">Payments</h4> --}}
              <div class="py-2.5">
                {{$bookingRemarks}}
              </div>
            </div>
          </div>
        </div>
      @endif

        <!--Send Booking Confirmation-->
        <div class="">
          <h3 class="text-xl text-normal text-[#241A00] mb-5">Payment Details</h3>
          <div class="bg-white  rounded-[10px]">
            <div class="md:px-8 px-10 py-12 ">
              <h4 class="text-lg font-medium  text-[#241A00]">Payments</h4>
              <div class="py-2.5">
                <ul id="payment_list" class="payment_box_sec py-4 border-t border-b border-[#E1D6BF]">
                      <li class="pb-2.5 last:pb-0">
                        <div class="flex items-center -mx-3 ">
                          <div class="px-3 w-1/2">
                            <p class=" sm:text-sm text-base font-normal text-[#666666] ">Advance Payment ({{$car_booking->created_at->format('d M Y')}})
                            </p>
                          </div>
                          <div class="px-3 w-1/2">
                            <p class="sm:text-sm text-right text-base font-normal text-black">+ ₹{{$car_booking->advance_booking_amount}}</p>
                          </div>
                        </div>
                      </li>
                      @foreach($booking_payments as $booking_payment)
                        @if(strcmp($booking_payment->received_refund, 'received') == 0)
                            <li class="pb-2.5 last:pb-0">
                                <div class="flex items-center -mx-3">
                                    <div class="px-3 w-1/2">
                                        <p class="sm:text-sm text-base font-normal text-[#666666] capitalize">
                                            @if(strcmp($booking_payment->payment_name,'advance_payment')==0)
                                                Advance Payment ({{$booking_payment->created_at->format('d M Y')}})
                                            @elseif(strcmp($booking_payment->payment_name,'regular_payment')==0)
                                                Regular Payment ({{$booking_payment->created_at->format('d M Y')}})
                                            @elseif(strcmp($booking_payment->payment_name,'payment_name')==0)
                                                Payment Name ({{$booking_payment->created_at->format('d M Y')}})
                                            @elseif(strcmp($booking_payment->payment_name,'security_refund')==0)
                                                Security Refund ({{$booking_payment->created_at->format('d M Y')}})
                                            @elseif(strcmp($booking_payment->payment_name,'other')==0)
                                                {{$booking_payment->other_payment_name}} ({{$booking_payment->created_at->format('d M Y')}})
                                            @endif
                                        </p>
                                    </div>
                                    <div class="px-3 w-1/2">
                                        <p class="sm:text-sm text-right text-base font-normal text-black">+ ₹{{$booking_payment->amount}}</p>
                                    </div>
                                </div>
                            </li>
                        @elseif(strcmp($booking_payment->received_refund, 'refund') == 0)
                            <li class="pb-2.5 last:pb-0">
                                <div class="flex items-center -mx-3">
                                    <div class="px-3 w-1/2">
                                        <p class="sm:text-sm text-base font-normal text-[#666666] capitalize">
                                            @if(strcmp($booking_payment->payment_name,'advance_payment')==0)
                                                Advance Payment ({{$booking_payment->created_at->format('d M Y')}})
                                            @elseif(strcmp($booking_payment->payment_name,'regular_payment')==0)
                                                Regular Payment ({{$booking_payment->created_at->format('d M Y')}})
                                            @elseif(strcmp($booking_payment->payment_name,'payment_name')==0)
                                                Payment Name ({{$booking_payment->created_at->format('d M Y')}})
                                            @elseif(strcmp($booking_payment->payment_name,'security_refund')==0)
                                                Security Refund ({{$booking_payment->created_at->format('d M Y')}})
                                            @elseif(strcmp($booking_payment->payment_name,'other')==0)
                                                {{$booking_payment->other_payment_name}} ({{$booking_payment->created_at->format('d M Y')}})
                                            @endif
                                        </p>
                                    </div>
                                    <div class="px-3 w-1/2">
                                        <p class="sm:text-sm text-right text-base font-normal text-black">- ₹{{$booking_payment->amount}}</p>
                                    </div>
                                </div>
                            </li>
                        @endif
                      @endforeach
                </ul>
              </div>
              <div class="flex items-center -mx-3 ">
                <div class="px-3 w-1/2">
                  <p class="text-lg font-medium  text-[#000]">Received:</p>
                </div>
                <div class="px-3 w-1/2">
                  <p id="total_amount" class="text-lg font-medium text-[#000] text-right">₹0.00</p>
              </div>
              </div>
              <div class="mt-10">
                <a href="#fancybox" onclick="" class="fancybox inline-flex text-[#000000]  rounded-[4px] items-center justify-center
                text-center w-full px-5 py-3 text-[14px] font-normal leading-tight transition-all duration-300 border border-siteYellow
                rounded-[4px] cursor-pointer bg-siteYellow  md:px-[20px] md:py-[14px] sm:text-sm sm:py-[12px]
                transition-all duration-500 ease-0 hover:bg-[#d7b50a]  disabled:opacity-40 disabled:cursor-not-allowed uppercase">
                  <img class="mr-[8px] w-[15px] h-[15px] "
                    src="https://dev4.projectstatus.info/RentalXBooking/images/fa-add-icon.svg" alt="">Add Payment</a>
              </div>
            </div>
          </div>
        </div>

     <!--Agent Commission-->
     <div class="">
      <h3 class="text-xl text-normal text-[#241A00] my-5">Booking Commission</h3>
      <div class="bg-white  rounded-[10px]">
        <div class="md:px-8 px-10 py-12 ">
          <div class="py-2.5">
            <ul class="payment_box_sec py-4 border-t border-b border-[#E1D6BF]">
                  <li class="pb-2.5 last:pb-0">
                    <div class="flex items-center -mx-3 ">
                      <div class="px-3 w-1/2">
                        <p class=" sm:text-sm text-base font-normal text-[#666666]">Booking Commission
                        </p>
                      </div>
                      <div class="px-3 w-1/2">
                        <p class="sm:text-sm text-right text-base font-normal text-black">+ ₹{{$car_booking->agent_commission}}</p>
                      </div>
                    </div>
                  </li>
                  <li class="pb-2.5 last:pb-0">
                    <div class="flex items-center -mx-3 ">
                      <div class="px-3 w-1/2">
                        <p class=" sm:text-sm text-base font-normal text-[#666666] ">Booking Commission Received</p>
                      </div>
                      <div class="px-3 w-1/2">
                        <p class="sm:text-sm text-right text-base font-normal text-black">+ ₹{{$car_booking->agent_commission_received}}</p>
                      </div>
                    </div>
                  </li>
            </ul>
          </div>
          @if($agent_commission > $agent_commission_received)
          <div class="flex items-center -mx-3 ">
            <div class="px-3 w-1/2">
              <p class="text-lg font-medium  text-[#000]">{{$partnerCompanyName}} to {{$agentCompanyName}}:</p>
            </div>
            <div class="px-3 w-1/2">
              <p id="total_amount" class="text-lg font-medium text-[#000] text-right">₹{{$agent_commission_received - $agent_commission}}</p>
          </div>
          </div>
          @elseif($agent_commission < $agent_commission_received)
          <div class="flex items-center -mx-3 ">
            <div class="px-3 w-1/2">
              <p class="text-lg font-medium  text-[#000]">{{$agentCompanyName}} to {{$partnerCompanyName}}:</p>
            </div>
            <div class="px-3 w-1/2">
              <p id="total_amount" class="text-lg font-medium text-[#000] text-right">₹{{$agent_commission_received - $agent_commission}}</p>
          </div>
          </div>
          @endif

        </div>
      </div>
      <!--Ends here-->
      </div>
      </div>
    </div>

    <div class=" hidden floatingButtonSec fixed right-3 bottom-16 ">
        <div class="pwa_fix_bottom">
            <a href="#fancyboxx" onclick="" class="fancybox floatingButton flex rounded-3xl py-2 px-4 bg-orange text-white items-center">
                <span class="block floatingButtonIcon pr-2">
                    <svg class="block align-middle w-4" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="-10 0 404 512">
                        <path fill="currentColor" d="M376 232c4.41992 0 8 3.58008 8 8v32c0 4.41992 -3.58008 8 -8 8h-160v160c0 4.41992 -3.58008 8 -8 8h-32c-4.41992 0 -8 -3.58008 -8 -8v-160h-160c-4.41992 0 -8 -3.58008 -8 -8v-32c0 -4.41992 3.58008 -8 8 -8h160v-160c0 -4.41992 3.58008 -8 8 -8h32
                    c4.41992 0 8 3.58008 8 8v160h160z"></path>
                    </svg>
                </span>
                <span class="block align-middle floatingButtonText pl-1">
                    <span class="floatingButtonText add_payment_pp whitespace-nowrap block overflow-hidden pl-1">
                        Add Payment
                    </span>
                </span>
            </a>
        </div>
    </div>

   <!-- fancy box starts here -->
  <div class="add_payment_fancybox min-w-[432px] sm:min-w-[90%] hidden" id="fancybox" >
    <form action="">
        <h4 class="head_title inline-block text-xl font-normal align-middle ">
            Add Payment
        </h4>
        <div class="mb-6 text-base font-normal">
            <label class="block w-full mb-[5px] text-black500" for="received_refund">Received or refund<span class="text-[#ff0000]">*</span></label>
            <select name="received_refund" id="received_refund" class="fancybox_select  required_field pl-3 pr-8 text-sm text-black500 w-full py-[10px] focus:border-siteYellow appearance-none outline-none drop_location_select border border-[#898376]" onchange="handleReceivedRefundChange();">
                <option value="" selected disabled>Select</option>
                <option value="received">Received</option>
                <option value="refund">Refund
                </option>

            </select>
        </div>
        <div class="mb-6 mt-6 text-base font-normal">
            <label class="block w-full mb-[5px] text-black500" for="payment_name">Payment name<span class="text-[#ff0000]">*</span></label>
            <select name="payment_name" id="payment_name" class="fancybox_select required_field pl-3 pr-8 text-sm text-black500 w-full py-[10px] focus:border-siteYellow appearance-none outline-none  drop_location_select border border-[#898376]"
            onchange="handlePaymentNameChange();" disabled>
                 <option value="0">Select Received or Refund Field First</option>
            </select>
        </div>

            <div class="mb-6 mt-6 text-base font-normal" id="other_payment_name_block" style="display: none;">
                <label class="block w-full mb-[5px] text-black500" for="other_payment_name">Other payment name<span class="text-[#ff0000]">*</span></label>
                <input type="text" name="other_payment_name" id="other_payment_name" class="required_field  focus:border-siteYellow w-full py-[10px] px-3  pl-3 text-sm rounded border border-[#898376]  outline-none text-black500" placeholder="Other payment name">
            </div>

        <div class="mb-6 mt-6 text-base font-normal">
            <label for="amount" class="block w-full mb-[5px] text-black500">Amount<span class="text-[#ff0000]">*</span></label>
                <div class="rupee_price_field">
                  <input type="text" name="amount" id="amount"
                          class="required_field focus:border-siteYellow w-full py-[10px] px-3 pl-5 text-sm rounded border border-[#898376] outline-none text-black500"
                           placeholder="Enter amount" >
                   <span class="rupee_icon">₹</span>
                  </div>
        </div>

        <!-- fancy box add payment btn  -->
        <div class="add_payment_btn w-full mr-4 text-center ">
            <a href="javascript:void(0);" id="add_payment" class=" inline-flex items-center justify-center w-full
            py-[10px] text-base font-normal text-center  text-[#000000]  capitalize ease-in-out cursor-pointer bg-siteYellow sm:px-14 rounded-lg hover:bg-[#e4b130]">
            <img class="mr-[8px] w-[15px] h-[15px]"
            src="https://dev4.projectstatus.info/RentalXBooking/images/fa-add-icon.svg" alt="">ADD PAYMENT
            </a>
        </div>
    </form>
  </div>

<!-- customer popup -->
<div class="customer_popup_container z-[10000] w-[650px] md:w-[90%]">
    <div class="popup_form_section">
    <h3 class="text-xl text-normal text-[#241A00] ">Send booking confirmation to customer</h3>
    <p class="text-base text-normal text-[#241A00] mb-5">Customer Number: {{$concatenatedNumber}}</p>
      <div class="popup_container_inner">
          <textarea class="collection_of_sub_heading max-h-[250px] overflow-auto focus:outline-none w-full customer_textarea p-2" id="w3review" name="w3review" rows="50" cols="70">
          </textarea>
      </div>
      <!--buttons-->
      <div class="flex w-full filter_apply_inner_sec !border-t-0">
        <div class="flex justify-end w-1/2 mr-[10px]">
          <a href="javascript:void(0);" id="send_to_customer" target="_blank" class="border border-siteYellow transition-all
          duration-300 capitalize text-sm text-[#393939] bg-siteYellow  hover:bg-siteYellow400 rounded py-[10px] px-[30px] font-medium">
          SEND
          </a>
        </div>

        <div class="flex justify-start w-1/2 ml-[10px]">
          <a href="javascript:void(0);"
          class="customer_popup_cancel  capitalize transition-all
          duration-300 border-siteYellow text-sm border  hover:bg-siteYellow400 rounded py-[10px] px-[30px] bg-white text-[#000] font-medium">
          cancel
          </a>
        </div>
      </div>
      <!---->
  </div>
</div>

<!-- partner popup -->
<div class="partner_popup_container z-[10000] w-[650px] md:w-[90%]">
  <div class="popup_form_section">
    <form action="">
      <h3 class="text-xl text-normal text-[#241A00] mb-5">Send booking details to partner</h3>
      <div class="popup_container_inner">
        <textarea class="collection_of_sub_heading max-h-[250px] overflow-auto focus:outline-none w-full partner_textarea p-2" id="w3review" name="w3review" rows="50" cols="70">
        </textarea>
      </div>
      <!--buttons-->
      <div class="flex w-full filter_apply_inner_sec">
          <div class="flex justify-end w-1/2 mr-[10px]">
            <a href="javascript:void(0);" id="send_to_partner" target="_blank" class="font-medium border border-siteYellow transition-all
            duration-300 capitalize text-sm font-bold text-[#393939] bg-siteYellow  hover:bg-siteYellow400 rounded py-[10px] px-[30px] ">
            SEND</a>
          </div>
          <div class="flex justify-start w-1/2 ml-[10px]">
            <a href="javascript:void(0);" class="partner_popup_cancel capitalize font-medium transition-all
            duration-300 border-siteYellow text-sm font-bold border  hover:bg-siteYellow400 rounded py-[10px] px-[30px] bg-white text-[#000] ">
            cancel
            </a>
          </div>
      </div>
    </form>
  </div>
</div>

<!---->
<div class="open_popup_secc">
    <div class="form_section_b modal custom_pick_drop_popup" id="open_popup">
        <div class="form_section_b_inner">
            <form method="post" id="car_popup_form" class="car_popup_form" autocomplete="off">

                <div class="form_outer_sec">
                    <div class="car_book_form_b current">
                        <a href="javascript:void(0);" class="close_popup">Close</a>
                        <div class="car_book__input_box pickup">
                            <div class="book_car_input fare_info_b ">
                                <a href="javascript:void(0);" class=" book_car_click_area tab_item_pickup">
                                    <div class="car_book__input_text">
                                        <div class="car_book__input_icon">
                                            <svg width="16" height="18" viewBox="0 0 16 18" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M15.3512 1.71609C15.5232 1.71645 15.688 1.7844 15.8096 1.90507C15.9312 2.02575 15.9996 2.18931 16 2.35997V17.3561C15.9996 17.5268 15.9312 17.6903 15.8096 17.811C15.688 17.9317 15.5232 17.9996 15.3512 18H0.648796C0.476836 17.9996 0.312021 17.9317 0.190427 17.811C0.0688319 17.6903 0.000360884 17.5268 0 17.3561V2.35997C0.000360884 2.18931 0.0688319 2.02575 0.190427 1.90507C0.312021 1.7844 0.476836 1.71645 0.648796 1.71609H2.46269V2.69343C2.46269 3.20572 2.66776 3.69704 3.03278 4.05929C3.3978 4.42154 3.89287 4.62505 4.40908 4.62505C4.9253 4.62505 5.42037 4.42154 5.78539 4.05929C6.15041 3.69704 6.35547 3.20572 6.35547 2.69343V1.71745H9.65272V2.69343C9.65272 3.20572 9.85779 3.69704 10.2228 4.05929C10.5878 4.42154 11.0829 4.62505 11.5991 4.62505C12.1153 4.62505 12.6104 4.42154 12.9754 4.05929C13.3404 3.69704 13.5455 3.20572 13.5455 2.69343V1.71745L15.3512 1.71609ZM14.4852 6.28421H1.51477V16.4981H14.4852V6.28421ZM4.39952 7.39709H2.70172V9.14843H4.39952V7.39709ZM4.39952 10.504H2.70172V12.2553H4.39952V10.504ZM4.39952 13.6108H2.70172V15.3608H4.39952V13.6108ZM5.16578 2.69343V0.75096C5.16578 0.551793 5.08606 0.360784 4.94415 0.219951C4.80224 0.0791189 4.60977 0 4.40908 0C4.20839 0 4.01592 0.0791189 3.87401 0.219951C3.73211 0.360784 3.65238 0.551793 3.65238 0.75096V2.69343C3.65238 2.89259 3.73211 3.0836 3.87401 3.22443C4.01592 3.36527 4.20839 3.44439 4.40908 3.44439C4.60977 3.44439 4.80224 3.36527 4.94415 3.22443C5.08606 3.0836 5.16578 2.89259 5.16578 2.69343ZM7.36896 7.39709H5.6698V9.14843H7.36896V7.39709ZM7.36896 10.504H5.6698V12.2553H7.36896V10.504ZM7.36896 13.6108H5.6698V15.3608H7.36896V13.6108ZM10.3384 7.39709H8.63923V9.14843H10.3384V7.39709ZM10.3384 10.504H8.63923V12.2553H10.3384V10.504ZM10.3384 13.6108H8.63923V15.3608H10.3384V13.6108ZM12.3558 2.69343V0.75096C12.3558 0.551793 12.2761 0.360784 12.1342 0.219951C11.9923 0.0791189 11.7998 0 11.5991 0C11.3984 0 11.206 0.0791189 11.064 0.219951C10.9221 0.360784 10.8424 0.551793 10.8424 0.75096V2.69343C10.8424 2.89259 10.9221 3.0836 11.064 3.22443C11.206 3.36527 11.3984 3.44439 11.5991 3.44439C11.7998 3.44439 11.9923 3.36527 12.1342 3.22443C12.2761 3.0836 12.3558 2.89259 12.3558 2.69343ZM13.3065 7.39709H11.6087V9.14843H13.3065V7.39709ZM13.3065 10.504H11.6087V12.2553H13.3065V10.504ZM13.3065 13.6108H11.6087V15.3608H13.3065V13.6108Z"
                                                    fill="#343637" />
                                            </svg>
                                        </div>
                                        <h3 class="head_sec sm:text-center sm:font-medium">
                                            Pick-up
                                        </h3>
                                    </div>
                                    <div class="car_book__input_val">

                                        <input type="text" class="fare_info_input" id="pickup_date" name="pickup_date"
                                            placeholder="dd/mm/yyyy | hh:mm" readonly="readonly" required
                                            data-parsley-errors-container="#pick_up_date" data-parsley-trigger="change">
                                        <p class="dapicker_val_text dapicker_val__pickup_text"><span>dd/mm/yyyy |
                                                hh:mm</span></p>
                                    </div>
                                </a>
                                <div id="pick_up_date" class="error_format"></div>
                            </div>
                            <div class="book_car_input fare_info_b ">
                                <a href="javascript:void(0);" class=" book_car_click_area tab_item_drop">
                                    <div class="car_book__input_text">
                                        <div class="car_book__input_icon">
                                            <svg width="16" height="18" viewBox="0 0 16 18" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M15.3512 1.71609C15.5232 1.71645 15.688 1.7844 15.8096 1.90507C15.9312 2.02575 15.9996 2.18931 16 2.35997V17.3561C15.9996 17.5268 15.9312 17.6903 15.8096 17.811C15.688 17.9317 15.5232 17.9996 15.3512 18H0.648796C0.476836 17.9996 0.312021 17.9317 0.190427 17.811C0.0688319 17.6903 0.000360884 17.5268 0 17.3561V2.35997C0.000360884 2.18931 0.0688319 2.02575 0.190427 1.90507C0.312021 1.7844 0.476836 1.71645 0.648796 1.71609H2.46269V2.69343C2.46269 3.20572 2.66776 3.69704 3.03278 4.05929C3.3978 4.42154 3.89287 4.62505 4.40908 4.62505C4.9253 4.62505 5.42037 4.42154 5.78539 4.05929C6.15041 3.69704 6.35547 3.20572 6.35547 2.69343V1.71745H9.65272V2.69343C9.65272 3.20572 9.85779 3.69704 10.2228 4.05929C10.5878 4.42154 11.0829 4.62505 11.5991 4.62505C12.1153 4.62505 12.6104 4.42154 12.9754 4.05929C13.3404 3.69704 13.5455 3.20572 13.5455 2.69343V1.71745L15.3512 1.71609ZM14.4852 6.28421H1.51477V16.4981H14.4852V6.28421ZM4.39952 7.39709H2.70172V9.14843H4.39952V7.39709ZM4.39952 10.504H2.70172V12.2553H4.39952V10.504ZM4.39952 13.6108H2.70172V15.3608H4.39952V13.6108ZM5.16578 2.69343V0.75096C5.16578 0.551793 5.08606 0.360784 4.94415 0.219951C4.80224 0.0791189 4.60977 0 4.40908 0C4.20839 0 4.01592 0.0791189 3.87401 0.219951C3.73211 0.360784 3.65238 0.551793 3.65238 0.75096V2.69343C3.65238 2.89259 3.73211 3.0836 3.87401 3.22443C4.01592 3.36527 4.20839 3.44439 4.40908 3.44439C4.60977 3.44439 4.80224 3.36527 4.94415 3.22443C5.08606 3.0836 5.16578 2.89259 5.16578 2.69343ZM7.36896 7.39709H5.6698V9.14843H7.36896V7.39709ZM7.36896 10.504H5.6698V12.2553H7.36896V10.504ZM7.36896 13.6108H5.6698V15.3608H7.36896V13.6108ZM10.3384 7.39709H8.63923V9.14843H10.3384V7.39709ZM10.3384 10.504H8.63923V12.2553H10.3384V10.504ZM10.3384 13.6108H8.63923V15.3608H10.3384V13.6108ZM12.3558 2.69343V0.75096C12.3558 0.551793 12.2761 0.360784 12.1342 0.219951C11.9923 0.0791189 11.7998 0 11.5991 0C11.3984 0 11.206 0.0791189 11.064 0.219951C10.9221 0.360784 10.8424 0.551793 10.8424 0.75096V2.69343C10.8424 2.89259 10.9221 3.0836 11.064 3.22443C11.206 3.36527 11.3984 3.44439 11.5991 3.44439C11.7998 3.44439 11.9923 3.36527 12.1342 3.22443C12.2761 3.0836 12.3558 2.89259 12.3558 2.69343ZM13.3065 7.39709H11.6087V9.14843H13.3065V7.39709ZM13.3065 10.504H11.6087V12.2553H13.3065V10.504ZM13.3065 13.6108H11.6087V15.3608H13.3065V13.6108Z"
                                                    fill="#343637" />
                                            </svg>
                                        </div>
                                        <h3 class="head_sec sm:text-center sm:font-medium">
                                            Drop-off
                                        </h3>
                                    </div>
                                    <div class="car_book__input_val">
                                        <input type="text" class="fare_info_input" id="dropoff_date" name="dropoff_date"
                                            placeholder="dd/mm/yyyy | hh:mm" readonly="readonly" required
                                            data-parsley-errors-container="#drop_off_date" data-parsley-trigger="change"
                                            data-parsley-group="fare_info">
                                        <p class="dapicker_val_text dapicker_val__dropoff_text"><span>dd/mm/yyyy |
                                                hh:mm</span></p>
                                    </div>
                                </a>
                                <div id="drop_off_date" class="error_format">
                                </div>
                            </div>
                        </div>

                        <div class="datepick_area_b" id="datepick_area_box">
                            <div class="datepick_area pickup_drop">
                                <div class="datepicker_tab_sec">
                                    <a href="javascript:void(0);"
                                        class="hidden md:block md:absolute close_popup">Close</a>
                                    <div class="datepicker_tab__inner_b md:pt-[60px] sm:pt-[40px]">
                                        <div class="datepicker_tab__content">
                                            <div class="datepicker_tab__btn tab_item_pickup"> <span>Pick-up</span>
                                                <p class="dapicker_val__tab_pickup_text"><span>dd/mm/yyyy | hh:mm</span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="datepicker_tab__content">
                                            <div class="datepicker_tab__btn tab_item_drop" id="dropOffBtn_click">
                                                <span>Drop-off</span>
                                                <p class="dapicker_val__tab_dropoff_text"><span>dd/mm/yyyy |
                                                        hh:mm</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="datepicker_calender_sec">
                                    <!-- ///////////////////// -->
                                    <div class="datepicker_sec tab_item calendar_pickup active" id="tab1">
                                        <input type="hidden" name="" class="pickup_time" value=""
                                            id="fancyBox_pickup_time">
                                        <div class="datepicker_inner_b">
                                            <div class="datepicker_inner">
                                                <h3 class="head_sec sm:text-center sm:font-medium">Pick-up Date</h3>
                                                <div class="datepicker_input"></div>
                                            </div>
                                            <div class="timepicker_sec ">
                                                <h3 class="head_sec sm:text-center sm:font-medium">Pick-up Time</h3>
                                                <div class="timepicker_inner">
                                                    <div class="timepicker_b">
                                                        <div class="time_show_box">
                                                            <a href="javascript:void(0);" class="count_up hour_up"><img
                                                                    width="20" height="20"
                                                                    src="{{asset('images/top-angle.svg')}}"
                                                                    alt="icon">
                                                            </a>
                                                            <div class="time_block "><input class="time_hour"
                                                                    type="text" maxlength="2" readonly>
                                                            </div>
                                                            <a href="javascript:void(0);"
                                                                class=" count_down hour_down"><img width="20"
                                                                    height="20"
                                                                    src="{{asset('images/top-angle.svg')}}"
                                                                    alt="icon">
                                                            </a>
                                                        </div><span class="time_divid">:</span>
                                                        <div class="time_show_box">
                                                            <a href="javascript:void(0);" class="count_up min_up"><img
                                                                    width="20" height="20"
                                                                    src="{{asset('images/top-angle.svg')}}"
                                                                    alt="icon">
                                                            </a>
                                                            <div class="time_block "><input class="time_min" type="text"
                                                                    maxlength="2" readonly>
                                                            </div>
                                                            <a href="javascript:void(0);"
                                                                class=" count_down min_down"><img width="20" height="20"
                                                                    src="{{asset('images/top-angle.svg')}}"
                                                                    alt="icon">
                                                            </a>
                                                        </div>
                                                        <div class="time_show_box">
                                                            <a href="javascript:void(0);" class="count_up am_pm_up"><img
                                                                    width="20" height="20"
                                                                    src="{{asset('images/top-angle.svg')}}"
                                                                    alt="icon">
                                                            </a>
                                                            <div class="time_block "><input class="time_am_pm"
                                                                    type="text" maxlength="2" value="AM" readonly>
                                                            </div>
                                                            <a href="javascript:void(0);"
                                                                class=" count_down am_pm_up"><img width="20" height="20"
                                                                    src="{{asset('images/top-angle.svg')}}"
                                                                    alt="icon">
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="control_btns">
                                                        <button type="button"
                                                            class="btn apply_btn pickup_save popup_btn">NEXT</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- //////////////////// -->
                                    <div class="datepicker_sec calendar_drop inactive" id="tab2">
                                        <input type="hidden" name="" class="drop_off_time" id="fancyBox_dropoff_time">
                                        <div class="datepicker_inner_b2">
                                            <div class="datepicker_inner">
                                                <h3 class="head_sec sm:text-center sm:font-medium">Drop-off Date</h3>
                                                <div class="datepicker_input2"></div>
                                            </div>
                                            <div class="timepicker_sec ">
                                                <h3 class="head_sec sm:text-center sm:font-medium">Drop-off Time</h3>
                                                <div class="timepicker_inner">
                                                    <div class="timepicker_b">
                                                        <div class="time_show_box">
                                                            <a href="javascript:void(0);" class="count_up hour_up"><img
                                                                    width="20" height="20"
                                                                    src="{{asset('images/top-angle.svg')}}"
                                                                    alt="icon">
                                                            </a>
                                                            <div class="time_block "><input class="time_hour"
                                                                    type="text" maxlength="2" readonly>
                                                            </div>
                                                            <a href="javascript:void(0);" class=" count_down hour_down"><img
                                                                    width="20" height="20"
                                                                    src="{{asset('images/top-angle.svg')}}"
                                                                    alt="icon">
                                                            </a>
                                                        </div><span class="time_divid">:</span>
                                                        <div class="time_show_box">
                                                            <a href="javascript:void(0);" class="count_up min_up"><img
                                                                    width="20" height="20"
                                                                    src="{{asset('images/top-angle.svg')}}"
                                                                    alt="icon">
                                                            </a>
                                                            <div class="time_block "><input class="time_min" type="text"
                                                                    maxlength="2" readonly>
                                                            </div>
                                                            <a href="javascript:void(0);" class=" count_down min_down"><img
                                                                    width="20" height="20"
                                                                    src="{{asset('images/top-angle.svg')}}"
                                                                    alt="icon">
                                                            </a>
                                                        </div>
                                                        <div class="time_show_box">
                                                            <a href="javascript:void(0);" class="count_up am_pm_up"><img
                                                                    width="20" height="20"
                                                                    src="{{asset('images/top-angle.svg')}}"
                                                                    alt="icon">
                                                            </a>
                                                            <div class="time_block "><input class="time_am_pm"
                                                                    type="text" maxlength="2" value="AM" readonly>
                                                            </div>
                                                            <a href="javascript:void(0);" class=" count_down am_pm_up"><img
                                                                    width="20" height="20"
                                                                    src="{{asset('images/top-angle.svg')}}"
                                                                    alt="icon">
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="control_btns">
                                                        <button type="button"
                                                            class="btn apply_btn drop_save popup_btn ">NEXT</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="car_book_form_b ">
                        <a href="javascript:void(0);" class="close_popup">Close</a>
                        <div class="fare_info_view">
                            <div class="fare_info_view_b">
                                <div class="fare_info_view__elements">
                                    <div class="car_book__input_text">
                                        <div class="car_book__input_icon">
                                            <svg width="15" height="19" viewBox="0 0 15 19" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M7.5 0C5.51162 0.00240628 3.60537 0.789592 2.19938 2.1889C0.793378 3.5882 0.00242555 5.48537 7.75478e-06 7.46429C-0.00234012 9.0815 0.528512 10.6548 1.51112 11.9429C1.51112 11.9429 1.71567 12.2106 1.74853 12.2494L7.5 19L13.2539 12.2464C13.2841 12.2103 13.4889 11.9426 13.4889 11.9426L13.4899 11.941C14.4719 10.6534 15.0024 9.08076 15 7.46429C14.9976 5.48537 14.2066 3.5882 12.8006 2.1889C11.3946 0.789592 9.48838 0.00240628 7.5 0ZM7.5 10.1786C6.9606 10.1786 6.43331 10.0194 5.98481 9.72113C5.53631 9.42288 5.18675 8.99897 4.98033 8.503C4.77391 8.00703 4.7199 7.46127 4.82513 6.93475C4.93037 6.40823 5.19011 5.9246 5.57153 5.545C5.95294 5.1654 6.4389 4.90689 6.96794 4.80215C7.49697 4.69742 8.04534 4.75117 8.54368 4.95661C9.04202 5.16205 9.46797 5.50995 9.76764 5.95631C10.0673 6.40267 10.2273 6.92745 10.2273 7.46429C10.2265 8.18391 9.93886 8.87383 9.42757 9.38268C8.91629 9.89153 8.22307 10.1778 7.5 10.1786Z"
                                                    fill="#343637" />
                                            </svg>

                                        </div>
                                        <h3 class="head_sec sm:text-center sm:font-medium ">
                                            Pick-up location
                                        </h3>
                                    </div>
                                    <p class="fare_info_text fare_pickup_text">Pickup Location</p>
                                </div>
                                <div class="fare_info_view__elements">
                                    <div class="car_book__input_text">
                                        <div class="car_book__input_icon">
                                            <svg width="15" height="19" viewBox="0 0 15 19" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M7.5 0C5.51162 0.00240628 3.60537 0.789592 2.19938 2.1889C0.793378 3.5882 0.00242555 5.48537 7.75478e-06 7.46429C-0.00234012 9.0815 0.528512 10.6548 1.51112 11.9429C1.51112 11.9429 1.71567 12.2106 1.74853 12.2494L7.5 19L13.2539 12.2464C13.2841 12.2103 13.4889 11.9426 13.4889 11.9426L13.4899 11.941C14.4719 10.6534 15.0024 9.08076 15 7.46429C14.9976 5.48537 14.2066 3.5882 12.8006 2.1889C11.3946 0.789592 9.48838 0.00240628 7.5 0ZM7.5 10.1786C6.9606 10.1786 6.43331 10.0194 5.98481 9.72113C5.53631 9.42288 5.18675 8.99897 4.98033 8.503C4.77391 8.00703 4.7199 7.46127 4.82513 6.93475C4.93037 6.40823 5.19011 5.9246 5.57153 5.545C5.95294 5.1654 6.4389 4.90689 6.96794 4.80215C7.49697 4.69742 8.04534 4.75117 8.54368 4.95661C9.04202 5.16205 9.46797 5.50995 9.76764 5.95631C10.0673 6.40267 10.2273 6.92745 10.2273 7.46429C10.2265 8.18391 9.93886 8.87383 9.42757 9.38268C8.91629 9.89153 8.22307 10.1778 7.5 10.1786Z"
                                                    fill="#343637" />
                                            </svg>
                                        </div>
                                        <h3 class="head_sec sm:font-medium">
                                            Drop-off location
                                        </h3>
                                    </div>
                                    <p class="fare_info_text fare_dropoff_text">Dropoff Location</p>
                                </div>
                                <div class="fare_info_view__elements">
                                    <div class="car_book__input_text">
                                        <div class="car_book__input_icon">
                                            <svg width="16" height="18" viewBox="0 0 16 18" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M15.3512 1.71609C15.5232 1.71645 15.688 1.7844 15.8096 1.90507C15.9312 2.02575 15.9996 2.18931 16 2.35997V17.3561C15.9996 17.5268 15.9312 17.6903 15.8096 17.811C15.688 17.9317 15.5232 17.9996 15.3512 18H0.648796C0.476836 17.9996 0.312021 17.9317 0.190427 17.811C0.0688319 17.6903 0.000360884 17.5268 0 17.3561V2.35997C0.000360884 2.18931 0.0688319 2.02575 0.190427 1.90507C0.312021 1.7844 0.476836 1.71645 0.648796 1.71609H2.46269V2.69343C2.46269 3.20572 2.66776 3.69704 3.03278 4.05929C3.3978 4.42154 3.89287 4.62505 4.40908 4.62505C4.9253 4.62505 5.42037 4.42154 5.78539 4.05929C6.15041 3.69704 6.35547 3.20572 6.35547 2.69343V1.71745H9.65272V2.69343C9.65272 3.20572 9.85779 3.69704 10.2228 4.05929C10.5878 4.42154 11.0829 4.62505 11.5991 4.62505C12.1153 4.62505 12.6104 4.42154 12.9754 4.05929C13.3404 3.69704 13.5455 3.20572 13.5455 2.69343V1.71745L15.3512 1.71609ZM14.4852 6.28421H1.51477V16.4981H14.4852V6.28421ZM4.39952 7.39709H2.70172V9.14843H4.39952V7.39709ZM4.39952 10.504H2.70172V12.2553H4.39952V10.504ZM4.39952 13.6108H2.70172V15.3608H4.39952V13.6108ZM5.16578 2.69343V0.75096C5.16578 0.551793 5.08606 0.360784 4.94415 0.219951C4.80224 0.0791189 4.60977 0 4.40908 0C4.20839 0 4.01592 0.0791189 3.87401 0.219951C3.73211 0.360784 3.65238 0.551793 3.65238 0.75096V2.69343C3.65238 2.89259 3.73211 3.0836 3.87401 3.22443C4.01592 3.36527 4.20839 3.44439 4.40908 3.44439C4.60977 3.44439 4.80224 3.36527 4.94415 3.22443C5.08606 3.0836 5.16578 2.89259 5.16578 2.69343ZM7.36896 7.39709H5.6698V9.14843H7.36896V7.39709ZM7.36896 10.504H5.6698V12.2553H7.36896V10.504ZM7.36896 13.6108H5.6698V15.3608H7.36896V13.6108ZM10.3384 7.39709H8.63923V9.14843H10.3384V7.39709ZM10.3384 10.504H8.63923V12.2553H10.3384V10.504ZM10.3384 13.6108H8.63923V15.3608H10.3384V13.6108ZM12.3558 2.69343V0.75096C12.3558 0.551793 12.2761 0.360784 12.1342 0.219951C11.9923 0.0791189 11.7998 0 11.5991 0C11.3984 0 11.206 0.0791189 11.064 0.219951C10.9221 0.360784 10.8424 0.551793 10.8424 0.75096V2.69343C10.8424 2.89259 10.9221 3.0836 11.064 3.22443C11.206 3.36527 11.3984 3.44439 11.5991 3.44439C11.7998 3.44439 11.9923 3.36527 12.1342 3.22443C12.2761 3.0836 12.3558 2.89259 12.3558 2.69343ZM13.3065 7.39709H11.6087V9.14843H13.3065V7.39709ZM13.3065 10.504H11.6087V12.2553H13.3065V10.504ZM13.3065 13.6108H11.6087V15.3608H13.3065V13.6108Z"
                                                    fill="#343637" />
                                            </svg>

                                        </div>
                                        <h3 class="head_sec sm:font-medium">
                                            Pick-up
                                        </h3>
                                    </div>
                                    <p class="fare_info_text fare_pickdate_text">Pickup Date</p>
                                </div>
                                <div class="fare_info_view__elements">
                                    <div class="car_book__input_text">
                                        <div class="car_book__input_icon">
                                            <svg width="16" height="18" viewBox="0 0 16 18" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M15.3512 1.71609C15.5232 1.71645 15.688 1.7844 15.8096 1.90507C15.9312 2.02575 15.9996 2.18931 16 2.35997V17.3561C15.9996 17.5268 15.9312 17.6903 15.8096 17.811C15.688 17.9317 15.5232 17.9996 15.3512 18H0.648796C0.476836 17.9996 0.312021 17.9317 0.190427 17.811C0.0688319 17.6903 0.000360884 17.5268 0 17.3561V2.35997C0.000360884 2.18931 0.0688319 2.02575 0.190427 1.90507C0.312021 1.7844 0.476836 1.71645 0.648796 1.71609H2.46269V2.69343C2.46269 3.20572 2.66776 3.69704 3.03278 4.05929C3.3978 4.42154 3.89287 4.62505 4.40908 4.62505C4.9253 4.62505 5.42037 4.42154 5.78539 4.05929C6.15041 3.69704 6.35547 3.20572 6.35547 2.69343V1.71745H9.65272V2.69343C9.65272 3.20572 9.85779 3.69704 10.2228 4.05929C10.5878 4.42154 11.0829 4.62505 11.5991 4.62505C12.1153 4.62505 12.6104 4.42154 12.9754 4.05929C13.3404 3.69704 13.5455 3.20572 13.5455 2.69343V1.71745L15.3512 1.71609ZM14.4852 6.28421H1.51477V16.4981H14.4852V6.28421ZM4.39952 7.39709H2.70172V9.14843H4.39952V7.39709ZM4.39952 10.504H2.70172V12.2553H4.39952V10.504ZM4.39952 13.6108H2.70172V15.3608H4.39952V13.6108ZM5.16578 2.69343V0.75096C5.16578 0.551793 5.08606 0.360784 4.94415 0.219951C4.80224 0.0791189 4.60977 0 4.40908 0C4.20839 0 4.01592 0.0791189 3.87401 0.219951C3.73211 0.360784 3.65238 0.551793 3.65238 0.75096V2.69343C3.65238 2.89259 3.73211 3.0836 3.87401 3.22443C4.01592 3.36527 4.20839 3.44439 4.40908 3.44439C4.60977 3.44439 4.80224 3.36527 4.94415 3.22443C5.08606 3.0836 5.16578 2.89259 5.16578 2.69343ZM7.36896 7.39709H5.6698V9.14843H7.36896V7.39709ZM7.36896 10.504H5.6698V12.2553H7.36896V10.504ZM7.36896 13.6108H5.6698V15.3608H7.36896V13.6108ZM10.3384 7.39709H8.63923V9.14843H10.3384V7.39709ZM10.3384 10.504H8.63923V12.2553H10.3384V10.504ZM10.3384 13.6108H8.63923V15.3608H10.3384V13.6108ZM12.3558 2.69343V0.75096C12.3558 0.551793 12.2761 0.360784 12.1342 0.219951C11.9923 0.0791189 11.7998 0 11.5991 0C11.3984 0 11.206 0.0791189 11.064 0.219951C10.9221 0.360784 10.8424 0.551793 10.8424 0.75096V2.69343C10.8424 2.89259 10.9221 3.0836 11.064 3.22443C11.206 3.36527 11.3984 3.44439 11.5991 3.44439C11.7998 3.44439 11.9923 3.36527 12.1342 3.22443C12.2761 3.0836 12.3558 2.89259 12.3558 2.69343ZM13.3065 7.39709H11.6087V9.14843H13.3065V7.39709ZM13.3065 10.504H11.6087V12.2553H13.3065V10.504ZM13.3065 13.6108H11.6087V15.3608H13.3065V13.6108Z"
                                                    fill="#343637" />
                                            </svg>

                                        </div>
                                        <h3 class="head_sec sm:font-medium">
                                            Drop-off
                                        </h3>
                                    </div>
                                    <p class="fare_info_text fare_dropdate_text">Dropoff Date</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </form>
    </div>
</div>
<!---->
@include('layouts.navigation')
<script>
 var bookingId={{$car_booking->id}};

var carId= {{$carId}}
  console.log('carId:',carId);
  // var allDisableDates;

  $('#cancelBookingBtn').on('click',function(e){
      e.preventDefault();

          Swal.fire({
              title: 'Are you sure want to cancel the booking?',
              icon: 'warning',
              showCancelButton: false,
              confirmButtonText: 'OK',
              showCancelButton: true,
              confirmButtonText: 'YES, CANCEL IT',
              cancelButtonText: 'NO, KEEP IT'
          }).then((result) => {
              if (result.isConfirmed) {
                $(".loader").css("display", "inline-flex");
                $(".overlay_sections").css("display", "block");

                $.ajax({
                      url:"{{ route('agent.booking.cancel')}}",
                      type: 'POST',
                      data: {
                          '_token': '{{ csrf_token() }}',
                          'bookingId': bookingId,
                          'carId':carId,
                      },
                      dataType: "json",
                      success: function (data) {
                        // console.log("data success",data);
                        if(data.success){
                          Swal.fire({
                                  title: 'Booking canceled',
                                  icon: 'success',
                                  showCancelButton: false,
                                  confirmButtonText: 'OK',
                                  customClass: {
                                      popup: 'popup_updated',
                                      title: 'popup_title',
                                      actions: 'btn_confirmation',
                                  },
                          }).then((result) => {
                              if (result.isConfirmed) {
                                window.location.reload();
                              }else{
                                window.location.reload();
                              }

                          });
                        }

                    },
                      error: function (xhr, status, error) {
                        console.log('error:',error);
                      },
                      complete: function (data) {
                          $(".loader").css("display", "none");
                          $(".overlay_sections").css("display", "none");
                      }
                });

              }
            });
});

    $(document).ready(function() {
      // toggling the edit bookings buttons
    // $('.edit_booking').on('click', function(e) {
    //   e.stopPropagation();
    //   $(this).toggleClass('active');
    //   var temp = $(this).closest('.booking_head_sec').find('.edit_booking_cta');
    //   temp.slideToggle(100);
    // });
    // $('body').on('click',function(e){
    //   if($('.edit_booking').hasClass('active')){
    //     $('.booking_head_sec').find('.edit_booking_cta').slideToggle(100);
    //     $('.edit_booking').removeClass('active');
    //   }
    // });
  });



function numberFormat(value, decimals, decimalSeparator, thousandsSeparator) {
    decimals = decimals || 0;
    decimalSeparator = decimalSeparator || '.';
    thousandsSeparator = thousandsSeparator || ',';
    var parts = value.toFixed(decimals).toString().split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator);
    return parts.join(decimalSeparator);
}
$('.customer_popup_cancel, .partner_popup_cancel').click(function(){
  $('body').css('overflow','unset');
  $(".overlay_sections").css("display", "none");
  $('.partner_popup_container').removeClass('active');
  $('.customer_popup_container').removeClass('active');
});

var partner_message_send = '';

var get_partner_country_code = "{{Helper::getUserMetaByCarId($carId, 'user_mobile_country_code')}}";
get_partner_country_code = get_partner_country_code.replace('+', '');
var partner_mobile = get_partner_country_code + "{{$partner->mobile}}";

$('#partner_popup').on('click', function (e) {
  e.preventDefault();
  console.log('partner_mobile:',partner_mobile);
  $('body').css('overflow', 'hidden');
  $('.partner_popup.sidebar_close').removeClass('sidebar_close');
  $(".overlay_sections").css("display", "block");
  $('.partner_popup_container').addClass('active');
  $('body').addClass('partner_popup');
  var partner_message = '{{$partner_encodedMessage}}';
  var decoded_message = decodeURIComponent(partner_message.replace(/\+/g, ' '));
  $('.partner_textarea').val(decoded_message);
  partner_message_send = $('.partner_textarea').val();
  sendPartnerMessage(partner_message_send,partner_mobile);
});

function sendPartnerMessage(partner_message_send,partner_mobile) {
  console.log('Partner message sent:', partner_message_send);
  $('#send_to_partner').attr('href', 'https://wa.me/' +partner_mobile + '?text=' + encodeURIComponent(partner_message_send));
}

$('.partner_textarea').on('input', function () {
  partner_message_send = $(this).val();
  $('#send_to_partner').attr('href', 'https://wa.me/' +partner_mobile + '?text=' + encodeURIComponent(partner_message_send));
});


var customer_message_send = '';

var get_cust_country_code = "{{$car_booking->customer_mobile_country_code}}";
get_cust_country_code = get_cust_country_code.replace('+', '');

var customer_mobile = get_cust_country_code + "{{$car_booking->customer_mobile}}";

$('#customer_popup').on('click', function (e) {
  e.preventDefault();
  console.log('customer_mobile:',customer_mobile);
  $('body').css('overflow', 'hidden');
  $('.partner_popup.sidebar_close').removeClass('sidebar_close');
  $(".overlay_sections").css("display", "block");
  $('.customer_popup_container').addClass('active');
  $('body').addClass('partner_popup');
  var customer_message = '{{$customer_encodedMessage}}';
  var decoded_message = decodeURIComponent(customer_message.replace(/\+/g, ' '));
  $('.customer_textarea').val(decoded_message);
  customer_message_send = $('.customer_textarea').val();
  sendCustomerMessage(customer_message_send,customer_mobile);
});

function sendCustomerMessage(customer_message_send,customer_mobile) {
  console.log('Customer message sent:', customer_message_send);
  $('#send_to_customer').attr('href', 'https://wa.me/' + customer_mobile + '?text=' + encodeURIComponent(customer_message_send));
}

$('.customer_textarea').on('input', function () {
  customer_message_send = $(this).val();
  $('#send_to_customer').attr('href', 'https://wa.me/' + customer_mobile + '?text=' + encodeURIComponent(customer_message_send));

});

// CANCEL BUTTON
$('#send_to_customer').on('click', function () {
  $('body').css('overflow','unset');
  $(".overlay_sections").css("display", "none");
  $('.customer_popup_container').removeClass('active');
});

$('#send_to_partner').on('click', function () {
  $('body').css('overflow','unset');
  $(".overlay_sections").css("display", "none");
  $('.partner_popup_container').removeClass('active');
});

$('.overlay_sections').on('click', function () {

  $('body').css('overflow','unset');
  $(".overlay_sections").css("display", "none");
  $('.partner_popup_container').removeClass('active');

  $('body').css('overflow','unset');
  $(".overlay_sections").css("display", "none");
  $('.customer_popup_container').removeClass('active');

});



$(document).ready(function() {
  $('textarea').each(function(){
    $(this).val($(this).val().trim());
  });
});

function calculateTotalAmount() {
  var totalAmount = "{{$car_booking->advance_booking_amount}}";
  console.log('totalAmount:',totalAmount);
  @foreach($booking_payments as $booking_payment)
      @if(strcmp($booking_payment->received_refund, 'received') == 0)
          totalAmount += {{$booking_payment->amount}};
      @elseif(strcmp($booking_payment->received_refund, 'refund') == 0)
          totalAmount -= {{$booking_payment->amount}};
      @endif
  @endforeach
  return totalAmount;
}

function updateTotalAmount() {
    var totalAmount = calculateTotalAmount();
    $('#total_amount').text('₹' + numberFormat(Math.abs(totalAmount), 0, '.', ',')); // Assuming numberFormat is a custom function
}

updateTotalAmount();
</script>

<script>
  function handleReceivedRefundChange() {
    var receivedRefund = $('#received_refund').val();
    var paymentNameSelect = $('#payment_name');
    var otherPaymentNameBlock = $('#other_payment_name_block');
    if (receivedRefund !== '') {
        paymentNameSelect.prop('disabled', false);
        updatePaymentNameOptions(receivedRefund);
        otherPaymentNameBlock.hide();
        $('#other_payment_name').val('');
    }
    else {
        paymentNameSelect.prop('disabled', true);
        paymentNameSelect.val('');
        otherPaymentNameBlock.hide();
        $('#other_payment_name').val('');
    }
}

function updatePaymentNameOptions(receivedRefund) {
    var paymentNameSelect = $('#payment_name');
    var options = '';
    paymentNameSelect.empty();
    if (receivedRefund === 'received') {
        options += '<option value="advance_payment">Advance Payment</option>';
        options += '<option value="regular_payment">Regular Payment</option>';
        options += '<option value="other">Other</option>';
    } else if (receivedRefund === 'refund') {
        options += '<option value="security_refund">Security Refund</option>';
        options += '<option value="payment_name">Payment Name</option>';
        options += '<option value="other">Other</option>';
    }
    paymentNameSelect.append(options);
}

function handlePaymentNameChange() {
    var selectedPaymentName = $('#payment_name').val();
    var otherPaymentNameBlock = $('#other_payment_name_block');
    if (selectedPaymentName === 'other') {
        otherPaymentNameBlock.show();
    } else {
      otherPaymentNameBlock.hide();
      $('#other_payment_name').val('');
    }
}


$('#add_payment').on('click', function () {
    var receivedRefund = $('#received_refund').val();
    var paymentName = $('#payment_name').val();
    var otherPaymentName = $('#other_payment_name').val();
    var amount = $('#amount').val();
if(receivedRefund && paymentName && amount || otherPaymentName)
{
    $.ajax({
        url: '{{ route('agent.addPayment') }}',
        type: 'POST',
        data: {
            bookingId: '{{$car_booking->id}}',
            received_refund: receivedRefund,
            payment_name: paymentName,
            other_payment_name: otherPaymentName,
            amount: amount,
            _token: '{{ csrf_token() }}'
        },
        success: function (response) {
          if (response.success) {
                $.fancybox.close();
                $('#payment_list').append(response.bookingPaymentsHtml);
                var formattedTotalAmount = '₹' + numberFormat(Math.abs(response.totalAmount), 0, '.', ',');
                if (response.totalAmount < 0) {
                    formattedTotalAmount = '- ' + formattedTotalAmount;
                }

                $('#total_amount').text(formattedTotalAmount);

                resetFormFields();
            } else {
                console.log("error");
            }
          },

      error: function (xhr, status, error) {
          console.error('AJAX Error:', status, error);
      }
    });

  }
  else{
    Swal.fire({
            title: 'Please fill all mandatory fields',
            icon: 'warning',
            showCancelButton: false,
            confirmButtonText: 'OK',
            customClass: {
            popup: 'popup_updated',
            title: 'popup_title',
            actions: 'btn_confirmation',
            },
        });
  }
});

function resetFormFields() {
      $('#received_refund').val('');
      $('#payment_name').prop('disabled', true);
      $('#other_payment_name').val('');
      $('#amount').val('');
}

 $('.fancybox').fancybox();
  $('.add_payment').on('click',function(e){
    e.preventDefault();

});

function formatIndianCurrency(value) {
value = value.replace(/\$/g, '').replace(/,/g, '');

if (!isNaN(value) && value !== '') {
return parseFloat(value).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}
return '';
}

function convertRateToIndianCurrency(inputId) {
var $input = $("#" + inputId);
var inputValue = $input.val();
var formattedValue = formatIndianCurrency(inputValue);
$input.val(formattedValue);
}

var inputIds = ['amount'];

inputIds.forEach(function(inputId) {
$('#' + inputId).on('keyup', function() {
convertRateToIndianCurrency(inputId);
});
$('#' + inputId).on('blur', function() {
var $input = $(this);
var value = $input.val();

if (value !== '') {
var numericValue = parseFloat(value.replace(/[^\d.]/g, ''));
$input.val(numericValue.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
}
});
});

$('body').on('click', '.booking_action_btn', function(e) {
    e.preventDefault();
    var booking_action = $(this).data('booking-action');

    var bookingLength =  $('.bookingLength').length;

    console.log('bookingLength:',bookingLength);
    var action;
    var bookingStatus;
    var bookingId = $(this).data('booking-id');
    console.log(bookingId);

    if (booking_action == 'confirm_delivery') {
        action = booking_action;
        bookingStatus = 'delivered';
    }
    if (booking_action == 'confirm_collection') {
        action = booking_action;
        bookingStatus = 'collected';
    }

    console.log(action);
    var bookingUrl = "{{ route('agent.booking.list') }}";

    Swal.fire({
    title: 'Are you sure want to update status?',
    icon: 'info',
    showCancelButton: true,
    confirmButtonText: 'YES, UPDATE IT!',
    cancelButtonText: 'NO, CANCEL'
    }).then((result) => {
    if(result.isConfirmed){
        $.ajax({
            url:"{{ route('agent.view.change.booking.status')}}",
            type: 'POST',
            data: {
                '_token': '{{ csrf_token() }}',
                'action': action,
                'bookingId': bookingId,
                'bookingStatus':bookingStatus,
                'bookingLength':bookingLength,

            },
            dataType: "json",
            success: function (data) {

              console.log("data",data);

              if(data.successMsg){
                        Swal.fire({
                        title: data.successMsg,
                        icon: 'success',
                        showCancelButton: false,
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'popup_updated',
                            title: 'popup_title',
                            actions: 'btn_confirmation',
                        },
                        });
                    }else if(data.errorMsg){
                        Swal.fire({
                        title: data.errorMsg,
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'popup_updated',
                            title: 'popup_title',
                            actions: 'btn_confirmation',
                        },
                        });
                    }

                if (data.goback) {
                  setTimeout(function() {
                      window.location.href = bookingUrl;
                  }, 2000);
                }
                else{

                  setTimeout(function() {
                    window.location.reload();
                  }, 2000);

                }

           },
            error: function (xhr, status, error) {
            },
            complete: function (data) {
                $(".loader").css("display", "none");
                $(".overlay_sections").css("display", "none");
            }
        });
    }
    });
});

</script>
@endsection
