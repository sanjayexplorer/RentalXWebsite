@extends('layouts.agent')
@section('title', 'Edit Booking Details')
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
    $partnerCompanyName = ucwords(Helper::getUserMetaByCarId($carId, 'company_name'));
    $agentCompanyName   = ucwords(Helper::getUserMeta(auth()->user()->id, 'company_name'));
     $car_name=Helper::getCarNameByCarId($carId);
    $registeration_number=Helper::getCarRagisterationNoByCarId($carId);
    $pickup_location = $car_booking->pickup_location;
    $dropoff_location = $car_booking->dropoff_location;
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
@media screen and (max-width: 992px) {
.main_header_container{display:none
}
}
 </style>
    <div class="dashboard_ct_section">
        <div class="lg:flex hidden lg:bg-white lg:mb-[0px] lg:px-[17px] lg:py-[15px] mb-[20px] justify-start items-center">
            <div class="flex items-center justify-start w-1/2">
                <a href="{{route('agent.booking.view',$car_booking->id)}}" class="links_item_cta inline-block p-2 mr-2 border border-[#C8C0C0] rounded-md hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E] ">
                    <img class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
                </a>
                <span class="inline-block sm:text-base sm:text-left text-xl font-normal leading-normal align-middle text-black800">
                Edit Booking
                </span>
            </div>
        </div>

        <div class="pt-[42px] px-[36px] lg:px-[17px] lg:py-[20px] xl:py-10 bg-textGray400 pb-[100px]  min-h-screen lg:flex lg:justify-center">

            <div class="lg:hidden flex items-center mb-[36px] lg:mb-[20px]">

                <div class="lg:hidden flex flex-col">
                    <div class="back-button">
                        <a href="{{route('agent.booking.view',$car_booking->id)}}" class="links_item_cta inline-flex py-1 px-2 border border-[#C8C0C0] rounded-md bg-[#fff] hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E] GobackDelete">
                            <img  class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
                            <span class="inline-block text-base leading-normal align-middle text-black800 ml-[10px] font-medium">
                                Booking View
                            </span>
                        </a>
                    </div>
                    <span class="inline-block text-[26px] font-normal leading-normal align-middle text-black800">
                        Edit Booking
                    </span>
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
                                                <img src="{{$modifiedImgUrl}}" alt="creta" class="object-contain max-w-full max-h-full">
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
                                                    $firstdateObj = new DateTime($car_booking->pickup_date);
                                                    $pickupFormattedDate = $firstdateObj->format('d M Y');
                                                @endphp

                                                <h4
                                                    class="sm:text-[13px] text-[14px] font-medium text-black mb-[5px] capitalize">
                                                    FROM</h4>
                                            <div class="block">
                                                <h4 class="text-black font-medium leading-4 text-[14px] sm:text-[13px] mb-[3px]"><span id="pickupDateTimeSec">{{$pickupFormattedDate}}, {{$car_booking->pickup_time}}</span></h4>
                                            </div>
                                            <div class="DisplayPickupLocation">
                                                 <p class="text-[#898376] font-normal leading-4 text-[14px] sm:text-[13px]"><span class="text-black500 capitalize">pickup:&nbsp;</span>{{$car_booking->pickup_location}}</p>
                                             </div>
                                            </div>
                                <div class="w-1/2 py-[16px] pl-2 text-center right_part py-[10px] form_inner_right sm:py-[10px]">
                                @php
                                    $lastdateObj = new DateTime($car_booking->dropoff_date);
                                    $dropoffFormattedDate = $lastdateObj->format('d M Y');
                                @endphp
                                <h4 class="sm:text-[13px] text-[14px] font-medium text-black mb-[5px] capitalize">TO</h4>
                                <div class="block">
                                        <h4 class="text-black font-medium leading-4 text-[14px] sm:text-[13px] mb-[3px]"> <span id="dropoffDateTimeSec">{{$dropoffFormattedDate}}, {{$car_booking->dropoff_time}}</span></h4>
                                </div>
                                <div class="DisplayDropoffLocation">
                                    <p class="text-[#898376] font-normal leading-4 text-[14px] sm:text-[13px]"><span class="text-black500 capitalize">dropoff:&nbsp;</span>{{$car_booking->dropoff_location}}</p>
                                 </div>
                                </div>

                                </div>
                                </div>
                                </div>
                            </div>
                        </div>
                <div class="mt-[28px] mb-[30px] form_btn_sec afclr">
                <a href="javascript:void(0);"  data-src="#open_popup"
                class="inline-block edit_date_and_time text-center w-full px-5 py-3 text-opacity-40 text-base font-medium
                leading-tight transition-all duration-300 border border-siteYellow rounded-[4px] cursor-pointer bg-siteYellow
                md:px-[20px] md:py-[14px] sm:text-sm sm:py-[12px] transition-all duration-500 ease-0 hover:bg-[#d7b50a]
                disabled:opacity-40 disabled:cursor-not-allowed uppercase">Edit Date Time</a>
        </div>
        <form action="{{route('agent.edit.customer.details.post',$car_booking->id)}}" class="mt-5" method="POST" onsubmit="return checkForm()">
          @csrf
            <input class="hidden" type="text" id="carId" name="carId" value="{{$carId}}">

            <input class="hidden" type="text" id="start_date" name="start_date" value="{{$car_booking->start_date}}">
            <input class="hidden" type="text" id="end_date" name="end_date" value="{{$car_booking->end_date}}">
            <input class="hidden" type="text" id="start_time" name="start_time" value="{{$car_booking->pickup_time}}">
            <input class="hidden" type="text" id="end_time" name="end_time" value="{{$car_booking->dropoff_time}}">
            <input class="hidden" type="text" id="bookingId" name="bookingId" value="{{$car_booking->bookingId}}">
            <input class="hidden" type="text" id="id" name="id" value="{{$car_booking->id}}">
            <input class="hidden" type="text" id="car_name" name="car_name" value="{{$car_name}}">
            <input class="hidden" type="text" id="registeration_number" name="registeration_number" value="{{$registeration_number}}">

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
                                value="{{$car_booking->customer_name}}"
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
                                <div class="w-1/2 px-3 sm:px-[0px] sm:w-full inp_container customer_mobile">
                                    <label for="customer_mobile"
                                        class="inline-block pb-2.5 text-sm font-normal leading-4 text-left text-black">Customer
                                        Mobile number<span class="text-[#ff0000]">*</span></label>
                                    <input type="text" tabindex="-1" name="customer_mobile" id="customer_mobile"
                                        class="required_field phone_validation mobile_num_validate w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border rounded-md
                                        border-black500 focus:outline-none focus:border-siteYellow" value="{{$car_booking->customer_mobile_country_code}} {{$car_booking->customer_mobile}}"
                                        placeholder="Enter mobile number" data-error-msg="Customer mobile number is required"
                                        onkeyup="checkBlankField(this.id,'Customer mobile number is required','errorCustomerMobile')">
                                    <span class="hidden required text-sm"></span>
                                   <input type="hidden" name="customer_mobile_country_code" class="customer_mobile_country_code" value="{{$car_booking->customer_mobile_country_code}}">

                                    @if ($errors->has('customer_mobile'))
                                    <div class="error text-sm">
                                        <ul>
                                            <li>{{ $errors->first('customer_mobile') }}</li>
                                        </ul>
                                    </div>
                                    @endif
                                </div>

                                <div class="w-1/2 px-3 sm:px-[0px] sm:w-full alt_customer_mobile">
                                    <label for="alt_customer_mobile"
                                        class="inline-block pb-2.5 text-sm font-normal leading-4 text-left text-black">Customer
                                        Alternate Mobile number</label>
                                    <input type="text" tabindex="-1" name="alt_customer_mobile" id="alt_customer_mobile"
                                        class="mobile_num_validate w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border rounded-md
                                        border-black500 focus:outline-none focus:border-siteYellow" value="{{$car_booking->alt_customer_mobile_country_code}} {{$car_booking->alt_customer_mobile}}"
                                        placeholder="Enter alternate mobile number"
                                       >
                                   <input type="hidden" name="alt_customer_mobile_country_code" class="alt_customer_mobile_country_code" value="{{$car_booking->alt_customer_mobile_country_code}}">
                                </div>

                                <!-- <div class="w-1/2 sm:mt-5 px-3 sm:px-[0px] sm:w-full">
                                    <label for="customer_email"
                                        class="inline-block pb-2.5 text-sm font-normal leading-4 text-left text-black">Customer
                                        email (optional)</label>
                                    <input type="email" tabindex="-1" id="customer_email" name="customer_email"
                                        class="w-full px-5 py-3 text-base font-normal leading-normal text-black
                                         bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow" value="{{$car_booking->customer_email}}"
                                        placeholder="Enter email" data-error-msg="">
                                    <span class=" hidden required text-sm"></span>
                                </div> -->

                            </div>

                            <div class="pb-5">
                                <div class="flex flex-wrap -mx-3">
                                    <div class="w-full px-3 sm:w-full">
                                        <label for="customer_email"
                                            class="block pb-2.5 text-sm font-normal leading-4 text-left text-black ">
                                            Customer email (optional)</label>
                                        <input id="customer_email" tabindex="-1"
                                            class="w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white
                                            border rounded-md border-black500 focus:outline-none focus:border-siteYellow"
                                            type="text" name="customer_email" placeholder="Enter email">
                                    </div>
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
                                            value="{{$car_booking->customer_city}}"
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
                                            <select name="pickup_location" id="pickup_location"
                                                onchange="checkBlankField(this.id,'Pickup location is required','errorPickup')"
                                                class="required_field w-full py-3 pl-5 pr-10 text-base font-normal leading-normal text-black bg-white border rounded-md appearance-none s_tag_haf border-black500 focus:outline-none focus:border-siteYellow drop_location_select"
                                                data-error-msg="Pickup location is required">
                                                <option value="{{ $pickup_location !== '' ? $pickup_location : '' }}">
                                                {{ strcmp($pickup_location,'') !== 0 ? $pickup_location : 'Select pickup location' }}
                                                 </option>
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
                                <input type="text" tabindex="-1" name="other_pickup_location" id="other_pickup_location"
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
                                       <select name="dropoff_location" id="dropoff_location"
                                        onchange="checkBlankField(this.id,'Dropoff location is required','errorDropoff')"
                                        class="required_field w-full py-3 pl-5 pr-10 text-base font-normal leading-normal text-black bg-white border rounded-md appearance-none s_tag_haf border-black500 focus:outline-none focus:border-siteYellow drop_location_select"
                                        data-error-msg="Dropoff location is required">
                                        <option value="{{ $dropoff_location !== '' ? $dropoff_location : '' }}">
                                        {{ strcmp($dropoff_location,'') !== 0 ? $dropoff_location : 'Select dropoff location' }}
                                       </option>
                                        </select>
                                        <span class=" hidden required text-sm"></span>
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
                                <input type="text" tabindex="-1" name="other_dropoff_location" id="other_dropoff_location"
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
                                    <input type="text" tabindex="-1" name="per_day_rental_charges" id="per_day_rental_charges"
                                        class="amount_total required_field w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow pl-[33px]"
                                        placeholder="Enter Per day rental charges"
                                        data-error-msg="Per day rental charges is required"
                                       value="{{number_format($car_booking->per_day_rental_charges, 0, '.', ',')}}">
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


                            <div class="flex flex-wrap -mx-3">
                                <input type="hidden"  id="number_of_days_default_value" value="{{$car_booking->number_of_days}}">
                                <div class="w-full px-3 sm:w-full inp_container">
                                    <label for="number_of_days" class="flex justify-between block pb-2.5 text-sm font-normal leading-4 text-left text-black">
                                        <p>Number of days<span class="text-[#ff0000]">*</span></p>
                                        <p class="text-[#777777]">Suggested number of days: {{intval($car_booking->number_of_days)}} days</p>
                                    </label>
                                    <input type="text" tabindex="-1" name="number_of_days" id="number_of_days"
                                    class="required_field w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white
                                    border rounded-md border-black500 focus:outline-none focus:border-siteYellow"
                                    placeholder="Enter number of days"
                                    data-error-msg="Number of days is required" value="{{$car_booking->number_of_days}}">
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
                                         placeholder="Enter pickup charges" value="{{$car_booking->pickup_charges}}"
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
                                    <input type="text" tabindex="-1" name="dropoff_charges" id="dropoff_charges"
                                            class="amount_total w-full px-5 py-3 required_field text-base font-normal leading-normal text-black bg-white border rounded-md  border-black500 focus:outline-none focus:border-siteYellow pl-[33px]"
                                             placeholder="Enter dropoff charges"
                                             data-error-msg="Dropoff charges is required" value="{{$car_booking->dropoff_charges}}">
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
                                        <input type="text" tabindex="-1" name="discount" id="discount"
                                            class="w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white
                                            border rounded-md border-black500 focus:outline-none focus:border-siteYellow pl-[33px]"
                                            placeholder="Enter discount" value="{{$car_booking->discount}}">
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
                        <input type="text" tabindex="-1" name="total_booking_amount" id="total_booking_amount"
                        class="calculate_total w-full px-5 py-[3px] text-base font-medium leading-normal text-black bg-white rounded-md outline-none lg:bg-[#F6F6F6] pl-[18px]"
                        placeholder="Enter Total booking amount" value="{{$car_booking->total_booking_amount}}"  readonly>
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
                                <input type="text" tabindex="-1" name="advance_booking_amount" id="advance_booking_amount"
                                    class="required_field w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow pl-[33px]"
                                    placeholder="Enter advance booking amount"
                                    data-error-msg="Advance booking amount is required" value="{{$car_booking->advance_booking_amount}}">
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
                                    data-error-msg="Refundable security deposit is required" value="{{$car_booking->refundable_security_deposit}}">
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
                                 <input type="text" tabindex="-1" name="due_at_delivery" id="due_at_delivery"
                                    class="w-full px-5 py-[3px] text-base font-medium leading-normal text-black bg-white rounded-md outline-none lg:bg-[#F6F6F6] pl-[18px]"
                                    placeholder="Enter due at delivery" value="{{$car_booking->due_at_delivery}}" readonly>
                                <span class="rupee_icon" style="left:5px;">₹</span>
                            </div>
                         </div>
                        </div>
                    </div>
                   <!-- -->

                    <!--booking remarks -->
                    <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full px-3 sm:w-full">
                                <label for="booking_remarks" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Internal notes (optional)</label>
                                <textarea type="text" name="booking_remarks" id="booking_remarks" rows="4" class="w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow">
                                    {{$car_booking->booking_remarks}}
                                </textarea>
                            </div>
                        </div>
                    </div>

                    <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full px-3 sm:w-full inp_container">
                                <label for="agent_commission" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Booking commission</label>

                                <div class="rupee_price_field">
                                    <input type="text" tabindex="-1" name="agent_commission" id="agent_commission"
                                            class="w-full px-5 py-3 text-base font-normal leading-normal text-black
                                                bg-white border rounded-md  border-black500 focus:outline-none focus:border-siteYellow pl-[33px] commission_amount"
                                                placeholder="Enter agent commission" value="{{$car_booking->agent_commission}}">
                                        <span class="rupee_icon">₹</span>
                                    </div>
                            </div>
                        </div>
                    </div>

                    <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full px-3 sm:w-full inp_container">
                                <label for="agent_commission" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Booking commission received</label>

                                <div class="rupee_price_field">
                                    <input type="text" tabindex="-1" name="agent_commission_received" id="agent_commission_received"
                                            class="w-full px-5 py-3 text-base font-normal leading-normal text-black
                                                bg-white border rounded-md  border-black500 focus:outline-none focus:border-siteYellow pl-[33px] commission_amount"
                                                placeholder="Enter agent commission received" value="{{$car_booking->agent_commission_received}}">
                                        <span class="rupee_icon">₹</span>
                                    </div>
                            </div>
                        </div>
                    </div>

                    <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full px-3 sm:w-full">
                               <label for="final_commission_amount" class="block pb-2 text-sm font-medium leading-4 text-left text-black final_commission_amount">
                                Final commission amount</label>
                                <div class="rupee_price_field">
                                 <input type="text" tabindex="-1" name="final_commission_amount" id="final_commission_amount"
                                    class="w-full px-5 py-[3px] text-base font-medium leading-normal text-black bg-white rounded-md outline-none lg:bg-[#F6F6F6] pl-[18px]"
                                    placeholder="" value="" readonly>
                                <span class="rupee_icon" style="left:5px;">₹</span>
                            </div>
                         </div>
                        </div>
                    </div>
                   <!-- -->

                    <!-- -->
                            <div class="mt-[20px] mb-[14px] form_btn_sec afclr">
                                <input type="submit" value="save"
                                    class="inline-block uppercase w-full px-5 py-3 text-opacity-40 text-base font-medium leading-tight
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


<!-- dates /pickup and dropoff popup -->
<div class="open_popup_secc">
    <div class="form_section_b modal custom_pick_drop_popup" id="open_popup">
        <div class="form_section_b_inner">
            <form method="post" id="car_popup_form" class="car_popup_form" autocomplete="off">
                <input type="hidden" name="book_car" value="" class="book_car">
                <input type="hidden" name="car_name" value="" class="car_name">
                <input type="hidden" name="car_id" value="" class="car_id" id="fancyBox_car_id">
                <input type="hidden" name="" value="" class="fancyBox_action_type" id="fancyBox_action_type">
                <input type="hidden" name="" value="" class="selected_date">
                <input type="hidden" name="" class="" id="datepicker_first_date">
                <input type="hidden" name="" class="" id="datepicker_last_date">
                <input type="hidden" name="" class="" id="datepicker_pickup_time">
                <input type="hidden" name="" class="" id="datepicker_dropoff_time">
                <input type="hidden" name="" class="" id="fancyBox_overlap_previous_pickup_time">
                <input type="hidden" name="" class="" id="fancyBox_overlapped_pickupTime">

                {{-- for overlappingDropoffTime --}}
                <input type="hidden" name="" class="" id="datepicker_overlappingTime">
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
                        <div class="number_box">


                        </div>
                    </div>
                </div>
        </div>
        </form>
    </div>
</div>

<input type="hidden" name="" id="pickupDate" value="{{$car_booking->pickup_date}}">
<input type="hidden" name="" id="dropoffDate" value="{{$car_booking->dropoff_date}}">
<input type="hidden" name="" id="viewRoute" value="{{route('agent.booking.view',$car_booking->id)}}">
<script>



    // console.warn('locationArr',locationArr);

    var agentCompanyName = '{{$agentCompanyName}}';
    var partnerCompanyName = '{{$partnerCompanyName}}';
    var agent_commission = parseFloat($('#agent_commission').val().replace(/,/g, '') || 0);
    var commission_received =  parseFloat($('#agent_commission_received').val().replace(/,/g, '') || 0);
    $(document).ready(function () {

    $('#agent_commission, #agent_commission_received').on('input', function() {
            agent_commission = parseFloat($('#agent_commission').val().replace(/,/g, '') || 0);
            commission_received = parseFloat($('#agent_commission_received').val().replace(/,/g, '') || 0);
            updateFinalAmount(agent_commission, commission_received);
        });
    updateFinalAmount(agent_commission, commission_received);

    $('textarea').each(function(){
            $(this).val($(this).val().trim());
        }
    );
});

function updateFinalAmount(agent_commission, commission_received) {

    console.warn('under fun',agent_commission);

    var final_amount = Math.abs(agent_commission - commission_received);
    $('#final_commission_amount').val(final_amount.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ','));

    if (agent_commission > commission_received) {
        $('.final_commission_amount').text(partnerCompanyName + ' to ' + agentCompanyName);
    } else if (agent_commission < commission_received) {
        $('.final_commission_amount').text(agentCompanyName + ' to ' + partnerCompanyName);

    } else {
        $('.final_commission_amount').text('Final commission amount');
    }
}

