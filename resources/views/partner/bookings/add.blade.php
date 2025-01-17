@extends('layouts.partner')
@section('title', 'Create booking - customer details')
@section('content')
 @php
    $modifiedImgUrl = '';
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

$carName= Helper::getCarNameByCarId($carId);
$registerationNumber= Helper::getCarRagisterationNoByCarId($carId);
@endphp

<style>

body.session_expire_popup .session_custom_popup{ visibility:visible; opacity: 1; }
body.session_expire_popup .overlay_sections{ display: block; }
body.session_expire_popup{ overflow: hidden; }
.session_custom_popup{ z-index: 1112; top: 0; bottom: 0; left: 0; right: 0; margin: auto; top: 50%; transform: translateY(-50%); visibility: hidden; opacity: 0; transition-property: all; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }
.mr-2{ margin-right: 5px;}
.required,.error{color:#ff0000;}
.header_bar{display:none;}
.right_dashboard_section > .right_dashboard_section_inner {padding-top: 0px;}
.rupee_price_field{position: relative;}
.rupee_price_field .rupee_icon {display: flex;align-items: center;}
span.rupee_icon {position: absolute !important; top: 0; bottom: 0; margin: auto; left: 9px; height: 100%; text-align: center; font-size: 16px; color: #000; font-weight: 500;}
.drop_location_select.s_tag_haf{background-position: 98% center;}
.iti--allow-dropdown{ width:100% !important; }
/* .edit_input_icon{ position: absolute; top: -3px; bottom: 0; margin: auto; right: 9px; height: 100%; text-align: center; font-size: 16px; color: #000; font-weight: 500; display: flex; align-items: center; } */
span.rupee_icon{ left:22px; }
.iti--allow-dropdown input { padding-right: 20px !important;}
@media screen and (max-width: 992px){
.layout_main_top_header {display: none;}
}

@media screen and (max-width: 992px){
    .main_header_container{display:none;}
}

</style>


    <div class="dashboard_ct_section">
        <div class="lg:flex hidden lg:bg-white lg:mb-[0px] lg:px-[17px] lg:py-[15px] mb-[20px] justify-start items-center">

            <div class="flex items-center justify-start w-1/2">
                <a href="{{route('partner.booking.calendar')}}" class="inline-block p-2 mr-2 border border-[#C8C0C0] rounded-md
            hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E] GobackDelete">
                <img class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
            </a>
                 <span class="inline-block sm:text-base sm:text-left text-xl font-normal leading-normal align-middle text-black800">Edit date &
                    car</span>
            </div>

            <div class="flex items-center justify-end w-1/2">
                <div class="mr-5 sm:mr-2  timer_sec chart-container sm:w-[40px] sm:h-[40px]">
                    <div class="chart" id="timer" class="timer">
                        <div class="countdown" id="">10:00</div>
                    </div>
                </div>
                <div>
                    <a href="javascript:void(0);" data-action-btn="cancel" class="inline-flex py-2 mr-2 sm:mr-0 mob_cancel_exit_btn">
                        <span
                            class="inline-block sm:text-base sm:text-left text-xl font-normal leading-normal capitalize align-middle text-black800">cancel
                            & exit
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <div class="pt-[42px] px-[36px] lg:px-[17px] lg:py-[20px] xl:py-10 bg-textGray400 pb-[100px]  min-h-screen lg:flex lg:justify-center">
            <div class="lg:hidden flex items-center mb-[36px] lg:mb-[20px] justify-between">

                {{-- <div class="flex items-center justify-start w-1/2">
                    <a href="javascript:void(0)" class="inline-block py-2 mr-2 GobackDelete">
                        <img class="w-[42px]" src="{{asset('images/panel-back-arrow.svg')}}">
                    </a>
                    <span class="inline-block text-[26px] font-normal leading-normal align-middle text-black800">New
                        Booking</span>
                </div> --}}

                <div class="lg:hidden flex flex-col">
                    <div class="back-button">
                        <a href="{{route('partner.booking.calendar')}}" class="inline-flex py-1 px-2 border border-[#C8C0C0] rounded-md bg-[#fff] hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E] GobackDelete">
                            <img  class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
                            <span class="inline-block text-base leading-normal align-middle text-black800 ml-[10px] font-medium">
                                Calender
                            </span>
                        </a>
                    </div>
                    <span class="inline-block text-[26px] font-normal leading-normal align-middle text-black800">
                        New Booking
                    </span>
                </div>

                <div class="flex items-center justify-end w-1/2">
                    <div class="mr-5 timer_sec chart-container">
                        <div class="chart" id="timer2" class="timer">
                            <div class="countdown" id="">10:00</div>
                        </div>
                    </div>
                    <div class="">
                        <a href="javascript:void(0);" data-action-btn="cancel"
                            class="inline-block desktop_cancel_exit_btn resetLink hover:bg-siteYellow400 transition-all
                            duration-300 ease-out rounded-[4px] text-black text-base md:text-sm font-normal  bg-siteYellow px-[20px] py-2.5 md:px-[15px] uppercase">
                            cancel & exit
                        </a>
                    </div>
                </div>
            </div>


            <div class="max-w-[768px] w-full bg-white lg:bg-[#F6F6F6] rounded-[10px] px-12 lg:px-[0px] py-9 lg:py-[0px]">
                <div class="booking_section">
                <!-- booking_heading -->
                <div class="hidden mb-5 booking_sec_heading lg:flex">
                <h4 class="inline-block text-black800 text-[26px] lg:text-[24px] md:text-xl font-normal leading-normal align-middle capitalize ">
                     new booking</h4>
                    </div>
                    <div class="booking_section_inner">
                        <div class="block">
                            <div class="booking_main_details lg:bg-white pb-[25px] lg:p-[22px] sm:p-[18px] rounded-[10px]">
                                <div class="block">
                                    <div class="flex booking_main_details_inner_top ">
                                      <div class="flex justify-center w-[45%] md:w-[50%] booking_main_inner_left min-w-[174px] sm:min-w-[149px] md:pr-[20px]">
                            <div class="w-[200px] h-[150px]">
                                <div class="flex flex-col items-center justify-center w-full h-full overflow-hidden car_image_container">
                                             @if($modifiedImgUrl)
                                                <img src="{{$modifiedImgUrl}}" alt="creta"  class="object-contain max-w-full max-h-full">
                                            @endif
                                        </div>
                                       </div>
                                        </div>
                                        <div
                                            class="w-[55%] md:w-[50%] flex text-left booking_main_inner_right md:pl-[0px] pl-[15px] items-center">
                                            <div class="car_details ">
                                                <p class="capitalize sm:text-[13px] text-[#898376]">{{$car->name}} </p>
                                                <p class="capitalize sm:text-[13px] text-[#898376]"><span>{{$car->roof_type}}</span>
                                                    - <span>{{$car->fuel_type}}</span></p>
                                                <p class="capitalize sm:text-[13px] text-[#700D4A] font-bold registeration_no">{{$car->registration_number}}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-[20px] md:mt-[5px] overflow-hidden form_outer">
                                        <div class="relative flex border border-[#898376] rounded-[10px] form_inner sm:bg-[#F6F6F6]">
                                            <div
                                                class="relative w-1/2 py-[16px] pr-2 text-center form_inner_left sm:py-[10px] left_part">

                                                @php
                                                    $firstdateObj = new DateTime($firstDate);
                                                    $pickupFormattedDate = $firstdateObj->format('d M Y');
                                                @endphp

                                                <h4
                                                    class="sm:text-[13px] text-[14px] font-medium text-black mb-[5px] capitalize">
                                                    FROM</h4>
                                            <div class="block">
                                                <h4 class="text-black font-medium leading-4 text-[14px] sm:text-[13px] mb-[3px]">{{$pickupFormattedDate}}, {{$pickupTime}}</h4>
                                            </div>
                                            <div class="DisplayPickupLocation hidden">
                                                 <p class="text-[#898376] font-normal leading-4 text-[14px] sm:text-[13px]"></p>
                                             </div>
                                            </div>
                                <div class="w-1/2 py-[16px] pl-2 text-center right_part py-[10px] form_inner_right sm:py-[10px]">
                                @php
                                    $lastdateObj = new DateTime($lastDate);
                                    $dropoffFormattedDate = $lastdateObj->format('d M Y');
                                @endphp
                                <h4 class="sm:text-[13px] text-[14px] font-medium text-black mb-[5px] capitalize">TO</h4>
                                <div class="block">
                                        <h4 class="text-black font-medium leading-4 text-[14px] sm:text-[13px] mb-[3px]">{{$dropoffFormattedDate}}, {{$dropoffTime}}</h4>
                                </div>
                                <div class="DisplayDropoffLocation hidden">
                                    <p class="text-[#898376] font-normal leading-4 text-[14px] sm:text-[13px]"></p>
                                 </div>
                                </div>

                                </div>
                                </div>
                                </div>
                            </div>
                        </div>
                <div class="mt-[28px] mb-[30px] form_btn_sec afclr">
                <a href="javascript:void(0);"
                class="inline-block text-center w-full px-5 py-3 text-opacity-40 text-base font-medium
                leading-tight transition-all duration-300 border border-siteYellow rounded-[4px] cursor-pointer bg-siteYellow
                md:px-[20px] md:py-[14px] sm:text-sm sm:py-[12px] transition-all duration-500 ease-0 hover:bg-[#d7b50a]
                disabled:opacity-40 disabled:cursor-not-allowed uppercase">Edit car</a>
        </div>
        <form action="{{route('partner.booking.add.post',$carId)}}" class="mt-5" id="formId" method="POST" onsubmit="return checkForm()">
          @csrf
            <input class="hidden" type="text" id="carId" name="carId" value="{{$carId}}">
            <input class="hidden" type="text" id="userId" name="userId" value="{{$userId}}">
            <input class="hidden" type="text" id="start_date" name="start_date" value="{{$firstDate}}">
            <input class="hidden" type="text" id="end_date" name="end_date" value="{{$lastDate}}">
            <input class="hidden" type="text" id="start_time" name="start_time" value="{{$pickupTime}}">
            <input class="hidden" type="text" id="end_time" name="end_time" value="{{$dropoffTime}}">
            <input class="hidden" type="text" id="action_type" name="action_type" value="{{$action_type}}">
            <input class="hidden" type="text" id="registration_number" name="registration_number" value="{{$registerationNumber}}">
            <input class="hidden" type="text" id="car_name" name="car_name" value="{{$carName}}">


                <div class="pb-5">
                  <div class="flex flex-wrap -mx-3">
                    <div class="w-full px-3 sm:w-full inp_container">
                        <label for="customer_name" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Customer name<span class="text-[#ff0000]">*</span></label>
                            <input id="customer_name" tabindex="-1"
                                class="w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border
                                rounded-md required_field border-black500 focus:outline-none focus:border-siteYellow"
                                type="text"  name="customer_name"
                                placeholder="Enter customer name"
                                data-error-msg="Customer name is required"
                                value="{{old('customer_name')}}"
                                onkeyup="checkBlankField(this.id,'Customer name is required','errorCustomerName')">
                                <span class=" hidden required text-sm"></span>
                                @if ($errors->has('customer_name'))
                                <div class="error text-sm">
                                    <ul>
                                        <li>{{ $errors->first('customer_name') }}</li>
                                    </ul>
                                </div>
                                @endif
                                    </div>
                                </div>
                              </div>

                            <div class="flex flex-wrap -mx-3 sm:-mx-[0px] pb-5">
                                <div class="w-1/2 px-3 sm:px-[0px] sm:w-full inp_container">
                                    <label for="customer_mobile"
                                        class="inline-block pb-2.5 text-sm font-normal leading-4 text-left text-black">Customer
                                        Mobile number<span class="text-[#ff0000]">*</span></label>
                                    <input type="text" name="customer_mobile" tabindex="-1" id="customer_mobile"
                                        class="required_field phone_validation mobile_num_validate w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border rounded-md
                                        border-black500 focus:outline-none focus:border-siteYellow"  value="{{old('customer_mobile')}}"
                                        placeholder="Enter mobile number" data-error-msg="Customer mobile number is required"
                                        onkeyup="checkBlankField(this.id,'Customer mobile number is required','errorCustomerMobile')">
                                    <span class="hidden required text-sm"></span>
                                   <input type="hidden" name="customer_mobile_country_code" class="customer_mobile_country_code">

                                    @if ($errors->has('customer_mobile'))
                                    <div class="error text-sm">
                                        <ul>
                                            <li>{{ $errors->first('customer_mobile') }}</li>
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                                <div class="w-1/2 sm:mt-5 px-3 sm:px-[0px] sm:w-full">
                                    <label for="customer_email"
                                        class="inline-block pb-2.5 text-sm font-normal leading-4 text-left text-black">Customer
                                        email (optional)</label>
                                    <input type="email" tabindex="-1" id="customer_email" name="customer_email"
                                        class="w-full px-5 py-3 text-base font-normal leading-normal text-black
                                         bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow"
                                        placeholder="Enter email" data-error-msg="">
                                    <span class=" hidden required text-sm"></span>
                                </div>
                            </div>

                            <div class="pb-5">
                                <div class="flex flex-wrap -mx-3">
                                    <div class="w-full px-3 sm:w-full">
                                        <label for="customer_city"
                                            class="block pb-2.5 text-sm font-normal leading-4 text-left text-black ">
                                            Customer city (optional)</label>
                                        <input id="customer_city" tabindex="-1"
                                            class="w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white
                                            border rounded-md border-black500 focus:outline-none focus:border-siteYellow"
                                            type="text" name="customer_city" placeholder="Enter city">
                                    </div>
                                </div>
                            </div>

                            <div class="pb-5">
                                <div class="flex flex-wrap -mx-3">
                                    <div class="pickup_location_bar w-1/2 px-3 sm:w-full">
                                    <div class=" sm:w-full sm:pb-5 inp_container">
                                        <label for="pickup_location"
                                            class="block pb-2.5 text-sm font-normal leading-4 text-left text-black ">
                                            Pickup location<span class="text-[#ff0000]">*</span></label>
                                            <select name="pickup_location" tabindex="-1" id="pickup_location"
                                            onchange="checkBlankField(this.id,'Pickup location is required','errorPickup')"
                                            class="required_field w-full py-3 pl-5 pr-10 text-base font-normal leading-normal text-black bg-white border rounded-md appearance-none s_tag_haf border-black500 focus:outline-none focus:border-siteYellow drop_location_select"
                                            data-error-msg="Pickup location is required">
                                            <option value="">Select pickup location</option>

                                        </select>
                                        <span class="hidden required text-sm"></span>
                                        @if ($errors->has('pickup_location'))
                                        <div class="error text-sm">
                                            <ul>
                                                <li>{{ $errors->first('pickup_location') }}</li>
                                            </ul>
                                        </div>
                                        @endif
                                    </div>
                                <div class="sm:w-full inp_container">
                                <div class="pt-5 lg:pt-5 sm:pt-0 sm:pb-5 other_pickup_location_container" style="display:none">
                                 <label for="other_pickup_location"
                                    class="inline-block pb-2.5 text-sm font-normal leading-4 text-left text-black">Other pickup location<span class="text-[#ff0000]">*</span></span>
                                </label>
                                <input type="text" name="other_pickup_location" tabindex="-1" id="other_pickup_location"
                                    class="w-full px-5 py-3 text-base font-normal
                                    leading-normal text-black bg-white border rounded-md
                                    border-black500 focus:outline-none focus:border-siteYellow"
                                    placeholder="Enter other pickup location"
                                    onkeyup="checkBlankField(this.id,'Other pickup location is required','errorPickup')"
                                    data-error-msg="Other pickup location is required">
                                    <span class="hidden required text-sm"></span>
                                    @if ($errors->has('other_pickup_location'))
                                    <div class="error text-sm">
                                        <ul>
                                            <li>{{ $errors->first('other_pickup_location') }}</li>
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                             </div>
                             </div>
                             <div class="dropoff_location_bar w-1/2 px-3 sm:w-full">
                                    <div class=" sm:w-full sm:pb-0 inp_container">
                                     <label for="dropoff_location" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black ">
                                       Dropoff location<span class="text-[#ff0000]">*</span></label>
                                       <select name="dropoff_location" tabindex="-1" id="dropoff_location"
                                            onchange="checkBlankField(this.id,'Dropoff location is required','errorDropoff')"
                                            class="required_field w-full py-3 pl-5 pr-10 text-base font-normal leading-normal text-black bg-white border rounded-md appearance-none s_tag_haf border-black500 focus:outline-none focus:border-siteYellow drop_location_select"
                                            data-error-msg="Dropoff location is required">
                                            <option value="">Select dropoff location</option>

                                        </select>
                                        <span class="hidden required text-sm"></span>
                                        @if ($errors->has('dropoff_location'))
                                        <div class="error text-sm">
                                            <ul>
                                                <li>{{ $errors->first('dropoff_location') }}</li>
                                            </ul>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="sm:w-full inp_container">
                                    <div class="pt-5 lg:pt-5 sm:pb-0 other_dropoff_location_container"style="display:none">
                                    <label for="other_dropoff_location"
                                    class="inline-block pb-2.5 text-sm font-normal leading-4 text-left text-black">Other dropoff location<span class="text-[#ff0000]">*</span></span>
                                </label>
                                <input type="text" name="other_dropoff_location" id="other_dropoff_location"
                                    class="w-full px-5 py-3 text-base font-normal
                                    leading-normal text-black bg-white border rounded-md
                                    border-black500 focus:outline-none focus:border-siteYellow"
                                    placeholder="Enter other dropofff location"
                                    onkeyup="checkBlankField(this.id,'Other dropoff location is required','errorDropoff')"
                                    data-error-msg="Other dropoff location is required">
                                    <span class=" hidden required text-sm"></span>
                                    @if ($errors->has('other_dropoff_location'))
                                    <div class="error text-sm">
                                        <ul>
                                            <li>{{ $errors->first('other_dropoff_location') }}</li>
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                                    </div>
                                </div>
                            </div>


                        <!--per day rental charges-->
                        <div class="pb-5">
                            <div class="flex flex-wrap -mx-3">
                                <div class="w-full px-3 sm:w-full inp_container">
                                    <label for="per_day_rental_charges" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Per day rental charges<span class="text-[#ff0000]">*</span></label>
                                    <div class="rupee_price_field">
                                    <input type="text" name="per_day_rental_charges" tabindex="-1" id="per_day_rental_charges"
                                        class="amount_total required_field w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow pl-[33px]"
                                        placeholder="Enter Per day rental charges"
                                        data-error-msg="Per day rental charges is required"
                                       value="{{number_format($perDayRentalCharges, 0, '.', ',')}}">
                                    <span class="rupee_icon">₹</span>
                                </div>
                                <span class="hidden required text-sm"></span>
                                @if ($errors->has('per_day_rental_charges'))
                                <div class="error text-sm">
                                    <ul>
                                        <li>{{ $errors->first('per_day_rental_charges') }}</li>
                                    </ul>
                                </div>
                                @endif
                            </div>
                            </div>
                        </div>
                        <!-- -->

                        <!--number of days-->
                        <div class="pb-5">
                            @php
                                $convertedformattedStartDate = \Carbon\Carbon::parse($firstDate);
                                $convertedformattedEndDate = \Carbon\Carbon::parse($lastDate);
                                // Calculate the date difference
                                $dateDifference = $convertedformattedEndDate->diff($convertedformattedStartDate);
                                // echo "Date difference: " . $dateDifference->format('%a days');
                                $roundedDays = $dateDifference->days;
                                $convertedformattedStartTime = \Carbon\Carbon::parse($pickupTime);
                                $convertedformattedEndTime = \Carbon\Carbon::parse($dropoffTime);
                                $pickupMoment = \Carbon\Carbon::parse($convertedformattedStartTime->format('h:i A'));
                                $dropoffMoment = \Carbon\Carbon::parse($convertedformattedEndTime->format('h:i A'));
                                if ($pickupMoment->lt(\Carbon\Carbon::parse('9:00 AM'))) {
                                        $roundedDays++;
                                }
                                if ($dropoffMoment->gt(\Carbon\Carbon::parse('9:00 AM'))) {
                                        $roundedDays++;
                                }
                                // else {
                                //     $roundedDays = $dateDifference->days;
                                // }
                            @endphp

                            <div class="flex flex-wrap -mx-3">
                                <input type="hidden"  id="number_of_days_default_value" value="{{$roundedDays}}">
                                <div class="w-full px-3 sm:w-full inp_container">
                                    <label for="number_of_days" class="flex justify-between block pb-2.5 text-sm font-normal leading-4 text-left text-black">
                                        <p>Number of days<span class="text-[#ff0000]">*</span></p>
                                        <p class="text-[#777777]">Suggested number of days: {{$roundedDays}} days</p>
                                    </label>
                                    <input type="text" tabindex="-1" name="number_of_days" id="number_of_days"
                                    class="required_field w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white
                                    border rounded-md border-black500 focus:outline-none focus:border-siteYellow"
                                    placeholder="Enter number of days"
                                    data-error-msg="Number of days is required" value="{{$roundedDays}}">
                                    <span class="hidden required text-sm"></span>
                                    @if ($errors->has('number_of_days'))
                                    <div class="error text-sm">
                                        <ul>
                                            <li>{{ $errors->first('number_of_days') }}</li>
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- -->

                        <!-- pickup charges and drop-off charges-->
                            <div class="pb-5">
                                <div class="flex flex-wrap -mx-3">
                                <!-- pickup charges -->
                                <div class="w-1/2 px-3 sm:w-full sm:pb-5 inp_container">
                                <label for="pickup_charges"class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Pickup charges<span class="text-[#ff0000]">*</span></label>
                                <div class="rupee_price_field">
                                <input type="text" tabindex="-1" name="pickup_charges" id="pickup_charges"
                                        class="amount_total w-full px-5 py-3 required_field text-base font-normal leading-normal text-black  bg-white border rounded-md  border-black500 focus:outline-none focus:border-siteYellow pl-[33px]"
                                         placeholder="Enter pickup charges"
                                         data-error-msg="Pickup charges is required">
                                 <span class="rupee_icon">₹</span>
                                </div>
                                <span class="hidden required text-sm"></span>
                                    @if ($errors->has('pickup_charges'))
                                        <div class="error text-sm">
                                            <ul>
                                                <li>{{ $errors->first('pickup_charges') }}</li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                <!-- drop-off charges -->
                                <div class="w-1/2 px-3 sm:w-full sm:pb-0 inp_container">
                                    <label for="dropoff_charges"class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Dropoff charges<span class="text-[#ff0000]">*</span></label>
                                    <div class="rupee_price_field">
                                    <input type="text" name="dropoff_charges" tabindex="-1" id="dropoff_charges"
                                            class="amount_total w-full px-5 py-3 required_field text-base font-normal leading-normal text-black bg-white border rounded-md  border-black500 focus:outline-none focus:border-siteYellow pl-[33px]"
                                             placeholder="Enter dropoff charges"
                                             data-error-msg="Dropoff charges is required">
                                     <span class="rupee_icon">₹</span>
                                    </div>
                                    <span class="hidden required text-sm"></span>
                                    @if ($errors->has('dropoff_charges'))
                                    <div class="error text-sm">
                                        <ul>
                                            <li>{{ $errors->first('dropoff_charges') }}</li>
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                                </div>
                            </div>
                            <!-- -->

                           <!--discount-->
                            <div class="pb-5">
                                <div class="flex flex-wrap -mx-3">
                                    <div class="w-full px-3 sm:w-full inp_container">
                                        <label for="discount" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Discount</label>
                                        <div class="rupee_price_field">
                                        <input type="text" name="discount" tabindex="-1" id="discount"
                                            class="w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white
                                            border rounded-md border-black500 focus:outline-none focus:border-siteYellow pl-[33px]"
                                            placeholder="Enter discount">
                                        <span class="rupee_icon">₹</span>
                                    </div>
                                    <span class="required text-sm"></span>
                                </div>
                                </div>
                            </div>
                         <!-- -->

                        <!--total booking amount-->
                        <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                          <div class="w-full px-3 sm:w-full">
                            <label for="total_booking_amount" class="block pb-2 text-sm font-medium leading-4 text-left text-black ">Total booking amount</label>
                        <div class="rupee_price_field">
                        <input type="text" name="total_booking_amount" tabindex="-1" id="total_booking_amount"
                        class="calculate_total w-full px-5 py-[3px] text-base font-medium leading-normal text-black bg-white rounded-md outline-none lg:bg-[#F6F6F6] pl-[18px]"
                        placeholder="Enter Total booking amount" readonly>
                        <span class="rupee_icon" style="left:5px;">₹</span>
                                    </div>
                                 </div>
                                </div>
                          </div>
                        <!-- -->

                     <!--advance booking amount-->
                     <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full px-3 sm:w-full inp_container">
                                <label for="advance_booking_amount" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Advance booking amount<span class="text-[#ff0000]">*</span></label>
                                <div class="rupee_price_field">
                                <input type="text" name="advance_booking_amount" tabindex="-1" id="advance_booking_amount"
                                    class="required_field w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow pl-[33px]"
                                    placeholder="Enter advance booking amount"
                                    data-error-msg="Advance booking amount is required">
                                <span class="rupee_icon">₹</span>
                            </div>
                            <span class="hidden required text-sm"></span>
                            @if ($errors->has('advance_booking_amount'))
                            <div class="error text-sm">
                                <ul>
                                    <li>{{ $errors->first('advance_booking_amount') }}</li>
                                </ul>
                            </div>
                            @endif
                        </div>
                        </div>
                    </div>
                    <!-- -->

                     <!--refundable security deposit-->
                     <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full px-3 sm:w-full inp_container">
                                <label for="refundable_security_deposit" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Refundable security deposit<span class="text-[#ff0000]">*</span></label>
                                <div class="rupee_price_field">
                                <input type="text" tabindex="-1" name="refundable_security_deposit" id="refundable_security_deposit"
                                    class="calculate_total required_field w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow pl-[33px]"
                                    placeholder="Enter refundable security deposit"
                                    data-error-msg="Refundable security deposit is required">
                                <span class="rupee_icon">₹</span>
                            </div>
                            <span class="hidden required text-sm"></span>
                            @if ($errors->has('refundable_security_deposit'))
                            <div class="error text-sm">
                                <ul>
                                    <li>{{ $errors->first('refundable_security_deposit') }}</li>
                                </ul>
                            </div>
                            @endif
                        </div>
                        </div>
                    </div>
                    <!-- -->

                    <!--due at delivery-->
                    <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full px-3 sm:w-full">
                               <label for="due_at_delivery" class="block pb-2 text-sm font-medium leading-4 text-left text-black ">Due at delivery</label>
                                <div class="rupee_price_field">
                                 <input type="text" name="due_at_delivery" tabindex="-1" id="due_at_delivery"
                                    class="w-full px-5 py-[3px] text-base font-medium leading-normal text-black bg-white rounded-md outline-none lg:bg-[#F6F6F6] pl-[18px]"
                                    placeholder="Enter due at delivery" readonly>
                                <span class="rupee_icon" style="left:5px;">₹</span>
                            </div>
                         </div>
                        </div>
                    </div>
                   <!-- -->

                   <!-- add driver -->
                   @if(count($drivers) > 0)
                   <div class="pb-5">
                          <div class="flex flex-wrap -mx-3">
                              <div class="pickup_location_bar w-full px-3 sm:w-full">
                                 <div class="sm:w-full sm:pb-5 inp_container">
                                   <label for="select_driver" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Select driver (optional)</label>
                                      <select name="select_driver" tabindex="-1" id="select_driver"
                                        class="w-full py-3 pl-5 pr-10 text-base font-normal leading-normal text-black bg-white border rounded-md
                                        appearance-none s_tag_haf border-black500 focus:outline-none focus:border-siteYellow drop_location_select">
                                        <option value="" selected disabled>Select driver</option>
                                         @foreach($drivers as $item)
                                         <option value="{{$item->id}}">{{$item->driver_name}}</option>
                                         @endforeach
                                        </select>
                                    </div>
                                    </div>
                                </div>
                        </div>
                        @endif
                   <!--  -->
                    <!--booking remarks -->
                    <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full px-3 sm:w-full">
                                <label for="booking_remarks" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Internal notes (optional)</label>
                                <textarea type="text" name="booking_remarks" id="booking_remarks" rows="4" class="w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow">
                                </textarea>
                            </div>
                        </div>
                    </div>
                    <!---->

                        <!-- agent charges-->
                        {{-- <div class="pb-5">
                          <div class="flex flex-wrap -mx-3">
                            <!-- agent charges add -->
                            <div class="w-1/2 px-3 sm:w-full sm:pb-5 inp_container">
                                <label for="agent_commission" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Agent commission</label>
                                <div class="rupee_price_field">
                                <input type="text" name="agent_commission" id="agent_commission"
                                        class="w-full px-5 py-3 text-base font-normal leading-normal text-black
                                         bg-white border rounded-md  border-black500 focus:outline-none focus:border-siteYellow"
                                         placeholder="Enter agent commission">
                                 <span class="rupee_icon">₹</span>
                                </div>
                            </div>

                            <!-- agent charges received -->
                            <div class="w-1/2 px-3 sm:w-full sm:pb-5">
                                <label for="agent_commission_received"class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Agent commission received</label>
                                    <div class="rupee_price_field">
                                    <input type="text" name="agent_commission_received" id="agent_commission_received"
                                            class="w-full px-5 py-3 text-base font-normal leading-normal text-black
                                             bg-white border rounded-md  border-black500 focus:outline-none focus:border-siteYellow"
                                             placeholder="Enter agent commission received">
                                     <span class="rupee_icon">₹</span>
                                    </div>
                                 </div>
                                </div>
                           </div> --}}
                            <!-- -->

                            <div class="mt-[20px] mb-[14px] form_btn_sec afclr">
                                <input type="submit" value="CREATE BOOKING" id="submitCtaBtn"
                                    class="inline-block w-full px-5 py-3 text-opacity-40 text-base font-medium leading-tight
                                     transition-all duration-300 border border-siteYellow rounded-[4px] cursor-pointer bg-siteYellow
                                       md:px-[20px] md:py-[14px] sm:text-sm sm:py-[12px]  ease-0 hover:bg-[#d7b50a]  disabled:opacity-40 disabled:cursor-not-allowed ">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="fixed top-0 bottom-0 mx-auto session_custom_popup session_expire_popup  mx-auto
 custom_popup max-w-[40%] sm:max-w-[90%] lg:max-w-[77%] md:max-w-[80%] shadow-sm">
    <div class="popup_inner">
        <div class="popup_main">
          <div class="popup_main_heading py-7 px-9 bg-[#F6F6F6] flex items-center">
              <div class="p_m_h_left w-1/2">
                <p class="capitalize text-[#323232]">session timeout </p>
            </div>
              <div class="p_m_h_left w-1/2 flex justify-end ">
                <span>
                    <img src="{{asset('images/session_timeout.svg')}}" alt="timeout icon">
                </span>
             </div>
          </div>
          <div class="popup_main_content_sec text-left py-7 px-9 border border-t-[#D9D9D9] border-b-[#D9D9D9] border-l-0 border-r-0 bg-[#FFFFFF]">
            <p class="capitalize text-lg text-[#000000]"> You are reaching the maximum idle session duration timeout!</p>
          </div>
          <div class="pop_main_cta_sec py-[30px] px-9 bg-[#FFFFFF]">
            <div class=" flex justify-end basis-1/4 2xl:basis-full 2xl:grow-0 2xl:shrink-0 2xl:mb-[8px] 2xl:justify-center">
                <div class="flex justify-end -mx-2 flex-end">
                    <div class="px-2">
                        <a href="javascript:void(0);" data-action-btn="cancel"
                            class="cursor-pointer session_cta cancel_session_cta inline-block px-6 py-2.5 text-sm capitalize
                             font-normal leading-4 text-black border rounded border-siteYellow  transition-all duration-300 ease-in-out hover:bg-siteYellow400"
                            >
                            cancel
                        </a>
                    </div>
                    <div class="px-2">
                        <a class="inline-block px-6 session_cta proceed_session_cta py-2.5 text-sm font-normal leading-4
                        text-black border bg-siteYellow rounded border-siteYellow transition-all duration-300 ease-in-out hover:bg-siteYellow400"
                            href="javascript:void(0);" data-action-btn="proceed">Proceed</a>
                    </div>

                </div>
            </div>
          </div>
        </div>
    </div>
</div>
@php
    $isPageLoaded = session('carData', null);
    $bookingStatusId;
    $carId;
@endphp
<script src="{{asset('js/location.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
<script>
    var car_id= {{$carId}};
    var pickupDate= $('#start_date').val();
    var dropoffDate= $('#end_date').val();
    var pickupTime= '{{$pickupTime}}';
    var dropoffTime= '{{$dropoffTime}}';
    var action_type= '{{$action_type}}';
    // console.warn('carDetails:',pickupDate,dropoffDate,pickupTime,dropoffTime,action_type);

function checkLockedAndBooked(car_id,pickupDate,dropoffDate,pickupTime,dropoffTime,action_type,callback){
      let flagBase;
        $.ajax({
            url: "{{ route('partner.checkBooked') }}",
            method: "post",
            data: {
                'startDate': pickupDate,
                'endDate':dropoffDate,
                'pickupTime':pickupTime,
                'dropoffTime':dropoffTime,
                'carId': car_id,
                'actionType':action_type,
                _token: '{{ csrf_token() }}',
            },
            dataType: "json",
            success: function (data) {
                if(data.success){
                    flagBase=true;
                }else{

                    flagBase=false;
                }
                // console.warn('error true:',flagBase);
                callback(flagBase);
            },

            complete: function (data) {
                $(".loader").css("display", "none");
                $(".overlay_sections").css("display", "none");
            }
        });
}


$('#submitCtaBtn').on('click',function(e){
    e.preventDefault();

   let isFormValidate= checkForm();

    if(isFormValidate===true){

        checkLockedAndBooked(car_id, pickupDate, dropoffDate,pickupTime,dropoffTime, action_type,  function(result) {

            if(result==true){
                            Swal.fire({
                                    title:'Please select valid date, Either dates are Locked or Booked !! ',
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
            else
            {
                // console.warn('under else',$(this).closest('form'),'current obj:',$(this),'submmit:',$(this).closest('form').submit());

                $('#formId').submit();
                // $(this).closest('form').submit();

            }


        });

    }


});
var interval;
var seconds = 0;
const carId =  $('#carId').val();
const currentDate = new Date().toISOString().split('T')[0];
var bookingStatusId = @json($bookingStatusId);

function startTimer() {
    var initialSeconds = 600;
    var seconds = initialSeconds;
    interval = setInterval(function() {
        seconds--;
        $('#timer, #timer2').css('--percentage', (seconds / initialSeconds) * 100 + '%');
        var minutes = Math.floor(seconds / 60);
        var remainingSeconds = seconds % 60;
        $('.countdown').text(minutes + ':' + (remainingSeconds < 10 ? '0' : '') + remainingSeconds);
        if (seconds === 0) {
            $(".overlay_sections").css("display", "block");
            $('body').addClass('session_expire_popup');
            clearInterval(interval);
        }
    }, 1000);
}



$(document).ready(function () {
    startTimer();
    $('textarea').each(function(){
            $(this).val($(this).val().trim());
        }
    );
});


$(document).ready(function () {
    $(window).on('beforeunload', function (e) {
        if (!confirm('Are you sure to leave the page?')) {
            e.preventDefault();
        }
    });

   $('#number_of_days,#per_day_rental_charges').on('input change', calculateAmountTotal);

function calculateAmountTotal()
{
   var amountTotal = 0;
   $('.amount_total').each(function () {
    var price = $(this).val();
        price = price.replace(/,/g, '');
        var numericPrice = parseFloat(price);
        if (!isNaN(numericPrice)) {
            amountTotal += numericPrice;
         }
    });
    var per_day_rental_charges = parseFloat($('#per_day_rental_charges').val().replace(/,/g, '')) || 0;
    var number_of_days = parseFloat($('#number_of_days').val().replace(/,/g, '')) || 0;
    var pickup_charges = parseFloat($('#pickup_charges').val().replace(/,/g, '')) || 0;
    var dropoff_charges = parseFloat($('#dropoff_charges').val().replace(/,/g, '')) || 0;

    // console.log('per_day_rental_charges',per_day_rental_charges);
    // console.log('number_of_days',number_of_days);
    // console.log('pickup_charges',pickup_charges);
    // console.log('dropoff_charges',dropoff_charges);

    if ($.isNumeric(per_day_rental_charges) && $.isNumeric(number_of_days)) {
        var amountTotal = per_day_rental_charges * number_of_days + pickup_charges + dropoff_charges;
        // console.log('result', amountTotal);
    }
    var discount = parseFloat($('#discount').val().replace(/,/g, '')) || 0;
    amountTotal -= discount;

    // Set amountTotal to 0 if it is negative
    amountTotal = Math.max(0, amountTotal);

    if (amountTotal <= 0) {
        $('#total_booking_amount').css('color', 'red');
        // console.log("red");
    } else {
        $('#total_booking_amount').css('color', ''); // Reset color
        // console.log("black");
    }

    $('#total_booking_amount').val(parseInt(amountTotal).toLocaleString());
    updateTotal();
}


$('.amount_total').on('keyup', function () {
    calculateAmountTotal();
});

function updateTotal() {
    var calculateTotal = 0;
    $('.calculate_total').each(function () {
        var price = $(this).val();
        price = price.replace(/,/g, '');
        var numericPrice = parseFloat(price);

        if (!isNaN(numericPrice)) {
            calculateTotal += numericPrice;
        }
    });
    var advance_booking_amount = parseFloat($('#advance_booking_amount').val().replace(/,/g, '')) || 0;
    calculateTotal -= advance_booking_amount;

    // Set amountTotal to 0 if it is negative
    calculateTotal = Math.max(0, calculateTotal);

    if (calculateTotal <= 0) {
        $('#due_at_delivery').css('color', 'red');
    } else {
        $('#due_at_delivery').css('color', '');
    }

    $('#due_at_delivery').val(parseInt(calculateTotal).toLocaleString());
}

$('.calculate_total').on('keyup', function () {
    updateTotal();
});


$('#discount').on('keyup input', function () {
    if (parseFloat($(this).val().replace(/,/g, '')) > parseFloat($('#total_booking_amount').val().replace(/,/g, ''))) {
        $(this).closest('.inp_container').find('.required').show().html('You cannot fill a value greater than the total booking amount');
    } else {
        $(this).closest('.inp_container').find('.required').hide().html('');
    }
    calculateAmountTotal();
});


$('#advance_booking_amount').on('keyup input', function () {
    if (parseFloat($(this).val().replace(/,/g, '')) > parseFloat($('#due_at_delivery').val().replace(/,/g, ''))) {
        $(this).closest('.inp_container').find('.required').show().html('You cannot fill a value greater than the due at delivery');
    }
    else {
        if($(this).val().length>0){
        $(this).closest('.inp_container').find('.required').hide().html('');
        }
        else{
            $(this).closest('.inp_container').find('.required').show();

        }
    }
    updateTotal();
});

calculateAmountTotal();
updateTotal();
});


//$('.GobackDelete').on('click', function(e) {
//   e.preventDefault();
//  window.location.reload();
// });

// window.onhashchange = function(e) {
//     $(window).on('beforeunload', function (e) {
//         return "Are you sure to leave the page?";
//     });
// }


// window.onhashchange = function() {
//     if (window.innerDocClick) {
//         window.innerDocClick = false;
//     } else {
//         if (window.location.hash != '#undefined') {
//             goBack();
//         } else {
//             history.pushState("", document.title, window.location.pathname);
//             location.reload();
//         }
//     }
// }

$('.session_cta').on('click',function(e){
    $('body').removeClass('session_expire_popup');
    $(".loader").css("display", "inline-flex");
    $(".overlay_sections").css("display", "block");
    var calendarUrl = "{{ route('partner.booking.calendar') }}";
        e.preventDefault();
        var action_btn = $(this).data('action-btn');
          var start_date= $('#start_date').val();
            var end_date= $('#end_date').val();
            $.ajax({
                url: "{{ route('partner.booking.set.time') }}",
                method: "post",
                data: {
                    '_token': '{{ csrf_token() }}',
                    'action':action_btn,
                    'carId':{{$carId}},
                    'start_date':start_date,
                    'last_date':end_date
                },
                dataType: "json",
                success: function(data) {
                    if(data.success){
                        if (data.action === 'proceed') {
                            $(".loader").css("display", "none");
                            $(".overlay_sections").css("display", "none");
                            startTimer();
                        }
                        if (data.action === 'notAvailable') {
                            $(window).unbind('beforeunload');
                            $(".loader").css("display", "none");
                            $(".overlay_sections").css("display", "none");
                            Swal.fire({
                                title: data.msg,
                                icon: 'warning',
                                showCancelButton: false,
                                confirmButtonText: 'OK',
                                customClass: {
                                popup: 'popup_updated',
                                title: 'popup_title',
                                actions: 'btn_confirmation',
                                },
                                }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = calendarUrl;
                                }
                            });
                        }
                        if(data.action === 'cancel') {
                            $(window).unbind('beforeunload');
                            $(".loader").css("display", "none");
                            $(".overlay_sections").css("display", "none");
                            window.location.href = calendarUrl;
                        }
                    }
                },
                complete: function (data) {
                $(".loader").css("display", "none");
                $(".overlay_sections").css("display", "none");
            }
        });

    });
    $('.desktop_cancel_exit_btn, .mob_cancel_exit_btn').on('click', function(e) {
        e.preventDefault();
        $(window).unbind('beforeunload');
        $(".loader").css("display", "inline-flex");
        $(".overlay_sections").css("display", "block");
        var calendarUrl = "{{ route('partner.booking.calendar') }}";
         e.preventDefault();
         var action_btn = $(this).data('action-btn');
          var start_date= $('#start_date').val();
            var end_date= $('#end_date').val();
            $.ajax({
                url: "{{ route('partner.booking.set.time') }}",
                method: "post",
                data: {
                    '_token': '{{ csrf_token() }}',
                    'action':action_btn,
                    'carId':{{$carId}},
                    'start_date':start_date,
                    'last_date':end_date
                },
                dataType: "json",
                success: function(data) {
                    if(data.success){
                        if (data.action === 'notAvailable') {
                            $(window).unbind('beforeunload');
                            $(".loader").css("display", "none");
                            $(".overlay_sections").css("display", "none");
                            window.location.href = calendarUrl;
                        }
                        if(data.action === 'cancel') {
                            $(window).unbind('beforeunload');
                            $(".loader").css("display", "none");
                            $(".overlay_sections").css("display", "none");
                            window.location.href = calendarUrl;
                        }
                    }
                },
                complete: function (data) {
                $(".loader").css("display", "none");
                $(".overlay_sections").css("display", "none");
            }
        });
    });


    function checkBlankField(id, msg, msgContainer){
        if ($("#" + id).val().trim().length < 1){
            $("." + msgContainer).css("display", "block");
            $("." + msgContainer).html(msg);
        }
        else{
            $("." + msgContainer).html('');
            $("." + msgContainer).css("display", "none");
        }
    }

    function checkForm() {
        $('.loader').css("display", "inline-flex");
        $('.overlay_sections').css("display", "block");
          var required_fields = $('.required_field');
           var hasErrors = false;
           var amountError = false;
           var firstErrorElement = null;

           required_fields.each(function() {
            if (!$(this).val()) {
            hasErrors = true;
            $(this).closest('.inp_container').find('.required').html($(this).data("error-msg"));
            $(this).closest('.inp_container').find('.required').css('display','inline-block');
            $(this).closest('.inp_container').find('.error').hide();
            }
            else {
            $(this).closest('.inp_container').find('.required').empty();
            $(this).closest('.inp_container').find('.required').css('display','none');
            }
            if ($(this).hasClass('phone_validation')) {
            var inputValue = $(this).val().trim();
            var validationSpan = $(this).closest('.inp_container').find('.required');

            if (!inputValue) {
            hasErrors = true;
            validationSpan.css('display','inline-block');
            validationSpan.html("Customer mobile number is required");
            $(this).closest('.inp_container').find('.error').hide();
            }

            else {
            if (inputValue.length < 10) {
            hasErrors = true;
            validationSpan.css('display','inline-block');
            validationSpan.html("Customer mobile number must be at least 10 digits");
            $(this).closest('.inp_container').find('.error').hide();
            }

            else if (inputValue.length === 10) {
            validationSpan.empty();
            validationSpan.css('display','none');
            }

            else {
            validationSpan.empty();
            validationSpan.css('display','none');
            }
            }
            }
          });

          if (parseFloat($('#discount').val().replace(/,/g, '')) > parseFloat($('#total_booking_amount').val().replace(/,/g, ''))) {
            $('#discount').closest('.inp_container').find('.required').show().html('You cannot fill a value greater than the total booking amount');
            if (!firstErrorElement) {
               firstErrorElement = $('#discount');
            }
            amountError = true;
            } else {
            $('#discount').closest('.inp_container').find('.required').hide().html('');
          }

          if (parseFloat($('#advance_booking_amount').val().replace(/,/g, '')) > parseFloat($('#due_at_delivery').val().replace(/,/g, ''))) {
           $('#advance_booking_amount').closest('.inp_container').find('.required').show().html('You cannot fill a value greater than the due at delivery');
           if (!firstErrorElement) {
               firstErrorElement = $('#advance_booking_amount');
            }
            amountError = true;
          }
          else {
            // $('#advance_booking_amount').closest('.inp_container').find('.required').hide().html('');
          }
     if (!hasErrors) {
        if (amountError) {
        Swal.fire({
            title: 'Please fill a valid value',
            icon: 'warning',
            showCancelButton: false,
            confirmButtonText: 'OK',
            customClass: {
                popup: 'popup_updated',
                title: 'popup_title',
                actions: 'btn_confirmation',
            },
        }).then((result) => {
            if (result.isConfirmed && firstErrorElement) {
                $("html, body").animate({
                    scrollTop: parseFloat(firstErrorElement.offset().top) - 150
                }, 500);
            }
        });
        hideLoader();
        return false;
     }
     else {
      $(window).unbind('beforeunload');
      return true;
    }
    }

         if (hasErrors) {
         hideLoader();
          Swal.fire({
          title: 'Please fill all required fields',
          icon: 'warning',
          showCancelButton: false,
          confirmButtonText: 'OK',
          customClass: {
          popup: 'popup_updated',
          title: 'popup_title',
          actions: 'btn_confirmation',
          },
          }).then((result) => {
          if (result.isConfirmed) {
          $("html, body").animate({
          scrollTop: parseFloat($(".required:visible:first").offset().top) - 150
         }, 500);
         }
         });
         hideLoader();
         return false;
         }
         else {
         $(window).unbind('beforeunload');
         // hideLoader();
         return true;
        }
     }


     $('.required_field').on('keyup', function () {
        var hasErrors = false;
        if (!$(this).val().trim()) {
            hasErrors = true;
            console.log('required_field:',$(this).data("error-msg"),'element:', $(this).closest('.inp_container').find('.required'));
            $(this).closest('.inp_container').find('.required').show().html($(this).data("error-msg"));

            $(this).closest('.inp_container').find('.error').hide();
        } else {

            $(this).closest('.inp_container').find('.required').hide();
            $(this).closest('.inp_container').find('.error').hide();
        }

        if ($(this).hasClass('phone_validation')) {
            var inputValue = $(this).val().trim();
            var validationSpan = $(this).closest('.inp_container').find('.required');
            $(this).closest('.inp_container').find('.error').hide();
            if (!inputValue) {
                validationSpan.html("Customer mobile number is required");
                $(this).closest('.inp_container').find('.error').hide();
            } else {
                if (inputValue.length < 10) {
                    validationSpan.show().html("Customer mobile number must be at least 10 digits");
                    $(this).closest('.inp_container').find('.error').hide();
                } else if (inputValue.length === 10) {
                    validationSpan.hide();
                } else {
                    validationSpan.hide();
                }
            }
        }
    });


    $('#other_pickup_location').on('keyup', function(e) {
    e.preventDefault();
    if ($(this).val() !== '') {
        $('#other_pickup_location').closest('.inp_container').find('.required').hide().empty();
        $('#other_pickup_location').closest('.inp_container').find('.error').hide().empty();
        $('.DisplayPickupLocation').removeClass('hidden').html('<span class="text-black500 capitalize">pickup: </span>'+$(this).val());

    }else{
        $('#other_pickup_location').closest('.inp_container').find('.required').show().html($(this).data("error-msg"));
        $('.DisplayPickupLocation').addClass('hidden');
    }
    });

    $('#other_dropoff_location').on('keyup', function(e) {
    e.preventDefault();
    if ($(this).val() !== '') {
        $('#other_dropoff_location').closest('.inp_container').find('.required').hide().empty();
        $('#other_dropoff_location').closest('.inp_container').find('.error').hide().empty();
        $('.DisplayDropoffLocation').removeClass('hidden').html('<span class="text-black500 capitalize">dropoff: </span>'+$(this).val());


    }
    else{
        $('#other_dropoff_location').closest('.inp_container').find('.required').show().html($(this).data("error-msg"));
        $('.DisplayDropoffLocation').addClass('hidden');
    }
    });


 $('body').on('input','.mobile_num_validate',function(){
   let numericValue =  $(this).val().replace(/[^0-9]/g, "");
   numericValue = numericValue.substring(0, 10);
   $(this).val(numericValue);
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

var inputIds = ['pickup_charges', 'dropoff_charges', 'per_day_rental_charges', 'discount', 'advance_booking_amount', 'refundable_security_deposit'
// , 'agent_commission', 'agent_commission_received'
];

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



$('#number_of_days').on('input', function () {
   let inputValue = $(this).val();
   let defaultValue= $('#number_of_days_default_value').val();
   if(!inputValue || inputValue==defaultValue || inputValue >=defaultValue || inputValue > 0){
    if (/^\d*\.?(\d{0,1})$/.test(inputValue) && /^[0-9.]*$/.test(inputValue)) {
    if (inputValue.includes('.') && (inputValue.endsWith('8') || inputValue.endsWith('9') || inputValue.endsWith('7') || inputValue.endsWith('6') || inputValue.endsWith('4') || inputValue.endsWith('3') || inputValue.endsWith('2') || inputValue.endsWith('1') || inputValue.endsWith('0'))) {
        let integerPart = inputValue.split('.')[0];
        let decimalPart = inputValue.split('.')[1].charAt(0);
        inputValue = integerPart + '.' + (decimalPart === '5' ? decimalPart : '5');
    }
    $(this).val(inputValue);
    }
    else{
        let truncatedValue = inputValue.split('.')[0];
        let decimalPart = inputValue.split('.')[1]?.charAt(0);
        if (decimalPart) {
            truncatedValue += '.' + decimalPart;
        }
        $(this).val(truncatedValue);
    }
  }
  else{
    $(this).val(defaultValue);
  }
});



$('#number_of_days').on('blur', function () {
    let inputValue = $(this).val();
    if (/^\d*\.?(\d{0,1})$/.test(inputValue) && /^[0-9.]*$/.test(inputValue)) {
        let decimalPart = inputValue.split('.')[1];
        if (!decimalPart) {
            $(this).val(inputValue.replace(/\.$/, ''));
        }
    }
});
  var utilsScript = "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js";
    var input = document.querySelector("#customer_mobile");
    window.intlTelInput(input, {
    initialCountry: "auto",
    preferredCountries:["in", "us", "gb"],
    formatOnDisplay: false,
    separateDialCode: true,
    geoIpLookup: callback => {
    fetch("https://ipapi.co/json")
    .then(res => res.json())
    .then(data => callback(data.country_code))
    .catch(() => callback("in"));
    },
    utilsScript: utilsScript
    });

    input.addEventListener("countrychange", function() {
        var intlNumber = $('.iti__selected-dial-code').html();
        $('.customer_mobile_country_code').val(intlNumber);
    });

    $(document).ready(function () {
    $('#dropoff_location').on('change', function () {
        if ($(this).val() == 'Other') {
        $('#other_dropoff_location').val('');
        $('.other_dropoff_location_container').show();
        $('#other_dropoff_location').addClass('required_field');
        $('.DisplayDropoffLocation').addClass('hidden');

    }else{
        $('#other_dropoff_location').removeClass('required_field');
        $('.other_dropoff_location_container').hide();
        $('#dropoff_location').closest('.inp_container').find('.error').hide().empty();
        $('#dropoff_location').closest('.inp_container').find('.required').hide().empty();
        $('.DisplayDropoffLocation').removeClass('hidden').html('<span class="text-black500 capitalize">dropoff: </span>' + $(this).val());
    }
    });
    $('#pickup_location').on('change', function () {
           if ($(this).val() == 'Other') {
               $('#other_pickup_location').val('');
               $('.other_pickup_location_container').show();
               $('#other_pickup_location').addClass('required_field');
               $('.DisplayPickupLocation').addClass('hidden');
           }
           else{
               $('#other_pickup_location').removeClass('required_field');
               $('.other_pickup_location_container').hide();
               $('#pickup_location').closest('.inp_container').find('.error').hide().empty();
               $('#pickup_location').closest('.inp_container').find('.required').hide().empty();
               $('.DisplayPickupLocation').removeClass('hidden').html('<span class="text-black500 capitalize">pickup: </span>'+ $(this).val());

           }
       });
    });

    $("#pickup_location, #dropoff_location").select2({
            ajax: {
                delay: 250,
                url: "{{ route('partner.location.json') }}",
                method: 'POST',
                dataType: 'json',
                data: function (params) {
                    return {
                        '_token': '{{ csrf_token() }}',
                        'search': params.term,
                        // page: params.page
                    };
                },
               processResults: function(data) {
                    var results = [];
                    console.log('data.locations:',data.locations);
                    $.each(data.locations, function(group, locations) {
                            if (locations.length > 0) {
                                var groupOptions = locations.map(function(location) {
                                    return {
                                        id: location.location,
                                        text: location.location
                                    };
                                });

                                results.push({
                                    text: group,
                                    children: groupOptions
                                });
                            }
                        });

                        console.log('results:',results);
                    return {
                        results: results
                    };
                },

                error: function(xhr, status, error) {
                    console.error(error);
                }
            }
        });
</script>
@endsection
