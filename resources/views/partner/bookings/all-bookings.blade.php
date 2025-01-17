@extends('layouts.partner')
@section('title', 'All booking list')
@section('content')
<style>
    .error { color: #ff0000; }
    .required { color: #ff0000; }
    .dp-highlight .ui-state-default { background: #f3cd0e; color: #FFF; }
    .chckBoxes_container input:checked~.checkmark:after { display: block; }
    .chckBoxes_container input:checked~.checkmark { background-color: #F4B20F; }
    input.cars_choice_check { position: absolute; opacity: 0; cursor: pointer; width: 0; height: 0; }
    .chckBoxes_container { width: 100%; display: block; position: relative; cursor: pointer;  }
    .checkmark { position: absolute; top: 50%; transform: translateY(-50%); left: 0; height: 18px; width: 18px; border-radius: 4px; border: 1px solid #D9D9D9; }
    .checkmark::after { content: ""; position: absolute; display: none; left: 6px; top: 2px; width: 5px; height: 10px; border: solid #fff; border-width: 0 2px 2px 0; -webkit-transform: rotate(45deg); -ms-transform: rotate(45deg); transform: rotate(45deg); } input.choice_checkBox { position: absolute; opacity: 0; cursor: pointer; width: 0; height: 0; }
    .filter_tab_sec ul li.active a { background: #fff; }
    .list_top_right_icons a { margin-right: 20px; padding: 4px; }
    .list_top_right_icons a:last-child { margin-right: 0px; }
    .box_main_sec { background: #FFFFFF; border-radius: 10px; }
    .mob_list_items li{ border-bottom: 1px solid #E7E7E7; }
    .mob_list_items li:last-child{ border-bottom: none; }

    /* /////////scroller//////////  */
    .ui-menu.ui-widget.ui-widget-content::-webkit-scrollbar{ width: .3em; }
    .ui-menu.ui-widget.ui-widget-content::-webkit-scrollbar,.scroller::-webkit-scrollbar-thumb{ overflow: visible;  border-radius: 20px 20px 0 0; }
    .ui-menu.ui-widget.ui-widget-content::-webkit-scrollbar-thumb{ overflow: visible; background: rgba(0, 0, 0, .2); border-radius: 20px 20px 0 0; }
    /* //////////////// end /////////////// */  /* search icon */

    .ul_class{ top:234.8px !important; }
    .ui-front { z-index: 9; }
    .ui-autocomplete { max-height: 250px; overflow-y: auto; /* Optional: Add other styles as needed */ }
    .customer_name_mob{ display: block; padding: 7px 15px!important; border-bottom: 1px solid #c5c5c5; font-family: 'Roboto', sans-serif;}
    .ui-menu-item:last-child .customer_name_mob{ border-bottom: none; }
    .search_icon_background{ background-position: 95% center;} /*  */
    .air-datepicker { width:auto ;}
    .mobile_datepicker{ width: 100%; }
    .ul_class li.ui-menu-item { width: 99.5%;}
    .desktp_min_height{ min-height: 100vh; }
    .adj_empty_car_sec{ height: calc(100vh - 250px); }

    .daterange .air-datepicker {
        border: none !important;
    }
    .drop_down_fillter_2 {
        border: 1px solid #d7d7d7;
    }

    @media screen and (max-width: 992px)
    {
        .desktp_min_height{ min-height: calc(100vh - 97px); }
        .adj_empty_car_sec{ height: calc(100vh - 245px);    }
    }

    @media screen and (max-width: 389px)
    {
    .reset_all_inner_sec { max-width: 33.3%; }
    }

    @media screen and (max-width: 992px){
        .header_section_container {display:none;}
    }

</style>

    <input type="hidden" class="get_loadmore_val" value="list" >

    <div class="desktp_min_height py-9 px-9 lg:pt-[20px] lg:pb-[100px] lg:px-[17px] bg-[#F6F6F6]  lg:flex lg:justify-center">
        <div class=" w-full  lg:bg-textGray400 rounded-[10px]">
            <div class="booking_section">
                <!-- mobile filtersec -->
                <div class="booking_head_sec">
                    <div class="flex items-center mb-[36px] lg:mb-[20px]">
                            <div class="flex items-center justify-start w-1/2">
                                <a href="javascript:void(0)" class="hidden py-2 mr-2">
                                    <img class="w-[42px]" src="{{asset('images/panel-back-arrow.svg')}}">
                                </a>
                                <span class="inline-block text-black800 text-[26px] md:text-xl font-normal leading-normal align-middle">All Bookings</span>
                            </div>

                            <div class="w-1/2 text-right flex justify-end">
                                <a href="{{route('partner.booking.calendar')}}" id="newBookingCta"
                                    class="inline-flex rounded-[4px] items-center uppercase text-black text-base md:text-sm font-normal border border-siteYellow bg-siteYellow px-[20px] py-2.5 md:px-[15px] hover:bg-siteYellow400 lg:fixed sm:bottom-[80px] lg:bottom-[80px]">
                                    new booking
                                </a>
                                <a href="javascript:void(0);" class="lg:block mobile_filter_Sec hidden inline-flex  items-center ">
                                    <span class="">
                                        <img src="{{asset('images/filter_icon.svg')}}" alt="filter icon">
                                    </span>
                                </a>
                            </div>

                    </div>
                </div>

                <!-- desktop filtersec -->
                <div class="bg-white py-2.5 px-4 rounded mb-9 lg:hidden">
                    <form  id="desktop_filters_form">
                        <input type="hidden" name="start_date" class="desktop_start_date">
                        <input type="hidden" name="end_date" class="desktop_end_date">
                        <div class="flex align-center ">
                         <div class="flex xl:basis-[65%] basis-[70%] align-center 2xl:grow-0 2xl:shrink-0 m-0">
                                    <div class="basis-[35%] xl:basis-[57%] 3xl:basis-[45%] pr-2">
                                        <div class="w-auto">
                                            <div class="booking_search dropdown_sec_drop dropdown__drop_fillter_btn1 relative   ">
                                            <span class="absolute top-[14px] right-[13px] icon-container">
                                                <img class="cross_icon hidden w-[12px] cursor-pointer" src="{{asset('images/cross.svg')}}" alt="cross_icon" onclick="clearSearchSecond()" >
                                                <img class="search_icon w-[14px]" src="{{asset('images/search_icon.svg')}}" alt="search_icon" >
                                            </span>

                                            <input type="text" class="w-full inline-block text-xm text-sm placeholder-[#CCCCCC]   bg-[#F4F4F4] appearance-none leading-4 outline-none rounded text-black  rounded bg-[#F4F4F4] py-3 pl-3 pr-[44px]" id="search_listing"
                                            placeholder="Search Customer Name / Mobile/ Registration Number">
                                                <ul
                                                    class="drop_down_fillter_1 hidden absolute left-0 top-[58px] w-full border border-[#E0DCDC] fltr_dropdown_shadow bg-[#FFFFFF] z-[2] rounded text-left max-h-[62vh] overflow-y-auto  hidden">
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="basis-[30%]  xl:basis-[40%] 3xl:basis-[35%] px-2">
                                        <div class="w-auto">
                                            <div class="cursor-pointer  dropdown_sec_drop dropdown__drop_fillter_btn2 relative py-2 pl-[18px] rounded pr-11 bg-[#F4F4F4] date_range_block 3xl:w-max min-w-[225px] xl:min-w-[170px]">

                                                <span class="absolute hidden top-[14px] right-[13px] cross_icon_date">
                                                    <img src="{{asset('images/cross.svg')}}" alt="cross_icon_date" class="w-[14px]">
                                                </span>
                                                <a href="javascript:void(0);"
                                                    class="relative text-sm font-normal leading-4 text-black dropdown_listing">
                                                    Date Range
                                                </a>
                                                {{-- <ul
                                                    class="drop_down_fillter_2 desk_daterange_filter absolute left-0 top-[58px] border-[#E0DCDC] fltr_dropdown_shadow  bg-[#FFFFFF] z-[2] min-w-[252px] w-full rounded-[5px] hidden" >
                                                    <li class="w-full text-sm font-normal">
                                                        <div class="block w-full daterange date_range_inner afclr">
                                                        </div>
                                                    </li>
                                                </ul> --}}
                                                <div class="drop_down_fillter_2 desk_daterange_filter absolute left-0 top-[58px]
                                                border-[#E0DCDC] fltr_dropdown_shadow rounded-[5px] bg-[#FFFFFF] min-w-[252px] w-full hidden">

                                                    <ul class="">
                                                        <li class="w-full text-sm font-normal">
                                                            <div class="block w-full daterange date_range_inner afclr">
                                                            </div>
                                                        </li>
                                                    </ul>

                                                    <div class="drop_down_fillter_2_action_sec pt-2 pb-3 mt-2 hidden">
                                                        <div class="flex justify-end flex-end">
                                                            <div class="px-2">
                                                                <a href="javascript:void(0);" class="inline-block cursor-pointer capitalize drop_down_fillter_2_done_btn px-5
                                                                py-1 text-sm font-normal leading-4 text-black border
                                                                rounded rounded border-siteYellow bg-siteYellow transition-all duration-300 ease-in-out hover:bg-siteYellow400">done</a>
                                                            </div>
                                                            <div class="px-2">
                                                                <a href="javascript:void(0);" class="inline-block drop_down_fillter_2_cancel_btn capitalize px-5
                                                                py-1 text-sm font-normal leading-4 text-black rounded rounded border-transparent  transition-all duration-300 ease-in-out hover:bg-siteYellow400 hover:border-siteYellow">
                                                                cancel
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class=" flex justify-end xl:basis-[35%] basis-[30%] 2xl:grow-0 2xl:shrink-0 align-center m-0 ">
                                    <div class="flex justify-end -mx-2 flex-end align-center">
                                        <div class="px-2">
                                            <a id="desktop_fltr_apply_btn" class="desktop_fltr_apply_btn cursor-pointer capitalize inline-block px-6 py-2.5 text-sm font-normal leading-4 text-black border rounded border-siteYellow bg-siteYellow transition-all duration-300 ease-in-out hover:bg-siteYellow400"
                                            >SEARCH</a>
                                        </div>
                                        <div class="px-2">
                                            <a class="clearAll inline-block px-6 py-2.5 desktop_clear_btn text-sm font-normal leading-4 text-black border rounded border-siteYellow transition-all duration-300 ease-in-out hover:bg-siteYellow400"
                                                href="javascript:void(0);">CLEAR</a>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </form>
                </div>

                <!-- mobile filter sec  -->
                <div class="filtersec fixed overflow-hidden   top-[20%] left-0  overflow-x-hidden
                        z-[11] opacity-0 invisible transition-all duration-[0.5s] ease-in  w-full">
                    <div class="relative">
                        <div class="w-full px-5 py-2 text-right bg-transparent close_mob_filter_sec">
                            <a href="javascript:void(0);" class="inline-block pt-5 close_mob_filter">
                                <span class="inline-block close_wh_icon">
                                    <img src="{{asset('images/cross_white.svg')}}" class="close_mobile_filter_btn" alt="cross">
                                </span>
                            </a>
                        </div>

                        <div class="bg-[#FFFFFF]">
                            <div class="w-full overflow-y-auto filter_height_sec">
                                <form action="{{route('partner.all.booking.list.filter')}}" id="mob_filters_form" method="post"
                                    class="mobile_filters_form">

                                    <input type="hidden" name="start_date" class="mobile_fltr_start_date">
                                    <input type="hidden" name="end_date" class="mobile_fltr_end_date">
                                    <div class="flex flex-wrap filter_main">
                                        <div class="filter_content_sec bg-textGray400 w-1/3 min-h-[751px]">
                                            <div class="filter_tab_sec afclr">
                                                <div class="w-full text-sm capitalize filter_heading text-[#4A4A4A] py-[10px] pl-[10px] text-sm font-normal">
                                                    filter
                                                </div>
                                                <ul>
                                                    <li class="active">
                                                        <a href="#tab1"
                                                            class=" pl-[10px] w-full flex items-center justify-between text-sm py-[18px] capitalize hover:bg-[#fff] hover:shadow-sm">customers
                                                            <span class=" inline-flex w-[20px] h-[20px]">
                                                                <svg class="w-full" xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                                                    viewBox="-10 0 202 512">
                                                                    <path fill="currentColor"
                                                                        d="M166.9 264.5l-117.801 116c-4.69922 4.7002 -12.2998 4.7002 -17 0l-7.09961 -7.09961c-4.7002 -4.7002 -4.7002 -12.3008 0 -17l102.3 -100.4l-102.2 -100.4c-4.69922 -4.69922 -4.69922 -12.2998 0 -17l7.10059 -7.09961c4.7002 -4.7002 12.2998 -4.7002 17 0
                                                                        l117.8 116c4.59961 4.7002 4.59961 12.2998 -0.0996094 17z">
                                                                    </path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    </li>
                                                    <li class="">
                                                        <a href="#tab2"
                                                            class="pl-[10px] w-full segment_tab flex items-center justify-between py-[18px]  capitalize text-sm hover:bg-[#fff] hover:shadow-sm">date
                                                            <span class=" inline-flex w-[20px] h-[20px]">
                                                                <svg class="w-full" xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                                                    viewBox="-10 0 202 512">
                                                                    <path fill="currentColor"
                                                                        d="M166.9 264.5l-117.801 116c-4.69922 4.7002 -12.2998 4.7002 -17 0l-7.09961 -7.09961c-4.7002 -4.7002 -4.7002 -12.3008 0 -17l102.3 -100.4l-102.2 -100.4c-4.69922 -4.69922 -4.69922 -12.2998 0 -17l7.10059 -7.09961c4.7002 -4.7002 12.2998 -4.7002 17 0
                                                                        l117.8 116c4.59961 4.7002 4.59961 12.2998 -0.0996094 17z">
                                                                    </path>
                                                                </svg>
                                                            </span>

                                                        </a>
                                                    </li>

                                                </ul>
                                            </div>

                                        </div>
                                        <div class="filter_table_sec  bg-[#fff] w-2/3 filter_client_sec afclr">
                                            <div class="filter_table_inner_sec afclr">
                                                <div class="hidden mobile_filter_box afclr" id="tab1" style="display:none;">
                                                    <div class="py-[10px] ">
                                                        <div class="flex mobile_filter_box_tittle px-[15px] ">
                                                            <div class="flex w-1/2 mobile_filter_left_sec ">
                                                                <div> <a href="javascript:void(0)" class="text-sm font-normal text-black capitalize cursor-auto">
                                                                        customers</a>
                                                                </div>
                                                            </div>
                                                            <div class="flex justify-end w-1/2 font-normal mobile_filter_right_sec">
                                                                <div>
                                                                    <a href="javascript:void(0)"
                                                                        class="text-sm font-normal segment_choices_clear reset_btn capitalize text-[#F4B20F] p-2">
                                                                        reset
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-4">

                                                            <div class="px-[15px] drop_down_fillter_4">
                                                                <div class="search_list_filter bg-[#FFFF] p-[12px] sm:px-0">
                                                                    <div class="search_list_item_p bg-[#F6F6F6] w-full rounded-[30px] py-[7px] px-[7px] flex items-center">
                                                                        <img src="{{asset('/images/search_icon.svg')}}" class="w-[17px]">
                                                                        <input type="text"
                                                                            class="py-0 px-[8px] bg-[#F6F6F6] w-full filter_search_inp2 search_list_src outline-none text-[12px] s_search_inp_categ"
                                                                            placeholder="Search">
                                                                    </div>
                                                                <div class="hidden flex items-center justify-center capitalize pt-4 text-sm text-[#686868] " id="customer_search_no_data_found"> no data found !!</div>

                                                                </div>
                                                            </div>

                                                        @php
                                                            // Get all unique mobile numbers along with their IDs
                                                            $uniqueMobiles = $allBookedCars->unique('customer_mobile')->sortBy(function($item) {
                                                                return strtolower($item->customer_name);
                                                            })->pluck('customer_mobile', 'id');
                                                        @endphp

                                                        <ul class="w-full pb-[200px] mob_list_items">
                                                            @foreach($uniqueMobiles as $id => $mobile)
                                                                @php
                                                                    // Find the booked car information by ID
                                                                    $bookedCar = $allBookedCars->where('id', $id)->first();
                                                                @endphp
                                                                <li class="text-xs font-normal pt-[8px] px-[25px] sm:px-5">
                                                                    <div class="mobile_customer_click flex flex-wrap w-full">
                                                                        <label class="chckBoxes_container mb-[8px] pl-[18px] sm:pl-0">
                                                                            <p class="text-xs mb-0 font-normal ml-[6px] capitalize py-2 sm:ml-0">
                                                                                {{ $bookedCar->customer_name }} ({{ $bookedCar->customer_mobile_country_code }}&nbsp;{{ $mobile }})
                                                                            </p>
                                                                            <input type="checkbox" name="booking_id[]" value="{{ $bookedCar->customer_name }}" data-customer_country_code="{{ $bookedCar->customer_mobile_country_code }}"
                                                                                data-customer_mobile="{{ $mobile }}"
                                                                                class="choice_checkBox mob_customer_checkboxes clear_checkbox mob_status_checkboxes">
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>

                                                      </div>
                                                    </div>
                                                </div>

                                                <div class="hidden mobile_filter_box afclr" id="tab2" style="display:none;">
                                                    <div class="py-[10px] px-[15px]">
                                                        <div class="flex mobile_filter_box_tittle ">
                                                            <div class="flex w-1/2 mobile_filter_left_sec">
                                                                <div>
                                                                    <a href="javascript:void(0)"
                                                                        class="text-sm font-normal text-black capitalize cursor-auto">select
                                                                        date
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="flex justify-end w-1/2 font-normal mobile_filter_right_sec">
                                                                <div>
                                                                    <a href="javascript:void(0)"
                                                                        class="text-sm font-normal reset_mob_date capitalize text-[#F4B20F]">
                                                                        reset
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mt-4">

                                                            <div class="mobile_datepicker_sec">
                                                                <div class="flex justify-center mobile_datepicker_inner">
                                                                    <div class="mobile_datepicker"></div>
                                                                </div>

                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </form>
                               <!-- filter apply section -->
                                <div class="flex flex-col w-full bg-[#fff] items-center  afclr filter_apply_sec">

                                    <div class="w-full reset_all_section">
                                        <div class="w-full text-center bg-transparent reset_all_inner_sec">
                                            <a href="javascript:void(0);" class="mob_clearAll  reset_all_link capitalize text-[#F4B20F] text-sm p-2">reset all</a>
                                        </div>
                                    </div>

                                    <div class="flex w-full filter_apply_inner_sec">
                                        <div class="flex justify-end w-1/2 mr-[10px]">
                                            <a href="javascript:void(0);"
                                                class="capitalize font-normal cancel_mobile_filter transition-all duration-300 border-siteYellow text-sm font-bold border  hover:bg-siteYellow400 rounded py-[10px] px-[30px] bg-white text-[#000] close_mob_filter">
                                                cancel
                                            </a>
                                        </div>
                                        <div class="flex justify-start w-1/2 ml-[10px]">
                                            <a href="javascript:void(0);" id="mob_fltr_apply_btn"
                                                class="mob_apply_section font-normal apply_mobile_filter border border-siteYellow transition-all duration-300 capitalize text-sm font-bold text-[#393939] bg-siteYellow  hover:bg-siteYellow400 transition-all duration-300 ease-in-out rounded py-[10px] px-[30px] ">
                                                Search</a>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                <!-- bottom sec -->
                <div class="main_booking_section_inner">
                    @if(count($booked_cars)>0)

                    <!-- Desktop list -->
                    <div class="desktop_booking_list block md:hidden">

                        @foreach($booked_cars as $booked_car)
                            @php
                                date_default_timezone_set('Asia/Kolkata');

                                $currentTimeDate = now();
                                // echo "test". $currentTimeDate;

                                $combinedStartDateTime = $booked_car->pickup_date;
                                // Set the default time zone to Asia/Kolkata

                                $carbonStartDateTime = \Carbon\Carbon::parse($combinedStartDateTime);
                                $pickupTimeBeforeThirty = $carbonStartDateTime->copy()->subMinutes(30);

                                $combinedEndDateTime = $booked_car->dropoff_date;
                                $carbonEndDateTime = \Carbon\Carbon::parse($combinedEndDateTime);
                                $dropoffTimeBeforeThirty = $carbonEndDateTime->copy()->subMinutes(30);

                                // $subtractedStartDateTime = $carbonStartDateTime->copy()->subMinutes(30);
                                $formattedStartDateTime = $carbonStartDateTime->format('d M Y h:i A');
                                $formattedEndDateTime = $carbonEndDateTime->format('d M Y h:i A');
                                $formattedStartTime = $carbonStartDateTime->format('h:i A');
                                $formattedEndTime = $carbonEndDateTime->format('h:i A');
                                $formattedStartDate = $carbonStartDateTime->format('d M Y');
                                $formattedEndDate = $carbonEndDateTime->format('d M Y');
                                // $formattedSubtractedEndTime = $subtractedEndTime->format('h:i a');
                                $convertedformattedStartDate = \Carbon\Carbon::parse($formattedStartDate);
                                $convertedformattedEndDate = \Carbon\Carbon::parse($formattedEndDate);
                                // Calculate the date difference
                                $dateDifference = $convertedformattedEndDate->diff($convertedformattedStartDate);
                                // echo "Date difference: " . $dateDifference->format('%a days');
                            @endphp

                            @php
                            // FOR TODAY/TOMORROW
                                $PickupDate = $carbonStartDateTime->format('Y-m-d');
                                $DropoffDate = $carbonEndDateTime->format('Y-m-d');

                                $currentDate =  \Carbon\Carbon::now('Asia/Kolkata')->format('Y-m-d');

                                $diffPickAndCurrentDate = strtotime($PickupDate) - strtotime($currentDate);
                                $pickupDaysGap = floor($diffPickAndCurrentDate / (60 * 60 * 24));

                                // Convert seconds to days and round down
                                $diffDropAndCurrentDate = strtotime($DropoffDate) - strtotime($currentDate);
                                $dropDaysGap = floor($diffDropAndCurrentDate / (60 * 60 * 24));
                            @endphp

                            @php
                                $roundedDays = $dateDifference->days;
                                $pickupMoment = \Carbon\Carbon::parse($carbonStartDateTime->format('h:i A'));
                                $dropoffMoment = \Carbon\Carbon::parse($carbonEndDateTime->format('h:i A'));
                                // echo "pickupMoment: " . $pickupMoment . "<br>";
                                // echo "dropoffMoment: " . $dropoffMoment . "<br>";
                                // echo "1: " . (\Carbon\Carbon::parse('9:00 AM')) . "<br>";
                                // echo "2: " . (\Carbon\Carbon::parse('9:00 AM')) . "<br>";
                                if ($pickupMoment->lt(\Carbon\Carbon::parse('9:00 AM'))||$dropoffMoment->gt(\Carbon\Carbon::parse('9:00 AM'))) {
                                    $roundedDays++;
                                    // echo 'Pickup date is before 9:00 AM: ' . $pickupMoment;
                                    }
                                else {
                                    $roundedDays = $dateDifference->days;
                                }
                                $nowDateTime = \Carbon\Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
                                // Parse $nowDateTime into a Carbon object
                                $nowDateTimeObject = \Carbon\Carbon::parse($nowDateTime);
                                // Parse $carbonStartDateTime into a Carbon object
                                $nowCarbonStartDateTime = \Carbon\Carbon::parse($carbonStartDateTime);
                                // Parse $carbonEndDateTime into a Carbon object
                                $nowCarbonEndDateTime = \Carbon\Carbon::parse($carbonEndDateTime);
                            @endphp

                            @php
                                $thumbnails = Helper::getFeaturedSetCarPhotoById($booked_car->carId);
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

                            <div class="mb-5 bookingLength">
                                <div class="booking_car_list_box p-4  rounded-[4px] md:hidden block


                                    {{-- @if(($currentTimeDate >= $pickupTimeBeforeThirty && strcmp($booked_car->status, 'delivered')  !== 0 ) && strcmp($booked_car->status, 'collected') ) --}}
                                    @if(($currentTimeDate >= $pickupTimeBeforeThirty && strcmp($booked_car->status, 'delivered') !== 0 ) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )

                                        bg-[#fffbe5]
                                    @elseif(($currentTimeDate >= $dropoffTimeBeforeThirty && strcmp($booked_car->status, 'delivered') == 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0))
                                        bg-[#fffbe5]
                                    @else
                                        bg-white
                                    @endif">

                                    <div class="list_cr_out flex items-center  @if(strcmp($booked_car->customer_name,'')!==0 || strcmp($booked_car->customer_mobile_country_code,'')!==0 || strcmp($booked_car->customer_mobile,'')!==0) justify-center @else justify-start @endif">

                                        <div class="list_cr_item">
                                            <div class="booking_list_card_top_section "data-id="{{ $booked_car->id }}">
                                            @if($booked_car->booking_owner_id)
                                                @php
                                                $agentCompanyName = ucwords(Helper::getUserMeta($booked_car->booking_owner_id, 'company_name'));

                                                @endphp
                                                <div class="flex pb-2"><p>Booked By {{$agentCompanyName}}</p></div>
                                            @endif

                                            <div class="flex">
                                              <div class="">
                                                <div class="flex items-center flex-wrap gap-[3px]">

                                                    @if(($currentTimeDate >= $pickupTimeBeforeThirty && strcmp($booked_car->status, 'delivered') !== 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
                                                        <div class="flex items-center justify-center mr-[32px] sm:mr-[10px] 3xl:mr-[25px]">
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

                                                    @elseif(($currentTimeDate >= $dropoffTimeBeforeThirty && strcmp($booked_car->status, 'delivered') == 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
                                                        <div class="flex items-center justify-center mr-[32px] sm:mr-[10px] 3xl:mr-[25px]">
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
                                                    @elseif( ($pickupDaysGap == 0) && ($nowDateTimeObject <= $nowCarbonStartDateTime) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
                                                        <div class="flex items-center justify-center mr-[32px] sm:mr-[10px] 3xl:mr-[25px]">
                                                            <div class="block">
                                                                    <img src="{{asset('images/time-clock-img.svg')}}" alt="clock" class="w-[19px]">
                                                            </div>
                                                            <div class="w-full ml-[6px]">
                                                                <span class="block text-siteYellow800 text-xs font-normal leading-normal">
                                                                    Pickup: {{$formattedStartTime}}
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <div class="text-xs">
                                                            <span class="uppercase status_time border border-[#CE3A3A] rounded-[12px]
                                                            text-[#CE3A3A] px-[10px] py-[2px] 3xl:py-[1px]">today
                                                            </span>
                                                        </div>

                                                    @elseif(($pickupDaysGap == 1) && ($nowDateTimeObject <= $nowCarbonStartDateTime ) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
                                                            <div class="flex items-center justify-center mr-[32px] sm:mr-[10px] 3xl:mr-[25px]">
                                                                <div class="block">
                                                                    <img src="{{asset('images/time-clock-img.svg')}}" alt="clock" class="w-[19px]">
                                                                </div>

                                                                <div class="w-full ml-[6px]">
                                                                    <span class="block text-siteYellow800 text-xs font-normal leading-normal">Pickup: {{$formattedStartTime}}
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <div class="text-xs">
                                                                <span class="uppercase status_time border border-[#5DB47F] rounded-[12px] text-[#5DB47F] px-[10px] py-[2px] 3xl:py-[1px]">Tomorrow</span>
                                                            </div>

                                                    <!-- drop-off -->
                                                    @elseif(($dropDaysGap == 0) && ($nowDateTimeObject <= $nowCarbonEndDateTime) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
                                                            <div class="flex items-center justify-center mr-[32px] sm:mr-[10px] 3xl:mr-[25px]">
                                                                <div class="block">
                                                                    <img src="{{asset('images/time-clock-img.svg')}}" alt="clock" class="w-[19px]">
                                                                </div>
                                                                <div class="w-full ml-[6px]">
                                                                    <span class="block text-siteYellow800 text-xs font-normal leading-normal">Drop-off: {{$formattedEndTime}}
                                                                    </span>
                                                                </div>
                                                            </div>

                                                            <div class="text-xs ">
                                                                <span class="uppercase status_time border border-[#CE3A3A] rounded-[12px] text-[#CE3A3A] px-[10px] py-[2px] 3xl:py-[1px]">today</span>
                                                            </div>

                                                    @elseif(($dropDaysGap == 1) && ($nowDateTimeObject <= $nowCarbonEndDateTime) && (strcmp($booked_car->status, 'collected') !== 0)
                                                    && (strcmp($booked_car->status, 'canceled') !== 0) )
                                                            <div class="flex items-center justify-center mr-[32px] sm:mr-[10px] 3xl:mr-[25px]">
                                                                <div class="block">
                                                                    <img src="{{asset('images/time-clock-img.svg')}}" alt="clock" class="w-[19px]">
                                                                </div>

                                                                <div class="w-full ml-[6px]">
                                                                    <span class="block text-siteYellow800 text-xs font-normal leading-normal">Drop-off: {{$formattedEndTime}}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="text-xs  ">
                                                                <span class="uppercase status_time border border-[#5DB47F]
                                                                rounded-[12px] text-[#5DB47F] px-[10px] py-[2px] 3xl:py-[1px]">Tomorrow
                                                                </span>
                                                            </div>

                                                    @elseif(($pickupDaysGap>1) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0))
                                                            <div class="flex items-center justify-center">
                                                                <div class="w-full ml-[6px]">
                                                                    <span class="block text-[#3D3D3D] text-xs font-normal leading-normal">Pickup: {{$formattedStartDate}}, {{$formattedStartTime}}
                                                                    </span>
                                                                </div>
                                                            </div>

                                                    @elseif(($dropDaysGap>1) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
                                                        <div class="flex items-center justify-center">
                                                            <div class="w-full ml-[6px]">
                                                                <span class="block text-[#3D3D3D] text-xs font-normal leading-normal">Drop-off: {{$formattedEndDate}}, {{$formattedEndTime}}
                                                                </span>
                                                            </div>
                                                        </div>

                                                    @elseif((strcmp($booked_car->status, 'collected') == 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
                                                        <div class="text-xs ">
                                                            <span class="uppercase status_time border border-[#25BE00] rounded-[12px] text-[#25BE00] px-[10px] py-[2px] 3xl:py-[1px] bg-[#FFFFFF] ">Booking Completed
                                                            </span>
                                                        </div>

                                                    @elseif( (strcmp($booked_car->status, 'canceled') == 0) )
                                                        <div class="text-xs ">
                                                            <span class="uppercase status_time border border-[#BD0000] rounded-[12px] text-[#BD0000] px-[10px] py-[2px] 3xl:py-[1px] bg-[#FFFFFF] ">Booking Canceled
                                                            </span>
                                                        </div>
                                                    @endif

                                                </div>
                                                </div>
                                              </div>
                                            </div>
                                            <div class="flex flex-wrap  booking_list_card_bottom_section mt-[9px]">
                                                <div class="min-w-[150px] max-w-[150px] w-[150px] 3xl:min-w-[135px] 3xl:max-w-[135px] 3xl:w-[135px]">
                                                        <div class="image_l_wh">
                                                            <img src="{{$modifiedImgUrl}}" alt="cars" class="object-contain max-h-full">
                                                        </div>
                                                </div>
                                                <div class="ctc_info_w">
                                                    <div class="mb-[4px]">
                                                        <h4 class="capitalize text-[#2B2B2B] font-medium leading-4 text-[13px]">{{Helper::getCarNameByCarId($booked_car->carId)}}</h4>
                                                        <p class="uppercase text-[#666] text-[13px] font-normal"> {{Helper::getCarRagisterationNoByCarId($booked_car->carId)}}</p>
                                                    </div>
                                                    <div class="block">
                                                        <a href="{{route('partner.booking.view',$booked_car->id)}}" class=" links_item_cta inline-block text-[#2B2B2B] font-medium pb-[1px] leading-4 text-[13px] border-b-2 border-siteYellow">View Booking</a>
                                                    </div>

                                                </div>

                                            </div>
                                            </div>


                                            <div class="list_cr_item">
                                                <div class="booking_list_card_top_section">
                                                    <div class="block">
                                                            <h4 class="text-[#898376] font-medium leading-4 text-[13px]">Booking Details</h4>
                                                    </div>
                                                    <div class="flex flex-wrap  mt-[9px]">
                                                        <div class="4xl:min-w-full 4xl:pr-[0px] 3xl:w-[100%] max-w-[165px] min-w-[165px] pr-[20px] 2xl:pr-[0px] 3xl:max-w-none 3xl:pr-[15px] ">
                                                                <p class="text-[13px] text-[#666666]">Start</p>
                                                                    <h4 class="date_time text-[13px] font-medium text-[#000]">
                                                                    {{$formattedStartDate}}<span class="date">&nbsp;|&nbsp;</span>{{$formattedStartTime}}
                                                                    </h4>
                                                                <p class="text-[13px] font-normal text-[#666666]">{{$booked_car->pickup_location}}</p>
                                                        </div>


                                                        <div class="4xl:min-w-full 4xl:my-[10px] 3xl:w-[100%] my-auto max-w-[42px] min-w-[42px] 3xl:my-[10px] 3xl:max-w-none">
                                                            <div class="inline-block py-1 border-t border-b border-1 border-[#898376] text-[12px] flex justify-center items-center w-[42px]">

                                                                {{$booked_car->number_of_days. " days"}}

                                                            </div>
                                                        </div>

                                                        <div class="4xl:min-w-full 4xl:pl-[0px] 3xl:w-[100%] max-w-[165px] min-w-[165px] pl-[20px] 3xl:max-w-none 3xl:pl-[0px]">
                                                            <div>
                                                                <p class="text-[13px] text-[#666666]">End</p>
                                                                <h4 class="date_time text-[13px] font-medium text-[#000]">
                                                                    {{$formattedEndDate}}<span class="date">&nbsp;|&nbsp;</span>{{$formattedEndTime}}</h4>
                                                                <p class="text-[13px] font-normal text-[#666666]">{{$booked_car->dropoff_location}}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                     @if(strcmp($booked_car->customer_name,'')!==0 || strcmp($booked_car->customer_mobile_country_code,'')!==0 || strcmp($booked_car->customer_mobile,'')!==0)
                                        <div class="list_cr_item">
                                                <div class="booking_list_card_top_section">
                                                <div class="block">
                                                        <h4 class="text-[#898376] font-normal leading-4 text-[13px] mb-[2px]">Customer Details</h4>
                                                        <p class="capitalize text-[13px] font-normal text-black">{{$booked_car->customer_name}}</p>
                                                        <p class="text-[13px] font-normal text-black">{{$booked_car->customer_mobile_country_code }}&nbsp;{{$booked_car->customer_mobile}}</p>
                                                        <div class="pt-[10px]">
                                                            <div class="flex">
                                                                <div class="pr-[30px] 2xl:pr-[13px]">
                                                                    <a class="inline-block px-6 2xl:px-[18px] py-2.5 text-sm font-normal leading-4 text-black border rounded border-siteYellow bg-siteYellow transition-all duration-300 ease-in-out hover:bg-siteYellow400 leading-none"
                                                                        href="tel:{{$booked_car->customer_mobile}}">CALL</a>

                                                                </div>


                                                    <div class="block">
                                                        <a class="inline-block px-6 2xl:px-[18px] py-2.5 text-sm font-normal leading-4 text-black border rounded border-siteYellow transition-all duration-300 ease-in-out hover:bg-siteYellow400  leading-none"
                                                        href="https://wa.me/{{ ltrim($booked_car->customer_mobile_country_code, '+') }}{{ $booked_car->customer_mobile }}" target="_blank">WHATSAPP</a>
                                                    </div>
                                                            </div>
                                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        </div>
                                    </div>


                                    @if( (($currentTimeDate >= $pickupTimeBeforeThirty && strcmp($booked_car->status, 'delivered') !== 0 ) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0)) ||
                                    (($currentTimeDate >= $dropoffTimeBeforeThirty && strcmp($booked_car->status, 'delivered') == 0) && (strcmp($booked_car->status, 'collected') !== 0)  && (strcmp($booked_car->status, 'canceled') !== 0)) )

                                        <!-- action buttons -->
                                        <div class="action_section_container ">
                                            <div class="action_section_inner flex justify-center">

                                                <div class="flex justify-center w-[34%]  m-auto  bg-[#fffbe5] py-2.5 2xl:px-6 px-10 rounded-md "
                                                style="border-bottom-left-radius: 35px; border-bottom-right-radius: 35px;
                                                clip-path: polygon(0 0, 100% 0, 96% 100%, 4% 100%);" >
                                                    <div class="w-full">
                                                        <div class="flex justify-center -mx-1.5 flex-wrap ">

                                                        <!-- 1st button -->

                                                            <div class=" flex 2xl:w-full 2xl:mb-3 w-1/2  px-1.5  justify-center" >
                                                                <a href="javascript:void(0)"  class=" flex justify-center
                                                                w-full items-center py-1 px-3
                                                                text-xs font-normal leading-4  border rounded
                                                                @if(strcmp($booked_car->status, 'delivered') == 0)
                                                                {{-- cursor-not-allowed  --}}
                                                                cursor-default
                                                                bg-[#EBEBE4] border-[#EBEBE4] text-[#AEAEAE]
                                                                @else
                                                                booking_action_btn
                                                                border-siteYellow transition-all duration-300
                                                                ease-in-out bg-siteYellow hover:bg-siteYellow400
                                                                @endif"
                                                                data-booking-id="{{$booked_car->id}}"
                                                                @if(strcmp($booked_car->status, 'delivered') == 0)
                                                                disabled="disabled"
                                                                @else
                                                                    data-booking-action="confirm_delivery"
                                                                @endif >
                                                                    @if(strcmp($booked_car->status, 'delivered') == 0)
                                                                        <img src="{{asset('images/blur_confirm_delivery.svg')}}" alt="clock" class="w-[20px] mr-2">
                                                                    @else
                                                                        <img src="{{asset('images/confirm_delivery.svg')}}" alt="clock" class="w-[20px] mr-2">
                                                                    @endif

                                                                CONFIRM DELIVERY
                                                                </a>
                                                            </div>

                                                        <!-- 2nd button -->

                                                            <div class="flex 2xl:w-full w-1/2  px-1.5 justify-center">
                                                                <a href="javascript:void(0)"  class=" flex justify-center
                                                                w-full  py-1 px-3
                                                                items-center text-xs font-normal leading-4 border rounded
                                                                @if(strcmp($booked_car->status, 'delivered') !== 0)
                                                                    {{-- cursor-not-allowed --}}
                                                                    cursor-default
                                                                    bg-[#EBEBE4] border-[#EBEBE4] text-[#AEAEAE]
                                                                    @else
                                                                    booking_action_btn
                                                                    border-siteYellow
                                                                    transition-all duration-300 ease-in-out bg-siteYellow hover:bg-siteYellow400
                                                                @endif
                                                                "
                                                                data-booking-id="{{$booked_car->id}}"
                                                                    @if(strcmp($booked_car->status, 'delivered') !== 0)
                                                                    disabled="disabled"
                                                                    @else
                                                                    data-booking-action="confirm_collection"
                                                                    @endif >

                                                                        @if(strcmp($booked_car->status, 'delivered') !== 0)
                                                                            <img src="{{asset('images/blur_carswithkey3.svg')}}" alt="clock" class="w-[20px] mr-2">
                                                                        @else
                                                                            <img src="{{asset('images/carswithkey.svg')}}" alt="clock" class="w-[20px] mr-2">
                                                                        @endif
                                                                CONFIRM COLLECTION
                                                                </a>
                                                            </div>
                                                        {{-- @endif --}}

                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <!--  -->

                                    @endif

                            </div>
                        @endforeach
                    </div>

                    <!-- Mobile list -->
                    <div class="mobile_booking_list hidden md:block ">
                        @foreach($booked_cars as $booked_car)
                            @php
                             date_default_timezone_set('Asia/Kolkata');

                            $currentTimeDate = \Carbon\Carbon::now('Asia/Kolkata');

                            $combinedStartDateTime = $booked_car->pickup_date;
                            $carbonStartDateTime = \Carbon\Carbon::parse($combinedStartDateTime);
                            $pickupTimeBeforeThirty = $carbonStartDateTime->copy()->subMinutes(30);

                            $combinedEndDateTime = $booked_car->dropoff_date;
                            $carbonEndDateTime = \Carbon\Carbon::parse($combinedEndDateTime);
                            $dropoffTimeBeforeThirty = $carbonEndDateTime->copy()->subMinutes(30);

                            // $subtractedStartDateTime = $carbonStartDateTime->copy()->subMinutes(30);
                            $formattedStartDateTime = $carbonStartDateTime->format('d M Y h:i A');
                            $formattedEndDateTime = $carbonEndDateTime->format('d M Y h:i A');
                            $formattedStartTime = $carbonStartDateTime->format('h:i A');
                            $formattedEndTime = $carbonEndDateTime->format('h:i A');
                            $formattedStartDate = $carbonStartDateTime->format('d M Y');
                            $formattedEndDate = $carbonEndDateTime->format('d M Y');
                            // $formattedSubtractedEndTime = $subtractedEndTime->format('h:i a');
                            $convertedformattedStartDate = \Carbon\Carbon::parse($formattedStartDate);
                            $convertedformattedEndDate = \Carbon\Carbon::parse($formattedEndDate);
                            // Calculate the date difference
                            $dateDifference = $convertedformattedEndDate->diff($convertedformattedStartDate);
                            // echo "Date difference: " . $dateDifference->format('%a days');
                            @endphp

                            @php
                        // FOR TODAY/TOMORROW
                            date_default_timezone_set('Asia/Kolkata');
                            $PickupDate = $carbonStartDateTime->format('Y-m-d');
                            $DropoffDate = $carbonEndDateTime->format('Y-m-d');
                            $currentDate =  \Carbon\Carbon::now('Asia/Kolkata')->format('Y-m-d');

                            $diffPickAndCurrentDate = strtotime($PickupDate) - strtotime($currentDate);
                            $pickupDaysGap = floor($diffPickAndCurrentDate / (60 * 60 * 24));

                            // Convert seconds to days and round down
                            $diffDropAndCurrentDate = strtotime($DropoffDate) - strtotime($currentDate);
                            $dropDaysGap = floor($diffDropAndCurrentDate / (60 * 60 * 24));
                        @endphp

                        @php
                            date_default_timezone_set('Asia/Kolkata');
                            $roundedDays = $dateDifference->days;
                            $pickupMoment = \Carbon\Carbon::parse($carbonStartDateTime->format('h:i A'));
                            $dropoffMoment = \Carbon\Carbon::parse($carbonEndDateTime->format('h:i A'));
                            // echo "pickupMoment: " . $pickupMoment . "<br>";
                            // echo "dropoffMoment: " . $dropoffMoment . "<br>";
                            // echo "1: " . (\Carbon\Carbon::parse('9:00 AM')) . "<br>";
                            // echo "2: " . (\Carbon\Carbon::parse('9:00 AM')) . "<br>";
                            if ( $pickupMoment->lt(\Carbon\Carbon::parse('9:00 AM')) || $dropoffMoment->gt(\Carbon\Carbon::parse('9:00 AM')) ) {
                                $roundedDays++;
                                // echo 'Pickup date is before 9:00 AM: ' . $pickupMoment;
                            }

                            else {
                                $roundedDays = $dateDifference->days;
                            }

                            $nowDateTime = \Carbon\Carbon::now('Asia/Kolkata')->format('d M Y h:i A');
                            // Parse $nowDateTime into a Carbon object
                            $nowDateTimeObject = \Carbon\Carbon::parse($nowDateTime);
                            // Parse $carbonStartDateTime into a Carbon object
                            $nowCarbonStartDateTime = \Carbon\Carbon::parse($carbonStartDateTime);
                            // Parse $carbonEndDateTime into a Carbon object
                            $nowCarbonEndDateTime = \Carbon\Carbon::parse($carbonEndDateTime);
                        @endphp
                        @php
                            $thumbnails = Helper::getFeaturedSetCarPhotoById($booked_car->carId);
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

                        <div class="bookingLength rounded-[8px] p-[14px] md:block hidden mb-[20px]
                            @if(($currentTimeDate >= $pickupTimeBeforeThirty && strcmp($booked_car->status, 'delivered') !== 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0))
                                bg-[#fffbe5]
                            @elseif(($currentTimeDate >= $dropoffTimeBeforeThirty && strcmp($booked_car->status, 'delivered') == 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0))
                                bg-[#fffbe5]
                            @else
                                bg-white
                            @endif
                        ">
                             @if($booked_car->booking_owner_id)
                                @php
                                $agentCompanyName = ucwords(Helper::getUserMeta($booked_car->booking_owner_id, 'company_name'));

                                @endphp
                                <div class="flex pb-2"><p>Booked By {{$agentCompanyName}}</p></div>
                            @endif

                            <div class="flex justify-between items-center gap-[10px]">
                                @if(($currentTimeDate >= $pickupTimeBeforeThirty && strcmp($booked_car->status, 'delivered') !== 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0))
                                    <div class="flex items-center flex-wrap gap-[5px]">
                                        <div class="flex items-center justify-center mr-[32px] sm:mr-[10px] 3xl:mr-[25px]">
                                            <div class="block">
                                                <img src="{{asset('images/time-clock-img.svg')}}" alt="clock" class="w-[19px]">
                                            </div>
                                            <div class="w-full ml-[6px]">
                                                <span class="block text-siteYellow800 text-[13px] font-normal leading-normal">Pickup: {{$formattedStartDate}}, {{$formattedStartTime}}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-xs ">
                                            <span class="uppercase status_time border border-[#fca728] rounded-[12px]
                                            text-[#fca728] px-[10px] py-[2px] 3xl:py-[1px] bg-[#FFFFFF]">Delivery Awaited
                                            </span>
                                        </div>
                                    </div>
                                @elseif(($currentTimeDate >= $dropoffTimeBeforeThirty && strcmp($booked_car->status, 'delivered') == 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
                                    <div class="flex items-center flex-wrap gap-[5px]">
                                        <div class="flex items-center justify-center mr-[32px] sm:mr-[10px] 3xl:mr-[25px]">
                                            <div class="block">
                                                <img src="{{asset('images/time-clock-img.svg')}}" alt="clock" class="w-[19px]">
                                            </div>
                                            <div class="w-full text-xs ml-[6px]">
                                                <span class="block text-siteYellow800 text-[13px] font-normal leading-normal">Drop-off: {{$formattedEndDate}}, {{$formattedEndTime}}</span>
                                            </div>
                                        </div>
                                        <div class="text-xs  ">
                                            <span class="uppercase status_time border border-[#79CEE9] rounded-[12px]
                                            text-[#79CEE9] px-[10px] py-[2px] 3xl:py-[1px] bg-[#FFFFFF] ">Collection Awaited
                                            </span>
                                        </div>
                                    </div>

                                <!-- pickup today/tomorrow  -->
                                @elseif(($pickupDaysGap == 0) && ($nowDateTimeObject <= $nowCarbonStartDateTime) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0))
                                    <div class="flex items-center flex-wrap gap-[5px]">
                                        <div class="flex items-center justify-center mr-[32px] sm:mr-[10px] 3xl:mr-[25px]">
                                            <div class="block">
                                                <img src="{{asset('images/time-clock-img.svg')}}" alt="clock" class="w-[19px]">
                                            </div>
                                            <div class="w-full ml-[6px]">
                                                <span class="block text-siteYellow800 text-[13px] font-normal leading-normal">Pickup: {{$formattedStartTime}}</span>
                                            </div>
                                        </div>
                                        <div class="text-xs ">
                                            <span class="uppercase status_time border border-[#CE3A3A] rounded-[12px] text-[#CE3A3A] px-[10px] py-[2px] 3xl:py-[1px]">today</span>
                                        </div>
                                    </div>
                                @elseif(($pickupDaysGap == 1) && ($nowDateTimeObject <= $nowCarbonStartDateTime) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
                                    <div class="flex items-center flex-wrap gap-[5px]">
                                        <div class="flex items-center justify-center mr-[32px] sm:mr-[10px] 3xl:mr-[25px]">
                                            <div class="block">
                                                <img src="{{asset('images/time-clock-img.svg')}}" alt="clock" class="w-[19px]">
                                            </div>
                                            <div class="w-full ml-[6px]">
                                                <span class="block text-siteYellow800 text-[13px] font-normal leading-normal">Pickup: {{$formattedStartTime}}</span>
                                            </div>
                                        </div>
                                        <div class="text-xs ">
                                            <span class="uppercase status_time border border-[#5DB47F] rounded-[12px] text-[#5DB47F] px-[10px] py-[2px] 3xl:py-[1px]">Tomorrow</span>
                                        </div>
                                    </div>

                                    <!-- drop-off -->
                                @elseif(($dropDaysGap == 0) && ($nowDateTimeObject <= $nowCarbonEndDateTime) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
                                    <div class="flex items-center flex-wrap gap-[5px]">
                                        <div class="flex items-center justify-center mr-[32px] sm:mr-[10px] 3xl:mr-[25px]">
                                            <div class="block">
                                                <img src="{{asset('images/time-clock-img.svg')}}" alt="clock" class="w-[19px]">
                                            </div>
                                            <div class="w-full ml-[6px]">
                                                <span class="block text-siteYellow800 text-[13px] font-normal leading-normal">Drop-off: {{$formattedEndTime}}</span>
                                            </div>
                                        </div>
                                        <div class="text-xs ">
                                            <span class="uppercase status_time border border-[#CE3A3A] rounded-[12px] text-[#CE3A3A] px-[10px] py-[2px] 3xl:py-[1px]">today</span>
                                        </div>
                                    </div>
                                @elseif(($dropDaysGap == 1) && ($nowDateTimeObject <= $nowCarbonEndDateTime) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
                                    <div class="flex items-center flex-wrap gap-[5px]">
                                        <div class="flex items-center justify-center mr-[32px] sm:mr-[10px] 3xl:mr-[25px]">
                                            <div class="block">
                                                <img src="{{asset('images/time-clock-img.svg')}}" alt="clock" class="w-[19px]">
                                            </div>
                                            <div class="w-full ml-[6px]">
                                                <span class="block text-siteYellow800 text-[13px] font-normal leading-normal">Drop-off: {{$formattedEndTime}}</span>
                                            </div>
                                        </div>
                                        <div class="text-xs ">
                                            <span class="uppercase status_time border border-[#5DB47F] rounded-[12px] text-[#5DB47F] px-[10px] py-[2px] 3xl:py-[1px]">Tomorrow</span>
                                        </div>
                                    </div>
                                <!-- default -->
                                @elseif(($pickupDaysGap>1) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
                                    <div class="flex items-center justify-center">
                                        <div class="w-full ml-[6px]">
                                            <span class="block text-[#3D3D3D] text-xs font-normal leading-normal">Pickup: {{$formattedStartDate}}, {{$formattedStartTime}}</span>
                                        </div>
                                    </div>
                                @elseif(($dropDaysGap>1) && (strcmp($booked_car->status, 'collected') !== 0))
                                    <div class="flex items-center justify-center">
                                        <div class="w-full ml-[6px]">
                                            <span class="block text-[#3D3D3D] text-xs font-normal leading-normal">Drop-off: {{$formattedEndDate}}, {{$formattedEndTime}}</span>
                                        </div>
                                    </div>

                                @elseif( (strcmp($booked_car->status, 'collected') == 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
                                    <div class="text-xs ">
                                        <span class="uppercase status_time border border-[#25BE00] rounded-[12px]
                                        text-[#25BE00] px-[10px] py-[2px] 3xl:py-[1px] bg-[#FFFFFF]">
                                        Booking Completed
                                        </span>
                                    </div>

                                @elseif( (strcmp($booked_car->status, 'canceled') == 0) )
                                    <div class="text-xs ">
                                        <span class="uppercase status_time border border-[#BD0000] rounded-[12px]
                                        text-[#BD0000] px-[10px] py-[2px] 3xl:py-[1px] bg-[#FFFFFF]">Booking Canceled
                                        </span>
                                    </div>

                                @endif

                                <div class="text-right flex items-center">
                                    <p class="text-[#898376] font-normal text-[14px] sm:w-max sm:leading-3">
                                        {{$booked_car->number_of_days . " days"}}
                                    </p>
                                </div>

                            </div>

                            <div class="flex items-center my-[10px] sm:my-[13px]">
                                    <div class="block">
                                        <div class="min-w-[150px] max-w-[150px] w-[150px] 3xl:min-w-[135px] 3xl:max-w-[135px] 3xl:w-[135px] sm:min-w-[107px] sm:w-[107px]">
                                        <div class="image_l_wh sm:w-[107px]">
                                            <img src="{{$modifiedImgUrl}}" alt="cars" class="object-contain max-h-full">
                                        </div>
                                    </div>
                                    </div>

                                    <div class="ctc_mid_r pl-[10px] sm:w-full">
                                        <div class="mb-[6px]">
                                            <h4 class="text-[#898376] font-normal leading-4 text-[15px] capitalize">{{Helper::getCarNameByCarId($booked_car->carId)}}</h4>
                                        </div>
                                        <div class="block">
                                            <h4 class="text-[#898376] font-normal leading-4 text-[15px] mb-[3px] capitalize">Customer: {{$booked_car->customer_name}}</h4>
                                            <p class="text-[#898376] font-normal leading-4 text-[15px] mob_br_sm">Mobile: {{$booked_car->customer_mobile_country_code}}&nbsp;{{$booked_car->customer_mobile}}</p>
                                        </div>
                                    </div>
                            </div>

                            <div class="flex">
                                <div class="w-1/2 pr-[10px] border-r border-1 border-[#C6C6C6]">
                                    <div class="mb-[4px] sm:mb-[0px]">
                                        <h5 class="text-[14px] text-black">Start:</h5>
                                    </div>
                                    <div class="block">
                                        <h4 class="text-[#898376] font-normal leading-4 text-[14px] mb-[3px] mob_br_sm">{{$formattedStartDate}}, <br>{{$formattedStartTime}}</h4>
                                        <p class="text-[#898376] font-normal leading-4 text-[14px]">{{$booked_car->pickup_location}}</p>
                                    </div>
                                </div>
                                <div class="pl-[25px] w-1/2 sm:pl-[15px]">
                                    <div class="mb-[4px] sm:mb-[0px]">
                                        <h5 class="text-[14px] text-black">End:</h5>
                                    </div>
                                    <div class="">
                                        <h4 class="text-[#898376] font-normal leading-4 text-[14px] mb-[3px] mob_br_sm">{{$formattedEndDate}}, <br>{{$formattedEndTime}}</h4>
                                        <p class="text-[#898376] font-normal leading-4 text-[14px]">{{$booked_car->dropoff_location}}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-[18px] sm:mt-[12px] afclr">
                                <div class="flex">
                                    <div class="block">
                                            <h4 class="text-[#898376] font-normal text-[15px]">Contact Customer</h4>
                                    </div>
                                    <div class="pl-[55px] pr-[25px] sm:pl-[15px] sm:pr-[15px]">
                                        <a href="tel:{{$booked_car->customer_mobile}}" class="text-[#700D4A] font-bold text-[15px]">CALL</a>
                                    </div>

                                    <div class="text-left">
                                    <a href="https://wa.me/{{ ltrim($booked_car->customer_mobile_country_code, '+') }}{{ $booked_car->customer_mobile }}" target="_blank" class="text-[#700D4A] font-bold text-[15px]">WHATSAPP</a>
                                    </div>

                                </div>
                                <div class="block mt-[2px]">
                                    <a href="{{route('partner.booking.view',$booked_car->id)}}" class="links_item_cta inline-flex items-center
                                            text-[#2B2B2B] font-medium leading-4 text-[14px]">
                                            View Booking <img src="{{asset('images/arrow-booking.svg')}}" alt="arro" class="ml-[9px] w-[24px]">
                                        </a>
                                </div>
                            </div>


                            @if( (($currentTimeDate >= $pickupTimeBeforeThirty && strcmp($booked_car->status, 'delivered') !== 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) ) ||
                            (($currentTimeDate >= $dropoffTimeBeforeThirty && strcmp($booked_car->status, 'delivered') == 0) && (strcmp($booked_car->status, 'collected') !== 0) && (strcmp($booked_car->status, 'canceled') !== 0) )
                            )

                            <!-- action buttons -->
                            <div class="action_section_container my-5">
                                <div class="action_section_inner flex justify-center">

                                    <div class="flex justify-center w-[100%]  m-auto  bg-[#FFF]  rounded-md" >
                                            <div class="w-full">
                                            <div class="flex justify-center -mx-1.5 flex-wrap bg-[#fffbe5]">

                                            <!-- 1st button -->
                                            <div class="flex 2xl:w-full 2xl:mb-3 w-1/2  px-1.5  justify-center" >
                                                <a href="javascript:void(0)"  class=" flex justify-center py-1 px-3
                                                w-full items-center  text-xs font-normal leading-4 border rounded
                                                    @if(strcmp($booked_car->status, 'delivered') == 0)
                                                        cursor-default
                                                        bg-[#EBEBE4] border-[#EBEBE4] text-[#AEAEAE]
                                                    @else
                                                        booking_action_btn                                                 border-siteYellow
                                                        transition-all duration-300 ease-in-out bg-siteYellow hover:bg-siteYellow400
                                                    @endif
                                                    "
                                                    data-booking-id="{{$booked_car->id}}"
                                                    @if(strcmp($booked_car->status, 'delivered') == 0)
                                                        disabled="disabled"
                                                    @else
                                                        data-booking-action="confirm_delivery"
                                                    @endif
                                                >
                                                    @if(strcmp($booked_car->status, 'delivered') == 0)
                                                        <img src="{{asset('images/blur_confirm_delivery.svg')}}" alt="clock" class="w-[20px] mr-2">
                                                    @else
                                                        <img src="{{asset('images/confirm_delivery.svg')}}" alt="CONFIRM DELIVERY" class="w-[20px] mr-2">
                                                    @endif
                                                CONFIRM DELIVERY
                                                </a>
                                            </div>

                                            <!-- 2nd button -->
                                            <div class="flex 2xl:w-full w-1/2  px-1.5 justify-center">
                                                <a href="javascript:void(0)"  class=" flex py-1 px-3
                                                justify-center w-full items-center text-xs font-normal leading-4  border rounded
                                                    @if(strcmp($booked_car->status, 'delivered') !== 0)
                                                        cursor-default
                                                        bg-[#EBEBE4] border-[#EBEBE4] text-[#AEAEAE]
                                                    @else
                                                        booking_action_btn
                                                        border-siteYellow
                                                        transition-all duration-300 ease-in-out bg-siteYellow hover:bg-siteYellow400
                                                    @endif
                                                "
                                                    data-booking-id="{{$booked_car->id}}"
                                                    @if(strcmp($booked_car->status, 'delivered') !== 0)
                                                        disabled="disabled"
                                                    @else
                                                        data-booking-action="confirm_collection"
                                                    @endif
                                                >
                                                    @if(strcmp($booked_car->status, 'delivered') !== 0)
                                                    <img src="{{asset('images/blur_carswithkey3.svg')}}" alt="clock" class="w-[20px] mr-2">
                                                @else
                                                    <img src="{{asset('images/carswithkey.svg')}}" alt="CONFIRM COLLECTION" class="w-[20px] mr-2">
                                                @endif

                                                CONFIRM COLLECTION</a>

                                            </div>


                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!--  -->

                            @endif

                        </div>
                        @endforeach
                    </div>

                        <!-- "Load More" button -->
                        @if(count($allBookedCars) > 5)
                            <div class="loadMoreBtnSec flex items-center justify-center py-4">
                                <a href="javascript:void(0);" id="loadMoreBtn" class="loadMoreBtn text-base font-normal leading-normal text-black border-b-2 border-siteYellow">Load More</a>
                            </div>
                        @endif
                    @else
                        <div class="empty_car_section_box bg-lightgray">
                            <div class="adj_empty_car_sec flex items-center justify-center  bg-white rounded-md">
                                There is no data to show
                            </div>
                        </div>
                    @endif
                </div>

            </div>
            @include('layouts.navigation')
        </div>
    </div>

<input type="hidden" class="stop_scroll" value="1">


<script>

$(document).ready(function() {
    // Function to hide loader and overlay
        $('#newBookingCta').on('click', function(e) {
            e.preventDefault();
            $('.loader').css("display", "inline-flex");
            $('.overlay_sections').css("display", "block");
            let calendarLink = this.getAttribute("href");
            window.location.href = calendarLink;
        });
});


function clearSearchSecond(){
    document.getElementById('search_listing').value = '';
    $('.cross_icon').hide();
    $('.search_icon').show();
}

// function clearSearch() {
//         document.getElementById('search_listing').value = '';
//         $('.cross_icon').hide();
//         $('.search_icon').show();
// }


    /////////////////////////////////////////////// LOAD MORE //////////////////////////////////////////////
    // Function to load more cars
    var offset = 5;  // Set the initial offset
    var loading = false;  // Flag to prevent multiple simultaneous requests
    function loadMoreCars(a)
    {

        // EXTRACT CUSTOMER & MOBILE
        var search_list = $('#search_listing').val().trim();

        if(search_list){
            // Regular expression to extract name and mobile number
            var regex = /([a-zA-Z ]+) \(\+(\d+) (\d+)\)/;
            var match = search_list.match(regex);

            if (match)
            {
                var customerName = match[1];
                var countryCode = match[2];
                var customerMobile = match[3];
            } else {
                console.log("No match found.");
            }
        }
        else{
            var customerName = $('.target_choice_checkBox.choice_checkBox').val();
            var customerMobile = $('.target_choice_checkBox.choice_checkBox').data('customer_mobile');
            var countryCode = $('.target_choice_checkBox.choice_checkBox').data('customer_country_code');
        }

        console.log("val of a is shown", a);

        if (loading) {
            return;  // If a request is already in progress, do nothing
        }

        loading = true;  // Set the loading flag to true

        var checkLoadMore = $('.get_loadmore_val').val();

        console.log(checkLoadMore);

        var form_data = {};  // Initialize an empty object for form data

        if (checkLoadMore === 'desktop_search') {
            form_data = $('#desktop_filters_form').serialize();
        }
        else if (checkLoadMore === 'mobile_search')
        {
            form_data = $('#mob_filters_form').serialize();
        }
         else if (checkLoadMore === 'autocomplete_search') {

            if ($('.desktop_start_date').val() || $('.desktop_end_date').val() ) {
                form_data = $('#desktop_filters_form').serialize();
            }
            else if ($('.mobile_fltr_start_date').val() || $('.mobile_fltr_end_date').val() ) {
                form_data = $('#mob_filters_form').serialize();
            }
        }

        $.ajax({
            url: '{{ route('partner.allLoadMoreBookings') }}',
            type: 'GET',
            data: {
                offset: offset,
                checkLoadMore: checkLoadMore,
                'form_data': form_data,
                'customerName':customerName,
                'customerMobile':customerMobile,
                'countryCode':countryCode
                },
            success: function (response) {
                console.log('response', response.booked_cars.length);

                if (response.booked_cars.length > 0) {
                    $(".loader").css("display", "inline-flex");
                    $(".overlay_sections").css("display", "block");

                    // Append the new cars to both desktop and mobile containers
                    $('.desktop_booking_list').append(response.desktopBookingHtml);
                    $('.mobile_booking_list').append(response.mobileBookingHtml);
                    // Increment the offset for the next request
                    offset += 5;
                } else {
                    // Disable the "Load More" button if no more cars are available
                    $('#loadMoreBtn').html('No More Data Available');
                    // Remove the click event
                    $('#loadMoreBtn').off('click');
                    $('#loadMoreBtn').css('cursor', 'default');
                    $('#loadMoreBtn').prop('disabled', true);
                }
            },
            error: function (error) {
                console.error('Error loading more cars:', error);
            },
            complete: function () {
                loading = false;  // Reset the loading flag

                // Hide the loader and overlay sections right before making the request
                $(".loader").css("display", "none");
                $(".overlay_sections").css("display", "none");
            }
        });
    }


    // Attach the loadMoreCars function to the "Load More" button click event
    $('#loadMoreBtn').click(function () {
        var a = $('.get_loadmore_val').val();
        loadMoreCars(a);
    });

    // After Searching Result, The Scroll Not Works
    $(window).scroll(function ()
    {
        if ((parseInt($('.stop_scroll').val())) > 0 )
        {
            // console.log('in loading');
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 1200)
            {
                var a = $('.get_loadmore_val').val();
                loadMoreCars(a);
            }
        }
    });

    ///////////////////////////////////////////////////  MOBILE //////////////////////////////////////////////////////

    // mobile filter
    $('#mob_fltr_apply_btn').on('click',function(e){

        var mobile_fltr_start_date = $('.mobile_fltr_start_date').val().trim();
        var mobile_fltr_end_date = $('.mobile_fltr_end_date').val().trim();

        // console.log('mobile_fltr_start_date',mobile_fltr_start_date);
        // console.log('mobile_fltr_end_date',mobile_fltr_end_date);

        if ((mobile_fltr_start_date !== '') || (mobile_fltr_end_date !== ''))
        {
            // EXTRACT CUSTOMER & MOBILE
            var search_list = $('#search_listing').val().trim();

            // Regular expression to extract name and mobile number
            var regex = /([a-zA-Z ]+) \(\+(\d+) (\d+)\)/;
            var match = search_list.match(regex);

            if (match)
            {
                var customerName = match[1];
                var countryCode = match[2];
                var customerMobile = match[3];
            } else {
                console.log("No match found.");
            }

            $('.get_loadmore_val').val("mobile_search");
            $('.get_loadmore_val').attr('value','mobile_search');

            // Reset the offset to 5
            offset = 5;

            var mobileForm = $('#mob_filters_form').serialize();

            console.log('mobile forms:',mobileForm);

            $('.loader').css("display","inline-flex");
            $('.overlay_sections').css("display","block");

            $.ajax({
            url:"{{route('partner.all.booking.list.filter')}}",
            type: 'POST',
            data:{
                    '_token':'{{ csrf_token() }}',
                    'form_data':mobileForm,
            },
            dataType: "json",
            success: function(data) {
                $('.loadMoreBtn').css('display','none');
                if(data.success){
                    // for stop scroll
                    parseInt($('.stop_scroll').val('0'));
                    $('.stop_scroll').attr('value','0');

                    console.log('data success',data);
                    if(data.desktopBookingHtml && data.mobileBookingHtml){
                        $('.desktop_booking_list').html('');
                        $('.mobile_booking_list').html('');
                        $('.desktop_booking_list').html(data.desktopBookingHtml);
                        $('.mobile_booking_list').html(data.mobileBookingHtml);
                    }
                }
                else{
                    $('.desktop_booking_list').html('<h5 class=" text-center capitalize">no data found...!! </h5>');
                    $('.mobile_booking_list').html('<h5 class=" text-center capitalize">no data found...!! </h5>');
                    // $('.main_booking_section_inner').html('<h5 class=" text-center capitalize">no data found...!! </h5>');
                }

                console.log(data.booked_cars.length);

                // when entries is greater than 4 then show load more
                if (data.booked_cars.length > 4 )
                {
                    // Disable the "Load More" button if no more cars are available
                    $('#loadMoreBtn').html('Load More');
                    // Remove the click event
                    $('#loadMoreBtn').on('click');
                    $('#loadMoreBtn').css('cursor', 'pointer');
                    $('#loadMoreBtn').prop('disabled', false);
                    console.log("load more");


                    console.log("greater than 4");
                    $('.loadMoreBtn').css('display','block');
                    parseInt($('.stop_scroll').val('1'));
                    $('.stop_scroll').attr('value','1');
                }

                // when entries is less than 4 then does'nt show load more
                if (data.booked_cars.length < 4 )
                {
                    $('.loadMoreBtn').css('display','none');
                    parseInt($('.stop_scroll').val('0'));
                    $('.stop_scroll').attr('value','0');
                }

                // when entries is less than 4 then does'nt print load more
                if (data.booked_cars.length < 4 )
                {
                    $('.loadMoreBtn').css('display','none');
                    parseInt($('.stop_scroll').val('0'));
                    $('.stop_scroll').attr('value','0');
                }


            },
            complete: function(data) {
                $('body').removeClass('mob_filter_popup');
                $(".loader").css("display", "none");
                $(".overlay_sections").css("display", "none");
            },
            error: function(xhr, status, error) {

            }
            });

        }
        else
        {
            Swal.fire({
            title: 'First select date, then press search button',
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

    // cancel
    $('.mobile_filter_Sec').on('click',function(e){
        $('body').addClass('mob_filter_popup');
    });

    $('.close_mob_filter,.mob_clearAll').on('click', function (e) {
            $('body').removeClass('mob_filter_popup');
    });

    // when click on customer in mobile view
    $('.choice_checkBox').on('click', function() {


        $('.loader').css("display", "inline-flex");
        $('.overlay_sections').css("display", "block");


        $('.get_loadmore_val').val("mobile_search");
        $('.get_loadmore_val').attr('value','mobile_search');

        var checkLoadMore = $('.get_loadmore_val').val();

        var form_data = {};  // Initialize an empty object for form data


            if ($('.desktop_start_date').val() || $('.desktop_end_date').val() ) {
                form_data = $('#desktop_filters_form').serialize();
            }
            else if ($('.mobile_fltr_start_date').val() || $('.mobile_fltr_end_date').val() ) {
                console.log("mobile filter");
                form_data = $('#mob_filters_form').serialize();
            }


        var customerName = $(this).val();
        var customerMobile = $(this).data('customer_mobile');
        var countryCode = $(this).data('customer_country_code');

        offset = 5;

        $(this).addClass('target_choice_checkBox');

        $.ajax({
                url: "{{ route('partner.allSearchCustomerAndMob') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'form_data': form_data,
                    'customerName':customerName,
                    'customerMobile':customerMobile,
                    'countryCode':countryCode
                },
                success: function (data) {

                    // $('.loadMoreBtn').css('display','none');
                    if (data.success) {
                            // for stop scroll
                            // parseInt($('.stop_scroll').val('0'));
                            // $('.stop_scroll').attr('value','0');

                        if (data.desktopBookingHtml && data.mobileBookingHtml) {
                            $('.desktop_booking_list').html(data.desktopBookingHtml);
                            $('.mobile_booking_list').html(data.mobileBookingHtml);
                        }
                    } else {
                        $('.desktop_booking_list').html('<h5 class=" text-center capitalize">no data found...!! </h5>');
                        $('.mobile_booking_list').html('<h5 class=" text-center capitalize">no data found...!! </h5>');
                        // $('.main_booking_section_inner').html('<h5 class=" text-center capitalize">no data found...!! </h5>');
                    }

                    // when entries is greater than 4 then show load more
                    if (data.booked_cars.length > 4 )
                    {

                        // Disable the "Load More" button if no more cars are available
                        $('#loadMoreBtn').html('Load More');
                        // Remove the click event
                        $('#loadMoreBtn').on('click');
                        $('#loadMoreBtn').css('cursor', 'pointer');
                        $('#loadMoreBtn').prop('disabled', false);




                        $('.loadMoreBtn').css('display','block');
                        parseInt($('.stop_scroll').val('1'));
                        $('.stop_scroll').attr('value','1');
                    }

                    // when entries is less than 4 then does'nt show load more
                    if (data.booked_cars.length < 4 )
                    {

                        $('.loadMoreBtn').css('display','none');
                        parseInt($('.stop_scroll').val('0'));
                        $('.stop_scroll').attr('value','0');
                    }





                },
                complete: function(data) {
                    $('body').removeClass('mob_filter_popup');
                    $(".loader").css("display", "none");
                    $(".overlay_sections").css("display", "none");
                },
                error: function (xhr, textStatus, errorThrown) {
                    console.error("Error:", errorThrown);
                }
        });
    });

/////////////////////////////////////////  DESKTOP  /////////////////////////////////////////

    // Desktop Filter
    $('#desktop_fltr_apply_btn').on('click',function(e)
    {
        var desktop_start_date = $('.desktop_start_date').val().trim();
        var desktop_end_date = $('.desktop_end_date').val().trim();

        if ((desktop_start_date !== '') || (desktop_end_date !== ''))
        {
            // EXTRACT CUSTOMER & MOBILE
            var search_list = $('#search_listing').val().trim();

            // Regular expression to extract name and mobile number
            var regex = /([a-zA-Z ]+) \(\+(\d+) (\d+)\)/;
            var match = search_list.match(regex);

            if (match)
            {
                var customerName = match[1];
                var countryCode = match[2];
                var customerMobile = match[3];
            } else {
                console.log("No match found.");
            }

            $('.get_loadmore_val').val("desktop_search");
            $('.get_loadmore_val').attr('value','desktop_search');

            // Reset the offset to 5
            offset = 5;

            // Both start and end dates are empty, so don't make the AJAX request
            var desktopForm = $('#desktop_filters_form').serialize();

            console.log('desktopForm', desktopForm);

            $('.loader').css("display","inline-flex");
            $('.overlay_sections').css("display","block");

            $.ajax({
            url:"{{route('partner.all.booking.list.filter')}}",
            type: 'POST',
            data:{
                '_token':'{{ csrf_token() }}',
                'form_data':desktopForm,
                'customerName':customerName,
                'customerMobile':customerMobile,
                'countryCode':countryCode
            },
            dataType: "json",
            success: function(data)
            {
                // $('.loadMoreBtn').css('display','none');

                // parseInt($('.stop_scroll').val('0'));
                // $('.stop_scroll').attr('value','0');

                if(data.success)
                {
                    console.log('data success',data);

                    if(data.desktopBookingHtml && data.mobileBookingHtml){
                        $('.desktop_booking_list').html('');
                        $('.mobile_booking_list').html('');
                        $('.desktop_booking_list').html(data.desktopBookingHtml);
                        $('.mobile_booking_list').html(data.mobileBookingHtml);
                    }
                }
                else
                {

                    $('.desktop_booking_list').html('<h5 class=" text-center capitalize">no data found...!! </h5>');
                    $('.mobile_booking_list').html('<h5 class=" text-center capitalize">no data found...!! </h5>');
                    // $('.main_booking_section_inner').html('<h5 class=" text-center capitalize">no data found...!! </h5>');
                }

                // console.log(data.booked_cars.length);

                // when entries is greater than 4 then show load more
                if (data.booked_cars.length > 4 )
                {
                    // Disable the "Load More" button if no more cars are available
                    $('#loadMoreBtn').html('Load More');
                    // Remove the click event
                    $('#loadMoreBtn').on('click');
                    $('#loadMoreBtn').css('cursor', 'pointer');
                    $('#loadMoreBtn').prop('disabled', false);
                    console.log("load more");


                    console.log("greater than 4");
                    $('.loadMoreBtn').css('display','block');
                    parseInt($('.stop_scroll').val('1'));
                    $('.stop_scroll').attr('value','1');
                }

                // when entries is less than 4 then does'nt show load more
                if (data.booked_cars.length < 4 )
                {
                    $('.loadMoreBtn').css('display','none');
                    parseInt($('.stop_scroll').val('0'));
                    $('.stop_scroll').attr('value','0');
                }

            },
            complete: function(data) {
                    $('body').removeClass('mob_filter_popup');
                    $(".loader").css("display", "none");
                    $(".overlay_sections").css("display", "none");
            },
            error: function(xhr, status, error) {

            }
          });
        }
        else
        {
            Swal.fire({
                title: 'First select date, then press search button',
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

    // Autocomplete in Desktop
    $("#search_listing").autocomplete({
        source: function (request, response) {

            $.ajax({
                url: '{{ route('partner.allAutocompleteCustomerAndMob') }}',
                type: 'GET',
                dataType: "json",
                data: {
                    search: request.term
                },
                success: function (data) {
                    if (data.length === 0) {
                        // e.preventDefault();
                        response([{ label: "No Data Found!!", value: null }]);
                    } else {
                        // Filter out duplicates based on customer_mobile
                        var uniqueData = {};
                        data.forEach(function(item) {
                            var key = item.customer_mobile;
                            if (!uniqueData[key]) {
                                uniqueData[key] = item;
                            }
                        });
                        var formattedData = Object.values(uniqueData).map(function (item) {
                            return {
                                label: item.customer_name + ' '+'('+ item.customer_mobile_country_code +' '+ item.customer_mobile + ')',
                                customerName: item.customer_name,
                                customerMobile : item.customer_mobile,
                                value: item.customer_name,
                                id: item.id,
                                countryCode: item.customer_mobile_country_code
                            };
                        });
                        response(formattedData);
                    }

                }
            });
        },
        select: function (event, ui) {

            var checkMobOrDesktop = $('.get_loadmore_val').val();
            console.log(checkMobOrDesktop);

            var form_data = {};  // Initialize an empty object for form data

            if ($('.desktop_start_date').val() || $('.desktop_end_date').val() ) {
                form_data = $('#desktop_filters_form').serialize();
            }
            else if ($('.mobile_start_date').val() || $('.mobile_end_date').val() ) {
                form_data = $('#mob_filters_form').serialize();
            }

            $('.get_loadmore_val').val("autocomplete_search");
            $('.get_loadmore_val').attr('value','autocomplete_search');

            // Reset the offset to 5
            offset = 5;

            // parseInt($('.stop_scroll').val('0'));
            // $('.stop_scroll').attr('value','0');

            if (ui.item.label) {
                $('.loader').css("display", "inline-flex");
                $('.overlay_sections').css("display", "block");

                $('#search_listing').val(ui.item.label);

                console.log('ui.customer_mobile_country_code',ui.item.countryCode);

                $.ajax({
                    url: "{{ route('partner.allSearchCustomerAndMob') }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'form_data': form_data,
                        'id': ui.item.id,
                        'customerName': ui.item.customerName,
                        'customerMobile': ui.item.customerMobile,
                        'countryCode': ui.item.countryCode
                    },
                    success: function (data) {

                        // $('.get_loadmore_val').val("autocomplete_search");
                        // $('.get_loadmore_val').attr('value','autocomplete_search');

                        // // Reset the offset to 5
                        // offset = 5;

                        $('.loadMoreBtn').css('display','block');
                        if (data.success) {

                            if (data.desktopBookingHtml && data.mobileBookingHtml) {
                                $('.desktop_booking_list').html(data.desktopBookingHtml);
                                $('.mobile_booking_list').html(data.mobileBookingHtml);
                            }

                        } else {
                            $('.desktop_booking_list').html('<h5 class=" text-center capitalize">no data found...!! </h5>');
                            $('.mobile_booking_list').html('<h5 class=" text-center capitalize">no data found...!! </h5>');
                            // $('.main_booking_section_inner').html('<h5 class=" text-center capitalize">No Data Found... !!</h5>');
                        }

                        // when entries is greater than 4 then show load more
                        if (data.booked_cars.length > 4 )
                        {
                            // Disable the "Load More" button if no more cars are available
                            $('#loadMoreBtn').html('Load More');
                            // Remove the click event
                            $('#loadMoreBtn').on('click');
                            $('#loadMoreBtn').css('cursor', 'pointer');
                            $('#loadMoreBtn').prop('disabled', false);
                            console.log("load more");


                            console.log("greater than 4");
                            $('.loadMoreBtn').css('display','block');
                            parseInt($('.stop_scroll').val('1'));
                            $('.stop_scroll').attr('value','1');
                        }

                        // when entries is less than 4 then does'nt show load more
                        if (data.booked_cars.length < 4 )
                        {
                            $('.loadMoreBtn').css('display','none');
                            parseInt($('.stop_scroll').val('0'));
                            $('.stop_scroll').attr('value','0');
                        }


                    },
                    complete: function(data) {
                        $(".loader").css("display", "none");
                        $(".overlay_sections").css("display", "none");
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        console.error("Error:", errorThrown);
                    }
                });
            }
            return false;
        },
        create: function() {
            $(this).data("ui-autocomplete")._renderItem = function(ul, item) {
                if ((item.label)) {
                    var li = $("<li>")
                        .append('<span class="customer_name_mob text-sm font-medium leading-none text-[#272522]">' + item.label + '</span>')
                        .appendTo(ul);

                    // Disable the "No Data Found!!" option
                    if (item.label === "No Data Found!!") {
                        li.addClass("ui-state-disabled");
                    }

                    return li;
                }
            };
        },
        response: function (event, ui)
        {
            if (ui.content.length === 1 && ui.content[0].label === "No Data Found!!") {
                // Handle the case where no data is found
            }
        },
        appendTo: ".ul_class",
        open: function (event, ui)
        {
            var $ul = $(this).autocomplete("widget");
            $ul.addClass("ul_class");
        }
    }).bind('focus', function(){ $(this).autocomplete("search"); });


    // Autocomplete Input cross and search
    $(document).ready(function() {
        $("#search_listing").keyup(function() {
            var inputVal = $(this).val().trim();
            if (inputVal !== '') {
                $('.cross_icon').show();
                $('.search_icon').hide();
            } else {
                $('.cross_icon').hide();
                $('.search_icon').show();
            }
        });

        $("#search_listing").focus(function() {
            $('.cross_icon').show();
            $('.search_icon').hide();
        });

        $("#search_listing").blur(function() {
            var inputVal = $(this).val().trim();
            if (inputVal === '') {
                $('.cross_icon').hide();
                $('.search_icon').show();
            }
        });
    });

    // Drop Down Filter
    $(".dropdown__drop_fillter_btn1 ").click(function (e) {
            e.stopPropagation();
            // $(".drop_down_fillter_1").slideToggle();
            $(".drop_down_fillter_2").slideUp();
    });

    $(".dropdown__drop_fillter_btn2 ").click(function (e) {
        e.stopPropagation();
        $(".drop_down_fillter_2").slideToggle();
        $(".drop_down_fillter_1").slideUp();
    });

    $("body").click(function (e) {
        $(".drop_down_fillter_1").slideUp();
        $(".drop_down_fillter_2").slideUp();
    });

    $(".drop_down_fillter_1").click(function (e) {
        e.stopPropagation();
    });

    $(".drop_down_fillter_2").click(function (e) {
        e.stopPropagation();
    });

    /////////////////////////////////////////  FOR POPUP REMOVE  /////////////////////////////////////////
    $(window).resize(function () {
        if($('body').hasClass('mob_filter_popup') ){
            let flag=true;
            if($(window).width()>992){
                $('body').removeClass('mob_filter_popup');
            }
            else
            {
                if(flag){
                    $('body').addClass('mob_filter_popup');
                }
            }
        }
    });

    if(window.matchMedia("(min-width: 992px)").matches)
    {
        if($('body').hasClass('mob_filter_popup')){
            $('body').removeClass('mob_filter_popup');
        }
    }

    if (window.matchMedia("(max-width: 992px)").matches)
    {
        function popupScrollFn() {
            let filter_height = 164;
            let filter_outer_sec_height = $(window).height();
            let form_outer_sec_height = $(window).height();
            filter_outer_sec_height = filter_outer_sec_height - filter_height;
            $('.client_table_sec ').css('height', form_outer_sec_height + 'px');
            $('.filter_height_sec ').css('height', form_outer_sec_height + 'px');
        }
        popupScrollFn();
        $(window).on('resize', function () {
            popupScrollFn();
        });
    }

    // Mobile filters
    $(document).ready(function() {
        var disabledDays = [0, 0];
        var mob_datePicker = new AirDatepicker('.mobile_datepicker', {
            language: 'en',
            autoClose: true,
            range: true,
        //  minDate: new Date(),
            onRenderCell: function(date, cellType) {
                // You can add rendering logic here if needed
            },
            onSelect: function(date, obt) {
                console.log('select date:', date.formattedDate[0]);

                if (date.formattedDate && date.formattedDate.length > 0) {
                    var $objDate = formatDate(date.formattedDate[0]);
                    console.log('start-------', $objDate);
                    $('.mobile_fltr_start_date').val($objDate);
                } else {
                    $('.mobile_fltr_start_date').val('');
                }

                if (date.formattedDate && date.formattedDate.length > 1) {
                    var $objEnd = formatDate(date.formattedDate[1]);
                    console.log('end------', $objEnd);
                    $('.mobile_fltr_end_date').val($objEnd);
                } else {
                    $('.mobile_fltr_end_date').val('');
                }
            }
        });

        $('.reset_mob_date, .reset_all_link').on('click',function(e){
            mob_datePicker.clear();
            $('.mobile_fltr_start_date').val('');
            $('.mobile_fltr_end_date').val(''); // Corrected this line
        });

        function formatDate(dateString) {
            if (dateString) {
                var parts = dateString.split('.');
                if (parts.length === 3) {
                    var d = new Date(parts[2], parts[1] - 1, parts[0]);
                    if (!isNaN(d.getTime())) {
                        var month = ('0' + (d.getMonth() + 1)).slice(-2);
                        var day = ('0' + d.getDate()).slice(-2);
                        var year = d.getFullYear();
                        return [year, month, day].join('-');
                    }
                }
            }
            return '';
        }
    });

    // desktop filters
    $(document).ready(function() {
        var disabledDays = [0, 0];
        var desk_datePicker = new AirDatepicker('.daterange', {
            language: 'en',
            autoClose: true,
            range: true,
        //  minDate: new Date(),
            onRenderCell: function(date, cellType) {
            // You can add rendering logic here if needed
            },
            onSelect: function(date, obt) {
                console.log('select date:', date.formattedDate[0]);

                if (date.formattedDate && date.formattedDate.length > 0) {
                    var $objDate = formatDate(date.formattedDate[0]);
                    console.log('start-------', $objDate);
                    $('.desktop_start_date').val($objDate);
                    $('.dropdown__drop_fillter_btn2 ').addClass('bg-[#F9F1CB]');
                    $('.dropdown__drop_fillter_btn2 ').removeClass('date_range_block');
                    $('.dropdown__drop_fillter_btn2 ').find('.cross_icon_date').show();
                    $('.dropdown_listing ').html("");
                    $('.dropdown_listing').append('<span class="relative text-sm font-normal leading-4 text-black ">' + $objDate + '</span>');
                    $('.drop_down_fillter_2_action_sec').show();

                } else {
                    $('.desktop_start_date').val('');
                    $('.dropdown__drop_fillter_btn2 ').removeClass('bg-[#F9F1CB]');
                    $('.dropdown__drop_fillter_btn2 ').addClass('date_range_block');
                    $('.dropdown__drop_fillter_btn2 ').find('.cross_icon_date').hide();
                    $('.dropdown_listing ').html("Date Range");
                    $('.drop_down_fillter_2_action_sec').hide();
                }

                if (date.formattedDate && date.formattedDate.length > 1) {
                    var $objEnd = formatDate(date.formattedDate[1]);
                    console.log('end------', $objEnd);
                    $('.desktop_end_date').val($objEnd);
                    $('.dropdown_listing').append('<span class="relative text-sm font-normal leading-4 text-black">' + ' — ' + $objEnd + '</span>');

                } else {
                    $('.desktop_end_date').val('');
                }
            }
        });



        $('.desktop_clear_btn').on('click',function(e){
            desk_datePicker.clear();
            $('.desktop_start_date').val('');
            $('.desktop_end_date').val('');
        });

        function formatDate(dateString) {
            if (dateString) {
                var parts = dateString.split('.');
                if (parts.length === 3) {
                    var d = new Date(parts[2], parts[1] - 1, parts[0]);
                    if (!isNaN(d.getTime())) {
                        var month = ('0' + (d.getMonth() + 1)).slice(-2);
                        var day = ('0' + d.getDate()).slice(-2);
                        var year = d.getFullYear();
                        return [year, month, day].join('-');
                    }
                }
            }
            return '';
        }
        $('.cross_icon_date').on('click',function(e){
             desk_datePicker.clear();
            $('.desktop_end_date').val('');
            $('.desktop_start_date').val('');
     });

    });

    $('.cross_icon_date').on('click',function(e){
            $('.dropdown__drop_fillter_btn2').find('.cross_icon_date').hide();
            $('.dropdown__drop_fillter_btn2').addClass('date_range_block');
            $('.dropdown__drop_fillter_btn2').removeClass('bg-[#F9F1CB]');
            $('.desktop_end_date').val('');
            $('.desktop_start_date').val('');
    });

    function formatDate(date) {
        var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();
        if (month.length < 2) month='0' + month; if (day.length < 2) day='0' + day; return [year, month, day].join('-');
    }


    ///////////////////////////////////////// filter tab toggling starts here  /////////////////////////////////////////
    $('.mobile_filter_box').hide();
    $('.mobile_filter_box:first').css('display', 'block');
    $('.filter_tab_sec ul li').on('click', function () {
            $('.filter_tab_sec ul li').removeClass('active');
            $(this).addClass('active');
            $('.mobile_filter_box').hide();
            var activeTab = $(this).find('a').attr('href');
            $(activeTab).fadeIn();
            return false;
    });

    $(".filter_search_inp2").on("keyup", function() {
        let value = $(this).val().toLowerCase();
        let items = $(".mob_list_items li");
        let filteredItems = items.filter(function() {
        return $(this).text().toLowerCase().indexOf(value) > -1;
        });
        items.hide();
        filteredItems.show();
        if (filteredItems.length === 0) {
        $("#customer_search_no_data_found").css('display','flex');
        } else {
        $("#customer_search_no_data_found").hide();
        }
    });

        $('.reset_btn,.desktop_clear_btn,.reset_all_link').on('click',function(e){
            e.preventDefault();
            var filtersForm = document.getElementById("mob_filters_form");
            var desktop_filters_form = document.getElementById("desktop_filters_form");
            filtersForm.reset();
            desktop_filters_form.reset();
            $('.mob_list_items li').css('display','block');
            $("#customer_search_no_data_found").hide();
        });

    ///////////////////////////////

    $('.clearAll,.mob_clearAll').on('click', function(e)
    {
        clearSearchSecond();

        // Show loader and overlay
        $('.loader').css("display", "inline-flex");
        $('.overlay_sections').css("display", "block");

        // Clear search


        // Perform AJAX request to clear all
        $.ajax({
            url: "{{ route('partner.allClearAll') }}",
            type: 'GET',
            dataType: "json",
            success: function(data) {
                if (data.success) {

                    parseInt($('.stop_scroll').val('1'));
                    $('.stop_scroll').attr('value','1');

                    $('.loadMoreBtn').css('display','block');

                    // Clear existing content in lists
                    $('.desktop_booking_list').html('');
                    $('.mobile_booking_list').html('');

                    // Append new content if available
                    if (data.desktopBookingHtml && data.mobileBookingHtml) {
                        $('.desktop_booking_list').html(data.desktopBookingHtml);
                        $('.mobile_booking_list').html(data.mobileBookingHtml);
                    }

                    // Reset the offset to its initial value
                    offset = 5;

                    // Enable the "Load More" button
                    $('#loadMoreBtn').html('Load More');
                    $('#loadMoreBtn').on('click', loadMoreCars);
                    $('#loadMoreBtn').css('cursor', 'pointer');
                    $('#loadMoreBtn').prop('disabled', false);
                } else {
                    // Handle case where no data is found
                    $('.desktop_booking_list').html('<h5 class=" text-center capitalize">no data found...!! </h5>');
                    $('.mobile_booking_list').html('<h5 class=" text-center capitalize">no data found...!! </h5>');
                    // $('.main_booking_section_inner').html('<h5 class="text-center capitalize">no data found...!! </h5>');
                }
            },
            complete: function() {
                // Hide loader and overlay
                $(".loader").css("display", "none");
                $(".overlay_sections").css("display", "none");
                // Reset the offset and trigger the loading of more cars
                loadMoreCars();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });

    });


    $('body').on('click', '.booking_action_btn', function(e) {

        // EXTRACT CUSTOMER & MOBILE
        var search_list = $('#search_listing').val().trim();
        var regex = /([a-zA-Z ]+) \(\+(\d+) (\d+)\)/;
        var match = search_list.match(regex);
        if (match)
        {
            var customerName = match[1];
            var countryCode = match[2];
            var customerMobile = match[3];
        } else {
            console.log("No match found.");
        }

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


        var checkStatus = $('.get_loadmore_val').val();

        var form_data = {};  // Initialize an empty object for form data

        if (checkStatus === 'desktop_search') {
            form_data = $('#desktop_filters_form').serialize();
        } else if (checkStatus === 'mobile_search') {
            form_data = $('#mob_filters_form').serialize();
        }

        Swal.fire({
        title: 'Are you sure want to update status?',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'YES, UPDATE IT!',
        cancelButtonText: 'NO, CANCEL'
        }).then((result) => {
        if(result.isConfirmed){

            $('.loader').css("display", "inline-flex");
            $('.overlay_sections').css("display", "block");
            console.log("spinner1");

            $.ajax({
                url:"{{ route('partner.all.change.booking.status')}}",
                type: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'action': action,
                    'bookingId': bookingId,
                    'bookingStatus':bookingStatus,
                    'bookingLength':bookingLength,
                    'checkStatus':checkStatus,
                    'form_data': form_data,
                    'customerName':customerName,
                    'customerMobile':customerMobile,
                    'countryCode':countryCode

                },
                dataType: "json",
                success: function (data)
                {
                    // if (data.success) {
                    //    alert('updated');

                    // }

                    if (data.success)
                    {
                        // console.log("testttttt");

                        parseInt($('.stop_scroll').val('1'));
                        $('.stop_scroll').attr('value','1');

                        $('.loadMoreBtn').css('display','block');

                        // Clear existing content in lists
                        $('.desktop_booking_list').html('');
                        $('.mobile_booking_list').html('');

                        // Append new content if available
                        if (data.desktopBookingHtml && data.mobileBookingHtml) {
                            $('.desktop_booking_list').html(data.desktopBookingHtml);
                            $('.mobile_booking_list').html(data.mobileBookingHtml);
                        }

                        // Reset the offset to its initial value
                        offset = bookingLength;

                        // Enable the "Load More" button
                        $('#loadMoreBtn').html('Load More');
                        $('#loadMoreBtn').on('click', loadMoreCars);
                        $('#loadMoreBtn').css('cursor', 'pointer');
                        $('#loadMoreBtn').prop('disabled', false);

                        // console.log(data.successMsg);
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

                    }
                    else {
                        // Handle case where no data is found
                        $('.desktop_booking_list').html('<h5 class=" text-center capitalize">no data found...!! </h5>');
                        $('.mobile_booking_list').html('<h5 class=" text-center capitalize">no data found...!! </h5>');
                        // $('.main_booking_section_inner').html('<h5 class="text-center capitalize">no data found...!! </h5>');
                    }


                    // when entries is greater than 4 then show load more
                    if (data.booked_cars.length > 4 )
                    {
                        // Disable the "Load More" button if no more cars are available
                        $('#loadMoreBtn').html('Load More');
                        // Remove the click event
                        $('#loadMoreBtn').on('click');
                        $('#loadMoreBtn').css('cursor', 'pointer');
                        $('#loadMoreBtn').prop('disabled', false);
                        console.log("load more");


                        console.log("greater than 4");
                        $('.loadMoreBtn').css('display','block');
                        parseInt($('.stop_scroll').val('1'));
                        $('.stop_scroll').attr('value','1');
                    }

                    // when entries is less than 4 then does'nt show load more
                    if (data.booked_cars.length < 4 )
                    {
                        $('.loadMoreBtn').css('display','none');
                        parseInt($('.stop_scroll').val('0'));
                        $('.stop_scroll').attr('value','0');
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


// for search by Customer Name and Mobile
$('body').on('click', '.search_booking_action_btn', function(e) {

    e.preventDefault();
    var booking_action = $(this).data('booking-action');

    var bookingLength =  $('.bookingLength').length;

    console.log('bookingLength:',bookingLength);
    var action;
    var bookingStatus;
    var bookingId = $(this).data('booking-id');

    var bookingIds = [];
    $('.booking_car_list_box').each(function() {
        bookingIds.push($(this).data('id'));
    });

    console.log(bookingId);

    console.log("bookingIds",bookingIds);

    if (booking_action == 'confirm_delivery') {
        action = booking_action;
        bookingStatus = 'delivered';
    }
    if (booking_action == 'confirm_collection') {
        action = booking_action;
        bookingStatus = 'collected';
    }

    // console.log(action);

    Swal.fire({
    title: 'Are you sure want to update status?',
    icon: 'info',
    showCancelButton: true,
    confirmButtonText: 'YES, UPDATE IT!',
    cancelButtonText: 'NO, CANCEL'
    }).then((result) => {
    if(result.isConfirmed){

        $('.loader').css("display", "inline-flex");
        $('.overlay_sections').css("display", "block");
        console.log("spinner1");

        $.ajax({
            url:"{{ route('partner.all.search.change.booking.status')}}",
            type: 'POST',
            data: {
                '_token': '{{ csrf_token() }}',
                'action': action,
                'bookingId': bookingId,
                'bookingStatus':bookingStatus,
                'bookingLength':bookingLength,
                'bookingIds':bookingIds
            },
            dataType: "json",
            success: function (data)
            {
                // if (data.success) {
                //    alert('updated');

                // }

                // $('.loadMoreBtn').css('display','none');

                if (data.success)
                {
                    console.log("testing");

                    // parseInt($('.stop_scroll').val('1'));
                    // $('.stop_scroll').attr('value','1');

                    // $('.loadMoreBtn').css('display','block');

                    // Clear existing content in lists
                    $('.desktop_booking_list').html('');
                    $('.mobile_booking_list').html('');

                    // Append new content if available
                    if (data.desktopBookingHtml && data.mobileBookingHtml) {
                        $('.desktop_booking_list').html(data.desktopBookingHtml);
                        $('.mobile_booking_list').html(data.mobileBookingHtml);
                    }

                    // // Reset the offset to its initial value
                    // offset = bookingLength;

                    // // Enable the "Load More" button
                    // $('#loadMoreBtn').html('Load More');
                    // $('#loadMoreBtn').on('click', loadMoreCars);
                    // $('#loadMoreBtn').css('cursor', 'pointer');
                    // $('#loadMoreBtn').prop('disabled', false);

                    console.log(data.successMsg);

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


                }
                else {
                    // Handle case where no data is found
                    $('.desktop_booking_list').html('<h5 class=" text-center capitalize">no data found...!! </h5>');
                    $('.mobile_booking_list').html('<h5 class=" text-center capitalize">no data found...!! </h5>');
                    // $('.main_booking_section_inner').html('<h5 class="text-center capitalize">no data found...!! </h5>');
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

$('.drop_down_fillter_2_done_btn , .drop_down_fillter_2_cancel_btn').on('click',function(){

    $(".drop_down_fillter_2").slideUp();

});

</script>
@endsection