var editClicked=false;
var bookingId={{$car_booking->id}};
var carId= {{$carId}};
var pickupDate=$('#pickupDate').val();
var dropoffDate=$('#dropoffDate').val();



var formattedPickup= $.datepicker.formatDate('dd/mm/yy', new Date(pickupDate));
var formattedDropoff=$.datepicker.formatDate('dd/mm/yy', new Date(dropoffDate));
var pickupTime=moment(pickupDate).format("hh:mm A");
// dropoffTime
var dropoffTime=moment(dropoffDate).format("hh:mm A");


// console.warn('pickupDate:',pickupDate,'dropoffDate:',dropoffDate,'pickupTime:',pickupTime,'dropoffTime',dropoffTime);

var  allDisableDates =[];

$('body').on('click', '.edit_date_and_time', function (e) {
    allDisableDates = [];

      $('#pickup_date').val(convertdates(pickupDate)+' | '+ pickupTime);

      if( editClicked===false){
        // console.warn('under the edit editClicked',editClicked);

          $('.fare_info_input').siblings('.dapicker_val__pickup_text').text( convertdates(pickupDate)+' | '+ pickupTime);
          $('.fare_info_input').siblings('.dapicker_val__dropoff_text').text( convertdates(dropoffDate)+' | '+ dropoffTime);

          $('#fancyBoxPickupText').val(convertdates(pickupDate)+' | '+ pickupTime);
          $('#fancyBoxDropoffText').val(convertdates(dropoffDate)+' | '+ dropoffTime);
      }
      else{

        // console.warn('under the else',editClicked);


        $('.fare_info_input').siblings('.dapicker_val__pickup_text').text($('#fancyBoxPickupText').val());
        $('.fare_info_input').siblings('.dapicker_val__dropoff_text').text( $('#fancyBoxDropoffText').val());

      }

      editClicked=true;

      isBookingCancelBtn = false;
      $(".loader").css("display", "inline-flex");
      $(".overlay_sections").css("display", "block");
      allDisableDates =getAllBookedAndLockedDates(carId);
      $.fancybox.open({
          src: $(this).data('src'),
          beforeShow: function (instance, current) {
              const fancybox = this;

            //   console.warn('pickupTime:',pickupDate,'dropoffDate:',dropoffDate);

            //   $('.car_book__input_val').siblings('#pickup_date').text()
            //   $('.fare_info_input').siblings('.dapicker_val_text').text('dd/mm/yyyy | hh:mm');

            //   $('.datepicker_input', current.$content).datepicker('setDate', formattedPickup);

              $('#fancyBox_car_id').val(carId);
            //   var date2 = moment(formattedDate, "DD.MM.YYYY");
            //   var gap_day = addDays(date2, day_gap);
            //   let convertedGapDates = moment(gap_day).format("YYYY-MM-DD HH:mm:ss");
            //   let clickedDate = moment(formattedDate, "DD/MM/YYYY").format("DD MMMM YYYY");

            //   $(".datepicker_input2", current.$content).datepicker('option', 'minDate',formattedDropoff );


            //   if ($(".datepicker_input", current.$content).val() < $(".datepicker_input2", current.$content).val()) {
            //       $('.datepicker_input2', current.$content).datepicker('setDate', $('.datepicker_input2', current.$content).val());
            //   } else {
            //     //   $('.datepicker_input2', current.$content).datepicker('setDate', gap_day);
            //   }
          }
      });
      select_pickup();


});




