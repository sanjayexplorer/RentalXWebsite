@extends('layouts.partner')
@section('title', 'External Booking')
@section('content')
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
span.rupee_icon{ left:22px; }
.iti--allow-dropdown input { padding-right: 20px !important; }
@media screen and (max-width: 992px){
.header_section_container {display:none;}
}
@media screen and (max-width: 992px){
.main_header_container{display:none;}
}
</style>
<div class="dashboard_ct_section">
    <div class="lg:flex hidden lg:bg-white lg:mb-[0px] lg:px-[17px] lg:py-[15px] mb-[20px] justify-start items-center">

        <div class="flex items-center justify-start w-full">
            <a href="{{route('partner.booking.calendar')}}" class="links_item_cta inline-block p-2 mr-2 border border-[#C8C0C0] rounded-md
            hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E] GobackDelete">
                <img class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
            </a>
            <span class="inline-block sm:text-base text-xl font-normal leading-normal align-middle text-black800">External Booking</span>
        </div>
    </div>

    <div class="pt-[42px] px-[36px] lg:px-[17px] lg:py-[20px] xl:py-10 bg-textGray400 pb-[100px]  min-h-screen lg:flex lg:justify-center">

        <div class="lg:hidden flex items-center mb-[36px] lg:mb-[20px] justify-between">
            <div class="lg:hidden flex flex-col">
                <div class="back-button">
                    <a href="{{route('partner.booking.calendar')}}" class="links_item_cta inline-flex py-1 px-2 border border-[#C8C0C0] rounded-md bg-[#fff] hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E] GobackDelete">
                        <img  class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
                        <span class="inline-block text-base leading-normal align-middle text-black800 ml-[10px] font-medium">
                            Calendar
                        </span>
                    </a>
                </div>
                <span class="inline-block text-[26px] font-normal leading-normal align-middle text-black800">
                    New Booking
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
                <!--  -->
                    <div class="mt-[20px] md:mt-[5px] overflow-hidden form_outer select_date_time hidden">
                        <div class="relative flex border border-[#898376] rounded-[10px] form_inner sm:bg-[#F6F6F6]">
                            <div
                                class="relative w-1/2 py-[16px] pr-2 text-center form_inner_left sm:py-[10px] left_part">
                                <h4 class="sm:text-[13px] text-[14px] font-medium text-black500 mb-[5px] capitalize ">FROM</h4>
                            <div class="block">
                                <h4 class="text-black font-medium leading-4 text-[14px] sm:text-[13px] mb-[3px]"><span id="pickupDateTimeSec"></span></h4>
                            </div>
                            <div class="DisplayPickupLocation hidden">
                                    <p class="text-[#898376] font-normal leading-4 text-[14px] sm:text-[13px]">Pickup:</p>
                            </div>
                        </div>
                    <div class="w-1/2 py-[16px] pl-2 text-center right_part py-[10px] form_inner_right sm:py-[10px]">
                    <h4 class="sm:text-[13px] text-[14px] font-medium text-black500 mb-[5px] capitalize">TO</h4>
                    <div class="block">
                        <h4 class="text-black font-medium leading-4 text-[14px] sm:text-[13px] mb-[3px]"><span id="dropoffDateTimeSec"></span></h4>
                    </div>
                    <div class="DisplayDropoffLocation hidden">
                        <p class="text-[#898376] font-normal leading-4 text-[14px] sm:text-[13px]">dropoff:</p>
                    </div>
                    </div>
                </div>
                </div>
                <!--  -->

                <form action="{{route('partner.booking.add.post','0')}}" class="mt-5" id="formId" method="POST" onsubmit="return checkForm()">
                    @csrf
                    <input type="hidden" name="userId" value="{{Auth::user()->id}}" class="userId">
                    <input type="hidden" name="start_date"  id="start_date">
                    <input type="hidden" name="end_date"  id="end_date">
                    <input type="hidden" name="start_time"  id="start_time">
                    <input type="hidden" name="end_time"  id="end_time">
                    <input type="hidden" name="booking_type" id="booking_type"  value="external">
                    <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full px-3 sm:w-full inp_container">
                                <label for="car_name" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Car name<span class="text-[#ff0000]">*</span></label>
                                    <input id="car_name" tabindex="-1"
                                        class="w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border
                                        rounded-md required_field border-black500 focus:outline-none focus:border-siteYellow"
                                        type="text" name="car_name"
                                        placeholder="Enter car name"
                                        data-error-msg="Car name is required"
                                        value="{{old('car_name')}}"
                                        onkeyup="checkBlankField(this.id,'Car name is required','errorCarName')">
                                        <span class="hidden required text-sm"></span>
                                        @if ($errors->has('car_name'))
                                        <div class="error text-sm">
                                            <ul>
                                                <li>{{ $errors->first('car_name') }}</li>
                                            </ul>
                                        </div>
                                    @endif
                            </div>
                        </div>
                    </div>
                   <!-- Registration number -->
                   <div class="pb-5">
                    <div class="flex flex-wrap -mx-3">
                        <div class="registration_number_container w-full px-3 sm:w-full inp_container">
                                <label class="block pb-2.5 font-normal leading-4 text-left text-black text-sm"
                                    for="registration_number">Registration Number<span class="text-[#ff0000]">*</span>
                                </label>
                                <div class="flex relative ">
                                    <!-- Format selection dropdown -->
                                    <div class="absolute inline-flex h-[100%] w-[135px] rounded-l-md bg-[rgba(0, 0, 0, 0.05)] top-0 bottom-0 p-[1px]">
                                        <select class="w-full rounded-l-md focus:outline-none pl-[16px] appearance-none reg_arrow text-base" id="formatSelect" onchange="updateCleaveFormat();" style="background-color: rgba(0, 0, 0, 0.05);">
                                            <option value="general">General</option>
                                            <option value="bh">Bharat (BH)</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>

                                    <input type="text" tabindex="-1" name="registration_number" value="{{ old('registration_number') }}"
                                         data-error-msg="Registration number is required"
                                        onkeyup="checkBlankField(this.id,'Registration number is required','errorRegistrationNumber')"
                                        onchange="checkBlankField(this.id,'Registration number is required','errorRegistrationNumber')"
                                        class="required_field w-full px-5 py-3 font-normal leading-normal text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow pl-[150px] text-base " placeholder="GA-01-CP-1234" id="car_number" data-error-msg="Registration number is required" >
                                </div>

                            <span class="scrollForReq hidden required errorRegistrationNumber text-sm"></span>
                            @if ($errors->has('registration_number'))
                            <div class="error text-sm">
                                <ul>
                                    <li>{{ $errors->first('registration_number') }}</li>
                                </ul>
                            </div>
                        @endif
                        </div>
                    </div>
                </div>

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
                                        <span class="hidden required text-sm"></span>
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
                    <div class="mt-[23px] mb-[30px] form_btn_sec afclr">
                        <a href="javascript:void(0);" data-src="#open_popup"
                            class="inline-block selectDate text-center w-full px-5 py-3 text-opacity-40 text-base font-medium
                            leading-tight transition-all duration-300 border border-siteYellow rounded-[4px] cursor-pointer bg-siteYellow
                            md:px-[20px] md:py-[14px] sm:text-sm sm:py-[12px] transition-all duration-500 ease-0 hover:bg-[#d7b50a]
                            disabled:opacity-40 disabled:cursor-not-allowed uppercase">
                            Select Date Time
                        </a>
                    </div>
                    <div class="flex flex-wrap -mx-3 sm:-mx-[0px] pb-5">
                        <div class="w-1/2 px-3 sm:px-[0px] sm:w-full inp_container customer_mobile">
                            <label for="customer_mobile"
                                class="inline-block pb-2.5 text-sm font-normal leading-4 text-left text-black">Customer
                                mobile number<span class="text-[#ff0000]">*</span></label>
                             <input type="text" tabindex="-1" name="customer_mobile" id="customer_mobile"
                                class="required_field phone_validation mobile_num_validate w-full px-5 py-3 text-base font-normal
                                leading-normal text-black bg-white border rounded-md
                                border-black500 focus:outline-none focus:border-siteYellow"  value="{{old('customer_mobile')}}"
                                placeholder="Enter customer mobile number" data-error-msg="Customer mobile number is required"
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

                        <div class="w-1/2 px-3 sm:px-[0px] sm:w-full alt_customer_mobile">
                            <label for="alt_customer_mobile"
                                class="inline-block pb-2.5 text-sm font-normal leading-4 text-left text-black">Customer
                                Alternate Mobile number</label>
                            <input type="text" tabindex="-1" name="alt_customer_mobile" id="alt_customer_mobile"
                                class="mobile_num_validate w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border rounded-md
                                border-black500 focus:outline-none focus:border-siteYellow" value="{{old('alt_customer_mobile')}}"
                                placeholder="Enter alternate mobile number"
                               >
                               <input type="hidden" name="alt_customer_mobile_country_code" class="alt_customer_mobile_country_code">

                        </div>

                        {{-- <div class="w-1/2 sm:mt-5 px-3 sm:px-[0px] sm:w-full">
                            <label for="customer_email"
                                class="inline-block pb-2.5 text-sm font-normal leading-4 text-left text-black">Customer
                                email (optional)</label>
                            <input type="email" tabindex="-1" id="customer_email" name="customer_email"
                                class="w-full px-5 py-3 text-base font-normal leading-normal text-black
                                    bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow"
                                placeholder="Enter email" data-error-msg="">
                            <span class=" hidden required text-sm"></span>
                        </div> --}}
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

                        <div class="pt-5 lg:pt-5 sm:pt-0 sm:pb-5  other_pickup_location_container" style="display:none">
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
                               <select name="dropoff_location" tabindex="-1" id="dropoff_location"
                                        onchange="checkBlankField(this.id,'Dropoff location is required','errorDropoff')"
                                        class="required_field w-full py-3 pl-5 pr-10 text-base font-normal leading-normal text-black bg-white border rounded-md appearance-none s_tag_haf border-black500 focus:outline-none focus:border-siteYellow drop_location_select"
                                        data-error-msg="Dropoff location is required">
                                          <option value="">Select dropoff location</option>

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
                                <label for="per_day_rental_charges"
                                    class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Per day rental charges<span
                                        class="text-[#ff0000]">*</span></label>
                                <div class="rupee_price_field">
                                    <input type="text" tabindex="-1" name="per_day_rental_charges" id="per_day_rental_charges"
                                        class="amount_total required_field w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow pl-[33px]"
                                        placeholder="Enter Per day rental charges" data-error-msg="Per day rental charges is required"
                                        value="">
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
                            <input type="hidden"  id="number_of_days_default_value" value="">
                            <div class="w-full px-3 sm:w-full inp_container">
                            <label for="number_of_days" class="flex justify-between block pb-2.5 text-sm font-normal leading-4 text-left text-black">
                                <p>Number of days<span class="text-[#ff0000]">*</span></p>
                                <p class="text-[#777777] hidden">Suggested number of days: <span class="suggested_number_of_days"></span> days</p>
                            </label>
                            <input type="text" tabindex="-1" name="number_of_days" id="number_of_days"
                            class="required_field w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white
                            border rounded-md border-black500 focus:outline-none focus:border-siteYellow"
                            placeholder="Enter number of days"
                            data-error-msg="Number of days is required" value="">
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
                        <label for="pickup_charges" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black ">Pickup charges<span class="text-[#ff0000]">*</span></label>
                        <div class="rupee_price_field">
                        <input type="text" tabindex="-1" name="pickup_charges" id="pickup_charges"
                                class=" amount_total w-full px-5 py-3 required_field text-base font-normal leading-normal text-black bg-white border rounded-md  border-black500 focus:outline-none focus:border-siteYellow pl-[33px]"
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
                            <label for="dropoff_charges"class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Dropoff charges<span class="text-[#ff0000]">*</span>
                            </label>
                            <div class="rupee_price_field">
                            <input type="text" tabindex="-1" name="dropoff_charges" id="dropoff_charges"
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
                                    <input type="text" tabindex="-1" name="discount" id="discount"
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
                                <input type="text" tabindex="-1" name="total_booking_amount" id="total_booking_amount"
                                class="calculate_total w-full px-5 py-[3px] text-base font-medium leading-normal text-black bg-white rounded-md outline-none lg:bg-[#F6F6F6] pl-[18px]"
                                placeholder="" readonly>
                            <span class="rupee_icon " style="left:5px;">₹</span>
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
                                    class="required_field w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white
                                    border rounded-md border-black500 focus:outline-none focus:border-siteYellow pl-[33px]"
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
                                <label for="due_at_delivery" class="block pb-2 text-sm font-medium leading-4 text-left text-black">Due at delivery</label>
                                <div class="rupee_price_field">
                                        <input type="text" tabindex="-1" name="due_at_delivery" id="due_at_delivery"
                                        class="w-full px-5 py-[3px] text-base font-medium leading-normal text-black bg-white rounded-md outline-none lg:bg-[#F6F6F6] pl-[18px]"
                                        placeholder="" readonly>
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
                                    <label for="select_driver"
                                        class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Select driver
                                        (optional)</label>
                                    <select name="select_driver" id="select_driver"
                                        class="w-full py-3 pl-5 pr-10 text-base font-normal leading-normal text-black bg-white border rounded-md
                                                               appearance-none s_tag_haf border-black500 focus:outline-none focus:border-siteYellow drop_location_select">
                                        <option value="" selected disabled>Select driver</option>
                                        @foreach($drivers as $item)
                                        <option value="{{$item->id}}">
                                            {{$item->driver_name}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <!----  ---->
                    <!--booking remarks -->
                    <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full px-3 sm:w-full">
                                <label for="booking_remarks" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Internal notes (optional)</label>
                                <textarea type="text" name="booking_remarks" id="booking_remarks" rows="4" class="w-full px-5 py-3 text-base
                                    font-normal leading-normal text-black bg-white
                                    border rounded-md border-black500 focus:outline-none focus:border-siteYellow">
                                </textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-[20px] mb-[14px] form_btn_sec afclr">
                        <input type="submit" value="CREATE BOOKING" id="submitCtaBtn" class="inline-block w-full px-5 py-3 text-opacity-40 text-base font-medium leading-tight transition-all duration-300 border border-siteYellow rounded-[4px] cursor-pointer bg-siteYellow md:px-[20px] md:py-[14px] sm:text-sm sm:py-[12px]  ease-0 hover:bg-[#d7b50a]  disabled:opacity-40 disabled:cursor-not-allowed">
                    </div>

                </form>
              </div>
            </div>
        </div>
    </div>
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
                            <a href="javascript:;" class="close_popup">Close</a>
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
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
<script>
      var cleave;
    function updateCleaveFormat() {
        var formatSelect = document.getElementById('formatSelect');
        var selectedFormat = formatSelect.value;
        var blocks;
        var placeholder;
        switch (selectedFormat) {
            case 'general':
                blocks = [2, 2, 2, 4];
                placeholder = 'GA-01-CP-1234';
                break;
            case 'bh':
                blocks = [2, 2, 4, 2];
                placeholder = '12-BH-3456-AA';
                break;
            case 'other':
                blocks = [4, 4, 4];
                placeholder = '1234-1234-1234';
                break;
            default:
                blocks = [3, 2, 4];
                placeholder = '123-12-1234';
                break;
        }

        document.getElementById('car_number').value = '';
        document.getElementById('car_number').placeholder = placeholder;

        cleave.destroy();
        cleave = new Cleave('#car_number', {
            delimiter: '-',
            blocks: blocks,
            uppercase: true,
        });
    }
    document.addEventListener('DOMContentLoaded', function () {
        cleave = new Cleave('#car_number', {
            delimiter: '-',
            blocks: [2, 2, 2, 4],
            uppercase: true,
        });
    });




var interval;
var seconds = 0;
const carId =  $('#carId').val();
const currentDate = new Date().toISOString().split('T')[0];

$(document).ready(function () {
    $('textarea').each(function(){
            $(this).val($(this).val().trim());
        }
    );
});


$(document).ready(function () {
$('#number_of_days,#per_day_rental_charges').on('input change', calculateAmountTotal);
var total_booking_amount;
function calculateAmountTotal() {
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
         amountTotal = per_day_rental_charges * number_of_days + pickup_charges + dropoff_charges;
         total_booking_amount = amountTotal;
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

$('.amount_total').on('keyup', function () {
    calculateAmountTotal();
});

var due_at_delivery;
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
    due_at_delivery = calculateTotal;
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

$('.calculate_total').on('keyup', function () {
    updateTotal();
});


$('#discount').on('keyup input', function () {
    if (parseFloat($(this).val().replace(/,/g, '')) > total_booking_amount) {
        $(this).closest('.inp_container').find('.required').show().html('You cannot fill a value greater than the total booking amount');
    } else {
        $(this).closest('.inp_container').find('.required').hide().html('');
    }
    calculateAmountTotal();
});


$('#advance_booking_amount').on('keyup input', function () {
    if (parseFloat($(this).val().replace(/,/g, '')) > due_at_delivery) {
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

    // calculateAmountTotal();
    // updateTotal();
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
        $('.DisplayPickupLocation').removeClass('hidden').html('<span class="text-black500 capitalize">pickup: </span>'+ $(this).val());

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
, 'agent_commission', 'agent_commission_received'
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


$('body').on('click', '.selectDate', function (e) {
    let formattedDate = $.datepicker.formatDate('dd/mm/yy', new Date());

    $.fancybox.open({

    src: $(this).data('src'),

      beforeShow: function (instance, current) {

        const fancybox = this;

        $('.fare_info_input').siblings('.dapicker_val_text').text('dd/mm/yyyy | hh:mm');

        $('.datepicker_input', current.$content).datepicker('setDate', formattedDate);

        var date2 = moment(formattedDate, "DD.MM.YYYY");

        var gap_day = addDays(date2, day_gap);

        let convertedGapDates = moment(gap_day).format("YYYY-MM-DD HH:mm:ss");

        let clickedDate = moment(formattedDate, "DD/MM/YYYY").format("DD MMMM YYYY");

        $(".datepicker_input2", current.$content).datepicker('option', 'minDate', gap_day);

        if ($(".datepicker_input", current.$content).val() < $(".datepicker_input2", current.$content).val()) {

            $('.datepicker_input2', current.$content).datepicker('setDate', $('.datepicker_input2', current.$content).val());

        } else {

            $('.datepicker_input2', current.$content).datepicker('setDate', gap_day);

        }
    }
    });

    select_pickup();
});



function removeExtraFromDateObject(date) {
    const slicedDate = date.slice(0, 10);
    return slicedDate;
}
   var day_gap = 0;
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

  $(".datepicker_inner_b").click(function () {

    var temp_time = $(this).find(".time_hour").val() + ":" +

    $(this).find(".time_min").val() + " " +

    $(this).find(".time_am_pm").val();

    selected_pick_time = temp_time;

    var date = moment($(".datepicker_input").val(), "DD/MM/YYYY").format("DD MMMM YYYY");

    var tab_date1 = moment($(".datepicker_input").val(), "DD/MM/YYYY").format("DD MMM YYYY");

    $("#pickup_date").val(date + " | " + temp_time);

    $(".dapicker_val__pickup_text").html(date + " | " + temp_time);

    $('#start_time').val(temp_time);

    $(".dapicker_val__tab_pickup_text").html(tab_date1 + " | " + temp_time);

    var temp_date = $(".datepicker_input").val();

    temp_date = moment(temp_date, "DD/MM/YYYY").format();

    temp_date = new Date(temp_date);

    });
    $(".datepicker_inner_b2").click(function () {

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
    $('.tab_item_pickup').on('click touchstart', function () {
        select_pickup();
    });

    $('.tab_item_drop').on('click touchstart', function (e) {
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
        }
        else {
            select_drop();
        }
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

  function diffrenceInDates(date1, date2, pickupTime, dropoffTime) {
        const firstDate = moment(date1);
        const secondDate = moment(date2);
        let difference = secondDate.diff(firstDate, 'days');

        const pickupMoment = moment(pickupTime, 'h:mm A');
        const dropoffMoment = moment(dropoffTime, 'h:mm A');

        if (pickupMoment.isBefore(moment('9:00 AM', 'h:mm A'))) {
            difference++;
        }
        if (dropoffMoment.isAfter(moment('9:00 AM', 'h:mm A'))) {
            difference++;
        }
        return difference;
    }

    function convertdates(date) {
        let originalDate = new Date(date);
        const formattedDate = originalDate.toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' });
        return formattedDate;
   }

   $('.drop_save').on('click', function () {
        var temp_time2 = $('.datepicker_inner_b2').find(".time_hour").val() + ":" +
        $('.datepicker_inner_b2').find(".time_min").val() + " " +
        $('.datepicker_inner_b2').find(".time_am_pm").val();
        $('#end_time').val(temp_time2);
        var temp_time1 = $('.datepicker_inner_b').find(".time_hour").val() + ":" +
        $('.datepicker_inner_b').find(".time_min").val() + " " +
        $('.datepicker_inner_b').find(".time_am_pm").val();
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
            }

        }
        else if (date2 > date) {
            $(".loader").css("display", "inline-flex");
            $(".overlay_sections").css("display", "block");
            afterDropOff();
        }
        var time_check;
        time_check = moment("09:00 AM", "hh:mm A").format("HH:mm A");
        var formatted = moment(temp_time2, "hh:mm A").format("HH:mm A");
        $(".loader").css("display", "none");
        $(".overlay_sections").css("display", "none");
    });

    function afterDropOff() {
        var date = moment($(".datepicker_input").val(), "DD/MM/YYYY").format();
        var date2 = moment($(".datepicker_input2").val(), "DD/MM/YYYY").format();

        $.fancybox.close();

        let trimmedfirstDate = date.slice(0, 10);

        let IsDateRangeMade = false;

        const currentUl = $('.list').filter(function () {
            return $(this).data('car-id') == car_id;
        });

        var startDate = removeExtraFromDateObject(date);

        var endDate = removeExtraFromDateObject(date2);

        $('#start_date').val(startDate);

        $('#end_date').val(endDate);

        let pickTime= $('#start_time').val();

        let dropTime= $('#end_time').val();

        //
        var pickup_time_val = $('#start_time').val();
        var dropoff_time_val = $('#end_time').val();

        $('.select_date_time').show();
        let numberOfDays= diffrenceInDates(startDate,endDate,pickup_time_val,dropoff_time_val);

        $('#number_of_days_default_value').val(numberOfDays);
        $('#number_of_days').val(numberOfDays);

        $('.suggested_number_of_days').closest('p').show();

        $('.suggested_number_of_days').text(numberOfDays);

        $('#fancyBoxPickupText').val(convertdates(startDate)+' | '+pickup_time_val);

        $('#fancyBoxDropoffText').val(convertdates(endDate)+' | '+dropoff_time_val);

        $('#pickupDateTimeSec').text(convertdates(startDate)+", "+pickup_time_val );
        $('#dropoffDateTimeSec').text(convertdates(endDate)+", "+ dropoff_time_val );

        //

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

    if (current_min >= 0 && current_min < 30) {
        current_min = 30;
    } else if (current_min >= 30) {
        total_min = total_min + 60;
        current_min = "00";
    }
    total_min = total_min + (hour_gap * 60);
    var time_light = 0;
    current_hour = Math.floor(total_min / 60);
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
    $(".hour_up").on("click", function () {
        var hour = $(this).siblings(".time_block").children(".time_hour").val();
        var temp = hour_increse(hour);
        $(this).siblings(".time_block").children(".time_hour").val(temp);
    });

    $(".hour_down").on("click", function () {
        var hour = $(this).siblings(".time_block").children(".time_hour").val();
        var temp = hour_decrese(hour);
        $(this).siblings(".time_block").children(".time_hour").val(temp);
    });

    $(".min_up").on("click", function () {
        var temp_min = $(this).siblings(".time_block").children(".time_min").val();
        temp_min = parseInt(temp_min);
        temp_min = temp_min + min_gap;
        if (temp_min >= 60) {
            temp_min = temp_min - 60;
        }
        if (temp_min.toString().length == 1) temp_min = '0' + temp_min;
        $(this).siblings(".time_block").children(".time_min").val(temp_min)
    });

    $(".min_down").on("click", function () {
        var temp_min = $(this).siblings(".time_block").children(".time_min").val();
        temp_min = parseInt(temp_min);
        temp_min = temp_min - min_gap;
        if (temp_min < 0) {
            temp_min = temp_min + 60;
        }
        if (temp_min.toString().length == 1) temp_min = '0' + temp_min;
        $(this).siblings(".time_block").children(".time_min").val(temp_min)
    });

    $(".am_pm_up").on("click", function () {
        var temp_timeline = $(this).siblings(".time_block").children(".time_am_pm").val();
        if (temp_timeline == "AM") {
            temp_timeline = "PM";
        } else if (temp_timeline == "PM") {
            temp_timeline = "AM";
        }
        $(this).siblings(".time_block").children(".time_am_pm").val(temp_timeline)
    });

    $('.pickup_save').on('click', function () {
        let pickup_date_val = $('#pickup_date').val();
        if (pickup_date_val != '') {
            $('.tab_item_drop').click();
        }
    });

    var interval;
    var is_verified = false;
    function countdown() {
        clearInterval(interval);
        interval = setInterval(function () {
            var timer = $('.counter_time').html();
            timer = timer.split(':');
            var minutes = timer[0];
            var seconds = timer[1];
            seconds -= 1;
            if (minutes < 0) return;
            else if (seconds < 0 && minutes != 0) {
                minutes -= 1;
                seconds = 59;
            } else if (seconds < 10 && length.seconds != 2) seconds = '0' + seconds;
            $('.counter_time').html(minutes + ':' + seconds);
            if (minutes == 0 && seconds == 0) {
                clearInterval(interval);
                $('.resend_code__sec').hide();
                if (is_verified == false) {
                    $('#verifiBtnSec').show();
                }
            }
        }, 1000);
    }

    var $sections = $('.car_book_form_b');
    function navigateTo(index) {
        $sections
            .removeClass('current')
            .eq(index)
            .addClass('current');
        $('.form-navigation .previous').toggle(index > 0);
        var atTheEnd = index >= $sections.length - 1;
        $('.form-navigation .next').toggle(!atTheEnd);
        $('.form-navigation [type=submit]').toggle(atTheEnd);
    }

    function curIndex() {
        return $sections.index($sections.filter('.current'));
    }

    $('.form-navigation .previous').click(function () {
        navigateTo(curIndex() - 1);
    });

    var $sections = $('.car_book_form_b');

    $(document).on('click touchstart', '.datepicker_tab__btn.tab_item_pickup', function () {
        $(".datepicker_tab__inner_b").removeClass("drop_tab");
        $(".datepicker_tab__inner_b").addClass("pickup_tab");
    });


    $(document).on('click touchstart', '.datepicker_tab__btn.tab_item_drop', function () {
        let pickup_value = $('#pickup_date').val();
        if (pickup_value == '') {
            $(".datepicker_tab__inner_b").removeClass("drop_tab");
            $(".datepicker_tab__inner_b").addClass("pickup_tab");
        } else {
            $(".datepicker_tab__inner_b").removeClass("pickup_tab");
            $(".datepicker_tab__inner_b").addClass("drop_tab");
        }
    });

    $('.datepicker_input').datepicker({
        // minDate: new Date(),
        changeMonth: true,
        changeYear: true,
        yearRange: "c:+3",
        numberOfMonths: 1,
        dateFormat: "dd/mm/yy",
        // defaultDate:,
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
            var formattedDate = $.datepicker.formatDate('dd/mm/yy', date);
            return formattedDate;
        },
    });

    var gap_day = addDays(new Date(), day_gap)
    $('.datepicker_input2').datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: "c:+3",
        numberOfMonths: 1,
        dateFormat: "dd/mm/yy",
        onSelect: () => {
            $('.datepicker_inner_b2').click();
        },
        beforeShowDay: function (date) {
            var formattedDate = $.datepicker.formatDate('dd/mm/yy', date);
            return formattedDate;
        },
    });

    $('.form_sec_1_next').on('click', function () {
        var pickup_date = $('#pickup_date').val();
        var dropoff_date = $('#dropoff_date').val();
        $('.fare_pickdate_text').html(pickup_date);
        $('.fare_dropdate_text').html(dropoff_date);
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

    $('body').on('click', '.close_popup', function (e) {
        e.preventDefault();
        $.fancybox.close();
        change_number = true;
        $('.data_list_here').val('');
        $('.car_book__input_box').removeClass('pickup,drop');
        $('.datepicker_sec').removeClass('active');
        navigateTo(0);
    });
    $(document).ready(function () {
    $('#dropoff_location').on('change', function () {
        $('.suggested_number_of_days').closest('p').show();
        if ($(this).val() == 'other') {
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
        $('.suggested_number_of_days').closest('p').show();
           if ($(this).val() == 'other') {
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
    //    $('#pickup_location').select2();
    //    $('#dropoff_location').select2();

     $("#pickup_location, #dropoff_location").select2({
            ajax: {
                delay: 250,
                url: "{{ route('partner.location.json') }}",
                method: 'POST',
                dataType: 'json',
                data: function (params) {
                    return {
                        '_token': '{{ csrf_token() }}',
                        'search': params.term, // search term
                        // page: params.page
                    };
                },
               processResults: function(data) {
                    var results = [];


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


                        // console.warn('resultsArr',results);


                    return {
                        results: results
                    };
                },

                    error: function(xhr, status, error) {
                        console.error(error);
                        // Handle error if needed
                    }
            }
     });
  });
</script>
@endsection