function diffrenceInDates(date1,date2,pickupTime,dropoffTime){
        const firstDate = moment(date1);
        const secondDate = moment(date2);
        let difference =secondDate.diff(firstDate, 'days');

        const pickupMoment = moment(pickupTime, 'h:mm A');
        const dropoffMoment = moment(dropoffTime, 'h:mm A');

        if (pickupMoment.isBefore(moment('9:00 AM', 'h:mm A'))  ) {
            difference++;
        }
        if(dropoffMoment.isAfter(moment('9:00 AM', 'h:mm A'))){
            difference++;
        }
        return difference;
}



$('body').on('click', '.close_popup', function (e) {
        e.preventDefault();
        $.fancybox.close();
        change_number = true;
        $('.data_list_here').val('');
        $('.car_book__input_box').removeClass('pickup,drop');
        $('.datepicker_sec').removeClass('active');
        navigateTo(0);
});

var day_gap = 1;
	var hour_gap = 0;
	var min_gap = 30;
	var current_time = new Date();
	var current_hour;
	var current_min;
	current_min = current_time.getMinutes();
	var total_min = (current_time.getHours() * 60) + current_time.getMinutes();


	function addDays(date, days) {
		var result = new Date(date);
		result.setDate(result.getDate() + days);
		return result;
	}

	$(".datepicker_inner_b").click(function() {
		var temp_time = $(this).find(".time_hour").val() + ":" +
        $(this).find(".time_min").val() + " " +
        $(this).find(".time_am_pm").val();
		selected_pick_time = temp_time;
		var date = moment($(".datepicker_input").val(), "DD/MM/YYYY").format("DD MMMM YYYY");
		var tab_date1 = moment($(".datepicker_input").val(), "DD/MM/YYYY").format("DD MMM YYYY");
		$("#pickup_date").val(date + " | " + temp_time);
		$(".dapicker_val__pickup_text").html(date + " | " + temp_time);
        $('#datepicker_pickup_time').val(temp_time);
		$(".dapicker_val__tab_pickup_text").html(tab_date1 + " | " + temp_time);
		var temp_date = $(".datepicker_input").val();
		temp_date = moment(temp_date, "DD/MM/YYYY").format();
		temp_date = new Date(temp_date);
	});

	$(".datepicker_inner_b2").click(function() {
		var temp_time = $(this).find(".time_hour").val() + ":" +
        $(this).find(".time_min").val() + " " +
        $(this).find(".time_am_pm").val();
		selected_drop_time = temp_time;
        $('#fancyBox_pickup_time').val(temp_time);
		var date2 = moment($(".datepicker_input2").val(), "DD/MM/YYYY").format("DD MMMM YYYY");
		var tab_date2 = moment($(".datepicker_input2").val(), "DD/MM/YYYY").format("DD MMM YYYY");
		$("#dropoff_date").val(date2 + " | " + temp_time);
		$(".dapicker_val__dropoff_text").html(date2 + " | " + temp_time);
		$(".dapicker_val__tab_dropoff_text").html(tab_date2 + " | " + temp_time);
	});

	$ = jQuery;
	$('.tab_item_pickup').on('click', function() {
		select_pickup();
	});

	function select_pickup() {

		$(".datepick_area_b").addClass('datepicker_sec_b');
		$('.calendar_pickup').addClass('active');
		$('.calendar_pickup').removeClass('inactive');
		$('.calendar_drop').removeClass('active');
		$('.pickup_drop').addClass('active');
		$('.car_book__input_box').addClass('pickup');
		$('.car_book__input_box').removeClass('drop');
		$(".datepicker_tab__inner_b").removeClass("drop_tab");
		$(".datepicker_tab__inner_b").addClass("pickup_tab");
	}

	$('.tab_item_drop').on('click', function(e) {
    e.stopPropagation();
	let pickup_value = $('#pickup_date').val();
	if (pickup_value == '') {
	select_pickup();
      Toastify({
				text: "Please select a pickup date first",
				duration: 1000,
				close: true,
				closeOnClick: true,
				gravity: "bottom",
				position: "right",
			}).showToast();
		} else {
			select_drop();
		}

	});

	function select_drop() {
		$('.calendar_pickup').removeClass('active');
		$('.calendar_pickup').addClass('inactive');
		$('.calendar_drop').addClass('active');
		$('.pickup_drop').addClass('active');
		$('.car_book__input_box').addClass('drop');
		$('.car_book__input_box').removeClass('pickup');
		$(".datepick_area_b").addClass('datepicker_sec_b');
		$(".datepicker_tab__inner_b").removeClass("pickup_tab");
		$(".datepicker_tab__inner_b").addClass("drop_tab");
	}

  $('.drop_save').on('click', function () {
        // removeSelectedClass();
        // $('.booking_new_content').remove();
        var temp_time2 = $('.datepicker_inner_b2').find(".time_hour").val() + ":" +
            $('.datepicker_inner_b2').find(".time_min").val() + " " +
            $('.datepicker_inner_b2').find(".time_am_pm").val();
        $('#datepicker_dropoff_time').val(temp_time2);

        var temp_time1 = $('.datepicker_inner_b').find(".time_hour").val() + ":" +
            $('.datepicker_inner_b').find(".time_min").val() + " " +
            $('.datepicker_inner_b').find(".time_am_pm").val();

         $('#datepicker_pickup_time').val(temp_time1);



        // console.log('temp_time1:',temp_time1,'temp_time2:',temp_time2);
        var date = moment($(".datepicker_input").val(), "DD/MM/YYYY").format();
        var date2 = moment($(".datepicker_input2").val(), "DD/MM/YYYY").format();

        let pickupMomentTime = moment(temp_time1, 'h:mm A');
        let dropoffMomentTime = moment(temp_time2, 'h:mm A');



        if (date == date2) {

            if (dropoffMomentTime.isAfter(pickupMomentTime)) {
                $(".loader").css("display", "inline-flex");
                $(".overlay_sections").css("display", "block");
                afterDropOff();

            } else {

                Toastify({
                    text: "Drop date must be greater than pickup date and time",
                    duration: 3000,
                    close: true,
                    closeOnClick: true,
                    gravity: "bottom",
                    position: "right",
                }).showToast();

                // console.log('pick time is greater');


            }

        }
        else if (date2 > date) {
            $(".loader").css("display", "inline-flex");
            $(".overlay_sections").css("display", "block");
            afterDropOff();
        }

        // var valid_date = addDays(date, day_gap);
        // valid_date = moment(valid_date).format();
        var time_check;
        // if (formatted < time_check && valid_date == date2)
        time_check = moment("09:00 AM", "hh:mm A").format("HH:mm A");
        var formatted = moment(temp_time2, "hh:mm A").format("HH:mm A");
        // console.log('formatted:',formatted,'time_check:',time_check,'valid_date',valid_date,'date2:',date2,'compare:',(formatted < time_check && valid_date == date2));

        // if(date2==date){
        //     console.log('both dates are equal:');
        // }else{

        // }


        // console.log('comaparision of dates:',(date2==date) );


    });

    function removeExtraFromDateObject(date) {
        const slicedDate = date.slice(0, 10);
        return slicedDate;
    }

    function afterDropOff() {
        var date = moment($(".datepicker_input").val(), "DD/MM/YYYY").format();
        var date2 = moment($(".datepicker_input2").val(), "DD/MM/YYYY").format();

        $.fancybox.close();
        let trimmedfirstDate = date.slice(0, 10);
        let car_id = carId;
        // let action_type = $('#fancyBox_action_type').val();

        // let IsDateRangeMade = false;

        var startDate = removeExtraFromDateObject(date);
        var endDate = removeExtraFromDateObject(date2);
        $('#datepicker_first_date').val(startDate);
        $('#datepicker_last_date').val(endDate);

        var action_type= 'normal';

        var details = {
            'carId': carId,
            'bookingId':bookingId,
            'start_date': startDate,
            'end_date': endDate,

        };

        // console.log(details);

        var pickup_time_val = $('#datepicker_pickup_time').val();
        var dropoff_time_val = $('#datepicker_dropoff_time').val();

        // console.log('action_type:', action_type);
        // car_id, startDate,pickTime,  endDate, dropTime, action_type, callback
        checkLockedAndBooked(car_id, startDate, pickup_time_val,  endDate, dropoff_time_val, action_type, function (result,msg) {
          // console.warn('msg:',msg);

            if (result == true) {

                Swal.fire({
                    title:msg,
                    icon: 'warning',
                    showCancelButton: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'popup_updated',
                        title: 'popup_title',
                        actions: 'btn_confirmation',
                    },
                });
                // isBookingCancelBtn = true;
            }
            else {

                // console.log('convert dates:',convertdates(date));
                // console.log('convert date2:',convertdates(date2));
                // console.warn('website:',window.location.href);


                Swal.fire({
                        title: 'Are you sure you want to update the dates',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        showCancelButton: true,
                        confirmButtonText: 'YES, UPDATE IT',
                        cancelButtonText: 'NO, CANCEL IT'
                    }).then((result) => {
                      if (result.isConfirmed) {


                        $.ajax({
                            url: "{{ route('agent.edit.booking.dates') }}",
                            type: 'POST',
                            data: {
                                '_token': '{{ csrf_token() }}',
                                'carDetails': details,
                                'start_time': pickup_time_val,
                                'end_time': dropoff_time_val,
                                'action_type': action_type,
                            },
                            dataType: "json",
                            success: function (data) {
                                if (data.success) {
                                    // console.log('success data:',data);
                                    // console.warn('after success pickupDate: ',data.pickup_date,'dropoffDate:',data.dropoff_date);

                                    $('#pickupDate').val(data.pickup_date);
                                    $('#dropoffDate').val(data.dropoff_date);
                                    // alert('updated bookings');
                                    Swal.fire({
                                        title: data.msg,
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

                                            Swal.fire({
                                                    title: ' Are you sure, you want to change the calulations amount',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonText: 'YES, UPDATE IT',
                                                    cancelButtonText: 'NO, CANCEL IT'

                                            }).then((result)=>{

                                                if (result.isConfirmed) {
                                                    let startDate= $('#datepicker_first_date').val();
                                                    let endDate= $('#datepicker_last_date').val();


                                                    // console.warn('datepicker_first_date:',$('#datepicker_first_date').val(),'datepicker_last_date:',$('#datepicker_last_date').val(),'pickupTime:',pickup_time_val,'dropoff:',dropoff_time_val);
                                                    let numberOfDays= diffrenceInDates(startDate,endDate,pickup_time_val,dropoff_time_val);

                                                    $('#number_of_days_default_value').val(numberOfDays);
                                                    $('#number_of_days').val(numberOfDays);
                                                    $('.suggested_number_of_days').text(numberOfDays);

                                                     $('#fancyBoxPickupText').val(convertdates(startDate)+' | '+pickup_time_val);

                                                    $('#fancyBoxDropoffText').val(convertdates(endDate)+' | '+dropoff_time_val);



                                                    $('#pickupDateTimeSec').text(convertdates(startDate)+", "+pickup_time_val );
                                                    $('#dropoffDateTimeSec').text(convertdates(endDate)+", "+ dropoff_time_val );

                                                    $('.fare_info_input').siblings('.dapicker_val__pickup_text').text( $('#fancyBoxPickupText').val());
                                                     $('.fare_info_input').siblings('.dapicker_val__dropoff_text').text( $('#fancyBoxDropoffText').val());


                                                    calculateAmountTotal();

                                                    // console.log(' inner ajax:',numberOfDays)

                                                }
                                                else{

                                                    window.location.href =$('#viewRoute').val();

                                                }

                                            });

                                      }else{
                                        Swal.fire({
                                                    title: ' Are you sure, you want to change the calulations amount',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonText: 'YES, UPDATE IT',
                                                    cancelButtonText: 'NO, CANCEL IT'

                                            }).then((result)=>{

                                                if (result.isConfirmed) {
                                                    let startDate= $('#datepicker_first_date').val();
                                                    let endDate= $('#datepicker_last_date').val();

                                                    let numberOfDays= diffrenceInDates(startDate,endDate,pickup_time_val,dropoff_time_val);

                                                    $('#number_of_days_default_value').val(numberOfDays)
                                                    $('#number_of_days').val(numberOfDays);
                                                    $('.suggested_number_of_days').text(numberOfDays);


                                                    $('#pickupDateTimeSec').text(convertdates(startDate)+", " +pickup_time_val );
                                                    $('#dropoffDateTimeSec').text(convertdates(endDate)+ ", "+dropoff_time_val);


                                                     $('#fancyBoxPickupText').val(convertdates(startDate)+' | '+pickup_time_val);

                                                    $('#fancyBoxDropoffText').val(convertdates(endDate)+' | '+dropoff_time_val);

                                                    $('.fare_info_input').siblings('.dapicker_val__pickup_text').text( $('#fancyBoxPickupText').val());
                                                     $('.fare_info_input').siblings('.dapicker_val__dropoff_text').text( $('#fancyBoxDropoffText').val());


                                                    calculateAmountTotal();

                                                    // console.log(' inner ajax:',numberOfDays);



                                                }
                                                else{

                                                    window.location.href =$('#viewRoute').val();

                                                }

                                            });
                                      }

                                    });
                                } else {
                                    console.log('data:', data.msg);
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
                                    });
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
                  // }

                // $('.activeDate').each(function (e) {
                //     if ($(this).closest('li').data('date-full_date') === trimmedfirstDate && $(this).closest('li').data('car-id') == car_id) {
                //         let selectedPopup = $(this).closest('.car_status_container').siblings('.car_showcase_cards_sec').find('.selected_dates_popup');
                //         selectedPopup.toggle();
                //         selectedPopup.find('.pickup_date').text(convertdates(date));
                //         selectedPopup.find('.pickup_time').text($('#datepicker_pickup_time').val());
                //         selectedPopup.find('.dropoff_date').text(convertdates(date2));
                //         selectedPopup.find('.dropoff_time').text($('#datepicker_dropoff_time').val());
                //     }
                // });

                $('.fare_info_input').siblings('.dapicker_val_text').find('span').text('dd/mm/yyyy | hh:mm');
            }

        });


        $('.pickup_drop').removeClass('active');
        $('.calendar_drop').addClass('inactive');
        $('.calendar_pickup ').removeClass('inactive');
        $('.car_book__input_box').removeClass('pickup');
        $('.car_book__input_box').removeClass('drop');
        $(".datepick_area_b").removeClass('datepicker_sec_b');

        setTimeout(function () {
            $('.calendar_drop').removeClass('inactive');
            $('.calendar_drop').removeClass('active');
        }, 300);

    }

	// current_time sec

	if (current_min >= 0 && current_min < 30) {

		current_min = 30;

	} else if (current_min >= 30) {

		total_min = total_min + 60;
		current_min = "00";

	}


	total_min = total_min + (hour_gap * 60);
	var time_light = 0;


	current_hour = Math.floor(total_min / 60);
	// current_min = total_min - (current_hour * 60);

	current_hour = moment(current_hour, "hh").format("hh");


	current_hour = "09";
	current_min = "00";
	total_min = parseInt(current_hour) * 60 + parseInt(current_min);
	total_min = total_min + (hour_gap * 60);

	$(".time_hour").val(current_hour);
	$(".time_min").val(current_min);


	if (total_min < 720) {
		$(".time_am_pm").val("AM");
	} else {
		$(".time_am_pm").val("PM");
	}


	////////// time change sec //////////////



	function hour_increse(hour) {
		var temp_h = hour;
		temp_h = (temp_h * 60) + 60;
		if (temp_h > 720) {
			temp_h = temp_h - 720;
		}
		temp_h = (temp_h / 60);
		if (temp_h < 10) {
			temp_h = "0" + temp_h;
		}
		return temp_h;
	}

	function hour_decrese(hour) {
		var temp_h = hour;
		temp_h = (temp_h * 60) - 60;
		if (temp_h < 60) {
			temp_h = temp_h + 720;
		}
		temp_h = (temp_h / 60);
		if (temp_h < 10) {
			temp_h = "0" + temp_h;
		}
		return temp_h;
	}

	$(".hour_up").on("click", function() {
		var hour = $(this).siblings(".time_block").children(".time_hour").val();
		var temp = hour_increse(hour);
		$(this).siblings(".time_block").children(".time_hour").val(temp);
	});
	$(".hour_down").on("click", function() {
		var hour = $(this).siblings(".time_block").children(".time_hour").val();
		var temp = hour_decrese(hour);
		$(this).siblings(".time_block").children(".time_hour").val(temp);
	});

	$(".min_up").on("click", function() {
		var temp_min = $(this).siblings(".time_block").children(".time_min").val();
		temp_min = parseInt(temp_min);


		temp_min = temp_min + min_gap;
		if (temp_min >= 60) {
			temp_min = temp_min - 60;
		}
		if (temp_min.toString().length == 1) temp_min = '0' + temp_min;

		$(this).siblings(".time_block").children(".time_min").val(temp_min)
	});

	$(".min_down").on("click", function() {
		var temp_min = $(this).siblings(".time_block").children(".time_min").val();
		temp_min = parseInt(temp_min);

		temp_min = temp_min - min_gap;
		if (temp_min < 0) {
			temp_min = temp_min + 60;
		}
		if (temp_min.toString().length == 1) temp_min = '0' + temp_min;

		$(this).siblings(".time_block").children(".time_min").val(temp_min)
	});
	$(".am_pm_up").on("click", function() {
		var temp_timeline = $(this).siblings(".time_block").children(".time_am_pm").val();
		if (temp_timeline == "AM") {
			temp_timeline = "PM";
		} else if (temp_timeline == "PM") {
			temp_timeline = "AM";
		}
		$(this).siblings(".time_block").children(".time_am_pm").val(temp_timeline)
	});


	var $sections = $('.car_book_form_b');

	function navigateTo(index) {
		// Mark the current section with the class 'current'
		$sections
			.removeClass('current')
			.eq(index)
			.addClass('current');
	}

	function curIndex() {
		// Return the current index by looking at which section has the class 'current'
		return $sections.index($sections.filter('.current'));
	}


  $('.pickup_save').on('click', function() {

		let pickup_date_val = $('.pickup_date').val();

		if (pickup_date_val != '') {

			$('.tab_item_drop').click();
		}

	});


	$(".datepicker_tab__btn.tab_item_pickup").on("click", function() {
		$(".datepicker_tab__inner_b").removeClass("drop_tab");
		$(".datepicker_tab__inner_b").addClass("pickup_tab");
	});
	$(".datepicker_tab__btn.tab_item_drop").on("click", function() {
		let pickup_value = $('#pickup_date').val();
		if (pickup_value == '') {
			$(".datepicker_tab__inner_b").removeClass("drop_tab");
			$(".datepicker_tab__inner_b").addClass("pickup_tab");
		} else {
			$(".datepicker_tab__inner_b").removeClass("pickup_tab");
			$(".datepicker_tab__inner_b").addClass("drop_tab");
		}
	});



  function initializeDatepickers() {

        if (!allDisableDates) {
            allDisableDates = [];

        }

        $('.datepicker_input').datepicker({
            // minDate: new Date(),
            changeMonth: true,
            changeYear: true,
            yearRange: "c:+3",
            numberOfMonths: 1,
            dateFormat: "dd/mm/yy",
            onSelect: (date, inst) => {
            $('.datepicker_inner_b').click();
            var date2 = moment(date, "DD.MM.YYYY");
            var gap_day = addDays(date2, day_gap);
            $(".datepicker_input2").datepicker('option', 'minDate', gap_day);

            if ($(".datepicker_input").val() < $(".datepicker_input2").val()) {
                $('.datepicker_input2').datepicker('setDate', $('.datepicker_input2').val());
            } else {
                $('.datepicker_input2').datepicker('setDate', gap_day);

            }
            },
            beforeShowDay: function (date) {
            if (allDisableDates ) {

                let disabledDates = allDisableDates.map(dateString => {
                    return $.datepicker.formatDate('dd/mm/yy', new Date(dateString));
                });

                // console.warn('disableDates:',disabledDates);

                var formattedDate = $.datepicker.formatDate('dd/mm/yy', date);
                var isDisabled = (disabledDates.indexOf(formattedDate) !== -1);
                return [!isDisabled];

            }


            },
        });

        var gap_day = addDays(new Date(), day_gap)
        $('.datepicker_input2').datepicker({
            changeMonth: true,
            changeYear: true,
            yearRange: "c:+3",
            // minDate: gap_day,
            numberOfMonths: 1,
            dateFormat: "dd/mm/yy",
            onSelect: () => {
            $('.datepicker_inner_b2').click();
            },
            beforeShowDay: function (date) {


                let disabledDates = allDisableDates.map(dateString => {
                    return $.datepicker.formatDate('dd/mm/yy', new Date(dateString));
                });
                var formattedDate = $.datepicker.formatDate('dd/mm/yy', date);
                var isDisabled = (disabledDates.indexOf(formattedDate) !== -1);
                return [!isDisabled];
            },
        });



        $('.datepicker_input').datepicker('setDate', formattedPickup);
        $('.datepicker_input2').datepicker('setDate', formattedDropoff);

        let pickup_date=$('#pickupDate').val();
        let dropoff_date=$('#dropoffDate').val();

        let pickupTime=moment(pickup_date).format("hh:mm A");

        let dropoffTime=moment(dropoff_date).format("hh:mm A");

        // console.warn('after initialization pickupTime:',pickupTime,'dropoffTime:',dropoffTime);


        let pickupMomentObj = moment(pickupTime, "hh:mm A");
        let dropoffMomentObj = moment(dropoffTime, "hh:mm A");

        // Get the overlap day total minutes
        let pick_totalMinutes = pickupMomentObj.hours() * 60 + pickupMomentObj.minutes();
        let drop_totalMinutes = dropoffMomentObj.hours() * 60 + dropoffMomentObj.minutes();

        let pick_time=pick_totalMinutes;
        let drop_time=drop_totalMinutes;

        // $('#fancyBox_overlappedPickup_time').val()

        // Convert total minutes back to hours and minutes
        let pick_hours = Math.floor(pick_time / 60);
        let pick_minutes = pick_time % 60;
        let drop_hours = Math.floor(drop_time / 60);
        let drop_minutes = drop_time % 60;



        // Determine AM or PM
        let pick_am_pm = pick_hours >= 12 ? 'PM' : 'AM';
        pick_hours = pick_hours % 12 || 12; // Convert to 12-hour format

        let drop_am_pm = drop_hours >= 12 ? 'PM' : 'AM';
        drop_hours = drop_hours % 12 || 12; // Convert to 12-hour format

        // this will add 0 if there is single string(9,7,8)

        pick_hours= (pick_hours.toString().length == 1) ? '0' + pick_hours : pick_hours;
        drop_hours= (drop_hours.toString().length == 1) ? '0' + drop_hours : drop_hours;
        pick_minutes = (pick_minutes.toString().length == 1) ? '0' + pick_minutes : pick_minutes;
        drop_minutes = (drop_minutes.toString().length == 1) ? '0' + drop_minutes : drop_minutes;


        // console.log("pick_hours:", pick_hours, "pick_minutes:", pick_minutes,'pick_am_pm:', pick_am_pm,"drop_hours:", drop_hours, "drop_minutes:", drop_minutes,'drop_am_pm:', drop_am_pm);

        $('.datepicker_inner_b').find(".time_hour").val(pick_hours);
        $('.datepicker_inner_b').find(".time_min").val(pick_minutes) ;
        $('.datepicker_inner_b').find(".time_am_pm").val(pick_am_pm);

        $('.datepicker_inner_b2').find(".time_hour").val(drop_hours);
        $('.datepicker_inner_b2').find(".time_min").val(drop_minutes) ;
        $('.datepicker_inner_b2').find(".time_am_pm").val(drop_am_pm);
   }


	$('.form_sec_1_next').on('click', function() {

		var pickup_date = $('#pickup_date').val();
		var dropoff_date = $('#dropoff_date').val();


		$('.fare_pickup_text').html(pickup_location);
		$('.fare_pickdate_text').html(pickup_date);
		$('.fare_dropdate_text').html(dropoff_date);

		// console.log($(".datepicker_input").val());
		// console.log($(".datepicker_input2").val());
		if ($('#pickup_date').val() != '' && $('#dropoff_date').val() != '') {
			if ($(".datepicker_input").val() < $(".datepicker_input2").val()) {
				var temp_time = $('.datepicker_inner_b2').find(".time_hour").val() + ":" +
					$('.datepicker_inner_b2').find(".time_min").val() + " " +
					$('.datepicker_inner_b2').find(".time_am_pm").val();
				selected_drop_time = temp_time;
				var date2 = moment($(".datepicker_input2").val(), "DD/MM/YYYY").format("DD MMMM YYYY");
				var tab_date2 = moment($(".datepicker_input2").val(), "DD/MM/YYYY").format("DD MMM YYYY");
				$("#dropoff_date").val(date2 + " | " + temp_time);
				$(".dapicker_val__dropoff_text").html(date2 + " | " + temp_time);
				$(".dapicker_val__tab_dropoff_text").html(tab_date2 + " | " + temp_time);
				$(".fare_dropdate_text").html(tab_date2 + " | " + temp_time);
			}
		}
	});

    function getAllBookedAndLockedDates(carId) {
        let car_id = carId;
        // let allDisableDates = [];
        $.ajax({
            url: "{{ route('agent.getAllBookedAndLockedWithTime') }}",
            method: "post",
            data: {
                'carId': car_id,
                'bookingId':bookingId,
                _token: '{{ csrf_token() }}',
            },
            dataType: "json",
            success: function (data) {
                if (data.success) {
                    if (data.disableDates.length > 0) {
                        // console.log('disable dates from response :', data.disableDates);

                        // for (let i = 0; i < data.disableDates.length; i++) {
                        //     // allDisableDates[i]=data.disableDates[i];

                        //     allDisableDates.push(data.disableDates[i]);

                        // }
                        allDisableDates = data.disableDates;


                        //  console.log('allDisableDates:',allDisableDates);
                        // data.disableDates.forEach(element => {
                        //     allDisableDates.push(element);
                        // });
                        //  allDisableDates.push(data.disableDates);

                        initializeDatepickers();
                        $(".datepicker_input").datepicker("refresh");
                        $(".datepicker_input2").datepicker("refresh");
                    }
                } else {
                //   console.log('from fails');
                        initializeDatepickers();

                    // console.log('disable dates is empty :',data.disableDates);
                }
            },
            complete: function (data) {
                $(".loader").css("display", "none");
                $(".overlay_sections").css("display", "none");
            }
        });

    }

    function checkLockedAndBooked(car_id, startDate,pickTime,  endDate, dropTime, action_type, callback) {
        let flagBase;
        let msg;
        let bookingId= $('#id').val();
        // console.warn('bookingId',bookingId);
        $.ajax({
            url: "{{ route('agent.checkLockedAndBookedDates') }}",
            method: "post",
            data: {
                'startDate': startDate,
                'endDate': endDate,
                'pickup': endDate,
                'carId': car_id,
                'pickupTime': pickTime,
                'dropoffTime': dropTime,
                'actionType': action_type,
                'bookingId':bookingId,
                _token: '{{ csrf_token() }}',
            },
            dataType: "json",
            success: function (data) {
                if (data.success) {
                    flagBase = true;
                    msg=data.msg;

                } else {
                   flagBase = false;

                }
                callback(flagBase,msg);
            },

            complete: function (data) {
                $(".loader").css("display", "none");
                $(".overlay_sections").css("display", "none");
            }
        });
    }

    function convertdates(date) {
        let originalDate = new Date(date);
        const formattedDate = originalDate.toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' });
        return formattedDate;
    }





///////////////////////////////////////////////////////////////////////////


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

    if ($.isNumeric(per_day_rental_charges) && $.isNumeric(number_of_days)) {
        var amountTotal = per_day_rental_charges * number_of_days + pickup_charges + dropoff_charges;

    }
    var discount = parseFloat($('#discount').val().replace(/,/g, '')) || 0;
    amountTotal -= discount;

    amountTotal = Math.max(0, amountTotal);

    if (amountTotal <= 0) {
        $('#total_booking_amount').css('color', 'red');

    } else {
        $('#total_booking_amount').css('color', '');

    }

    $('#total_booking_amount').val(parseInt(amountTotal).toLocaleString());
    updateTotal();
}

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

    calculateTotal = Math.max(0, calculateTotal);

    if (calculateTotal <= 0) {
        $('#due_at_delivery').css('color', 'red');
    } else {
        $('#due_at_delivery').css('color', '');
    }

    $('#due_at_delivery').val(parseInt(calculateTotal).toLocaleString());
}

$(document).ready(function () {
   $('#number_of_days,#per_day_rental_charges').on('input change', calculateAmountTotal);


$('.amount_total').on('keyup', function () {
    calculateAmountTotal();
});


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
    if (parseFloat($(this).val().replace(/,/g, '')) > parseFloat($('#total_booking_amount').val().replace(/,/g, ''))) {

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
     function hideLoader()
    {
        $('.loader').css("display", "none");
        $('.overlay_sections').css("display", "none");
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

         return true;
        }
     }


     $('.required_field').on('keyup', function () {
        var hasErrors = false;
        if (!$(this).val().trim()) {
            hasErrors = true;
            // console.log('required_field:',$(this).data("error-msg"),'element:', $(this).closest('.inp_container').find('.required'));
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

var inputIds = ['pickup_charges', 'dropoff_charges', 'per_day_rental_charges', 'discount', 'advance_booking_amount', 'refundable_security_deposit','agent_commission','agent_commission_received'
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
    var input1 = document.querySelector("#customer_mobile");
    window.intlTelInput(input1, {
    initialCountry: "IN",
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

    input1.addEventListener("countrychange", function() {
        var intlNumber1 = $('.iti__selected-dial-code');
        var getIntlNumber1 = intlNumber1.closest('.customer_mobile').find('.iti__selected-dial-code').html();
        $('.customer_mobile_country_code').val(getIntlNumber1);
    });

    var customer_mobile_country_code = $('.customer_mobile_country_code').val();
    if (customer_mobile_country_code === '') {
        $('.customer_mobile_country_code').val('+91');
    }

    var input2 = document.querySelector("#alt_customer_mobile");
    window.intlTelInput(input2, {
    initialCountry: "IN",
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

    input2.addEventListener("countrychange", function() {
        var intlNumber2 = $('.iti__selected-dial-code');
        var getIntlNumber2 = intlNumber2.closest('.alt_customer_mobile').find('.iti__selected-dial-code').html();
        $('.alt_customer_mobile_country_code').val(getIntlNumber2);
    });

    var alt_customer_mobile_country_code = $('.alt_customer_mobile_country_code').val();
    if (alt_customer_mobile_country_code === '') {
        $('.alt_customer_mobile_country_code').val('+91');
    }


    $(document).ready(function () {
    $('#dropoff_location').on('change', function () {

        if ($(this).val() == 'Other') {
        $('#other_dropoff_location').val('');

        $('.other_dropoff_location_container').show();

        console.log('under:',$('.other_dropoff_location_container'));
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


        var selectElement = $('#pickup_location');
        $("#pickup_location, #dropoff_location").select2({
            ajax: {
                delay: 250,
                url: "{{ route('agent.location.json') }}",
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


  });
</script>
@endsection
