@extends('layouts.partner')
@section('title', 'Create booking - by date')
@section('content')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.32/moment-timezone.min.js"></script>

@php
$uniqueCarTypes = $allCarsFilter->unique('car_type')->pluck('car_type');
$uniqueCarNames =$allCarsFilter->unique('name')->pluck('name');
$uniqueTransmissions =$allCarsFilter->unique('transmission')->pluck('transmission');
@endphp

    @if (Session::has('warning'))
    <script>
        Swal.fire({
            title: "{{ Session::get('warning') }}",
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
            }
        });
    </script>
    @endif

@php
    $bookedArr = [];
@endphp

@foreach ($cars as $car)

    @foreach($dates as $date)
        @php
            $fullDate = $date['full_date'];

            if(Helper::isBooked($car->id,$date['full_date']))
            {

                $additionalBookingData = Helper::getAdditionalBookingsByCarId($car->id, $fullDate);
                $bookingData = Helper::getBookingDataByCarId($car->id, $fullDate);
                $key = $fullDate;
                $carId=$car->id;
                $value = [
                    'additional' => [
                        'dropoff_date' => $additionalBookingData->dropoff_date,
                        'pickup_date' => $additionalBookingData->pickup_date,
                        'pickup_time' => $additionalBookingData->pickup_time,
                        'dropoff_time' => $additionalBookingData->dropoff_time,
                        'customer_name' => $additionalBookingData->customer_name,
                        'customer_mobile_country_code' => $additionalBookingData->customer_mobile_country_code,
                        'customer_mobile' => $additionalBookingData->customer_mobile,
                        'pickup_location' => $additionalBookingData->pickup_location,
                        'dropoff_location' => $additionalBookingData->dropoff_location,
                        'bookingId' => $additionalBookingData->bookingId,
                        'id'=>$additionalBookingData->id,
                         'booking_owner_id'=>$additionalBookingData->booking_owner_id
                    ],
                    'booking' => [
                        'bookingId' => $bookingData->bookingId,
                        'pickup_date' => $bookingData->pickup_date,
                        'dropoff_date' => $bookingData->dropoff_date,
                        'customer_name' => $bookingData->customer_name,
                        'customer_mobile_country_code' => $bookingData->customer_mobile_country_code,
                        'customer_mobile' => $bookingData->customer_mobile,
                        'pickup_location' => $bookingData->pickup_location,
                        'dropoff_location' => $bookingData->dropoff_location,
                        'pickup_time' => $bookingData->pickup_time,
                        'dropoff_time' => $bookingData->dropoff_time,
                        'id'=>$bookingData->id,
                        'booking_owner_id'=>$bookingData->booking_owner_id
                    ]
                ];
                $bookedArr[$key][$car->id] = $value;
            }
            // else if(Helper::isLocked($car->id,$date['full_date'])){
            //     $lockedData= Helper::getlockedDataByCarId($car->id,$fullDate);
            //     'locked' => [
            //           'locked_start_date' => $lockedData->start_date,
            //          'locked_end_date'=>$lockedData->end_date]
            // }
        @endphp
    @endforeach

@endforeach

<style>
/* fancybox */
.required,.error{color:#ff0000;}
.bgYellow{background-color:#f3cd0e !important;}
.bgGreen{background-color:#25BE00 !important;}
#booking_Details_showcase_popup button.fancybox-button.fancybox-close-small{ top:12px; }
.adj_empty_car_sec{ height: calc(100vh - 265px) }
/* for booking details popup */
#booking_Details_showcase_popup.fancybox-content{ padding: 0; }
.daterange .air-datepicker{ border: none !important; }
.drop_down_fillter_1{ border: 1px solid #d7d7d7; }
/* .cell_dates_day:hover .date_day{ transition: all .4s; visibility: visible; } */
.header_bar{display:none;}
.right_dashboard_section > .right_dashboard_section_inner {padding-top: 0px;}
/* correct ul li structure */
ul.list.selectable{display: flex;}
li.inline-block.adj_margin.clickable{display: flex; align-content: center; justify-content: center;}
ul.custom_calender_dates_container{display: flex;}
.adj_status_width{padding: 0 0px;}
.car_status_main_content {padding: 10px 10px 19px 0;}
.toastify{background: #ff0000;}
/* sticky styling */
.sticky_header { position: fixed; /* left: 0; */ /* right: 0; */ top: 0px; -webkit-transition: all .5s ease; -moz-transition: all .5s ease; transition: all .5s ease; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.19); }
.desktp_min_height{ min-height: 100vh; }
.adj_empty_car_sec { height: calc(100vh - 250px); }

.dates_sec_sticky.scrollActive{top:60px;}
@media screen and (max-width: 992px){
.desktp_min_height{ min-height: calc(100vh - 125px); }
.dates_sections::after{ content: ''; position: absolute; right: -10px; top: 0px; bottom: 0; width: 11px; background-color: white; height: calc(100% + 0px); display: block; }
.main_header_container{display:none;}
}
@media screen and (max-width: 767px){
.right_dashboard_section_inner {padding:20px 0;}
.adj_margin { min-width: 62px; max-width: 62px; width: 62px; margin: 0 0px; }
.toastify{ max-width: calc(100% - 20px)}
}

/*  */
</style>

<!-- mobile header -->
<div class="lg:flex hidden lg:bg-white lg:mb-[0px] lg:px-[17px] lg:py-[15px] mb-[20px] justify-start items-center">
    <div class="flex items-center justify-start w-full bg-white">
        <a href="{{route('partner.booking.list')}}" class=" links_item_cta inline-block p-2 mr-2 border border-[#C8C0C0] rounded-md
        hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
            <img class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
        </a>
        <span
            class="inline-block text-xl font-normal leading-normal align-middle text-black800">Create New Booking
        </span>
    </div>
</div>

<input type="hidden" class="start_time_val">
<input type="hidden" class="end_time_val">

<div class="right_dashboard_section_inner py-9 px-9  lg:py-[20px] lg:px-[17px] bg-textGray400  desktp_min_height">

    <div class="lg:hidden flex items-center mb-[36px] lg:mb-[20px] justify-between">
        <div>
            <div class="back-button">
                <a href="{{route('partner.booking.list')}}" class="links_item_cta inline-flex py-1 px-2 border border-[#C8C0C0] rounded-md bg-[#fff] hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
                    <img  class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
                    <span class="inline-block text-base leading-normal align-middle text-black800 ml-[10px] font-medium">
                        BOOKINGS
                    </span>
                </a>
            </div>
            <div class="flex justify-between items-center">
                <span class="inline-block text-[26px] font-normal leading-normal align-middle text-black800">Create New Booking</span>
            </div>
        </div>

        <div class="w-1/2 text-right">
            <a href="{{route('partner.external.booking.add')}}"
                class=" links_item_cta inline-block hover:bg-siteYellow400 transition-all duration-300 ease-out rounded-[4px] text-black text-base border border-siteYellow md:text-sm font-normal  bg-siteYellow px-[20px] py-2.5 md:px-[15px]

                "> EXTERNAL BOOKING
            </a>
        </div>
    </div>

    {{-- mobile external booking --}}
    <div class="w-1/2 text-right hidden lg:block">
        <a href="{{route('partner.external.booking.add')}}"
            class="links_item_cta inline-flex rounded-[4px] items-center uppercase text-black text-base md:text-sm font-normal border border-siteYellow bg-siteYellow px-[20px] py-2.5 md:px-[15px] hover:bg-siteYellow400 lg:fixed sm:bottom-[80px] lg:bottom-[80px] z-10 lg:right-[18px] "> EXTERNAL BOOKING
        </a>
    </div>


    <!-- pb-[120px] 2xl:pb-[90px] lg:pb-[80px]  lg:pb-[100px] -->
    <div class=" mx-auto main_sec sm:pb-5 bg-lightgray">
        @if (Session::has('success'))
        <div class="w-[300px] my-0 mx-auto render py-2">
            <p class="text-[#fff] text-sm md:text-[18px] py-[10px] rounded bg-[#008000] text-center">
                {{Session::get('success')}}
            </p>
        </div>
        @endif

        <!-- filter section --->
        <div class="dashboard_ct_section">
            <div class="">
                <div class=" w-full lg:px-[0px] lg:py-[0px]">
                    <div class="bg-white py-2.5 px-4 rounded mb-9 lg:hidden">
                        <form id="desktop_main_filter" action="{{route('partner.booking.calendar')}}">
                            <input type="hidden" name="start_date" class="desktop_start_date">
                            <input type="hidden" name="end_date" class="desktop_end_date">
                            <div class="flex flex-wrap align-center ">
                             <div class="flex basis-3/4 align-center 2xl:basis-full 2xl:grow-0 2xl:shrink-0 2xl:mb-5 2xl:justify-center">
                             @php /*<div class="basis-[25%] pr-2">
                              <div class="w-auto">
                                <div class="cursor-pointer  dropdown_sec_drop dropdown__drop_fillter_btn1 relative py-2 pl-[18px] rounded pr-11 bg-[#F4F4F4] date_range_block 3xl:w-max min-w-[225px]">
                                                    <span class="absolute hidden top-[14px] right-[13px] cross_icon_date">
                                                        <img src="{{asset('images/cross.svg')}}" alt="cross_icon_date" class="w-[14px]">
                                                   </span>
                                                    <a href="javascript:void(0);"
                                                        class="relative text-sm font-normal leading-4 text-black dropdown_listing">
                                                        Date Range
                                                    </a>

                                                <div class="drop_down_fillter_1 desk_daterange_filter absolute left-0 top-[58px]
                                                border-[#E0DCDC] fltr_dropdown_shadow rounded-[5px] bg-[#FFFFFF] min-w-[252px] w-full hidden">

                                                    <ul class="">
                                                        <li class="w-full text-sm font-normal">
                                                            <div class="block w-full daterange date_range_inner afclr">
                                                            </div>
                                                        </li>
                                                    </ul>
                                                    <div class="drop_down_fillter_1_action_sec pt-2 pb-3 mt-2 hidden">
                                                        <div class="flex justify-end flex-end">
                                                            <div class="px-2">
                                                                <a href="javascript:void(0);" class="inline-block cursor-pointer capitalize drop_down_fillter_1_done_btn px-5
                                                                py-1 text-sm font-normal leading-4 text-black border
                                                                rounded rounded border-siteYellow bg-siteYellow transition-all duration-300 ease-in-out hover:bg-siteYellow400">done</a>
                                                            </div>
                                                            <div class="px-2">
                                                                <a href="javascript:void(0);" class="inline-block drop_down_fillter_1_cancel_btn capitalize px-5
                                                                py-1 text-sm font-normal leading-4 text-black rounded rounded border-transparent  transition-all duration-300 ease-in-out hover:bg-siteYellow400 hover:border-siteYellow">
                                                                cancel
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                        </div>*/@endphp
                                        <div class="basis-[33%]  px-2 pl-0">
                                            <div class="w-auto">
                                               <div
                                                    class="dropdown_sec_drop dropdown__drop_fillter_btn2 relative cursor-pointer py-2
                                                     pl-[18px] rounded pr-11 bg-[#F4F4F4] date_range_block">
                                                    <span class="absolute hidden top-[14px] right-[13px] cross_icon"><img
                                                            src="{{asset('images/cross.svg')}}" alt="cross_icon" class="w-[14px]"></span>
                                                    <span
                                                        class="text-sm font-normal leading-4 text-black fltr_count desk_segment_count"></span>
                                                    <a href="javascript:void(0);"
                                                        class="text-sm font-normal leading-4 text-black capitalize">
                                                        segments
                                                    </a>
                                                <div class="drop_down_fillter_2 absolute left-0 top-[58px] w-full border border-[#E0DCDC]
                                                fltr_dropdown_shadow bg-[#FFFFFF]  rounded text-left max-h-[62vh] overflow-y-auto hidden scroller">
                                                <ul class="overflow-y-scroll scroller py-[7px] carType_list_items ">
                                                        @foreach ($uniqueCarTypes as $uniqueCarType)
                                                        <li class="text-[14px] font-normal">
                                                            <div
                                                                class="chckbox_out_x w-full border-b border-[#E7E7E7] p-2 flex flex-wrap ">
                                                                <label class="chckBoxes_container">
                                                                    <p class="text-[12px] mb-0  font-normal ml-[10px] capitalize break-all">
                                                                        @if(strcmp($uniqueCarType,'compact_suv')==0)
                                                                         Compact Suv
                                                                         @elseif(strcmp($uniqueCarType,'luxury')==0)
                                                                         Luxury
                                                                         @elseif(strcmp($uniqueCarType,'hatchback')==0)
                                                                         Hatchback
                                                                         @elseif(strcmp($uniqueCarType,'off_road')==0)
                                                                         Off-Road
                                                                         @elseif(strcmp($uniqueCarType,'sedan')==0)
                                                                         Sedan
                                                                         @elseif(strcmp($uniqueCarType,'suv')==0)
                                                                         Suv
                                                                        @endif
                                                                    </p>
                                                                    <input type="checkbox" name="car_type[]" value="{{$uniqueCarType}}"
                                                                        class="choice_checkBox car_type_checkboxes dsktp_car_type_checkboxes">
                                                                    <span class="checkmark"></span>
                                                                </label>
                                                            </div>
                                                        </li>
                                                    @endforeach
                                                </ul>

                                                <div class="drop_down_fillter_2_action_sec pt-2 pb-3 hidden">
                                                    <div class="flex justify-start flex-start">
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
                                         <div class="basis-[30%] px-2">
                                            <div class="w-auto">
                                                <div
                                                    class="dropdown_sec_drop dropdown__drop_fillter_btn3 relative cursor-pointer py-2 pl-[18px] rounded pr-11 bg-[#F4F4F4] date_range_block">
                                                    <span class="absolute hidden top-[14px] right-[13px] cross_icon">
                                                        <img src="{{asset('images/cross.svg')}}" alt="cross_icon" class="w-[14px]">
                                                    </span>
                                                    <span class="text-sm font-normal leading-4 text-black fltr_count desk_cars_count">
                                                    </span>
                                                    <a href="javascript:void(0);" class="text-sm font-normal leading-4 text-black capitalize">
                                                    transmissions</a>

                                                    <div
                                                        class="drop_down_fillter_3 absolute left-0 top-[58px] w-full border border-[#E0DCDC] fltr_dropdown_shadow bg-[#FFFFFF]  rounded text-left hidden ">
                                                        <div class="search_list_filter bg-[#F5F5F5] p-[12px] ">
                                                            <div
                                                                class="search_list_item_p cars_search_main bg-[#ffff] w-full rounded-[30px] py-[3px] px-[7px] flex items-center">
                                                                <img src="{{asset('images/search_icon.svg')}}" class="w-[17px]">
                                                                <input type="text" name="search"
                                                                    class="py-0 px-[8px] w-full filter_search_inp search_list_src outline-none text-[12px] s_search_inp_categ"
                                                                    placeholder="Search">
                                                            </div>
                                                            <div class="hidden flex items-center justify-center capitalize pt-4 text-sm text-[#686868] " id="cars_search_no_data_found"> no data found !!</div>
                                                        </div>

                                                        <ul class="desktop_filter_search  max-h-[62vh]  overflow-y-scroll scroller py-[7px]">
                                                            @foreach ($uniqueTransmissions as $car)
                                                            <li class="text-[14px] font-normal">
                                                                <div
                                                                    class="chckbox_out_x w-full border-b border-[#E7E7E7] p-2 flex flex-wrap ">
                                                                    <label class="chckBoxes_container">
                                                                        <p class="text-[12px] mb-0  font-normal ml-[10px] capitalize break-word">
                                                                            {{$car}}
                                                                        </p>
                                                                        <input type="checkbox" name="transmission[]" value="{{$car}}"
                                                                            class="choice_checkBox car_name_checkboxes dsktp_cars_checkboxes">
                                                                        <span class="checkmark"></span>
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            @endforeach
                                                        </ul>
                                                        <div class="drop_down_fillter_3_action_sec pt-2 pb-3 hidden">
                                                            <div class="flex justify-start flex-start">
                                                                <div class="px-2">
                                                                    <a href="javascript:void(0);" class="inline-block cursor-pointer capitalize drop_down_fillter_3_done_btn px-5
                                                                    py-1 text-sm font-normal leading-4 text-black border
                                                                    rounded rounded border-siteYellow bg-siteYellow transition-all duration-300 ease-in-out hover:bg-siteYellow400">done</a>
                                                                </div>
                                                                <div class="px-2">
                                                                    <a href="javascript:void(0);" class="inline-block drop_down_fillter_3_cancel_btn capitalize px-5
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
                                    <div class=" flex justify-end basis-1/4 2xl:basis-full 2xl:grow-0 2xl:shrink-0 2xl:mb-[8px] 2xl:justify-center">
                                    <div class="flex justify-end -mx-2 flex-end">
                                    <div class="px-2">
                                        <input type="submit" id="applyDesktopFilterBtn" class="inline-block cursor-pointer capitalize px-6
                                        py-2.5 text-sm font-normal leading-4 text-black border
                                        rounded rounded border-siteYellow bg-siteYellow transition-all duration-300 ease-in-out hover:bg-siteYellow400" value="SEARCH">
                                    </div>
                                    <div class="px-2">
                                        <a href="javascript:void(0);" class="inline-block desktop_clear_btn capitalize px-6
                                        py-2.5 text-sm font-normal leading-4 text-black rounded rounded border border-siteYellow transition-all duration-300 ease-in-out hover:bg-siteYellow400">
                                        CLEAR
                                        </a>
                                    </div>
                                        </div>
                                    </div>
                            </div>
                        </form>
                    </div>
                    <div class="hidden lg:block mob_filter_sec">
                        <div class="flex filter_block_out md:pl-[15px] md:pr-[15px] bg-[#F6F6F6]">
                            <div class="filter_date_l mr-7">
                                <div class=" border-b border-[#F4B20F]">
                                    <a href="javascript:void(0)" class="date_text_st mobile_filter capitalize py-[5px] ">
                                        <span class="inline-block calender_image_container mr-[6px]">
                                            <img src="{{asset('/images/calender_icon.svg')}}" alt="calender icon">
                                        </span>
                                        {{$startDate->format('D, d M')}} - {{$endDate->format('D, d M')}}
                                    </a>
                                </div>
                            </div>
                            <div class="filter_date_r flex text-right border-b border-[#F4B20F]">
                                <div class="text-left ">
                                    <a href="javascript:void(0)" class="date_text_st capitalize mobile_filter py-[5px]">
                                        <span class="inline-block filter_image_container mr-[6px]">
                                            <img src="{{asset('/images/filter.svg')}}" alt="filter icon">
                                        </span>
                                        filter
                                        @if($filtersCount>0)
                                            <span class="mob_filter_count_sec ml-[10px]">
                                                <span class="inline-block w-[20px] text-center h-[20px] sm:w-[19px] sm:h-[19px] bg-[#FFB600] rounded-full sm:text-[15px] " >{{$filtersCount}}</span>
                                            </span>
                                         @endif

                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
      <!--main calendar starts from here -->

        @if(count($cars)>0)
            <div class="relative out_car_range_b">
                <div class=" cars_custom_calender md:border-gray">
                    <div class="cars_custom_calender_inner">

                        <div class=" dates_section dates_sec_sticky  scrollingContent">
                        <div class="flex bg-textGray400 dates_sec_bg   mb-[13px] afclr ">
                            <div class="relative car_status_container  bg-textGray400 right_dates_section">
                                <!-- dates_section starts -->
                                <div class="flex justify-between bg-textGray400 dates_main_sec">
                                    <a href="javascript:void(0)" id="left_btn" class="action_btn lr_btn_fix" data-action-btn="decrease">
                                        <img src="{{asset('images/angle-down-n.svg')}}" alt="">
                                    </a>
                                    <ul class="custom_calender_dates_container">
                                        @foreach ($dates as $date)
                                        <li class="inline-block px-[13px] py-1 custom_calender_dates">
                                            <p class="text-xs text-black capitalize date_month_name"
                                                data-date-full_date="{{$date['full_date']}}">{{$date['month']}}</p>
                                            <p class="lg:text-sm font-normal lg:font-medium text-[17px] leading-4 lg:leading-[14px]">
                                            {{$date['day']}}</p>
                                            <p class="text-xs text-black capitalize">{{$date['day_name']}}</p>
                                        </li>
                                        @endforeach
                                    </ul>
                                    <a href="javascript:void(0)" id="right_btn" class="action_btn lr_btn_fix" data-action-btn="increase">
                                        <img src="{{asset('images/angle-down-n.svg')}}" alt="">
                                    </a>
                                </div>
                                <!-- dates_section ends -->
                            </div>
                        </div>
                        </div>

                       <!--cars loops starts here -->
                    <div class="outer_main scrollingContent">
                        @foreach ($cars as $car)
                            <div class="main_loop">
                                @php
                                $thumbnails = Helper::getFeaturedSetCarPhotoById($car->id);
                                $featureImageUrl = asset('images/no_image.svg');
                                if (count($thumbnails) > 0) {
                                    foreach ($thumbnails as $thumbnail) {
                                        if (strcmp($thumbnail->featured, 'set') == 0) {
                                            $featuredImage = Helper::getPhotoById($thumbnail->imageId);
                                            $featureImageUrl = $featuredImage->url;
                                        }
                                    }
                                }
                                $modifiedImgUrl = $featureImageUrl;
                                @endphp
                                @php
                                    $partnerName = Helper::getUserMeta($car->user_id, 'company_name') ? Helper::getUserMeta($car->user_id, 'company_name') : 'Not available';
                                    $defaultImageUrl = asset('images/no_image.svg');
                                    if (strcmp(Helper::getUserMeta($car->user_id, 'CompanyImageId'), '') != 0) {
                                        $profileImage = Helper::getPhotoById(Helper::getUserMeta($car->user_id, 'CompanyImageId'));
                                        if ($profileImage) {
                                            $defaultImageUrl = $profileImage->url;
                                        }
                                    }
                                    $modifiedUrl = $defaultImageUrl;
                                @endphp

                                <div class="car_status_container h-[200px] md:h-[151px]">
                                    <div class="car_status_main_content">
                                        <div class="car_status_row relative">
                                            {{-- w-max --}}
                                            <div class="sticky w-max left-[28px] sm:left-[14px]">
                                            <div class="flex justify-start justify-left md:mb-[10px]">
                                                <div class="car_status_row_sec  flex justify-center items-center md:w-[85px] md:p-[5px] md:bg-[#fff] md:rounded-[17px] left_section car_showcase_main_card md:bg-#fff">
                                                    <div class="h-[67px] md:h-[45px] py-[10px] overflow-hidden md:py-[5px] sm:py-1">
                                                        <img src="{{$modifiedImgUrl}}" alt="car image" class="object-contain  h-full">
                                                    </div>
                                                </div>
                                                <div class="car_status_row_sec pl-[15px] right_section md:flex md:flex-row  flex  md:items-start">
                                                    <div class="car_details_content md:flex-col md:items-start flex justify-start items-center">
                                                        <div class="car_name_sec">
                                                          <a href="{{route('partner.car.view',$car->id)}}" class="links_item_cta text-base md:text-center font-medium capitalize showcar_title_b hover:underline transition-all duration-300 ease-out  text-purple md:font-bold md:text-sm sm:text-xs">
                                                            {{$car->name}} ({{ucfirst($car->transmission)}})
                                                       </a>
                                            </div>
                                    <div class="md:block hidden cursor-default">
                                     <p class="py-1 showcase_ct_b text-[14px] font-normal text-textGray500  md:text-sm uppercase sm:text-xs">
                                        {{$car->registration_number}}
                                    </p>
                                 </div>
                                    <div class="car_ragisteration_sec md:hidden pl-2 cursor-default">
                                        <p class="py-1 showcase_ct_b text-[14px] font-normal text-textGray500  md:text-sm uppercase">
                                            {{$car->registration_number}}
                                        </p>
                                    </div>
                                </div>

                                <div class="hidden flex items-center mb-[25px] md:mb-[10px] lg:mb-[15px] md:ml-[8px] ">
                                    <div class="mr-[8px] h-[36px] md:h-[25px] overflow-hidden">
                                        <img class="object-contain h-full" src="{{$modifiedUrl}}">
                                    </div>
                                    <h2 class="md:hidden text-base font-medium leading-normal text-black">{{$partnerName}}</h2>
                                </div>
                                  </div>
                                    </div>
                                </div>
                                <ul class="list selectable" data-car-id="{{$car->id}}" data-user-id="{{$car->user_id}}">
                                    @foreach ($dates as $date)

                                    <li class="inline-block adj_margin clickable relative" data-date-full_date="{{$date['full_date']}}"
                                    data-date-month="{{$date['month']}}" data-date-day="{{$date['day']}}"
                                    data-car-id="{{$car->id}}" data-date-check="{{Helper::isBooked($car->id,$date['full_date'])}}">
                                    @php

                                    $fullDate = $date['full_date'];
                                    $formattedFullDate = date("d M Y", strtotime($fullDate));
                                    $lockedData = Helper::getlockedDataByCarId($car->id, $fullDate);
                                    $hasAdditionalBookings = Helper::hasAdditionalBookingsByCarId($car->id,$date['full_date']);
                                    $overlapLockedOrNot = Helper::overlapLockedOrNot($car->id,$date['full_date']);
                                    $isBooked = Helper::isBooked($car->id, $fullDate);
                                    $isLocked = Helper::isLocked($car->id, $fullDate);

                                    @endphp
                                     <a href="javascript:void(0);" @if($isBooked)  data-fancybox
                                        data-overlap-pickupdate="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['pickup_date'] : '' }}"

                                        data-overlap-dropoffdate="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['dropoff_date'] : '' }}"

                                        data-overlap-pickuptime="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['pickup_time'] : '' }}"
                                        data-overlap-dropofftime="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['dropoff_time'] : '' }}"
                                        data-overlap-bookingId="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['bookingId'] : '' }}"
                                        data-overlap-customer-name="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['customer_name'] : '' }}"
                                        data-overlap-customer-country-code="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['customer_mobile_country_code'] : '' }}"
                                        data-overlap-customer-number="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['customer_mobile'] : '' }}"
                                        data-overlap-pickup-location="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['pickup_location'] : '' }}"
                                        data-overlap-dropoff-location="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['dropoff_location'] : '' }}"
                                        data-overlap-booking-owner-id="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['booking_owner_id'] : '' }}"
                                        data-overlap-booked-id="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['additional']['id'] : '' }}" data-src="#booking_Details_showcase_popup"

                                       data-booking-id="{{array_key_exists($date['full_date'], $bookedArr) ?
                                    $bookedArr[$date['full_date']][$car->id]['booking']['id'] : '' }}"
                                       data-booked-startdate="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['pickup_date'] : '' }}"
                                       data-booked-enddate="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['dropoff_date'] : '' }}"
                                       data-booked-customer-name="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['customer_name'] : '' }}"
                                       data-booked-customer-country-code="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['customer_mobile_country_code'] : '' }}"
                                       data-booked-customer-number="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['customer_mobile'] : '' }}"
                                       data-booked-pickup-location="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['pickup_location'] : '' }}"
                                       data-booked-dropoff-location="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['dropoff_location'] : '' }}"
                                       data-pickupTime="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['pickup_time'] : '' }}"
                                       data-dropoffTime="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['dropoff_time'] : '' }}"
                                       data-booked-booking-owner-id="{{array_key_exists($date['full_date'], $bookedArr) ? $bookedArr[$date['full_date']][$car->id]['booking']['booking_owner_id'] : '' }}"

                                       @elseif($isLocked)
                                       data-locked-startdate="{{$lockedData->start_date}}"
                                       data-locked-enddate="{{$lockedData->end_date}}"@endif
                                       data-fancybox  data-src="#open_popup"  data-options='{"touch" : false}'  data-clickable_date='true'
                                       class="custom_pick_drop_popup booking_details_popup inline-flex flex-col items-center justify-center links
                                        @if($isBooked) booked adj_status_width
                                        @elseif($isLocked) locked adj_status_width
                                        @else activeDate  @endif">
                                        <div class="@if($hasAdditionalBookings) overlap_booking @endif @if($overlapLockedOrNot) overlap_locked @endif">
                                            <div class="relative range_active spaces_around_status " title="{{$formattedFullDate}}">
                                                <div class="mx-auto leading-[0] car_status_container_inner flex items-center cell_dates_day
                                                    @if($isBooked)
                                                    @elseif($isLocked)
                                                    @else
                                                    bg-[#E4FFDD] border border-[#25BE00]
                                                    @endif
                                                    justify-center w-[36px] h-[36px]  md:w-[30px] md:h-[30px] rounded-full">
                                                    @if($isBooked)
                                                    <span class="inline-block "></span>
                                                    @elseif($isLocked)
                                                    <span class="inline-block"></span>
                                                    @else<span class="inline-block date_day ">{{$date['day']}}</span>
                                                    {{-- invisible md:hidden --}}
                                                    @endif

                                                </div>
                                            </div>
                                            <div class="w-full car_status_title spaces_around_status">
                                                <span class="text-xs font-normal capitalize title ">
                                                    <!-- <span class="text-xs font-normal capitalize title sm:text-sm"> -->
                                                    @if($isBooked)
                                                        <!-- Booked -->
                                                    @elseif($isLocked)
                                                        <!-- Locked -->
                                                    @else
                                                        <!-- Available -->
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                    @endforeach
                                  </ul>
                                  </div>
                                </div>
                                </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                </div>

                @if(count($cars)>=10)
                    <div class=" relative text-center py-[50px] lg:py-[25px] loadMore_main_sec flex justify-center lg:my-7 md:mt-0 " id="loadMore_main_sec">
                    <div class="absolute -top-[2px] lg:-top-[25px] z-[8] hidden inline_loader w-[40px] h-[40px]">
                        <svg version="1.1" class="svg-loader" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 80 80" xml:space="preserve">
                            <path id="spinner" fill="#F3CD0E" d="M40,72C22.4,72,8,57.6,8,40C8,22.4,
                             22.4,8,40,8c17.6,0,32,14.4,32,32c0,1.1-0.9,2-2,2
                             s-2-0.9-2-2c0-15.4-12.6-28-28-28S12,24.6,12,40s12.6,
                             28,28,28c1.1,0,2,0.9,2,2S41.1,72,40,72z">
                               <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 40 40" to="360 40 40" dur="0.75s" repeatCount="indefinite" />
                            </path>

                         </svg>
                    </div>

                        <a href="javascript:void(0)" class="text-base font-normal leading-normal
                        text-black transition-all duration-[0.5s] ease-in border-b-2 border-siteYellow " id="loadMore">Load More</a>
                    </div>
                @endif

            </div>
            @else
            <div class="empty_cars empty_data_container md:px-[15px] w-full">
                <div class="empty_data_msgContainer adj_empty_car_sec w-full adj_empty_car_sec flex bg-white justify-center items-center">
                    There is no data to show
                </div>
            </div>
    @endif
    <!-- mobile filter popup starts here -->
    <div class="filtersec fixed overflow-hidden top-[20%] left-0 overflow-x-hidden z-[11] opacity-0 invisible transition-all duration-[0.5s]  ease-in w-full">
            <div class="relative">
                <div class="w-full px-5 py-2 text-right bg-transparent close_mob_filter_sec">
                        <a href="javascript:void(0);" class="inline-block pt-5 close_mob_filter">
                            <span class="inline-block close_wh_icon">
                                <img src="{{asset('images/cross_white.svg')}}" class="close_mobile_filter_btn"
                                    alt="cross">
                            </span>
                        </a>
                    </div>
                    <div class="bg-[#FFFFFF]">
                        <div class="w-full overflow-y-auto filter_height_sec">
                            <form action="{{route('partner.booking.calendar')}}" id="mob_filters_form" method="GET" class="mobile_filters_form" >
                                <input type="hidden" name="start_date" class="mobile_fltr_start_date">
                                <input type="hidden" name="end_date" class="mobile_fltr_end_date">
                                <div class="flex flex-wrap filter_main">
                                    <div class="filter_content_sec bg-textGray400 xsm:w-[26%]  w-1/3 min-h-[751px]">
                                        <div class="filter_tab_sec afclr">
                                            <div
                                                class="w-full text-sm capitalize filter_heading text-[#4A4A4A] py-[10px] pl-[10px] text-sm font-normal">
                                                filter
                                            </div>
                                            <ul>
                                                @php /* <li class="active">
                                                    <a href="#tab1"
                                                        class=" pl-[10px] w-full flex items-center justify-between text-sm py-[18px] mobDate_tab
                                                        capitalize hover:bg-[#fff] duration-300 hover:shadow-sm">Date
                                                        <span class=" inline-flex w-[20px] h-[20px]">
                                                            <svg class="w-full" xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                                                viewBox="-10 0 202 512">
                                                                <path fill="currentColor"
                                                                    d="M166.9 264.5l-117.801 116c-4.69922 4.7002 -12.2998
                                                                    4.7002 -17 0l-7.09961 -7.09961c-4.7002 -4.7002
                                                                    -4.7002 -12.3008 0 -17l102.3 -100.4l-102.2 -100.4c-4.69922
                                                                    -4.69922 -4.69922 -12.2998 0 -17l7.10059 -7.09961c4.7002 -4.7002 12.2998 -4.7002 17 0
                                                                    l117.8 116c4.59961 4.7002 4.59961 12.2998 -0.0996094 17z">
                                                                </path>
                                                            </svg>
                                                        </span>
                                                    </a>
                                                </li> */@endphp
                                                <li class="active">
                                                    <a href="#tab2"
                                                        class="pl-[10px] w-full segment_tab flex items-center
                                                        justify-between py-[18px]  capitalize text-sm duration-300  hover:bg-[#fff] hover:shadow-sm">segmants
                                                        <span class=" inline-flex w-[20px] h-[20px]">
                                                            <svg class="w-full" xmlns="http://www.w3.org/2000/svg"
                                                                xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"
                                                                viewBox="-10 0 202 512">
                                                                <path fill="currentColor"
                                                                    d="M166.9 264.5l-117.801 116c-4.69922 4.7002
                                                                    -12.2998 4.7002 -17 0l-7.09961 -7.09961c-4.7002
                                                                    -4.7002 -4.7002 -12.3008 0 -17l102.3 -100.4l-102.2
                                                                    -100.4c-4.69922 -4.69922 -4.69922 -12.2998 0 -17l7.10059 -7.09961c4.7002 -4.7002 12.2998 -4.7002 17 0
                                                                    l117.8 116c4.59961 4.7002 4.59961 12.2998 -0.0996094 17z">
                                                                </path>
                                                            </svg>
                                                        </span>

                                                    </a>
                                                </li>
                                                 <li class="">
                                                    <a href="#tab3"
                                                        class="pl-[10px] cars_tab text-sm w-full flex items-center justify-between py-[18px] hover:bg-[#fff] capitalize duration-300  hover:shadow-sm">transmissions
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
                                    <div class="filter_table_sec  bg-[#fff] xsm:w-[73%] w-2/3 filter_client_sec afclr">
                                        <div class="filter_table_inner_sec afclr">
                                            @php /*<div class="hidden mobile_filter_box afclr" id="tab1" style="display:none;">
                                                <div class="py-[10px] px-[15px]">
                                                    <div class="flex mobile_filter_box_tittle ">
                                                        <div class="flex w-1/2 mobile_filter_left_sec">
                                                            <div>
                                                                <a href="javascript:void(0)"
                                                                    class=" cursor-auto text-sm font-normal text-black capitalize">select
                                                                    date
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="flex justify-end w-1/2 font-normal mobile_filter_right_sec">
                                                            <div>
                                                                <a href="javascript:void(0)"
                                                                    class="p-2 text-sm font-normal reset_mob_date capitalize text-[#F4B20F]">
                                                                    reset
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-4">
                                                        <div class="mobile_datepicker_sec">
                                                            <div class="flex justify-center mobile_datepicker_inner">
                                                                <div class="mobile_datepicker w-full lg:px-2"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>*/@endphp

                                            <div class="hidden mobile_filter_box afclr " id="tab2" style="display:block;">
                                                <div class="py-[10px] px-[15px]">
                                                    <div class="flex mobile_filter_box_tittle ">
                                                        <div class="flex w-1/2 mobile_filter_left_sec ">
                                                            <div> <a href="javascript:void(0)"
                                                                    class="cursor-auto text-sm font-normal text-black capitalize">select
                                                                    segment</a>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="flex justify-end w-1/2 font-normal mobile_filter_right_sec">
                                                            <div>
                                                                <a href="javascript:void(0)"
                                                                    class="p-2 text-sm font-normal segment_choices_clear reset_btn capitalize text-[#F4B20F]">
                                                                    reset
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-4">
                                                        <ul class=" w-full  pb-[10px] flex flex-wrap gap-[10px]">
                                                            @foreach ($uniqueCarTypes as $uniqueCarType)
                                                             <li class="font-normal mb-[12px] md:mb-0 ml-[10px] ">
                                                                <div class="flex flex-wrap w-[130px]  break-words">
                                                                    <label
                                                                        class="chckBoxes_container segment_container border border-[#D9D9D9] rounded">
                                                                        <p class="text-xs mb-0 font-normal checkbox_title  capitalize py-[10px] ml-[17px] mr-[20px] ">
                                                                            @if(strcmp($uniqueCarType,'compact_suv')==0)
                                                                         Compact Suv
                                                                         @elseif(strcmp($uniqueCarType,'luxury')==0)
                                                                         Luxury
                                                                         @elseif(strcmp($uniqueCarType,'hatchback')==0)
                                                                         Hatchback
                                                                         @elseif(strcmp($uniqueCarType,'off_road')==0)
                                                                         Off-Road
                                                                         @elseif(strcmp($uniqueCarType,'sedan')==0)
                                                                         Sedan
                                                                         @elseif(strcmp($uniqueCarType,'suv')==0)
                                                                         Suv
                                                                        @endif
                                                                        </p>
                                                                        <input type="checkbox" name="car_type[]"
                                                                            value="{{$uniqueCarType}}"
                                                                            class="choice_checkBox mob_car_type_checkboxes clear_checkbox car_segment_checkboxes">
                                                                        <span class="checkmark checkbox_checkmark"></span>
                                                                    </label>
                                                                </div>
                                                            </li>

                                                            @endforeach
                                                            </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="hidden mobile_filter_box afclr " id="tab3" style="display:none;">
                                                <div class="py-[10px] px-[15px]">
                                                    <div class="flex mobile_filter_box_tittle ">
                                                        <div class="flex w-1/2 mobile_filter_left_sec ">
                                                            <div> <a href="javascript:void(0)"
                                                                    class="cursor-auto text-sm font-normal text-black capitalize">select
                                                                    transmission</a>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="flex justify-end w-1/2 font-normal mobile_filter_right_sec">
                                                            <div> <a href="javascript:void(0) "
                                                                    class="p-2 text-sm car_choices_clear reset_btn font-normal capitalize text-[#F4B20F]">
                                                                    reset</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-4">
                                                        <ul class=" w-full  pb-[10px] mobile_filter_box_list_items">
                                                            @foreach ($uniqueTransmissions as $car)
                                                            <li class="text-xs font-normal">
                                                                <div class="flex flex-wrap w-full ">
                                                                    <label class="chckBoxes_container">
                                                                        <p
                                                                            class="text-xs mb-0  font-normal ml-[10px] capitalize py-[10px] ">
                                                                            {!!$car!!}
                                                                        </p>
                                                                        <input type="checkbox" name="transmission[]"
                                                                            value="{!!$car!!}"
                                                                            class="choice_checkBox mob_car_name_checkboxes car_checkboxes clear_checkbox mob_status_checkboxes">
                                                                        <span class="checkmark"></span>
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            @endforeach

                                                        </ul>
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
                                        <a href="javascript:void(0);"
                                            class="reset_all_link p-2 capitalize text-[#F4B20F] text-sm">reset all</a>
                                    </div>
                                </div>

                                <div class="flex w-full filter_apply_inner_sec">
                                    <div class="flex justify-end w-1/2 mr-[10px]">
                                        <a href="javascript:void(0);"
                                            class=" capitalize font-normal  cancel_mobile_filter transition-all
                                            duration-300 border-siteYellow text-sm font-bold border  hover:bg-siteYellow400 rounded py-[10px] px-[30px] bg-white text-[#000] ">
                                            cancel
                                        </a>
                                    </div>
                                    <div class="flex justify-start w-1/2 ml-[10px]">
                                        <a href="javascript:void(0);" id="applyMobileFilterBtn"
                                         {{-- onclick="document.getElementById('mob_filters_form').submit();" --}}
                                            class="font-normal apply_mobile_filter border border-siteYellow transition-all
                                             duration-300 capitalize text-sm font-bold text-[#393939] bg-siteYellow  hover:bg-siteYellow400 rounded py-[10px] px-[30px] ">
                                            apply</a>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
            <!-- mobile filter section ends here -->
        </div>


    <!-- navigation -->
    @include('layouts.navigation')
</div>

<!----add booking popup -->
<div class="add_popcar">
    <div class=" mob_booking_details py-2 items-center pt-[15px] pb-2   hidden md:flex flex-col justify-center">
        <div class="pickup_bookingDetails pl-[18px]">
            <p class="font-[18px] font-normal text-[#000] pickup_booking_inner_detail sm:text-[15px]"><span>Pickup:</span>
                <span class="" id="mob_show_pickup_booking_date"></span> <span>-</span>  <span id="mob_show_pickup_booking_time"></span> </p>
        </div>

        <div class="dropoff_bookingDetails">
            <p class="font-[18px] font-normal text-[#000] pickup_booking_inner_detail sm:text-[15px]"><span>Drop:</span>
                <span class="" id="mob_show_dropoff_booking_date"></span> <span>-</span>  <span id="mob_show_dropoff_booking_time"></span> </p>
        </div>
    </div>

    <div class="relative flex items-center justify-center py-[35px] 2xl:py-[30px] lg:py-[25px] sm:py-[20px]">
        <div class="details_bookings md:hidden w-1/2 flex flex-col items-center justify-center pr-[20px]">
            <a href="javascript:void(0);" data-fancybox data-src="#open_popup" class="edit_pickupdatesnd_dropoffdates">
                <div class="md:hidden pickup_bookingDetails flex justify-center pl-4">
                        <p class="font-[18px] font-normal text-[#000] pickup_booking_inner_detail"><span>Pickup:</span>
                            <span class="" id="show_pickup_booking_date"></span> <span>-</span>  <span id="show_pickup_booking_time"></span> </p>
                </div>
                <div class="md:hidden dropoff_bookingDetails flex justify-center">
                        <p class="font-[18px] font-normal text-[#000] dropoff_booking_inner_detail"><span>Drop:</span>
                            <span class="dropoff_booking_date" id="show_dropoff_booking_date"></span>
                            <span>-</span>     <span id="show_dropoff_booking_time"></span>
                        </p>
                </div>
            </a>
        </div>
        <div class="w-1/2 flex md:flex-col justify-start md:w-[90%]">
            <div class="w-full text-left ">
                <a href="javascript:void(0);"class=" md:w-full md:inline-flex md:justify-between text-center inline-block hover:bg-siteYellow400  min-w-[230px] transition-all duration-300 ease-out rounded-[4px] text-black text-base md:text-sm font-normal bg-siteYellow px-[20px] py-2.5 md:px-[15px] md:min-w-0" id="add_booking">ADD TO BOOKING
                <span class="inline-flex"><img class="md:w-[36px] md:inline-block hidden rotate-180" src="{{asset('images/panel-back-arrow.svg')}}"></span>
            </a>
            </div>
            <div class="add_close_p  md:hidden p-[5px] absolute top-[0px] right-[56px] md:right-[15px] bottom-[0px] mx-auto my-0 text-center flex items-center justify-center">
                <div class="p-2 add_cl_block " >
                    <img class="w-[24px] lg:w-[21px]" src="{{asset('images/fa-cross-svgrepo.svg')}}">
                </div>
            </div>

            <div class="hidden md:flex md:mt-5 md:justify-between -mx-2 flex-end">
                <div class="px-2">
                    <a  href="javascript:void(0)" data-fancybox data-src="#open_popup"  class="edit_pickupdatesnd_dropoffdates inline-block capitalize capitalize px-6
                    py-2.5 text-sm font-normal leading-4 text-black rounded rounded border border-siteYellow transition-all duration-300 ease-in-out hover:bg-siteYellow400">CHANGE DATE/TIME
                    </a>
                </div>
                <div class="px-2">
                    <a href="javascript:void(0);" class=" add_cl_block inline-block capitalize px-6
                    py-2.5 text-sm font-normal leading-4 text-black rounded rounded border border-siteYellow transition-all duration-300 ease-in-out hover:bg-siteYellow400">
                    CANCEL
                    </a>
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
                                    <a href="javascript:void(0);" class="hidden md:block md:absolute close_popup">Close</a>
                                    <div class="datepicker_tab__inner_b md:pt-[60px] sm:pt-[40px]">
                                        <div class="datepicker_tab__content">
                                            <div class="datepicker_tab__btn tab_item_pickup"> <span>Pick-up</span>
                                                <p class="dapicker_val__tab_pickup_text"><span>dd/mm/yyyy | hh:mm</span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="datepicker_tab__content">
                                            <div class="datepicker_tab__btn tab_item_drop"> <span>Drop-off</span>
                                                <p class="dapicker_val__tab_dropoff_text"><span>dd/mm/yyyy | hh:mm</span>
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
                                            <div class="timepicker_sec">
                                                <div class="timepicker_inner">
                                                    <div class="half_div w-full md:w-[50%] sm:w-full">
                                                     <h3 class="head_sec sm:font-medium sm:text-center">Pick-up Time</h3>
                                                        <div class="timepicker_b sm:justify-center">
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
                                                    </div>

                                                    <div class="half_div w-full md:w-[50%] sm:w-full">
                                                    <div class="half_inner flex flex-col items-center">
                                                        <!--location inputs starts-->
                                                            <div class="pickup_location_bar w-full">
                                                                <div class="pb-5 inp_container w-full pt-[20px] sm:w-[280px] sm:mx-auto">
                                                                 <label for="pickup_location" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">
                                                                    Pickup location<span class="text-[#ff0000]">*</span>
                                                                  </label>
                                                                   <select name="pickup_location" tabindex="-1" id="pickup_location"
                                                                               onchange="checkBlankField(this.id,'Pickup location is required','errorPickup')"
                                                                               class="pickup_required w-full py-3 pl-5 pr-10 text-base font-normal leading-normal text-black bg-white border rounded-md appearance-none s_tag_haf border-black500 focus:outline-none focus:border-siteYellow drop_location_select"
                                                                               data-error-msg="Pickup location is required">
                                                                               <option value="">Select pickup location</option>
                                                                   </select>
                                                                    <span class="hidden required text-sm"></span>
                                                                 </div>
                                                                 <div class="w-full inp_container sm:w-[280px] sm:mx-auto">
                                                                   <div class="other_pickup_location_container" style="display:none">
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
                                                                   </div>
                                                                </div>
                                                                </div>
                                                               <!--location inputs ends-->
                                                               <div class="control_btns">
                                                                <button type="button"
                                                                    class="btn apply_btn pickup_save popup_btn">NEXT</button>
                                                            </div>
                                                        </div>
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
                                            <div class="timepicker_sec">
                                                <div class="timepicker_inner">
                                                <div class="half_div w-full md:w-[50%] sm:w-full">
                                                    <h3 class="head_sec sm:font-medium sm:text-center">Drop-off Time</h3>
                                                    <div class="timepicker_b sm:justify-center">
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
                                                </div>

                                                <div class="half_div w-full md:w-[50%] sm:w-full">
                                                <div class="half_inner flex flex-col items-center">

                                                   <!--location inputs starts-->
                                                   <div class="dropoff_location_bar w-full">

                                                    <div class="pb-5 inp_container w-full pt-[20px] sm:w-[280px] sm:mx-auto">
                                                     <label for="dropoff_location" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black ">
                                                       Dropoff location<span class="text-[#ff0000]">*</span></label>
                                                       <select name="dropoff_location" tabindex="-1" id="dropoff_location"
                                                            onchange="checkBlankField(this.id,'Dropoff location is required','errorDropoff')"
                                                            class="dropoff_required w-full py-3 pl-5 pr-10 text-base font-normal leading-normal text-black bg-white border rounded-md appearance-none s_tag_haf border-black500 focus:outline-none focus:border-siteYellow drop_location_select"
                                                            data-error-msg="Dropoff location is required">
                                                            <option value="">Select dropoff location</option>

                                                        </select>
                                                        <span class="hidden required text-sm"></span>
                                                    </div>

                                                    <div class="w-full inp_container sm:w-[280px] sm:mx-auto">
                                                    <div class="other_dropoff_location_container"style="display:none">
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
                                                </div>
                                            </div>
                                           </div>
                                            <!--location inputs ends-->


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
                            <input type="tel" name="phone_number" id="phone_number" data-parsley-required="true"
                                data-parsley-errors-container="#phone_number_block"
                                data-parsley-validation-threshold="0" data-parsley-trigger="keyup"
                                data-parsley-pattern="/^(?:\+?\+91|0)?[6-9]\d{9}$/" data-parsley-maxlength="15"
                                data-parsley-minlength="10"
                                data-parsley-minlength-message="Please enter a valid mobile number"
                                data-parsley-pattern-message="Please enter a valid mobile number"
                                data-parsley-maxlength-message="Please enter a valid mobile number" tabindex="3">
                            <input type="hidden" name="country_code" class="country_code_input">

                            <div id="phone_number_block" class="error_format"></div>

                            <div class="mail_verification_b" id="mail_verification_input_sec">


                                <div class="mail_verification_b_inner">
                                    <input type="email" name="email" placeholder="Enter your email..."
                                        class="verified_email_address" data-parsley-trigger="keyup"
                                        data-parsley-errors-container="#email_address_verifacation"
                                        data-parsley-validation-threshold="0">
                                </div>
                                <div id="email_address_verifacation" class="error_format"></div>
                            </div>
                        </div>

                        <div class="form-navigation">
                            <button type="button" id="send__otp__btn" class="btn next btn-info pull-left send__otp__btn"
                                disabled tabindex="4">NEXT &gt;
                                <div class="c_cart_loader_add afclr" style="display:none;">
                                </div>
                            </button>
                            <button type="button" class="next btn-info pull-left send__otp__btn_pj_next">&lt;
                                next</button>
                            <div class="pre_btn_sec">
                                <button type="button" class="previous btn-info pull-left">&lt; Go Back</button>
                            </div>
                            <span class="clearfix"></span>
                        </div>


                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<input type="hidden" name="" id="datepicker_start_date" value="{{$start_date}}">
<input type="hidden" name="" id="datepicker_end_date" value="{{$end_date}}">

<div class="booking_Details_showcase_popup_sec rounded-[10px] hidden" id="booking_Details_showcase_popup">
    <div class="model_content">
        <div class=" rounded-[5px] rounded-tl-10 rounded-tr-10 rounded-bl-0 rounded-br-0 custom-shadow">
            <div class="flex items-center px-5 py-5 flex-nowrap bg-siteYellow ">
                <div class="popup_heading w-1/2 text-[18px] text-[#000000] font-normal">
                    <h3 class="text-lg capitalize">Booking Details </h3>
                </div>
            </div>

            <div class="booking_Details_showcase_popup_inner  md:top-[48px] top-[50px] flex flex-col bg-white p-5 sm:p-3">
                <div class="showcase_popup_inner_content">
                    <div class="customer_sec customer_name_left_sec flex w-full">
                        <div class="customer_left text-base font-normal flex items-center">
                            <div class="w-3 h-3">
                                <svg class="block w-3 h-3" width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.6875 0C5.2832 0 5.83008 0.14649 6.32812 0.43946C6.83594 0.73243 7.23633 1.13282 7.5293 1.64063C7.82227 2.13868 7.9688 2.68555 7.9688 3.28125C7.9688 3.83789 7.83691 4.35547 7.57324 4.83399C7.31934 5.3125 6.96777 5.70313 6.51855 6.00586C7.0752 6.25 7.56836 6.58692 7.998 7.01661C8.4375 7.44629 8.7744 7.93946 9.0088 8.4961C9.2529 9.0723 9.375 9.6777 9.375 10.3125H8.4375C8.4375 9.6289 8.2666 9.0039 7.9248 8.4375C7.59277 7.86133 7.13867 7.40723 6.5625 7.0752C5.99609 6.7334 5.37109 6.5625 4.6875 6.5625C4.00391 6.5625 3.37402 6.7334 2.79785 7.0752C2.23145 7.40723 1.77734 7.86133 1.43555 8.4375C1.10352 9.0039 0.9375 9.6289 0.9375 10.3125H0C0 9.6777 0.12207 9.0723 0.36621 8.4961C0.60059 7.93946 0.93262 7.44629 1.3623 7.01661C1.80176 6.58692 2.2998 6.25 2.85645 6.00586C2.40723 5.70313 2.05078 5.3125 1.78711 4.83399C1.5332 4.35547 1.40625 3.83789 1.40625 3.28125C1.40625 2.68555 1.55273 2.13868 1.8457 1.64063C2.13867 1.13282 2.53418 0.73243 3.03223 0.43946C3.54004 0.14649 4.0918 0 4.6875 0ZM4.6875 0.9375C4.25781 0.9375 3.8623 1.04493 3.50098 1.25977C3.14941 1.46485 2.86621 1.74805 2.65137 2.10938C2.44629 2.46094 2.34375 2.85157 2.34375 3.28125C2.34375 3.71094 2.44629 4.10645 2.65137 4.46778C2.86621 4.81934 3.14941 5.10254 3.50098 5.31739C3.8623 5.52247 4.25781 5.625 4.6875 5.625C5.11719 5.625 5.50781 5.52247 5.85938 5.31739C6.2207 5.10254 6.50391 4.81934 6.70898 4.46778C6.92383 4.10645 7.03125 3.71094 7.03125 3.28125C7.03125 2.85157 6.92383 2.46094 6.70898 2.10938C6.50391 1.74805 6.2207 1.46485 5.85938 1.25977C5.50781 1.04493 5.11719 0.9375 4.6875 0.9375Z" fill="black"></path>
                                </svg>
                            </div>
                            <div class="pl-2">
                                <p>Customer:</p>
                            </div>
                        </div>
                        <div class="customer_right customer_name_right_sec text-left text-base font-normal ml-1">
                            <p></p>
                        </div>
                    </div>
                    <div class="customer_sec flex w-full customer_mobile_left_sec">
                        <div class="customer_left text-base font-normal flex items-center">
                            <div class="w-3 h-3">
                                <svg class="block w-3 h-3" width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.66429 0.0625C2.91819 0.0625 3.14769 0.145499 3.35276 0.311499L3.39671 0.340799L5.31565 2.3184L5.30101 2.333C5.50608 2.5283 5.60862 2.7676 5.60862 3.0508C5.60862 3.334 5.51097 3.5732 5.31565 3.7686L4.37815 4.7061C4.71019 5.4678 5.14476 6.1025 5.68187 6.6104C6.23851 7.1377 6.87815 7.5772 7.60081 7.9287L8.53831 6.9912C8.73362 6.7959 8.97776 6.6982 9.27073 6.6982C9.56367 6.6982 9.80787 6.7959 10.0032 6.9912L10.0178 7.0205L11.9221 8.9248C12.1174 9.1201 12.2151 9.3643 12.2151 9.6572C12.2151 9.9404 12.1174 10.1797 11.9221 10.375L10.4426 11.8545C10.218 12.0498 9.95918 12.1768 9.66628 12.2354C9.38308 12.2939 9.09984 12.2744 8.81664 12.1768H8.80198C6.70237 11.3564 4.92992 10.2236 3.48461 8.7783C2.6057 7.8994 1.83909 6.9082 1.18479 5.8047C0.716055 5.0039 0.349835 4.2227 0.0861669 3.4609V3.4463C-0.0114891 3.1729 -0.0261372 2.8945 0.0422218 2.6113C0.110581 2.3184 0.257066 2.0693 0.481675 1.8643L0.467025 1.8496L1.94651 0.326199L1.97581 0.311499C2.18089 0.145499 2.41038 0.0625 2.66429 0.0625ZM2.66429 1C2.63499 1 2.60081 1.0147 2.56175 1.0439L1.11155 2.5234C1.03343 2.5918 0.979725 2.6895 0.950425 2.8164C0.921125 2.9336 0.926015 3.041 0.965075 3.1387C1.20921 3.832 1.55101 4.5596 1.99046 5.3213C2.60569 6.3662 3.32347 7.2988 4.14378 8.1191C5.4719 9.4473 7.12718 10.502 9.1096 11.2832C9.38307 11.3711 9.62228 11.3272 9.82738 11.1514L11.2922 9.6865C11.3118 9.667 11.3215 9.6572 11.3215 9.6572C11.3215 9.6475 11.3118 9.6328 11.2922 9.6133L9.32938 7.6504C9.30008 7.6211 9.2805 7.6064 9.27073 7.6064C9.26097 7.6064 9.24144 7.6211 9.21214 7.6504L7.80589 9.0566L7.14671 8.749C6.83421 8.5928 6.53147 8.417 6.23851 8.2217C5.82835 7.958 5.46214 7.6797 5.13987 7.3867L5.03733 7.2988C4.7053 6.9766 4.3928 6.6006 4.09983 6.1709C3.89476 5.8584 3.70922 5.5313 3.5432 5.1895L3.36741 4.7793L3.26487 4.4863L3.48461 4.2813L4.65647 3.1094C4.68577 3.0801 4.69554 3.0459 4.68577 3.0068L2.76683 1.0439C2.72776 1.0147 2.69358 1 2.66429 1Z" fill="black"></path>
                                </svg>
                            </div>
                            <div class="pl-2">
                                <p>Mobile:</p>
                            </div>
                        </div>
                        <div class="customer_right customer_mobile_sec text-left text-base font-normal ml-1">
                            <p></p>
                        </div>
                    </div>
                    <div class="destination_sec flex w-full mt-[30px] mt-[30px] md:mt-[25px] sm:mt-[15px]">
                        <div class="left w-1/2 pr-2">
                            <div class="left_inner">
                                <p class="uppercase text-[#898376] font-normal text-[15px] pb-2 text-left">from</p>
                                <div class="pickup_sec">
                                    <div class="pickup_Date_sec text-sm text-left">
                                        <p></p>
                                    </div>
                                    <div class="pickup_time_sec text-sm text-left">
                                        <p></p>
                                    </div>
                                    <div class="pickup_location_sec text-sm flex pickup_location_left_sec">
                                        <span class="capitalize text-[#898376]">Pickup:</span>
                                        <p class="ml-1 pickup_location_right_sec whitespace-normal breaks-word text-left">  </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="right w-1/2">
                            <p class="uppercase text-[#898376] font-normal text-[15px] pb-2 text-left">to</p>
                            <div class="dropoff_sec">
                                <div class="dropoff_Date_sec text-sm text-left">
                                    <p></p>
                                </div>
                                <div class="dropoff_time_sec text-sm text-left">
                                    <p></p>
                                </div>
                                <div class="dropoff_location_sec text-sm flex dropoff_location_left_sec">
                                    <span class="text-[#898376] capitalize">Drop:</span>
                                    <p class="dropoff_location_right_sec ml-1 whitespace-normal breaks-word text-left">  </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="booking_Details_showcase_popup_links pt-6">
                    {{-- <div class="popup_link">
                        <a href="javascript:void(0); " class="py-2 px-2 bg-siteYellow">view</a>
                    </div> --}}
                    <div class="flex justify-start -mx-2 flex-end align-center popup_links_inner">

                        <div class="px-2 popup_link">
                            <a id="view_booking_cta"
                            href="javascript:void(0);"
                                class="links_item_cta cursor-pointer capitalize inline-block px-6 py-2.5 text-sm font-normal leading-4 text-black border rounded border-siteYellow bg-siteYellow transition-all duration-300 ease-in-out hover:bg-siteYellow400">view</a>
                        </div>

                        <div class="px-2 popup_link hidden">
                            <a id="new_booking_cta" data-src="#open_popup" class=" inline-block capitalize px-6 py-2.5 text-sm font-normal leading-4 text-black rounded rounded border border-siteYellow transition-all duration-300 ease-in-out hover:bg-siteYellow400"
                                href="javascript:void(0);">new booking</a>
                        </div>

                    </div>

                </div>

                <input type="hidden" id="details_showcase_popup_carId">
                <input type="hidden" id="details_showcase_popup_fullDate">
                <input type="hidden" id="details_showcase_popup_dropoffTime">


            </div>

        </div>
    </div>
</div>


    <!-- Logic for stop scroll -->
    @if(count($cars) < 10)
        <input type="hidden" class="stop_scroll" value="0">
    @else
        <input type="hidden" class="stop_scroll" value="1">
    @endif

<script>
// apply mobile filter
$('#applyMobileFilterBtn').on('click',function(e){
    e.preventDefault();
    $('body').removeClass('mob_filter_popup');
    // console.warn('under the mobile click');
    $('.loader').css("display", "inline-flex");
    $('.overlay_sections').css("display", "block");
    setTimeout(function() {
        $('#mob_filters_form').submit();
    }, 2000);

});

 $('#applyDesktopFilterBtn').on('click',function(e){

        e.preventDefault();

         $('.loader').css("display","inline-flex");
         $('.overlay_sections').css("display","block");

         setTimeout(function() {
             document.getElementById('desktop_main_filter').submit();
         }, 2000);

    });



// for opening popup for booking details
    $(document).ready(function () {

        if(window.matchMedia("(max-width: 767px)").matches){
                if($('.main_loop').length>0){
                    $('.outer_main').addClass('mobBottomPadding')
                }
        }

        $('body').on('click','.overlapped_right_part',function(e){
        e.stopPropagation();
         let isPinkishBgClicked = $(this).find('.pinkishBg').length > 0;
        //  console.warn('text:',isPinkishBgClicked);
        $.fancybox.open({
        src: $(this).closest('.links').data('src'),});
        var customer_name=$(this).closest('.links').data('overlap-customer-name');
        var customer_number=$(this).closest('.links').data('overlap-customer-number');
        var pickup_time=$(this).closest('.links').data('overlap-pickuptime');
        var dropoff_time=$(this).closest('.links').data('overlap-dropofftime');
        var pickup_date=$(this).closest('.links').data('overlap-pickupdate');
        var formattedPickupDate=convertdates(pickup_date);
        var dropoff_date=$(this).closest('.links').data('overlap-dropoffdate');
        var formattedDropoffDate=convertdates(dropoff_date);
        var pickup_location=$(this).closest('.links').data('overlap-pickup-location');
        var dropoff_location=$(this).closest('.links').data('overlap-dropoff-location');
        var customer_country_code=$(this).closest('.links').data('overlap-customer-country-code');
        var id = $(this).closest('.links').data('overlap-booked-id');
        var viewRoute = `{{ route('partner.booking.view', '__ID__') }}`.replace('__ID__', id);


        $('.customer_name_right_sec').html(customer_name);
        $('.customer_mobile_sec').html(customer_country_code+'&nbsp;' + customer_number);
        $('.pickup_Date_sec').html(formattedPickupDate);
        $('.pickup_time_sec').html(pickup_time);
        $('.pickup_location_right_sec').html(pickup_location);
        $('.dropoff_Date_sec').html(formattedDropoffDate);
        $('.dropoff_time_sec').html(dropoff_time);
        $('.dropoff_location_right_sec').html(dropoff_location);
        $('#view_booking_cta').attr('href', viewRoute);


         if(isPinkishBgClicked){
            $('#new_booking_cta').closest('.popup_link').show();
        }else{
            $('#new_booking_cta').closest('.popup_link').hide();
        }


        });

        $('body').on('click','.booked',function(e){
            e.preventDefault();
            var offset = $(this).offset();
            var width = $(this).outerWidth();
            var clickX = e.pageX - offset.left;
            var position = clickX / width;
            let isPinkishBgClicked = $(this).find('.pinkishBg').length > 0;
            let carId =$(this).closest('li').data('car-id');
            let fullDate =$(this).closest('li').data('date-full_date');
            let dropofftime=$(this).data('dropofftime');
            $.fancybox.open({
                src: $(this).data('src'),
                beforeShow: function(data,item){
                    let a = $.fancybox.getInstance().current.opts.$orig;
                    // console.error('invoiceId',carId,fullDate,dropofftime);
                    $('#details_showcase_popup_carId').val(carId);
                    $('#details_showcase_popup_fullDate').val(fullDate);
                    $('#details_showcase_popup_dropoffTime').val(dropofftime);
                }
            });

            if($(this).find('.overlapped_span').hasClass('overlapped_right_part')){
                // console.log('under the overlapped_left_part');
                if (position > 0.5) {
                // console.error('under the overlapped_right_part and > than');
                var customer_name = $(this).data('overlap-customer-name');
                var customer_number = $(this).data('overlap-customer-number');
                var pickup_time = $(this).data('overlap-pickuptime');
                var dropoff_time = $(this).data('overlap-dropofftime');
                var pickup_date = $(this).data('overlap-pickupdate');
                var formattedPickupDate = convertdates(pickup_date);
                var dropoff_date = $(this).data('overlap-dropoffdate');
                var formattedDropoffDate = convertdates(dropoff_date);
                var pickup_location = $(this).data('overlap-pickup-location');
                var dropoff_location = $(this).data('overlap-dropoff-location');
                var customer_country_code = $(this).data('overlap-customer-country-code');
                var id = $(this).data('overlap-booked-id');
                var booking_owner_id= $(this).data('overlap-booking-owner-id');
                // console.log('id:', id);
                // let that=$(this
                }
                else{
                // console.error('under the overlapped_left_part and < than');
                    var customer_name = $(this).data('booked-customer-name');
                    var customer_number = $(this).data('booked-customer-number');
                    var pickup_time = $(this).data('pickuptime');
                    var dropoff_time = $(this).data('dropofftime');
                    var pickup_date = $(this).data('booked-startdate');
                    var formattedPickupDate = convertdates(pickup_date);
                    var dropoff_date = $(this).data('booked-enddate');
                    var formattedDropoffDate = convertdates(dropoff_date);
                    var pickup_location = $(this).data('booked-pickup-location');
                    var dropoff_location = $(this).data('booked-dropoff-location');
                    var customer_country_code = $(this).data('booked-customer-country-code');
                    var id = $(this).data('booking-id');
                    var booking_owner_id= $(this).data('booked-booking-owner-id');
                }
            }
            else{
                var customer_name = $(this).data('booked-customer-name');
                if(customer_name !=''){
                    $('.customer_name_left_sec').css('display','flex');
                }else{
                    $('.customer_name_left_sec').css('display','none');
                }

                var customer_number = $(this).data('booked-customer-number');
                if(customer_number !=''){
                    $('.customer_mobile_left_sec').css('display','flex');
                }
                else{
                    $('.customer_mobile_left_sec').css('display','none');
                }


                var pickup_time = $(this).data('pickuptime');
                var dropoff_time = $(this).data('dropofftime');
                var pickup_date = $(this).data('booked-startdate');
                var formattedPickupDate = convertdates(pickup_date);
                var dropoff_date = $(this).data('booked-enddate');
                var formattedDropoffDate = convertdates(dropoff_date);
                var pickup_location = $(this).data('booked-pickup-location');
                if(pickup_location !=''){
                    $('.pickup_location_left_sec').css('display','flex');
                }
                else{
                    $('.pickup_location_left_sec').css('display','none');
                }

                var dropoff_location = $(this).data('booked-dropoff-location');

                if(dropoff_location !=''){
                    $('.dropoff_location_left_sec').css('display','flex');
                }
                else{
                    $('.dropoff_location_left_sec').css('display','none');
                }

                var customer_country_code = $(this).data('booked-customer-country-code');
                var id = $(this).data('booking-id');
                var booking_owner_id= $(this).data('booked-booking-owner-id');
            }


           let viewRoute = `{{ route('partner.booking.view', '__ID__') }}`.replace('__ID__', id);
            // console.warn('viewRoute:',viewRoute);
            $('.customer_name_right_sec').html(customer_name);
            $('.customer_mobile_sec').html(customer_country_code+'&nbsp;' + customer_number);
            $('.pickup_Date_sec').html(formattedPickupDate);
            $('.pickup_time_sec').html(pickup_time);
            $('.pickup_location_right_sec').html(pickup_location);
            $('.dropoff_Date_sec').html(formattedDropoffDate);
            $('.dropoff_time_sec').html(dropoff_time);
            $('.dropoff_location_right_sec').html(dropoff_location);
            $('#view_booking_cta').attr('href', viewRoute);
             if(isPinkishBgClicked){
                $('#new_booking_cta').closest('.popup_link').show();
             }else{
                $('#new_booking_cta').closest('.popup_link').hide();
             }
        });

    });

    function maskPhoneNumber(phoneNumber) {
            phoneNumber = phoneNumber.toString();

            var firstThree = phoneNumber.substring(0, 3);

            var lastTwo = phoneNumber.substring(phoneNumber.length - 2);

            var maskedString = firstThree + "*****" + lastTwo;
            return maskedString;
    }

    function getFirstName(fullName) {

        var nameParts = fullName.split(' ');

        var firstName = nameParts[0];
        return firstName;
    }


    $('.drop_down_fillter_3_done_btn, .drop_down_fillter_3_cancel_btn').on('click',function(){

        $(".drop_down_fillter_3").slideUp();

    });

    $('.drop_down_fillter_2_done_btn, .drop_down_fillter_2_cancel_btn').on('click',function(){

        $(".drop_down_fillter_2").slideUp();

    });

    $('.drop_down_fillter_1_done_btn, .drop_down_fillter_1_cancel_btn').on('click',function(){

        $(".drop_down_fillter_1").slideUp();

    });


var start_date = @json($start_date);
var end_date = @json($end_date);

var allDisableDates=[];
var ajaxFlag;
$(document).ready(function () {
calendarScroll();
// mobScroll();
});

//for popup remove if active, after 992
 $(window).resize(function () {
    calendarScroll();
    // mobScroll();
    if($('body').hasClass('mob_filter_popup') ){
        let flag=true;
        if($(window).width()>992){
            $('body').removeClass('mob_filter_popup');
        }
        else{
            if(flag){
                $('body').addClass('mob_filter_popup');
            }
        }
    }
});

// check for min width
if(window.matchMedia("(min-width: 992px)").matches){
    if($('body').hasClass('mob_filter_popup')){
        $('body').removeClass('mob_filter_popup');
    }
}

function calendarScroll(){
    let isDown = false;
    let startX;
    let scrollLeft;
    var slider=$(".scrollingContent");
    slider.on('mousedown', function(e) {
        isDown = true;
        slider.addClass('active');
        startX = e.pageX - slider.offset().left;
        scrollLeft = slider.scrollLeft();
        slider.css('cursor', 'grabbing');
    });

    slider.on('mouseleave ', function() {
        isDown = false;
        slider.removeClass('active');
        slider.css('cursor', 'grab');
    });

    slider.on('mouseup', function() {
        isDown = false;
        slider.removeClass('active');
        slider.css('cursor', 'grab');
    });

    slider.on('mousemove', function(e) {
        if (!isDown) return;
        e.preventDefault();
        var x = e.pageX - slider.offset().left;
        var walk = (x - startX);
        slider.scrollLeft(scrollLeft - walk);

    });
}


$(document).ready(function () {
    if(window.matchMedia("(max-width: 479px)").matches){
        $('.scrollingContent').scrollLeft(256);

    }
    else{
        $('.scrollingContent').scrollLeft(273);
    }

});

var dates_sec_sticky_active = true;
var outer_main_active = true;

$('.dates_sec_sticky').on('scroll', function(ev) {
    if (dates_sec_sticky_active) {
        $('.outer_main').scrollLeft($(this).scrollLeft());
        outer_main_active = false;
    }
});

$('.outer_main').on('scroll', function(e) {
    if (outer_main_active) {
        $('.dates_sec_sticky').scrollLeft($(this).scrollLeft());
        dates_sec_sticky_active = false;
    }
});

// Reset flags when scrolling stops
$('.dates_sec_sticky, .outer_main').on('scroll', function() {
    clearTimeout($.data(this, 'scrollTimer'));
    $.data(this, 'scrollTimer', setTimeout(function() {
        dates_sec_sticky_active = true;
        outer_main_active = true;
    }, 250));
});


//////////////// selected filter values  /////////////////////////////////
    var car_type = @json($car_type);
    var car_typeArray = [];
    if(car_type.length>0){
     car_type.forEach(element => {
        car_typeArray.push(element);
    });
    }

    var car_name = @json($car_name);
    var car_nameArray = [];
    if(car_name.length>0){
     car_name.forEach(element => {
        car_nameArray.push(element);
    });
    }

    var transmission = @json($transmission);
    var transmissionArray = [];
    if (transmission.length > 0) {
        transmission.forEach(element => {
            transmissionArray.push(element);
        });
    }


    $(document).ready(function(){
        $('.drop_down_fillter_2').find('.carType_list_items').children('li').each(function(){
            var carTypeelementValue = $(this).find('.dsktp_car_type_checkboxes').val();
            if (car_typeArray.includes(carTypeelementValue)) {

                $(this).find('.dsktp_car_type_checkboxes').prop('checked', true);
                $('.dropdown__drop_fillter_btn2').addClass('bg-[#F9F1CB]');
                $('.dropdown__drop_fillter_btn2').removeClass('date_range_block');
                $('.dropdown__drop_fillter_btn2').find('.cross_icon').show();
                let checkedCount = $('.car_type_checkboxes:checked').length;
                $('.desk_segment_count').html('(' + checkedCount + ')');

            }
        });




         $('.car_segment_checkboxes').each(function () {

             let carTypeElementValue=  $(this).val();

             if (car_typeArray.includes(carTypeElementValue)) {
                $(this).prop('checked', true);
                $(this).closest('.chckBoxes_container').css({ 'background': '#F4E7C3', 'border': '1px solid #F4B20F' });
                $('.segment_tab').addClass('bg-[#F9F1CB]');

             }

        });



        $('.mob_car_name_checkboxes').each(function(){
            let carNameElementValue=  $(this).val();
            if(transmissionArray.includes(carNameElementValue)){
                $(this).prop('checked', true);
                $('.cars_tab').addClass('bg-[#F9F1CB]');
            }

        });


        $('.desktop_filter_search').children('li').each(function(){
            var carNameelementValue = $(this).find('.dsktp_cars_checkboxes').val();
            if (transmissionArray.includes(carNameelementValue)) {

                    $(this).find('.dsktp_cars_checkboxes').prop('checked', true);
                    $('.dropdown__drop_fillter_btn3').addClass('bg-[#F9F1CB]');
                    $('.dropdown__drop_fillter_btn3').removeClass('date_range_block');
                    $('.dropdown__drop_fillter_btn3').find('.cross_icon').show();
                    let checkedCount = $('.car_name_checkboxes:checked').length;
                    $('.desk_cars_count').html('(' + checkedCount + ')');
            }

        });

    });

    if (start_date != '' || end_date != ''){
        $('.dropdown__drop_fillter_btn1').addClass('bg-[#F9F1CB]');
        $('.dropdown__drop_fillter_btn1').removeClass('date_range_block');
        $('.dropdown_listing').html('');
        $('.dropdown__drop_fillter_btn1').find('.cross_icon_date').show();
        $('.mobDate_tab').addClass('bg-[#F9F1CB]');
    }



// ends here

// filter click functionality
//for car type
     $('.car_type_checkboxes').on('change',function(e){
        if($('.car_type_checkboxes:checked').length>0){
            $(this).closest('.dropdown_sec_drop').addClass('bg-[#F9F1CB]');
            $(this).closest('.dropdown_sec_drop').removeClass('date_range_block');
            $(this).closest('.dropdown_sec_drop').find('.cross_icon').show();
            let anchor=  $(this).closest('.dropdown_sec_drop').find('a');
            let checkedCount = $('.car_type_checkboxes:checked').length;
            $('.desk_segment_count').html('(' + checkedCount + ')');
            $('.drop_down_fillter_2_action_sec').show();
        }else{
           $(this).closest('.dropdown_sec_drop').removeClass('bg-[#F9F1CB]');
           $(this).closest('.dropdown_sec_drop').find('.cross_icon').hide();
           $(this).closest('.dropdown_sec_drop').addClass('date_range_block');
           $('.desk_segment_count').html('');
           $('.drop_down_fillter_2_action_sec').hide();
        }
     });

     //for car name
     $('.car_name_checkboxes').on('change',function(e){
        if($('.car_name_checkboxes:checked').length>0){
            $('.car_type_checkboxes');
            $(this).closest('.dropdown_sec_drop').addClass('bg-[#F9F1CB]');
            $(this).closest('.dropdown_sec_drop').removeClass('date_range_block');
            $(this).closest('.dropdown_sec_drop').find('.cross_icon').show();
            let anchor=  $(this).closest('.dropdown_sec_drop').find('a');
            let checkedCount = $('.car_name_checkboxes:checked').length;
            $('.desk_cars_count').html('(' + checkedCount + ')');
            $('.drop_down_fillter_3_action_sec').show();
        }else{
           $(this).closest('.dropdown_sec_drop').removeClass('bg-[#F9F1CB]');
            $(this).closest('.dropdown_sec_drop').find('.cross_icon').hide();
            $(this).closest('.dropdown_sec_drop').addClass('date_range_block');
            $('.desk_cars_count').html('');
            $('.drop_down_fillter_3_action_sec').hide();
        }
     });


    // filters cross icon functionality
    $('.cross_icon').on('click',function(e){
        $('.drop_down_fillter_2_action_sec').hide();
        $('.drop_down_fillter_3_action_sec').hide();
        $('.drop_down_fillter_1_action_sec ').hide();
        $(this).closest('.dropdown_sec_drop').find('.choice_checkBox').prop('checked', false);
        $(this).closest('.dropdown_sec_drop').find('.cross_icon').hide();
        $(this).closest('.dropdown_sec_drop').addClass('date_range_block');
        $(this).closest('.dropdown_sec_drop').removeClass('bg-[#F9F1CB]');
        $(this).closest('.dropdown_sec_drop').find('.fltr_count').html('');
    });

    // for mobile filter click functionality
    $('.mob_car_type_checkboxes').on('change',function(e){
        if($('.mob_car_type_checkboxes:checked').length>0){
        $('.segment_tab').addClass('bg-[#F9F1CB]');
        }else{
        $('.segment_tab').removeClass('bg-[#F9F1CB]');
        }
    });

    $('.mob_car_name_checkboxes').on('change',function(e){
        if($('.mob_car_name_checkboxes:checked').length>0){
        $('.cars_tab').addClass('bg-[#F9F1CB]');
        }else{
        $('.cars_tab').removeClass('bg-[#F9F1CB]');
        }
    });

    //  searching in dropdown functionality starts here
     $('.filter_search_inp').on("keyup", function() {
        let value = $(this).val().toLowerCase();
        let items = $(".desktop_filter_search li");
        let filteredItems = items.filter(function() {
            return $(this).text().toLowerCase().indexOf(value) > -1;
        });
        items.hide();
        filteredItems.show();
        if (filteredItems.length === 0) {
            $("#cars_search_no_data_found").css('display','flex');
        } else {
            $("#cars_search_no_data_found").hide();
        }
     });

    // end filter clicking
    $('.segment_choices_clear').on('click', function (e) {
        $('.car_segment_checkboxes').each(function () {
            $(this).prop('checked', false);
            $(this).closest('.chckBoxes_container').css({ 'background': '#FFF', 'border': '1px solid #D9D9D9' });
            $('.segment_tab').removeClass('bg-[#F9F1CB]');
        });
    });

    // car choices clear/reset
    $('.car_choices_clear').on('click', function (e) {
        $('.car_checkboxes').each(function () {
            $(this).prop('checked', false);
            $('.cars_tab').removeClass('bg-[#F9F1CB]');
        });
    });

    $('.car_segment_checkboxes').on('click', function (e) {
        if ($(this).prop('checked')) {
            $(this).closest('.chckBoxes_container').css({ 'background': '#F4E7C3', 'border': '1px solid #F4B20F' });
        } else {
            $(this).closest('.chckBoxes_container').css({ 'background': '#FFF', 'border': '1px solid #D9D9D9' });
        }
    });

    $('.reset_all_link').on('click', function (e) {
        $('.clear_checkbox').each(function () {
            $(this).prop('checked', false);
            if ($(this).closest('.segment_container')) {
                $(this).closest('.segment_container').css({ 'background': '#FFF', 'border': '1px solid #D9D9D9' });
            }
        });

        $('.cars_tab').removeClass('bg-[#F9F1CB]');
        $('.segment_tab').removeClass('bg-[#F9F1CB]');
        $('.partner_tab').removeClass('bg-[#F9F1CB]');
        $('.mobile_fltr_end_date').val('');
        $('.mobile_fltr_start_date').val('');

    });

    $('.mobile_filter').on('click', function (e) {
        $('body').addClass('mob_filter_popup');
    });

    $('.close_mob_filter').on('click', function (e) {
        $('body').removeClass('mob_filter_popup');
    });

    if (window.matchMedia("(max-width: 992px)").matches) {
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

    function clearCalendar(){
        const calanderUrl= "{{route('partner.booking.calendar')}}";
        window.location.href=calanderUrl;
    }

    // filter tab toggling starts here
    $('.mobile_filter_box').hide();
    $('.mobile_filter_box:first').show();
    $('.filter_tab_sec ul li').on('click', function () {
        $('.filter_tab_sec ul li').removeClass('active');
        $(this).addClass('active');
        $('.mobile_filter_box').hide();
        var activeTab = $(this).find('a').attr('href');
        $(activeTab).fadeIn();
        return false;
    });

    //  filter tab toggling ends here

   //  mobile datepicker filters
    // $(document).ready(function(){
    //     var disabledDays = [0, 0];
    //     var mob_datePicker=  new AirDatepicker('.mobile_datepicker ', {
    //         language: 'en',
    //         autoClose: true,
    //         minDate: new Date(),
    //         range: true,
    //         onRenderCell: function(date, cellType) {},
    //         onSelect: function(date, obt) {
    //             if (date.formattedDate && date.formattedDate.length > 0) {
    //                 var $objDate = formatDate(date.formattedDate[0]);
    //                  $('.mobile_fltr_start_date').val($objDate);
    //             }
    //             else{
    //                 $('.mobile_fltr_start_date').val('');
    //             }

    //             if (date.formattedDate&& date.formattedDate.length > 1) {
    //                 var $objEnd = formatDate(date.formattedDate[1]);
    //                     $('.mobile_fltr_end_date').val($objEnd);
    //             } else {
    //                 $('.mobile_fltr_end_date').val('');
    //             }

    //         }
    //     });

    //     mob_datePicker.selectDate(new Date(start_date));
    //     mob_datePicker.selectDate(new Date(end_date));

    //     $('.reset_all_link').on('click', function (e) {

    //         setTimeout(function() {

    //             clearCalendar();
    //             $('.clear_checkbox').each(function () {
    //                 $(this).prop('checked', false);
    //                 if ($(this).closest('.segment_container')) {
    //                     $(this).closest('.segment_container').css({ 'background': '#FFF', 'border': '1px solid #D9D9D9' });
    //                 }
    //             });

    //             $('.cars_tab').removeClass('bg-[#F9F1CB]');
    //             $('.segment_tab').removeClass('bg-[#F9F1CB]');
    //             $('.partner_tab').removeClass('bg-[#F9F1CB]');
    //             mob_datePicker.clear();
    //             $('.mobile_fltr_end_date').val('');
    //             $('.mobile_fltr_start_date').val('');

    //         }, 2000);

    // });

    // $('.reset_mob_date').on('click', function (e) {
    // mob_datePicker.clear();
    // $('.mobile_fltr_start_date').val('');
    // $('.mobile_fltr_end_date').val('');
    // });

    //  // function for format date
    //     function formatDate(dateString) {
    //         if (dateString) {
    //             var parts = dateString.split('.');
    //             if (parts.length === 3) {
    //                 var d = new Date(parts[2], parts[1] - 1, parts[0]);
    //                 if (!isNaN(d.getTime())) {
    //                     var month = ('0' + (d.getMonth() + 1)).slice(-2);
    //                     var day = ('0' + d.getDate()).slice(-2);
    //                     var year = d.getFullYear();
    //                     return [year, month, day].join('-');
    //                 }
    //             }
    //         }
    //         return '';
    //     }
    //  });
    // ends here

    //mobile filter dropdowns starts
    $(".dropdown__drop_fillter_btn1 ").click(function (e) {
        e.stopPropagation();
        $(".drop_down_fillter_1").slideToggle();
        $(".drop_down_fillter_2").slideUp();
        $(".drop_down_fillter_3").slideUp();
    });

    $(".dropdown__drop_fillter_btn2 ").click(function (e) {
        e.stopPropagation();
        $(".drop_down_fillter_2").slideToggle();
        $(".drop_down_fillter_1").slideUp();
        $(".drop_down_fillter_3").slideUp();
    });

    $(".dropdown__drop_fillter_btn3 ").click(function (e) {
        e.stopPropagation();
        $(".drop_down_fillter_3").slideToggle();
        $(".drop_down_fillter_1").slideUp();
        $(".drop_down_fillter_2").slideUp();
    });

    $("body").click(function (e) {
        $(".drop_down_fillter_1").slideUp();
        $(".drop_down_fillter_2").slideUp();
        $(".drop_down_fillter_3").slideUp();
    });

    $(".drop_down_fillter_1").click(function (e) {
        e.stopPropagation();
    });

    $(".drop_down_fillter_2").click(function (e) {
        e.stopPropagation();
    });

    $(".drop_down_fillter_3").click(function (e) {
        e.stopPropagation();
    });


    $(".drop_down_fillter_4").click(function (e) {
        e.stopPropagation();
    });
    //filter dropdowns ends

    // desktop air datepicker starts here
    // $(document).ready(function() {
    // var disabledDays = [0, 0];
    // var desk_datePicker = new AirDatepicker('.daterange', {
    //     language: 'en',
    //     autoClose: true,
    //     minDate: new Date(),
    //     range: true,
    //     onRenderCell: function(date, cellType) {
    //     // You can add rendering logic here if needed
    //     },
    //     onSelect: function(date, obt) {
    //         if (date.formattedDate && date.formattedDate.length > 0) {
    //             var $objDate = formatDate(date.formattedDate[0]);
    //             $('.desktop_start_date').val($objDate);
    //             $('.dropdown__drop_fillter_btn1 ').addClass('bg-[#F9F1CB]');
    //             $('.dropdown__drop_fillter_btn1 ').removeClass('date_range_block');
    //             $('.dropdown__drop_fillter_btn1 ').find('.cross_icon_date').show();
    //             $('.dropdown_listing ').html("");
    //             $('.dropdown_listing').append('<span class="relative text-sm font-normal leading-4 text-black ">' + $objDate + '</span>');
    //             $('.drop_down_fillter_1_action_sec').show();
    //         } else {
    //             $('.desktop_start_date').val('');
    //             $('.dropdown__drop_fillter_btn1 ').removeClass('bg-[#F9F1CB]');
    //             $('.dropdown__drop_fillter_btn1 ').addClass('date_range_block');
    //             $('.dropdown__drop_fillter_btn1 ').find('.cross_icon_date').hide();
    //             $('.dropdown_listing ').html("Date Range");
    //             $('.drop_down_fillter_1_action_sec').hide();
    //         }

    //         if (date.formattedDate && date.formattedDate.length > 1) {
    //             var $objEnd = formatDate(date.formattedDate[1]);
    //             $('.desktop_end_date').val($objEnd);
    //             $('.dropdown_listing').append('<span class="relative text-sm font-normal leading-4 text-black">' + ' - ' + $objEnd + '</span>');
    //         } else {
    //             $('.desktop_end_date').val('');
    //         }
    //     }
    // });

    // desk_datePicker.selectDate(new Date(start_date));
    // desk_datePicker.selectDate(new Date(end_date));

    // // $('.desktop_clear_btn').on('click',function(e){
    // //     desk_datePicker.clear();
    // //     $('.desktop_start_date').val('');
    // //     $('.desktop_end_date').val('');
    // // });

    // function formatDate(dateString) {
    //     if (dateString) {
    //         var parts = dateString.split('.');
    //         if (parts.length === 3) {
    //             var d = new Date(parts[2], parts[1] - 1, parts[0]);
    //             if (!isNaN(d.getTime())) {
    //                 var month = ('0' + (d.getMonth() + 1)).slice(-2);
    //                 var day = ('0' + d.getDate()).slice(-2);
    //                 var year = d.getFullYear();
    //                 return [year, month, day].join('-');
    //             }
    //         }
    //     }
    //     return '';
    // }

    // $('.cross_icon_date').on('click',function(e){
    //      desk_datePicker.clear();
    //     $('.desktop_end_date').val('');
    //     $('.desktop_start_date').val('');
    //     $
    // });

    // });

    // $('.cross_icon_date').on('click',function(e){
    //         $('.drop_down_fillter_1_action_sec').hide();
    //         $('.dropdown__drop_fillter_btn1').find('.cross_icon_date').hide();
    //         $('.dropdown__drop_fillter_btn1').addClass('date_range_block');
    //         $('.dropdown__drop_fillter_btn1').removeClass('bg-[#F9F1CB]');
    //         $('.desktop_end_date').val('');
    //         $('.desktop_start_date').val('');
    //         $('.filtered_dates').remove();
    // });


    function formatDate(date) {
        var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();
        if (month.length < 2) month='0' + month; if (day.length < 2) day='0' + day; return [year, month, day].join('-');
    }


    $(document).ready(function() {

        $('.desktop_clear_btn').on('click',function(e){
            e.preventDefault();
            $('.loader').css("display", "inline-flex");
            $('.overlay_sections').css("display", "block");
            setTimeout(function() {
            clearCalendar();
            var deskForm = document.getElementById("desktop_main_filter");
            deskForm.reset();
            $('.dropdown_sec_drop').each(function(){

                $(this).closest('.dropdown_sec_drop').removeClass('bg-[#F9F1CB]');
                $(this).closest('.dropdown_sec_drop').find('.cross_icon').hide();
                $(this).closest('.dropdown_sec_drop').addClass('date_range_block');
                $('.desk_segment_count').html('');
                $('.desk_cars_count').html('');
                $('.desk_vendor_count').html('');
                $('.filter_search_inp').html('');
                $('.filter_search_inp2').html('');
                $('#vendor_search_no_data_found').hide();
                $('#cars_search_no_data_found').hide();
            });

            desk_datePicker.clear();
            $('.desktop_end_date').val('');
            $('.desktop_start_date').val('');

            }, 2000);
        });
        // desktop clear functionality ends here

        // formatDate for format date into formated date like 01-01-1997
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

      //this will check the current date
      checkCurrentDate();
      if($('.main_loop').length>=10){

        $('#loadMore_main_sec').show();
      }
      else{
        $('#loadMore_main_sec').hide();
      }

    // main calendar functionality starts here
    var allStatusList=[];
    var carDetails = [];
    var selectedDates = [];
    var isBookingCancelBtn = false;

    // this function is storing data for which car is going to book
    function updateCarDetails() {
          carDetails = [];
          $('.selected').each(function () {
            var selectedCellsId = $(this).parent('li').data('date-day');
            var full_date = $(this).parent('li').data('date-full_date');
            var carId = $(this).closest('.list').data('car-id');
            var details = {
                'carId': carId,
                'selectedCellsId': selectedCellsId,
                'full_date': full_date,
            };
            carDetails.push(details);
        });
    }

    // start_time and end_time_val should be empty/remove and its remaining selected class while cancelling
    function onBookingCancel(){

        $('.activeDate').find('.range_active').removeClass('active left_arc right_arc');
        // $('.range_active').removeClass('active left_arc right_arc');
        // $('.range_active').find('.car_status_container_inner').removeClass('pinkishBg');

        if($('.selected').find('.car_status_container_inner').hasClass('overlap_right_bg')){
              $('.selected').find('.car_status_container_inner').html('');
        }
        $('.selected').find('.car_status_container_inner').removeClass('overlap_right_bg');

        $('.selected').find('.range_active').removeClass('single_date_selection');


        if($('.selected').find('.car_status_container_inner').hasClass('overlap_right_greenSqBg')){
              $('.selected').find('.car_status_container_inner.overlap_right_greenSqBg').html('');
        }

        $('.selected').find('.car_status_container_inner').removeClass('overlap_right_greenSqBg');
        // $('.selected').find('.car_status_container_inner').html('');
        $('.activeDate').removeClass('selected');
        $('.list').removeClass('active');
        $('body').removeClass('body_overlay selected_pop_bl');
        $('.start_time_val').val('');
        $('.end_time_val').val('');
        $('.selected_dates_popup').hide();
        $('.booking_new_content').remove();
        $('.car_status_container_inner').removeClass('selectedRange');
    }

    $('body').on('click','.add_cl_block',function(e){
            isBookingCancelBtn=true;
            onBookingCancel();
    });

    // check current date for show&hide left navigation button and highlight current date
    function checkCurrentDate(){
        var currentdate = new Date().toJSON().slice(0, 10);
        var full_date = $('.date_month_name:first').data('date-full_date');
        if(currentdate ==  full_date){
            // $('#left_btn').hide();
        }
        else{
            $('#left_btn').show();
        }
    }

   function highlightCurrentDate(){
        let currentdate = new Date().toJSON().slice(0, 10);
        let currentDateIndex =$('.custom_calender_dates').filter(function() {
            return $(this).find('.date_month_name').data('date-full_date') == currentdate;
        });
        $(currentDateIndex).addClass('border-[2px] border-[#47B6C1] rounded-[4px]');
   }

    highlightCurrentDate();


    checkCurrentDate();

    //load more month functionality (increase/decrease)
    $('.action_btn').on('click', function (e) {
        e.stopPropagation();
        $(window).width();
        var action_btn = $(this).data('action-btn');
        var clickableElements = document.querySelectorAll(".list");
        var userIds=[];
        clickableElements.forEach(function(element) {
        var userId = element.getAttribute('data-user-id');
        userIds.push(userId);
        allStatusList.push(element);
     });

        var action_btn = $(this).data('action-btn');
        if (action_btn == 'decrease') {
            var full_date = $('.date_month_name:first').data('date-full_date');
            var action = $(this).data('action-btn');
            var module_length = $('.main_loop').length;
            scrollContainer($(this).closest(".dates_sec_sticky"), 'left',action_btn);
        }

        if (action_btn == 'increase') {
            var full_date = $('.date_month_name:last').data('date-full_date');
            var action = $(this).data('action-btn');
            var module_length = $('.main_loop').length;
            scrollContainer($(this).closest(".dates_sec_sticky"), 'right',action_btn);
        }
    });

    // slide/scroll functionality
    function scrollContainer(container, direction,actionBtn) {
        let scroll_left_value = $(this).closest('.dates_sec_sticky').scrollLeft();
        $(this).closest('.dates_sec_sticky').scrollLeft(scroll_left_value+100);
        let allCarsMainContainer=$('.outer_main');
        var action_btn=actionBtn;
        let extraWidth = 24;
        var fullWidth = $(".car_status_container").width();
        let scrollAmount = $('.dates_section').width();
        let currentPosition = container.scrollLeft();


        if (direction === 'left') {

            // console.warn('left');

            if(window.matchMedia("(max-width: 479px)").matches){
                    container.animate({ scrollLeft: currentPosition - scrollAmount},500 );
                    allCarsMainContainer.animate({ scrollLeft: currentPosition - scrollAmount },500 );
            }else{
                container.animate({ scrollLeft: currentPosition - scrollAmount }, 1000);
               allCarsMainContainer.animate({ scrollLeft: currentPosition - scrollAmount }, 1000);
            }
            // Scroll to the left

            if (currentPosition >= 0 && currentPosition < scrollAmount || currentPosition == scrollAmount) {
              let currentdate = new Date().toJSON().slice(0, 10);
              let full_date = $('.date_month_name:first').data('date-full_date');
              if(currentdate == full_date){

             loadMoreDates(action_btn);


            //   $('#left_btn').hide();
              }
             else if(currentPosition==0){
             loadMoreDates(action_btn);
            }
            }
          }

          else if (direction === 'right') {

            // console.warn('inright');
            // Scroll to the right
            if (currentPosition + scrollAmount <= fullWidth) {
                if(window.matchMedia("(max-width: 479px)").matches){
                    container.animate({ scrollLeft: currentPosition + scrollAmount},500 );
                    allCarsMainContainer.animate({ scrollLeft: currentPosition + scrollAmount },500 );
                }
                else{
                container.animate({ scrollLeft: currentPosition + scrollAmount},1000 );
                allCarsMainContainer.animate({ scrollLeft: currentPosition + scrollAmount },1000 );

                }
            }

            if(currentPosition + scrollAmount > (fullWidth - 100)){
                container.animate({ scrollLeft: currentPosition - fullWidth }, 1000);
                allCarsMainContainer.animate({ scrollLeft: currentPosition - fullWidth },1000 );

                loadMoreDates(action_btn);
            }
            $('#left_btn').show();
        }
    }

    // this will make range when more month is loaded through AJAX
    function checkSelectedDates(elements, startDate, endDate)
    {
        let flag= false;
        elements.find('li').each(function() {
        const liDate = $(this).data('date-full_date');
        if (liDate >= startDate && liDate <= endDate  ) {
            let linksElement = $(this).find('.links');
            let rangeActiveElement =  $(this).find('.range_active');
            linksElement.addClass('selected');
            rangeActiveElement.addClass('active');
            if (liDate === startDate) {
                rangeActiveElement.addClass('left_arc');
            }
            if (liDate === endDate) {
                rangeActiveElement.addClass('right_arc');
            }
            flag=true;
        }
        });
        return flag;
    }

    // this will load more dates/month
    function loadMoreDates(action_btn)
    {
      var action_btn = action_btn;
        var clickableElements = document.querySelectorAll(".list");
        var carIds=[];
        var userIds=[];
        clickableElements.forEach(function(element) {
            var userId = element.getAttribute('data-user-id');
            userIds.push(userId);
            allStatusList.push(element);
        });

        clickableElements.forEach(function(e) {
            var carId = e.getAttribute('data-car-id');
            carIds.push(carId);
        });



        if (action_btn == 'decrease') {
            var full_date = $('.date_month_name:first').data('date-full_date');
            var action = action_btn;
            var module_length = $('.main_loop').length;

        }

        if (action_btn == 'increase') {
            var full_date = $('.date_month_name:last').data('date-full_date');
            var action = action_btn;
            var module_length = $('.main_loop').length;
        }
         $(".loader").css("display", "inline-flex");
         $(".overlay_sections").css("display", "block");
         $.ajax({
            url:"{{ route('partner.booking.filter')}}",
            type: 'POST',
            data: {
                '_token': '{{ csrf_token() }}',
                'full_date': full_date,
                'type': action,
                'carIds': carIds,
                'userIds': userIds,
                'cars_length':module_length,
            },
            dataType: "json",
            success: function (data) {
                if (data.success) {
                    $('.outer_main').html('');
                    $('.outer_main').html(data.main_loop);
                    $('.custom_calender_dates_container').html(data.date_data);
                }
            },
            error: function (xhr, status, error) {
            },
            complete: function (data) {
                $(".loader").css("display", "none");
                $(".overlay_sections").css("display", "none");
                 calendarScroll();
                 checkCurrentDate();
                if(isBookingCancelBtn==false){
                        let start_date = $('#datepicker_first_date').val();
                        let end_date = $('#datepicker_last_date').val();
                        let carId = $('#fancyBox_car_id').val();
                        const currentUl = $('.list').filter(function() {
                        return $(this).data('car-id') == carId;
                });
                handleDateRangeConditions(currentUl,start_date,end_date);
                }
                highlightCurrentDate();
                $('.bookingDetails').remove();
                handleBookedDateRangeConditions();
                handleLockedDateRangeConditions();

            }
        });
    }

    function getSelectedDates(){
    $('.selected').each(function () {
        var selectedCellsId = $(this).parent('li').data('date-day');
        var full_date = $(this).parent('li').data('date-full_date');
        var carId = $(this).closest('.list').data('car-id');
        var details = {
            'carId': carId,
            'selectedCellsId': selectedCellsId,
            'full_date': full_date,
        };
        selectedDates.push(details);
    });
    }

    // add booking functionality
    $('#add_booking').on('click', function ()
    {
        var pickup_time_val =  $('#datepicker_pickup_time').val();
        var dropoff_time_val =  $('#datepicker_dropoff_time').val();
        let carId = parseInt($('#fancyBox_car_id').val());
        let start_date = $('#datepicker_first_date').val();
        let end_date = $('#datepicker_last_date').val();
        let action_type = $('#fancyBox_action_type').val();
        var pickup_location = $('#pickup_location').val();
        var other_pickup_location = $('#other_pickup_location').val();
        var dropoff_location = $('#dropoff_location').val();
        var other_dropoff_location = $('#other_dropoff_location').val();
        var details = {
            'carId': carId,
            'start_date': start_date,
            'end_date': end_date,
        };
         $(".loader").css("display", "inline-flex");
         $(".overlay_sections").css("display", "block");
         $.ajax({
            url:"{{ route('partner.booking.calendar.post') }}",
            type: 'POST',
            data: {
                '_token': '{{ csrf_token() }}',
                'carDetails': details,
                'start_time': pickup_time_val,
                'end_time': dropoff_time_val,
                'pickup_location':pickup_location,
                'other_pickup_location':other_pickup_location,
                'dropoff_location':dropoff_location,
                'other_dropoff_location':other_dropoff_location,
                'action_type':action_type,
            },
            dataType: "json",
            success: function (data) {
            if (data.success) {
                Swal.fire({
                title: 'Done',
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
                        title: 'You want to add further booking details?',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'ADD CUSTOMER DETAILS',
                        cancelButtonText: 'BOOK & CLOSE',
                        customClass: {
                            popup: 'popup_updated',
                            title: 'popup_title',
                            actions: 'btn_confirmation',
                            confirmButton: 'bgGreen',
                            cancelButton: 'bgYellow'
                        },
                    }).then((result) => {
                        if (result.isConfirmed) {
                            var bookingId = data.bookingId;
                            var url = "{{ route('partner.edit.customer.details', ':id') }}";
                            url = url.replace(':id', bookingId);
                            window.location.href = url;
                        }
                        else if (!result.isConfirmed){
                            window.location.href = "{{route('partner.booking.list')}}";
                        }
                    });
                }
            });
            }else{
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
    });

    var start = 3;
    var loading = false;
    var scrolled = false;
    function loadMore()
    {

        if (!scrolled) {
            return;
        }

        if (loading) {
            return;
        }

        if ($('#loadMore').html() === 'No More Data Available') {
            return;
        }


        $(".inline_loader").css("display", "inline-flex");
        // $(".overlay_sections").css("display", "block");

        var start_date = $('.date_month_name:first').data('date-full_date');
        var last_date = $('.date_month_name:last').data('date-full_date');
        var clickableElements = document.querySelectorAll(".list");
        var carIds=[];

        let fltrStartDate= $('#datepicker_start_date').val();
        let fltrEndDate= $('#datepicker_end_date').val();


        clickableElements.forEach(function(element) {
            var carId = element.getAttribute('data-car-id');
            carIds.push(carId);
        });

        loading = true;

        $.ajax({
            url: "{{ route('partner.booking.calendar.load.more') }}",
            method: "GET",
            data:
            {
                'start': start,
                'carIds':carIds,
                'start_date':start_date,
                'last_date':last_date,
                'car_type':car_type,
                'car_name':car_name,
                'filter_start_date':fltrStartDate,
                'filter_end_date':fltrEndDate,
            },
            dataType: "json",
            success: function(data)
            {
                if (data.data.length > 0)
                {
                    $('.outer_main').append(data.data);
                    $('#loadMore').html('Load More');
                    $('#loadMore').attr('disabled', false);
                    start = data.next;
                }
                else
                {
                    $('#loadMore').html('No More Data Available');
                    $('#loadMore').off('click');
                    $('#loadMore').css('cursor', 'default');
                    $('#loadMore').prop('disabled', true);
                }
            },
            complete: function (data)
            {
                loading = false;
                $(".inline_loader").css("display", "none");
                // $(".overlay_sections").css("display", "none");
                calendarScroll();
                highlightCurrentDate();

                $('.bookingDetails').remove();
                $('.overlapBookingDetails').remove();

                handleBookedDateRangeConditions();
                handleLockedDateRangeConditions();
            }
        });
    }

    $('#loadMore').click(loadMore);

    $(window).scroll(function () {

        if(window.matchMedia("(min-width: 992px)").matches){
        $('.dates_sec_sticky').addClass('scrollActive');
        }
        else{
        $('.dates_sec_sticky').removeClass('scrollActive');
        }

        if ((parseInt($('.stop_scroll').val())) > 0 )
        {
            if ($(this).scrollTop() > 0) {
                scrolled = true;
            }

            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 1500) {
                loadMore();
            }
        }

    });


    // manage mobile filter when user visit portal first time
    $(document).ready(function() {
    var userEntered = localStorage.getItem("userEntered");

    if (!userEntered) {
        function isMobileDevice() {
            localStorage.setItem("mobFilterAdded", "true");
            return $(window).width() < 992;
        }
        var oncembFilterAdded = localStorage.getItem("OncemobFilterAdded");
        if (!oncembFilterAdded) {
            var isMobile = isMobileDevice();
            if (isMobile) {
                $('body').addClass('mob_filter_popup');
            }
             else {
                $('body').removeClass('mob_filter_popup');
            }
            localStorage.setItem("mobFilterAdded", "true");
            localStorage.setItem("userEntered", "true");
        }
    }

    $('.close_mob_filter').on('click', function() {
        localStorage.setItem("OncemobFilterAdded", "true");
        $('body').removeClass('mob_filter_popup');
        localStorage.removeItem("mobFilterAdded");
        });
    });



    function getAllBookedAndLockedDates(carId){
      let car_id= carId;
       $.ajax({
            url: "{{ route('partner.getAllBookedAndLockedDates') }}",
            method: "post",
            data: {
                'carId': car_id,
                _token: '{{ csrf_token() }}',
            },
            dataType: "json",
            success: function (data) {
                if(data.success){
                    if(data.disableDates.length>0){
                        // console.log('disable dates from response :',data.disableDates);
                        // console.log('response length:',data.disableDates.length);
                        for(let i=0;i<data.disableDates.length;i++){
                            // allDisableDates[i]=data.disableDates[i];
                                allDisableDates.push(data.disableDates[i]);
                            // forCheckDates[i]=data.disableDates[i];
                            // console.log('allDisableDates:',i,data.disableDates[i]);
                        }
                        // data.disableDates.forEach(element => {
                        //     allDisableDates.push(element);
                        // });
                        //  allDisableDates.push(data.disableDates);
                        $(".datepicker_input" ).datepicker("refresh");
                        $(".datepicker_input2" ).datepicker("refresh");
                    }
                }
            },
            complete: function (data) {
                $(".loader").css("display", "none");
                $(".overlay_sections").css("display", "none");
            }
        });

    }

    function getAllBookedAndLockedByCarId(carId){
        let car_id=carId;
        let bookedAndLockedDates = [];
        const currentUl = $('.list').filter(function() {
            return $(this).data('car-id') == car_id;
        });
        currentUl.find('li').each(function(e){
            if( $(this).find('.links ').hasClass('booked')|| $(this).find('.links ').hasClass('locked')){
                let date = $(this).data('date-full_date');
                bookedAndLockedDates.push(date);
            }
        });
        return bookedAndLockedDates;
    }

    datesHaveToDelete=[];

 // select dates for calender

 function selectedClear() {
    $('#pickup_location').val('');
    var other_pickup_location = $('#other_pickup_location');
    $('.pickup_required').closest('.inp_container').find('.required').hide().empty();
    $('#select2-pickup_location-container').html('Select pickup location');

    if (other_pickup_location.hasClass('pickup_required')) {
        $('#pickup_location').addClass('pickup_required');
        $('#other_pickup_location').removeClass('pickup_required');
        $('#other_pickup_location').closest('.inp_container').find('.required').hide().empty();
        $('#pickup_location').val('');
        $('.other_pickup_location_container').css('display', 'none');
    }

    $('#dropoff_location').val('');
    var other_dropoff_location = $('#other_dropoff_location');
    $('.dropoff_required').closest('.inp_container').find('.required').hide().empty();
    $('#select2-dropoff_location-container').html('Select dropoff location');

    if (other_dropoff_location.hasClass('dropoff_required')) {
        $('#dropoff_location').addClass('dropoff_required');
        $('#other_dropoff_location').removeClass('dropoff_required');
        $('#other_dropoff_location').closest('.inp_container').find('.required').hide().empty();
        $('#dropoff_location').val('');
        $('.other_dropoff_location_container').css('display', 'none');
    }
}


    $('body').on('click','.links, .edit_pickupdatesnd_dropoffdates, #new_booking_cta',function(e) {
        e.preventDefault();
        if($(this).hasClass('links') || $(this).is('#new_booking_cta')) {
            selectedClear();
        }
        let isNewBookingClicked=false;
        let car_id= $(this).closest("li").data("car-id");
        let isDatesEditClicked=$(this).hasClass('edit_pickupdatesnd_dropoffdates');
        let isPinkishBgClicked = $(this).find('.pinkishBg').length > 0;

        //return false if isPinkishBgClicked (now its working through #new_booking_cta btn)
         isNewBookingClicked=$(this).attr('id') === 'new_booking_cta';

        // console.log('isNewBookingClicked',isNewBookingClicked);
        if(isPinkishBgClicked){
        console.log('pinkish');
        return false;
      }
     if(isDatesEditClicked){
        let pickupSelectedDate = $.datepicker.formatDate('dd/mm/yy', new Date($('#datepicker_first_date').val()));
        let dropoffSelectedDate = $.datepicker.formatDate('dd/mm/yy', new Date($('#datepicker_last_date').val()));
        let pickupSelectedTime=$('#datepicker_pickup_time').val();
        $('.datepicker_input').datepicker('setDate', pickupSelectedDate);
        $('#fancyBox_action_type').val('edit_date');
        $.fancybox.open({
            src: $(this).data('src'),
            beforeShow: function(instance, current){

                const fancybox = this;
                $('.datepicker_input', current.$content).datepicker('setDate', pickupSelectedDate);
                $('.fare_info_input').siblings('.dapicker_val__pickup_text').text( convertdates($('#datepicker_first_date').val())+'| '+ pickupSelectedTime);
                var date2 = moment(pickupSelectedDate, "DD.MM.YYYY");
                var gap_day = addDays(date2, day_gap);
                $(".datepicker_input2", current.$content).datepicker('option', 'minDate', gap_day);
                if ($(".datepicker_input", current.$content).val() < $(".datepicker_input2", current.$content).val()){
                    $('.datepicker_input2', current.$content).datepicker('setDate', $('.datepicker_input2', current.$content).val());
                } else {
                    $('.datepicker_input2', current.$content).datepicker('setDate', gap_day);
                }
              }
            });
        select_pickup();
     } else if(isPinkishBgClicked || isNewBookingClicked)
      {


           $('.car_status_container_inner').removeClass('selectedRange');
            allDisableDates=[];
            // console.log('links:',allDisableDates);
            $('.booking_new_content').remove();
            let dayMinutes=1440;
            let gapMinutes=1*60;
            let checkMinutes= dayMinutes-gapMinutes;
            // let drop_off_time= $(this).data('dropofftime');
            let drop_off_time= $('#details_showcase_popup_dropoffTime').val();
            let momentObj = moment(drop_off_time, "hh:mm A");

            // Get the overlap day total minutes
            let o_d_totalMinutes = momentObj.hours() * 60 + momentObj.minutes();

            let valid_time=o_d_totalMinutes+60;

            $('#fancyBox_overlappedPickup_time').val()

            // Convert total minutes back to hours and minutes
            let valid_hours = Math.floor(valid_time / 60);
            let valid_minutes = valid_time % 60;

            // Determine AM or PM
            let am_pm = valid_hours >= 12 ? 'PM' : 'AM';
            valid_hours = valid_hours % 12 || 12; // Convert to 12-hour format

            // added preceding 0 when hours is single digit
            valid_hours = (valid_hours.toString().length == 1) ? '0' + valid_hours : valid_hours;
            valid_minutes = (valid_minutes.toString().length == 1) ? '0' + valid_minutes : valid_minutes;

            // console.log("Valid Time:", valid_hours, ":", valid_minutes, am_pm);

            $('.datepicker_inner_b').find(".time_hour").val(valid_hours);
                $('.datepicker_inner_b').find(".time_min").val(valid_minutes) ;
                $('.datepicker_inner_b').find(".time_am_pm").val(am_pm);


                // for setting the dropoff time default one

                $('.datepicker_inner_b2').find(".time_hour").val('09');
                $('.datepicker_inner_b2').find(".time_min").val('00');
                $('.datepicker_inner_b2').find(".time_am_pm").val('AM');

            //  store time in fancyBox_overlapped_pickupTime for comparison (overlap dates)
           $('#fancyBox_overlapped_pickupTime').val( $('.datepicker_inner_b').find(".time_hour").val() + ":" +
            $('.datepicker_inner_b').find(".time_min").val() + " " +
            $('.datepicker_inner_b').find(".time_am_pm").val());

            // check if the day contains enough time/hour to booking
            if(checkMinutes>o_d_totalMinutes){

                $(".loader").css("display", "inline-flex");
                $(".overlay_sections").css("display", "block");

                removeSelectedClass();
            removeAddandSelectedDatePopup();

            // $(this
            const liElement = $(this).closest("li");
            // const fullDate = liElement.data("date-full_date");
            const fullDate = $('#details_showcase_popup_fullDate').val();
            // const carId = liElement.data("car-id");
            const carId= $('#details_showcase_popup_carId').val();

            getAllBookedAndLockedDates(carId);
            $('#fancyBox_car_id').val(carId);
            $('#fancyBox_overlap_previous_pickup_time').val($('#details_showcase_popup_dropoffTime').val());
            $('#fancyBox_action_type').val('overlapped_date');
            let formattedDate = $.datepicker.formatDate('dd/mm/yy', new Date(fullDate));
            $('.selected_date').val(formattedDate);
            $('.datepicker_input').datepicker('setDate', null);
            $('.datepicker_input2').datepicker('setDate', null);
            $.fancybox.open({
                touch: false,
                drag: false,
                src: $(this).data('src'),
                beforeShow: function(instance, current){
                 const fancybox = this;

                $('.fare_info_input').siblings('.dapicker_val_text').text('dd/mm/yyyy | hh:mm');
                $('.datepicker_input', current.$content).datepicker('setDate', formattedDate);
                $('#fancyBox_car_id').val(carId);

                var date2 = moment(formattedDate, "DD.MM.YYYY");
                var gap_day = addDays(date2, day_gap);
                let convertedGapDates =moment(gap_day).format("YYYY-MM-DD HH:mm:ss");
                let clickedDate = moment(formattedDate, "DD/MM/YYYY").format("DD MMMM YYYY");

                $(".datepicker_input2", current.$content).datepicker('option', 'minDate', gap_day);
                if ($(".datepicker_input", current.$content).val() < $(".datepicker_input2", current.$content).val()){
                    $('.datepicker_input2', current.$content).datepicker('setDate', $('.datepicker_input2', current.$content).val());
                } else {
                    $('.datepicker_input2', current.$content).datepicker('setDate', gap_day);
                }
                }
                });

            select_pickup();

            }else{

                Toastify({
				text: "Car isn't available on this date please select next date",
				duration: 1000,
				close: true,
				closeOnClick: true,
				gravity: "bottom",
				position: "right",
			}).showToast();

            }



     }
     else{

        // console.warn('under the else part');

        // let isPinkishBgClicked= $(this).find('.car_status_container_inner').hasClass('pinkishBg'); if(!isPinkishBgClicked){

        $('#pickup_date').val('');
        isBookingCancelBtn= false;
        // && $(this).hasClass('booked')
        if (!$(this).hasClass('booked') && !$(this).hasClass('locked')  ){

            $(".loader").css("display", "inline-flex");
            $(".overlay_sections").css("display", "block");
            $('.car_status_container_inner').removeClass('selectedRange');
            allDisableDates=[];
            // console.log('links:',allDisableDates);
            $('.booking_new_content').remove();
            removeSelectedClass();
            removeAddandSelectedDatePopup();
            const liElement = $(this).closest("li");
            const fullDate = liElement.data("date-full_date");
            const carId = liElement.data("car-id");
            getAllBookedAndLockedDates(carId);
            $('#fancyBox_car_id').val(carId);
            $('#fancyBox_action_type').val('normal_date');
            let formattedDate = $.datepicker.formatDate('dd/mm/yy', new Date(fullDate));
            $('.selected_date').val(formattedDate);
            $('.datepicker_input').datepicker('setDate', null);
            $('.datepicker_input2').datepicker('setDate', null);
            $.fancybox.open({
                src: $(this).data('src'),
                touch: false,
                drag: false,
                beforeShow: function(instance, current){
                 const fancybox = this;
                $('.fare_info_input').siblings('.dapicker_val_text').text('dd/mm/yyyy | hh:mm');
                $('.datepicker_input', current.$content).datepicker('setDate', formattedDate);
                $('#fancyBox_car_id').val(carId);
                var date2 = moment(formattedDate, "DD.MM.YYYY");
                var gap_day = addDays(date2, day_gap);
                let convertedGapDates =moment(gap_day).format("YYYY-MM-DD HH:mm:ss");
                let clickedDate = moment(formattedDate, "DD/MM/YYYY").format("DD MMMM YYYY");

                // for setting the dropoff time default one

                $('.datepicker_inner_b2').find(".time_hour").val('09');
                $('.datepicker_inner_b2').find(".time_min").val('00');
                $('.datepicker_inner_b2').find(".time_am_pm").val('AM');

                $(".datepicker_input2", current.$content).datepicker('option', 'minDate', gap_day);
                if ($(".datepicker_input", current.$content).val() < $(".datepicker_input2", current.$content).val()){
                    $('.datepicker_input2', current.$content).datepicker('setDate', $('.datepicker_input2', current.$content).val());
                } else {
                    $('.datepicker_input2', current.$content).datepicker('setDate', gap_day);
                }
                }
                });
            select_pickup();
        }

     }


    });





    function openAddandSelectedDatePopup(){
        $('body').addClass('selected_pop_bl');
    }

    function removeAddandSelectedDatePopup(){
        $('body').removeClass('selected_pop_bl');
        $('.selected_dates_popup').hide();
    }

    $('.cancel_mobile_filter').on('click',function(e){
        e.preventDefault();
        $('body').removeClass('mob_filter_popup');
    });


    // var day_gap = 1;
    // removed the gap
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
	$('.tab_item_pickup').on('click touchstart', function() {
		select_pickup();
	});

	$('.tab_item_drop').on('click touchstart', function(e) {
        // console.log('under click')
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
        var checkLog = checkCondtionForLocation();
        if(checkLog){
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
	}

    function removeExtraFromDateObject(date){
        const slicedDate = date.slice(0, 10);
        return slicedDate;
    }

    function removeSelectedClass() {

        // for removing the selectedclass which is added before
        if($('.selected').find('.car_status_container_inner').hasClass('overlap_right_greenSqBg')){
              $('.selected').find('.car_status_container_inner.overlap_right_greenSqBg').html('');
              $('.selected').find('.car_status_container_inner').removeClass('overlap_right_greenSqBg');

        }

        $('.clickable').find('.links').removeClass('selected');
        $('body').find('.activeDate .range_active').removeClass('active');
        $('body').find('.activeDate .range_active').removeClass('left_arc right_arc');
    }

    function areDatesAvailable(elements, startDate, endDate) {
        let flag = true;
        elements.find('li').each(function() {
            const liDate = $(this).data('date-full_date');
            if (liDate >= startDate && liDate <= endDate) {
                const anchor = $(this).find('a.links');

                if(anchor.find('.car_status_container_inner ').hasClass('pinkishBg')){
                    flag=true;
                    return false;
                }
                else if (anchor.hasClass('booked') || anchor.hasClass('locked')) {
                    flag = false;
                    return false;
                }
            }
        });
     return flag;
    }


    $(document).ready(function () {
        var allBookedDatesArr=[];
        // const allBookedDates = getAllBookedDates();
        handleBookedDateRangeConditions();
        handleLockedDateRangeConditions();
    });

    function getBookedDatesByCarid(carId) {
        const currentUl = $('.list').filter(function() {
             return $(this).data('car-id') == carId;
        });
        const bookedDates = currentUl.find('.booked').map(function() {
            return $(this).closest('li').data('date-full_date');
        }).get();
        return bookedDates;
    }

       function getAllBookedDates() {

        const carIds = $('.selectable').map(function() {
            if($(this).find('.links').hasClass('booked')){
                return $(this).data('car-id');
            }
        }).get();

        const bookedDatesByCar = [];

        carIds.forEach(function(carId) {
            const bookedDates = getBookedDatesByCarid(carId);
            bookedDatesByCar.push({ carId, bookedDates });
        });

        // console.log('bookedDatesByCar:',bookedDatesByCar);

        return bookedDatesByCar;
     }

    function convertfirstLetterToCapitalize(string){
        const letter =string;
        return letter[0].toUpperCase() + letter.substring(1);
    }

    function convertDateIntoObject(dateTime){
        const formatString = "YYYY-MM-DD hh:mm A";

        const dateObject = moment(dateTime, formatString).toDate();
        return dateObject;
    }

    function bookedDatesDiff(startDate,EndDate){
        const bookedstartDate = moment(startDate);
        const bookedEndDate = moment(EndDate);
        return bookedEndDate.diff(bookedstartDate, 'days');
    }

    function makeRangeForBookedDates(carId,firstDate,secondDate){
        // console.warn('under make range');
        var isCosecutiveSame=false;
        // console.log('firstDate:',firstDate,'lastDate:',secondDate);
         if(firstDate && secondDate){
         let startDate= firstDate; let endDate=secondDate;
            const currentUl = $('.list').filter(function() {
                return $(this).data('car-id') == carId;
            });

        // console.warn('under currentUl:',currentUl);

            let diffBetweenDates;
            const totalLiElements = currentUl.find('li').length;
            const liElements=currentUl.find('li');
            let firstIndex = -1;
            let lastIndex = -1;
            var rangeCount=0;


            currentUl.find('li').each(function(index) {
                const liDate = $(this).data('date-full_date');
                if (liDate >= startDate && liDate <= endDate ) {
                    // console.log('booking_id:', $(this).data('bookingId'));

                    // console.log('index:',index);
                    if (firstIndex === -1 ) {
                            firstIndex = index;
                    }

                    lastIndex = index;

                    let linksElement = $(this).find('.links');
                    let PickupTime = linksElement.first().data('pickuptime');
                    let DropoffTime = linksElement.first().data('dropofftime');

                    // console.log('pickupTime:',PickupTime,'DropoffTime:',DropoffTime);


                    let rangeActiveElement =  $(this).find('.range_active');
                    linksElement.addClass('selected');

                    rangeActiveElement.addClass('active');

                    // unformatted booking start and ending dates

                    let bookingStartDate=$(this).find('.links').data('booked-startdate');
                    let bookingEndDate=$(this).find('.links').data('booked-enddate');

                    let liBookingStartDate=bookingStartDate;
                    let liBookingEndDate=bookingEndDate;

                    // console.log('booked start date:',bookingStartDate,'booked end',bookingEndDate);

                    //  parsing dates
                    let parsedStartDate = moment(bookingStartDate, "YYYY-MM-DD HH:mm:ss");
                    let parsedEndDate = moment(bookingEndDate, "YYYY-MM-DD HH:mm:ss");


                    // console.log('pickupDateTime:',pickupDateTime,'dateObject:',dateObject);
                    bookingStartDate = parsedStartDate.format("YYYY-MM-DD");
                    bookingEndDate = parsedEndDate.format("YYYY-MM-DD");

                    const pickupDateTime=bookingStartDate+' '+PickupTime;

                    const dropoffDateTime=bookingEndDate+' '+DropoffTime;


                    let bookingPickup=convertDateIntoObject(pickupDateTime);
                    let bookingDropoff=convertDateIntoObject(dropoffDateTime);


                    let overlapDateActive=$(this).find('.overlap_booking').length>0;
                    // console.log('overlapDateActive:',overlapDateActive,'bookingStartDate:',bookingStartDate,'bookingEndDate',bookingEndDate);

                               diffBetweenDates= diffrenceInDates(bookingStartDate,bookingEndDate,PickupTime,DropoffTime);



                               var calender_start_date = currentUl.find('li:first').data('date-full_date');

                               var calender_end_date = currentUl.find('li:last').data('date-full_date');

                            //    console.log('bookingStartDate:',bookingStartDate,'bookingEndDate:',bookingEndDate,'startdate:',startDate,'enddate:',endDate);

                               if(bookingStartDate!=bookingEndDate ){
                                    // console.log('normal one',bookingStartDate,bookingEndDate);

                                    if(liDate == bookingStartDate)
                                    {
                                        rangeCount++;
                                        if(rangeCount>1){
                                            firstIndex = index;
                                        }

                                        let pickupMonth= convertfirstLetterToCapitalize(rangeActiveElement.closest('.clickable').data('date-month'));
                                        rangeActiveElement.addClass('left_arc');
                                        rangeActiveElement.find('.car_status_container_inner').addClass('NyanzaBg');
                                            // if(liDate === startDate){

                                                    let rangeBookingStartDate=bookingStartDate;
                                                    let rangeBookingEndDate=bookingEndDate;

                                                    // console.log('bookingEndDate',bookingEndDate,'bookingStartDate',bookingStartDate, 'calender_end_date:',calender_end_date);


                                                if(diffBetweenDates>=3){
                                                    const bookingDiff = bookedDatesDiff(bookingStartDate,calender_end_date);



                                                    if(bookingDiff==0){

                                                        rangeActiveElement.closest('.clickable').append('<div class=" bookingDetails bookingDetails1 absolute z-2 left-[5px] top-[47px] text-xs ">' +
                                                        '<p class="text-[#808080] text-left md:text-sm sm:text-xs"> <span class="hidden">Start</span>' +'&nbsp'+
                                                        rangeActiveElement.closest('.clickable').data('date-day') +'&nbsp'+
                                                        pickupMonth +
                                                        '</p> <p class="bookedPickupTime text-left w-[45px] md:text-sm md:break-normal whitespace-normal leading-none md:text-sm  md:w-full md:break-normal sm:text-xs uppercase sm:w-full">'+PickupTime+'</p></div>');


                                                    }else{

                                                        rangeActiveElement.closest('.clickable').append('<div class=" bookingDetails bookingDetails2 absolute z-2 left-[5px] text-xs top-[47px] ">' +
                                                        '<p class="text-[#808080] text-left text-xs"> <span class="md:hidden">Start</span>' +'&nbsp'+
                                                        rangeActiveElement.closest('.clickable').data('date-day') +'&nbsp'+
                                                        pickupMonth +
                                                        '</p> <p class="bookedPickupTime text-left text-xs uppercase">'+PickupTime+'</p></div>');

                                                    }


                                                    if( bookingEndDate > calender_end_date){

                                                            // console.log('bookingEndDate is greater than');

                                                            const bookingDiff = bookedDatesDiff(bookingStartDate,calender_end_date);

                                                    }

                                                    let forRangeBookingEndDate = moment(rangeBookingEndDate, "YYYY-MM-DD HH:mm:ss");

                                                    let parsedforRangeBookingEndDate = forRangeBookingEndDate.format("YYYY-MM-DD");


                                                    let momentRangeBookingEndDate=moment(parsedforRangeBookingEndDate);

                                                }

                                                if(diffBetweenDates==2){

                                                    const bookedDateDiff= bookedDatesDiff(bookingStartDate,bookingEndDate);
                                                    //   console.log('bookedDateDiff',bookedDateDiff);
                                                    if(bookedDateDiff==1){
                                                        rangeActiveElement.closest('.clickable').append('<div class=" bookingDetails bookingDetails3 absolute z-2 left-[5px] top-[47px] ">' +
                                                        '<p class="text-[#808080] text-left text-xs"> <span class="hidden ">Start</span>' +'&nbsp'+
                                                        rangeActiveElement.closest('.clickable').data('date-day') +'&nbsp'+
                                                        pickupMonth +
                                                        '</p> <p class="bookedPickupTime text-left w-[45px]  whitespace-normal leading-none md:break-normal   md:w-full  text-xs sm:w-[42px] uppercase">'+PickupTime+'</p></div>');
                                                    }else{

                                                        rangeActiveElement.closest('.clickable').append('<div class=" bookingDetails bookingDetails4 text-xs absolute z-2 left-[5px] top-[47px] ">' +
                                                        '<p class="text-[#808080] text-left text-xs"> <span class="hidden ">Start</span>' +'&nbsp'+
                                                        rangeActiveElement.closest('.clickable').data('date-day') +'&nbsp'+
                                                        pickupMonth +
                                                        '</p> <p class="bookedPickupTime text-left text-xs uppercase">'+PickupTime+'</p></div>');

                                                    }


                                                }

                                                if(diffBetweenDates==1){

                                                    // console.log('start date',bookingStartDate,'endDate:',bookingEndDate);

                                                    // console.log('rangeActiveElement:',$(rangeActiveElement))



                                                    rangeActiveElement.closest('.clickable').append('<div class=" bookingDetails bookingDetails5 absolute z-2 left-[5px] top-[47px] text-xs ">' +
                                                        '<p class="text-[#808080] text-left text-xs"> <span class="hidden ">Start</span>'+
                                                        rangeActiveElement.closest('.clickable').data('date-day') +'&nbsp'+
                                                        pickupMonth +
                                                        '</p> <p class="bookedPickupTime text-left w-[45px] sm:w-[42px]  md:break-normal whitespace-normal leading-none md:break-normal text-xs uppercase">'+PickupTime+'</p></div>');

                                                }
                                    }

                                    if (liDate === bookingEndDate) {

                                        // checks if overlape date active or not
                                        if(!overlapDateActive){
                                            // this will check if booking startDate is lesser than current calander start date
                                            rangeActiveElement.addClass('right_arc');
                                            rangeActiveElement.find('.car_status_container_inner').addClass('pinkishBg');

                                            // rangeActiveElement.closest('.links').data('src','#open_popup');
                                            // console.log('fancybox:',rangeActiveElement.closest('.links').data('src'));

                                            let dropoffMonth= convertfirstLetterToCapitalize(rangeActiveElement.closest('.clickable').data('date-month'));

                                                if(diffBetweenDates>=3){

                                                    const bookingDiff = bookedDatesDiff(calender_start_date,bookingEndDate);

                                                        if(bookingDiff==0){

                                                            rangeActiveElement.closest('.clickable').append('<div class="absolute z-2 right-[5px] top-[47px] bookingDetails bookingDetails6 text-xs"><p class="text-[#808080] text-right text-xs"> <span class="hidden">End</span>'+'&nbsp'+rangeActiveElement.closest('.clickable').data('date-day')+'&nbsp'+ dropoffMonth +' </p> <p class="bookedDropoffTime text-right  w-[45px] md:break-normal whitespace-normal leading-none md:w-[full] text-xs uppercase">'+DropoffTime+' </p></div>');
                                                        }
                                                        else{

                                                                rangeActiveElement.closest('.clickable').append('<div class="absolute right-[5px] z-2 top-[47px] bookingDetails bookingDetails7 text-xs"><p class="text-[#808080] text-right text-xs"> <span class="md:hidden">End</span>'+'&nbsp'+rangeActiveElement.closest('.clickable').data('date-day')+'&nbsp'+ dropoffMonth +' </p> <p class="bookedDropoffTime text-right text-xs uppercase">'+DropoffTime+' </p></div>');
                                                        }

                                                }
                                                if(diffBetweenDates==2){

                                                        const bookedDateDiff= bookedDatesDiff(bookingStartDate,bookingEndDate);

                                                        if(bookedDateDiff==1){

                                                        rangeActiveElement.closest('.clickable').append('<div class="absolute right-[5px] z-2 top-[47px] bookingDetails bookingDetails8  text-xs"><p class="text-[#808080] text-right text-xs"> <span class="hidden">End</span>'+'&nbsp'+rangeActiveElement.closest('.clickable').data('date-day')+'&nbsp'+ dropoffMonth +' </p> <p class="bookedDropoffTime text-right  w-[45px] md:break-normal whitespace-normal leading-none  md:w-full md:break-normal text-xs sm:w-[42px] uppercase">'+DropoffTime+' </p></div>');

                                                    }else{


                                                        const bookingDiff = bookedDatesDiff(calender_start_date,bookingEndDate);

                                                        if(bookingDiff==0){

                                                            rangeActiveElement.closest('.clickable').append('<div class="absolute right-[5px] z-2 top-[47px] bookingDetails bookingDetails9 text-xs"><p class="text-[#808080] text-right text-xs"> <span class="hidden">End</span>'+'&nbsp'+rangeActiveElement.closest('.clickable').data('date-day')+'&nbsp'+ dropoffMonth +' </p> <p class="bookedDropoffTime text-right  w-[45px]  md:break-normal whitespace-normal leading-none text-xs  md:w-full sm:w-[42px] md:break-normal  uppercase">'+DropoffTime+' </p></div>');

                                                        }else{
                                                            rangeActiveElement.closest('.clickable').append('<div class="absolute right-[5px] z-2 top-[47px] bookingDetails bookingDetails10 text-xs"><p class="text-[#808080] text-right text-xs"> <span class="hidden">End</span>'+'&nbsp'+rangeActiveElement.closest('.clickable').data('date-day')+'&nbsp'+ dropoffMonth +' </p> <p class="bookedDropoffTime text-right text-xs uppercase">'+DropoffTime+' </p></div>');
                                                        }


                                                    }

                                                }

                                                if(diffBetweenDates==1){
                                                    rangeActiveElement.closest('.clickable').append('<div class="absolute right-[5px] z-2 top-[47px] bookingDetails bookingDetails11 text-xs"><p class="text-[#808080] text-right text-xs"> <span class="hidden">End</span>'+'&nbsp'+rangeActiveElement.closest('.clickable').data('date-day')+'&nbsp'+ dropoffMonth +' </p> <p class="bookedDropoffTime text-right whitespace-normal w-[45px] text-xs md:break-normal whitespace-normal sm:w-[42px] leading-none  text-xs uppercase ">'+DropoffTime+' </p></div>');
                                                }

                                                // for showing difference days

                                                let BookedFirstIndex= liElements.filter(function () {
                                                                    return $(this).data('date-full_date') === bookingStartDate;
                                                                }).index();
                                                         let BookedLastIndex= liElements.filter(function () {
                                                                    return $(this).data('date-full_date') === bookingEndDate;
                                                                }).index();


                                                     const middleIndex = Math.floor((BookedFirstIndex + BookedLastIndex) / 2)

                                                        const middleElement = liElements.eq(middleIndex);

                                                        const  anchor= middleElement.find('.links');

                                                if(diffBetweenDates==1 ){
                                                    if(firstIndex !== -1 && lastIndex !== -1){
                                                        const middleIndex = Math.floor((firstIndex + lastIndex) / 2);
                                                        const middleElement = liElements.eq(middleIndex);

                                                        // console.log('under single dates:',(!rangeActiveElement.children('.days_showcase').length > 0));

                                                        if(!rangeActiveElement.children('.days_showcase').length > 0){

                                                            rangeActiveElement.prepend('<div class="absolute days_showcase w-max right-0 md:text-[14px] md:font-normal z-[2] text-white -left-[7px] sm:top-3  md:-left-[5px] top-[10px] sm:text-xs ">' + diffBetweenDates + 'D</div>');
                                                        }

                                                    }

                                                }


                                                if(diffBetweenDates==2){
                                                        // console.warn('bookingStartDate',bookingStartDate,'bookingEndDate',bookingEndDate,'overlapStartDate:',overlapPickupDate,'overdropoffdate:',overlapDropoffDate,'overlapDiffBetweenDates:',overlapDiffBetweenDates,'overlapDiffBookedDates:',overlapDiffBookedDates);

                                                        if(firstIndex !== -1 && lastIndex !== -1){

                                                            if(bookingStartDate<calender_start_date){
                                                                const bookingDiff = bookedDatesDiff(calender_start_date,bookingEndDate);

                                                                if(bookingDiff>0){

                                                                    if(!anchor.find('.range_active').children('.days_showcase').length > 0){

                                                                        anchor.find('.range_active').prepend('<div class=" absolute  days_showcase w-[75px]  md:text-[14px] z-[2] text-white right-0 left-0 top-[10px] md:top-3  sm:text-xs">' + diffBetweenDates + ' Days</div>');

                                                                    }

                                                                    }

                                                            }
                                                            else
                                                            {

                                                            const bookedDateDiff= bookedDatesDiff(bookingStartDate,bookingEndDate);

                                                            if(bookedDateDiff==1){

                                                                if(!anchor.find('.range_active').children('.days_showcase').length > 0){

                                                            anchor.find('.range_active').prepend('<div class="absolute   days_showcase w-max md:text-[14px] z-[2] text-white -right-2  md:top-3   top-[10px] sm:text-xs">' + diffBetweenDates + 'D</div>');
                                                                }

                                                            }
                                                            else
                                                            {
                                                                if(!anchor.find('.range_active').children('.days_showcase').length > 0){

                                                                anchor.find('.range_active').prepend('<div class="absolute days_showcase w-[76px] md:text-[14px] z-[2] text-white right-0 left-0 md:top-3   top-[10px] sm:text-xs">' + diffBetweenDates + ' Days</div>');

                                                                }

                                                            }


                                                            }


                                                        }
                                                }

                                                if(diffBetweenDates==3){

                                                        if (firstIndex !== -1 && lastIndex !== -1) {

                                                            if(bookingStartDate<calender_start_date){
                                                                const bookingDiff = bookedDatesDiff(calender_start_date,bookingEndDate);

                                                                if(bookingDiff>0){

                                                                    if(!anchor.find('.range_active').children('.days_showcase').length > 0){


                                                                        anchor.find('.range_active').prepend('<div class=" absolute days_showcase w-[65px]   md:text-[14px] z-[2] text-white right-0 left-0 top-[10px] md:top-3  sm:text-xs">' + diffBetweenDates + ' Days</div>');
                                                                    }

                                                                }
                                                            }
                                                            else
                                                            {

                                                                const bookingDiff = bookedDatesDiff(calender_start_date, bookingEndDate);

                                                                    // this will check if
                                                                    if(bookingStartDate==bookingStartDate && liElements.eq(firstIndex).data('date-full_date')!=bookingStartDate){

                                                                            if(!anchor.find('.range_active').children('.days_showcase').length > 0){

                                                                                anchor.find('.range_active').prepend('<div class=" absolute days_showcase w-[110px]  md:text-[14px] z-[2] text-white right-0 left-0 top-[10px] md:top-3  sm:text-xs">' + diffBetweenDates + ' Days</div>');

                                                                            }

                                                                    }
                                                                    else
                                                                    {

                                                                        const bookingDiff = bookedDatesDiff(bookingStartDate, bookingEndDate);

                                                                    // console.warn('check:','bookingDiff:',bookingDiff,'anchor:',anchor);



                                                                        // console.warn('under ');



                                                                        if(bookingDiff==1 || bookingDiff==0 ){

                                                                             if (!anchor.find('.range_active').children('.days_showcase').length > 0) {
                                                                                // console.warn('current anchor:',anchor);
                                                                                anchor.find('.range_active').prepend('<div class=" absolute w-max days_showcase  md:text-[14px] z-[2] text-white -right-2 sm:-right-[6px]  top-[10px] md:top-3  sm:text-xs">' + diffBetweenDates + 'D</div>');
                                                                              }

                                                                        }else{

                                                                            if(!anchor.find('.range_active').children('.days_showcase').length > 0){
                                                                            anchor.find('.range_active').prepend('<div class=" absolute w-[90px] days_showcase md:text-[14px] z-[2] text-white right-0 left-0 top-[10px] md:top-3  sm:text-xs">' + diffBetweenDates + ' Days</div>');
                                                                            }

                                                                        }


                                                                    }

                                                            }

                                                        }
                                                }

                                               if(diffBetweenDates>=4){

                                                        if (firstIndex !== -1 && lastIndex !== -1) {

                                                            if(bookingStartDate<calender_start_date){
                                                                const bookingDiff = bookedDatesDiff(calender_start_date,bookingEndDate);

                                                                if(bookingDiff>0){

                                                                    if(!anchor.find('.range_active').children('.days_showcase').length > 0){

                                                                        anchor.find('.range_active').prepend('<div class=" absolute days_showcase w-[90px]  md:text-[14px] z-[2] text-white right-0 left-[0px] md:top-3 top-[10px] sm:text-xs">' + diffBetweenDates + ' Days</div>');
                                                                    }

                                                                }

                                                            }

                                                            else{

                                                                let startDateIndex = liElements.filter(function() {
                                                                        return $(this).data('date-full_date')== bookingStartDate;
                                                                    }).index();

                                                                    let totalIndex= startDateIndex+rangeActiveElement.closest('.clickable').index();

                                                                    if(totalIndex % 2!=0){
                                                                        totalIndex++;
                                                                    }

                                                                let middleIndex = Math.floor(totalIndex / 2);


                                                                    const middleElement = liElements.eq(middleIndex);
                                                                    const  anchor= middleElement.find('.links');
                                                                    if(!anchor.find('.range_active').children('.days_showcase').length > 0){

                                                                        anchor.find('.range_active').prepend('<div class=" absolute days_showcase w-max  md:text-[14px] z-[2] text-white right-0 left-[0px] md:top-3 top-[10px] sm:text-xs">' + diffBetweenDates + ' Days</div>');

                                                                    }

                                                            }


                                                        }
                                                    }




                                        }
                                        else
                                        {

                                            // console.log('overlap dates');

                                            let dropoffMonth= convertfirstLetterToCapitalize(rangeActiveElement.closest('.clickable').data('date-month'));
                                            let DropoffTime = linksElement.first().data('dropofftime');

                                            let overlapDropoffTime = linksElement.first().data('overlap-dropofftime');

                                            let overlapPickupTime = linksElement.first().data('overlap-pickuptime');
                                            let overlapDropoffDate = linksElement.first().data('overlap-dropoffdate');
                                            let overlapPickupDate = linksElement.first().data('overlap-pickupdate');


                                            // console.log('overlaped  booked start date:',overlapPickupDate,'booked end',overlapDropoffDate,'linksElement.first():',linksElement.first());

                                            //  parsing dates
                                            let parsedStartDate = moment(overlapPickupDate, "YYYY-MM-DD HH:mm:ss");
                                            let parsedEndDate = moment(overlapDropoffDate, "YYYY-MM-DD HH:mm:ss");


                                            // console.log('pickupDateTime:',pickupDateTime,'dateObject:',dateObject);
                                            overlapPickupDate = parsedStartDate.format("YYYY-MM-DD");
                                            overlapDropoffDate = parsedEndDate.format("YYYY-MM-DD");


                                            const pickupDateTime=overlapPickupDate+' '+overlapPickupTime;

                                            const dropoffDateTime=overlapDropoffDate+' '+overlapDropoffTime;

                                            // console.log('overlapPickupDate:',overlapPickupDate,'overlapDropoffDate:',overlapDropoffDate,'overlapdropoffDateTime:',dropoffDateTime,'overlappickupDateTime:',pickupDateTime);


                                            let bookingPickup=convertDateIntoObject(pickupDateTime);
                                            let bookingDropoff=convertDateIntoObject(dropoffDateTime);


                                            let overlapDateActive=$(this).find('.overlap_booking').length>0;

                                             let overlapBookedFirstIndex= liElements.filter(function () {
                                                                    return $(this).data('date-full_date') === overlapPickupDate;
                                                                }).index();
                                             let overlapBookedLastIndex= liElements.filter(function () {
                                                                    return $(this).data('date-full_date') === overlapDropoffDate;
                                                                }).index();


                                             let BookedFirstIndex= liElements.filter(function () {
                                                                    return $(this).data('date-full_date') === bookingStartDate;
                                                                }).index();
                                             let BookedLastIndex= liElements.filter(function () {
                                                                    return $(this).data('date-full_date') === bookingEndDate;
                                                                }).index();



                                            let overlapDiffBetweenDates= diffrenceInDates(bookingStartDate,bookingEndDate,PickupTime,DropoffTime);

                                            let overlapBetweenDays=diffrenceInDates(overlapPickupDate,overlapDropoffDate,overlapPickupDate,overlapDropoffTime);

                                            //   console.warn('overlapBookedFirstIndex:',overlapBookedFirstIndex,'overlapBookedLastIndex:',overlapBookedLastIndex);

                                            const middleIndex = Math.floor((BookedFirstIndex + BookedLastIndex) / 2)

                                            const middleElement = liElements.eq(middleIndex);

                                            const  anchor= middleElement.find('.links');


                                            const overlapMiddleIndex= Math.floor((overlapBookedFirstIndex + overlapBookedLastIndex) / 2);
                                            const overlapMiddleElement = liElements.eq(overlapMiddleIndex);

                                            const  overlapAnchor= overlapMiddleElement.find('.links');


                                            let overlapDiffBookedDates = bookedDatesDiff(overlapPickupDate,overlapDropoffDate);




                                            // console.warn('overlapDiffBetweenDates:',overlapDiffBetweenDates,'overlapDiffBookedDates:',overlapDiffBookedDates);





                                            // console.log('overlapDiffBookedDates:',overlapDiffBookedDates,'overlapPickupDate:',overlapPickupDate,'overlapDropoffDate',overlapDropoffDate);




                                            if(overlapDiffBookedDates>=1){

                                            // console.log('when difference is >1');

                                            rangeActiveElement.find('.car_status_container_inner').html('<span class="inline-flex h-full overlapped_span  overlapped_left_part "></span><span class="inline-flex h-full overlapped_span  overlapped_right_part "></span>');

                                            // normal

                                            rangeActiveElement.closest('.clickable').append('<div class=" bookingDetails  bookingDetails12 absolute z-2 -left-[14px] top-[47px] text-sm text-right">' +
                                            '<p class="text-[#808080] text-right text-xs "> <span class="hidden">Start</span>' +'&nbsp'+
                                            rangeActiveElement.closest('.clickable').data('date-day') +'&nbsp'+dropoffMonth+
                                            '</p> <p class="bookedPickupTime text-right w-[45px] md:break-normal whitespace-normal leading-none  sm:w-[42px]  text-xs  uppercase">'+DropoffTime+'</p></div>');


                                            // overlapDate

                                            // console.warn('bookedStartDate:',bookingStartDate,'bookedEndDate:',bookingEndDate,'overlapPickupDate:',overlapPickupDate,'overlapDropoffDate:',overlapDropoffDate,'check:',(bookingEndDate<overlapDropoffDate));

                                            if(bookingEndDate<overlapDropoffDate){
                                                    rangeActiveElement.closest('.clickable').append('<div class=" overlapBookingDetails overlapBookingDetails1 overlap absolute z-2 top-[47px] -right-[13px]  text-xs">' +
                                                '<p class="text-[#808080] text-left text-xs"> <span class="hidden">Start</span>' +'&nbsp'+
                                                rangeActiveElement.closest('.clickable').data('date-day') +'&nbsp'+dropoffMonth+
                                                '</p> <p class="overlap_bookedPickupTime text-left w-[45px]  md:break-normal whitespace-normal leading-none sm:w-[42px]  md:break-normal text-xs uppercase ">'+overlapPickupTime+'</p><p class="overlap_bookedDropoffTime hidden text-left w-[45px]  md:break-normal whitespace-normal leading-none sm:w-[42px]  text-xs uppercase">'+overlapDropoffTime+'</p></div>');

                                              }
                                              else{

                                                rangeActiveElement.closest('.clickable').append('<div class=" overlapBookingDetails overlapBookingDetails2 absolute z-2 top-[47px] -right-[9px]  text-xs">' +
                                                '<p class="text-[#808080] text-left text-xs"> <span class="hidden">Start</span>' +'&nbsp'+
                                                rangeActiveElement.closest('.clickable').data('date-day') +'&nbsp'+dropoffMonth+
                                                '</p> <p class="overlap_bookedPickupTime text-left w-[45px]  md:break-normal whitespace-normal leading-none  md:break-normal text-xs uppercase">'+overlapPickupTime+'</p><p class="overlap_bookedDropoffTime text-left w-[45px]  md:break-normal whitespace-normal leading-none sm:w-[42px]  text-xs uppercase">'+overlapDropoffTime+'</p></div>');

                                            }


                                            }else if(overlapDiffBookedDates == 0){

                                                // console.log('when difference is 0',$(this));
                                            rangeActiveElement.addClass('right_arc');
                                            // rangeActiveElement.find('.car_status_container_inner').addClass('pinkishBg');

                                            rangeActiveElement.find('.car_status_container_inner').html('<span class="inline-flex h-full overlapped_span  overlapped_left_part"></span><span class="inline-flex h-full overlapped_span  overlapped_right_part "></span>');

                                            // normal
                                            rangeActiveElement.closest('.clickable').append('<div class=" bookingDetails bookingDetails13 absolute z-2 top-[47px] -left-[14px]  text-xs">' +
                                            '<p class="text-[#808080] text-left text-xs"> <span class="hidden">Start</span>' +'&nbsp'+
                                            rangeActiveElement.closest('.clickable').data('date-day') +'&nbsp'+dropoffMonth+
                                            '</p> <p class="bookedPickupTime text-left w-[45px]  md:break-normal whitespace-normal leading-none  sm:w-[42px] text-xs uppercase">'+DropoffTime+'</p></div>');

                                             // for showing difference of days

                                            //  const  anchor= middleElement.find('.links');
                                            // anchor.find('.range_active').prepend('<div class=" absolute w-[75px]  md:text-[14px] z-[2] text-white right-0 left-0 top-[10px] md:top-3  sm:text-xs">' + diffBetweenDates + ' Days</div>');

                                            // overlapDate

                                            rangeActiveElement.closest('.clickable').append('<div class=" overlapBookingDetails overlapBookingDetails3 absolute z-2 top-[47px] -right-[9px]  text-xs">' +
                                            '<p class="text-[#808080] text-left text-xs"> <span class="hidden">Start</span>' +'&nbsp'+
                                            rangeActiveElement.closest('.clickable').data('date-day') +'&nbsp'+dropoffMonth+
                                            '</p> <p class="overlap_bookedPickupTime text-left w-[45px]  md:break-normal whitespace-normal leading-none  md:break-normal text-xs uppercase sm:w-[42px]">'+overlapPickupTime+'</p><p class="overlap_bookedDropoffTime text-left w-[45px]  md:break-normal whitespace-normal leading-none text-xs sm:w-[42px] uppercase">'+overlapDropoffTime+'</p></div>');


                                            }

                                                    if(secondDate!=firstDate){
                                                        if(diffBetweenDates==1 ){

                                                            // overlapOneDayCount++;


                                                        //    console.warn('bookingStartDate',bookingStartDate,'bookingEndDate',bookingEndDate,'overlapStartDate:',overlapPickupDate,'overdropoffdate:',overlapDropoffDate,'overlapDiffBetweenDates:',overlapDiffBetweenDates,'overlapDiffBookedDates:',overlapDiffBookedDates);

                                                            if(firstIndex !== -1 && lastIndex !== -1){


                                                                    // console.warn("overlapCount",overlapCount);
                                                                    // if(overlapOneDayCount<2){


                                                                const middleIndex = Math.floor((firstIndex + lastIndex) / 2);
                                                                const middleElement = liElements.eq(middleIndex);

                                                                if(!rangeActiveElement.children('.days_showcase').length > 0){

                                                                    rangeActiveElement.prepend('<div class="absolute days_showcase w-max right-0 md:text-[14px] md:font-normal z-[2] text-white -left-[7px] sm:top-3  md:-left-[5px] top-[10px] sm:text-xs ">' + diffBetweenDates + 'D</div>');
                                                                }



                                                            }

                                                        }

                                                        if(overlapBetweenDays==1){

                                                             if(overlapPickupDate != overlapDropoffDate  ){

                                                                if(liElements.eq(overlapMiddleIndex+1).find('.overlapped_span').length>0){

                                                                    if(firstIndex !== -1 && lastIndex !== -1){

                                                                        if(!overlapAnchor.find('.range_active').children('.days_showcase').length > 0){

                                                                                overlapAnchor.find('.range_active').prepend('<div class="absolute days_showcase w-max -right-2 md:text-[14px] md:font-normal z-[2] text-white  sm:top-3  md:-right-[5px] top-[10px] sm:text-xs ">' + overlapBetweenDays + 'D</div>');
                                                                        }

                                                                    }

                                                                }
                                                             }


                                                        }
                                                    }



                                                    if(diffBetweenDates==2){
                                                        // console.warn('bookingStartDate',bookingStartDate,'bookingEndDate',bookingEndDate,'overlapStartDate:',overlapPickupDate,'overdropoffdate:',overlapDropoffDate,'overlapDiffBetweenDates:',overlapDiffBetweenDates,'overlapDiffBookedDates:',overlapDiffBookedDates);

                                                        if(firstIndex !== -1 && lastIndex !== -1){

                                                            if(bookingStartDate<calender_start_date){
                                                                const bookingDiff = bookedDatesDiff(calender_start_date,bookingEndDate);

                                                                if(bookingDiff>0){

                                                                    if(!anchor.find('.range_active').children('.days_showcase').length > 0){

                                                                        anchor.find('.range_active').prepend('<div class=" absolute days_showcase w-[75px]  md:text-[14px] z-[2] text-white right-0 left-0 top-[10px] md:top-3  sm:text-xs">' + diffBetweenDates + ' Days</div>');

                                                                    }

                                                                    }

                                                            }
                                                            else
                                                            {

                                                            const bookedDateDiff= bookedDatesDiff(bookingStartDate,bookingEndDate);

                                                            if(bookedDateDiff==1){
                                                                if(!anchor.find('.range_active').children('.days_showcase').length > 0){

                                                            anchor.find('.range_active').prepend('<div class="absolute days_showcase w-max md:text-[14px] z-[2] text-white -right-2  md:top-3   top-[10px] sm:text-xs">' + diffBetweenDates + 'D</div>');
                                                                }

                                                            }
                                                            else
                                                            {
                                                                if(!anchor.find('.range_active').children('.days_showcase').length > 0){

                                                                anchor.find('.range_active').prepend('<div class="absolute days_showcase w-[76px] md:text-[14px] z-[2] text-white right-0 left-0 md:top-3   top-[10px] sm:text-xs">' + diffBetweenDates + ' Days</div>');

                                                                }

                                                            }


                                                            }


                                                        }
                                                    }


                                                    if(overlapBetweenDays==2){
                                                        if(firstIndex !== -1 && lastIndex !== -1){
                                                            // console.warn('overlapDiffBetweenDates==2','middleIndex',overlapMiddleElement,'match:',(overlapPickupDate<calender_start_date),'overlapPickupDate:',overlapPickupDate,'calender_start_date:',calender_start_date);


                                                            if(overlapPickupDate<calender_start_date){
                                                                const bookingDiff = bookedDatesDiff(calender_start_date,overlapDropoffDate);

                                                                if(bookingDiff>0){

                                                                    if(!overlapAnchor.find('.range_active').children('.days_showcase').length > 0){
                                                                    overlapAnchor.find('.range_active').prepend('<div class=" absolute days_showcase  w-[75px]  md:text-[14px] z-[2] text-white right-0 left-0 top-[10px] md:top-3  sm:text-xs">' + overlapBetweenDays + ' Days</div>');

                                                                    }
                                                                }

                                                            }
                                                            else
                                                            {

                                                            const bookedDateDiff= bookedDatesDiff(overlapPickupDate,overlapDropoffDate);

                                                            if(bookedDateDiff==1){

                                                            //     const middleIndex = Math.floor((firstIndex + lastIndex) / 2);
                                                            // const middleElement = liElements.eq(middleIndex);

                                                            if(!overlapAnchor.find('.range_active').children('.days_showcase').length > 0){
                                                            overlapAnchor.find('.range_active').prepend('<div class="absolute days_showcase w-max md:text-[14px] z-[2] text-white -right-2 md:top-3   top-[10px] sm:text-xs">' + overlapBetweenDays + 'D</div>');
                                                            }

                                                            }
                                                            else
                                                            {

                                                                if(!overlapAnchor.find('.range_active').children('.days_showcase').length > 0){

                                                                 overlapAnchor.find('.range_active').prepend('<div class="absolute days_showcase w-[76px] md:text-[14px] z-[2] text-white right-0 left-0 md:top-3   top-[10px] sm:text-xs">' + overlapBetweenDays + ' Days</div>');

                                                                }

                                                            }


                                                            }


                                                        }
                                                    }


                                                    if(diffBetweenDates==3){

                                                        if (firstIndex !== -1 && lastIndex !== -1) {

                                                            if(bookingStartDate<calender_start_date){
                                                                const bookingDiff = bookedDatesDiff(calender_start_date,bookingEndDate);

                                                            if(bookingDiff>0){

                                                                if(!anchor.find('.range_active').children('.days_showcase').length > 0){


                                                                    anchor.find('.range_active').prepend('<div class=" days_showcase absolute w-[65px]  md:text-[14px] z-[2] text-white right-0 left-0 top-[10px] md:top-3  sm:text-xs">' + diffBetweenDates + ' Days</div>');

                                                                }

                                                            }
                                                        }
                                                        else{
                                                                // this will check if
                                                            if(bookingStartDate==bookingStartDate && liElements.eq(firstIndex).data('date-full_date')!=bookingStartDate){

                                                                if(!anchor.find('.range_active').children('.days_showcase').length > 0){
                                                                anchor.find('.range_active').prepend('<div class=" days_showcase absolute w-[110px]  md:text-[14px] z-[2] text-white right-0 left-0 top-[10px] md:top-3  sm:text-xs">' + diffBetweenDates + ' Days</div>');
                                                                }

                                                            }
                                                            else
                                                            {

                                                                if(!anchor.find('.range_active').children('.days_showcase').length > 0){

                                                                anchor.find('.range_active').prepend('<div class=" absolute days_showcase w-[90px]  md:text-[14px] z-[2] text-white right-0 left-0 top-[10px] md:top-3  sm:text-xs">' + diffBetweenDates + ' Days</div>');
                                                                }

                                                            }

                                                        }

                                                        }
                                                    }


                                                    if(overlapBetweenDays==3){



                                                        if (firstIndex !== -1 && lastIndex !== -1) {

                                                            if(overlapPickupDate<calender_start_date){
                                                                const bookingDiff = bookedDatesDiff(calender_start_date,overlapDropoffDate);

                                                            if(bookingDiff>0){

                                                                if(!overlapAnchor.find('.range_active').children('.days_showcase').length > 0){

                                                                overlapAnchor.find('.range_active').prepend('<div class=" days_showcase absolute w-[65px]  md:text-[14px] z-[2] text-white right-0 left-0 top-[10px] md:top-3  sm:text-xs">' + overlapBetweenDays + ' Days</div>');

                                                                }

                                                            }
                                                        }
                                                        else{
                                                                // this will check if
                                                            if(overlapPickupDate==overlapPickupDate && liElements.eq(firstIndex).data('date-full_date')!=overlapPickupDate){

                                                                if(!overlapAnchor.find('.range_active').children('.days_showcase').length > 0){
                                                                overlapAnchor.find('.range_active').prepend('<div class=" days_showcase  absolute w-[110px]  md:text-[14px] z-[2] text-white right-0 left-0 top-[10px] md:top-3  sm:text-xs">' + overlapBetweenDays + ' Days</div>');
                                                                }

                                                            }
                                                            else
                                                            {
                                                                if(!overlapAnchor.find('.range_active').children('.days_showcase').length > 0){

                                                                overlapAnchor.find('.range_active').prepend('<div class=" days_showcase absolute w-[90px]  md:text-[14px] z-[2] text-white right-0 left-0 top-[10px] md:top-3  sm:text-xs">' + overlapBetweenDays + ' Days</div>');

                                                                }

                                                            }

                                                        }

                                                        }
                                                    }


                                                    if(diffBetweenDates>=4){
                                                        // console.log('bookingStartDate:',bookingStartDate,'bookingEndDate',bookingEndDate);

                                                        if (firstIndex !== -1 && lastIndex !== -1) {

                                                            if(bookingStartDate<calender_start_date){
                                                                const bookingDiff = bookedDatesDiff(calender_start_date,bookingEndDate);

                                                                if(bookingDiff>0){
                                                                    const middleIndex = Math.floor((firstIndex + rangeActiveElement.closest('.clickable').index()) / 2);
                                                                    const middleElement = liElements.eq(middleIndex);
                                                                    if(!anchor.find('.range_active').children('.days_showcase').length > 0){

                                                                        const  anchor= middleElement.find('.links');
                                                                        anchor.find('.range_active').prepend('<div class="days_showcase hellothere absolute w-[90px]  md:text-[14px] z-[2] text-white right-0 left-[0px] md:top-3 top-[10px] sm:text-xs">' + diffBetweenDates + ' Days</div>');
                                                                    }

                                                                }

                                                            }

                                                            else{

                                                                let startDateIndex = liElements.filter(function() {
                                                                        return $(this).data('date-full_date')== bookingStartDate;
                                                                    }).index();

                                                                    let totalIndex= startDateIndex+rangeActiveElement.closest('.clickable').index();

                                                                    if(totalIndex % 2!=0){
                                                                        totalIndex++;
                                                                    }

                                                                let middleIndex = Math.floor(totalIndex / 2);


                                                                    const middleElement = liElements.eq(middleIndex);
                                                                    // console.log('firstIndex:',startDateIndex,'lastindex:',rangeActiveElement.closest('.clickable').index(),'middeleIndex',middleElement);


                                                                    if(!anchor.find('.range_active').children('.days_showcase').length > 0){

                                                                    // const  anchor= middleElement.find('.links');
                                                                    anchor.find('.range_active').prepend('<div class=" days_showcase hellothere2 absolute w-max  md:text-[14px] z-[2] text-white right-0 left-[0px] md:top-3 top-[10px] sm:text-xs">' + diffBetweenDates + ' Days</div>');

                                                                    }

                                                            }


                                                        }
                                                    }

                                                    if(overlapBetweenDays>=4){
                                                        // console.log('bookingStartDate:',bookingStartDate,'bookingEndDate',bookingEndDate);

                                                        if (firstIndex !== -1 && lastIndex !== -1) {

                                                            if(overlapPickupDate<calender_start_date){
                                                                const bookingDiff = bookedDatesDiff(calender_start_date,overlapDropoffDate);

                                                                if(bookingDiff>0){

                                                                    if(!overlapAnchor.find('.range_active').children('.days_showcase').length > 0){

                                                                        overlapAnchor.find('.range_active').prepend('<div class=" days_showcase  absolute w-[90px]  md:text-[14px] z-[2] text-white right-0 left-[0px] md:top-3 top-[10px] sm:text-xs">' + overlapBetweenDays + ' Days</div>');

                                                                    }

                                                                }

                                                            }

                                                            else{

                                                                if(calender_end_date> overlapDropoffDate){

                                                                    //     if(totalIndex % 2!=0){
                                                                    //         totalIndex++;
                                                                    //     }

                                                                    // let middleIndex = Math.floor(totalIndex / 2);

                                                                    // if(!overlapAnchor.find('.range_active').children('.days_showcase').length > 0){

                                                                    //     overlapAnchor.find('.range_active').prepend('<div class=" days_showcase absolute w-max  md:text-[14px] z-[2] text-white right-0 left-[0px] md:top-3 top-[10px] sm:text-xs">' + overlapBetweenDays + ' Days</div>');
                                                                    // }

                                                                }

                                                            }


                                                        }
                                                    }



                                        }


                                    }

                               }
                               else
                               {

                                // console.log('when dates one',bookingStartDate,bookingEndDate);


                                if(bookingStartDate == bookingEndDate){

                                        rangeActiveElement.addClass('single_date_booking');
                                    rangeActiveElement.find('.car_status_container_inner').html('');

                                    // md:text-sm sm:text-xs
                                    rangeActiveElement.closest('.clickable').append('<div class="absolute z-2 right-[5px] top-[47px] bookingDetails"><p class=" uppercase text-xs text-right "> <span class="hidden">End</span>'+rangeActiveElement.closest('.links').data('pickuptime')+' </p> <p class="uppercase  bookedDropoffTime text-right text-xs md:break-normal whitespace-normal leading-none md:w-[full] ">'+rangeActiveElement.closest('.links').data('dropofftime')+' </p></div>');

                                }


                                // console.log('both date is equal ');



                               }

                            flag=true;
                        }
                    });
        }
        else{
            // console.log('one of dates are undefine ');
        }

    }

    // for handling the booked date range
    function handleBookedDateRangeConditions() {
      const allBookedDates = getAllBookedDates();
    //   console.warn('all booked dates:',allBookedDates,'allBookedDates.length:',allBookedDates.length);
      let flag= false;


       for(i=0; i<allBookedDates.length;i++){

           let carId = allBookedDates[i].carId;
           let dates= allBookedDates[i].bookedDates;
        //    console.warn('carId:',carId,'dates:',dates);
        var secondDate= null;
        var firstDate= null;
        var isDateConsecutive;
        var pos = 0;
        var counterVal=0;
        // console.log(' consecutive before:',isDateConsecutive);

        // console.warn('dates:',dates,'secondDate:',secondDate,'firstDate:',firstDate);


        for(j=pos; j<dates.length;j++){
            firstDate= dates[j];
            secondDate= dates[j+1];
            // this will works whenever someone goes back(past dates) to calendar
            if(secondDate==undefined){
                secondDate=firstDate
            }
            isDateConsecutive = areConsecutiveDates(firstDate,secondDate);
            counterVal++;
            // console.warn(' consecutive after:',isDateConsecutive,firstDate,secondDate);
            if(isDateConsecutive==false){
            pos=j;
            // console.warn('carId:',carId,dates[j-(counterVal-1)],dates[j]);
            makeRangeForBookedDates(carId,dates[j-(counterVal-1)],dates[j]);
            counterVal=0;
                continue;
            }

        }

        // console.log(' consecutive after:',isDateConsecutive);


       }
    }

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

    function convertdates(date){
        let originalDate = new Date(date);
        const formattedDate = originalDate.toLocaleDateString('en-GB', { day: '2-digit', month: 'long', year: 'numeric' });
        return formattedDate;
    }

    function areConsecutiveDates(date1, date2) {
        const firstDate = moment(date1);
        const secondDate = moment(date2);

        // console.warn('firstDate:',firstDate,'secondDate:',secondDate,secondDate.diff(firstDate, 'days') === 1);

        return secondDate.diff(firstDate, 'days') === 1;
    }


    function getLockedDatesByCarid(carId) {
        const currentUl = $('.list').filter(function() {
             return $(this).data('car-id') == carId;
        });
        const lockedDates = currentUl.find('.locked').map(function() {
            return $(this).closest('li').data('date-full_date');
        }).get();
        return lockedDates;
    }

    function makeRangeForLockedDates(carId,firstDate,secondDate){

        // console.log('firstDate:',firstDate,'secondDate',secondDate);

        if(firstDate && secondDate){
            let startDate= firstDate;
            let endDate=secondDate;
            const currentUl = $('.list').filter(function() {
                return $(this).data('car-id') == carId;
            });

            let diffBetweenDates;
            const totalLiElements = currentUl.find('li').length;
            const liElements=currentUl.find('li');

            let firstIndex = -1;
            let lastIndex = -1;
            var rangeCount=0;

          currentUl.find('li').each(function (index) {

                const liDate = $(this).data('date-full_date');
                if (liDate >= startDate && liDate <= endDate) {

                    var calender_start_date = currentUl.find('li:first').data('date-full_date');

                    var calender_end_date = currentUl.find('li:last').data('date-full_date');

                    if (firstIndex === -1) {
                        firstIndex = index;
                    }
                    lastIndex = index;

                    let linksElement = $(this).find('.links');
                    let rangeActiveElement = $(this).find('.range_active');
                    linksElement.addClass('selected');
                    rangeActiveElement.addClass('active');

                    let lockedStartDate = $(this).find('.links').data('locked-startdate');
                    let lockedEndDate = $(this).find('.links').data('locked-enddate');

                    let parsedStartDate = moment(lockedStartDate, "YYYY-MM-DD HH:mm:ss");
                    let parsedEndDate = moment(lockedEndDate, "YYYY-MM-DD HH:mm:ss");

                    lockedStartDate = parsedStartDate.format("YYYY-MM-DD");
                    lockedEndDate = parsedEndDate.format("YYYY-MM-DD");
                    diffBetweenDates = diffrenceInDates(lockedStartDate, lockedEndDate);

                    // console.log('lockedStartDate',lockedStartDate,'lockedEndDate:',lockedEndDate,'liDate',liDate,startDate);

                    const lockedPreviousDate = currentUl.find('li[data-date-full_date="' + lockedStartDate + '"]');

                    // console.log('lockedPreviousDate',lockedPreviousDate.find('.links').hasClass('booked'));


                    if (startDate == endDate || lockedStartDate ==lockedEndDate ) {

                        // console.log('both dates equals','lidate:',liDate,'lockedEndDate:',lockedEndDate,'check:',lockedPreviousDate.children('.links').find('.overlap_locked').length>0,'previousDate:',lockedPreviousDate);


                       if(lockedPreviousDate.children('.links').find('.overlap_locked').length>0){

                            if (lockedPreviousDate.find('.links').hasClass('booked') && lockedPreviousDate.data('date-full_date') == lockedStartDate)
                            {

                                lockedPreviousDate.find('.range_active').removeClass('right_arc');

                                lockedPreviousDate.find('.car_status_container_inner').addClass('overlap_right_yellowSqBg').html('');

                                lockedPreviousDate.find('.car_status_container_inner').html('<span class="inline-flex h-full overlapped_locked_span  overlapped_locked_left_part "></span><span class="inline-flex h-full overlapped_locked_span  overlapped_locked_right_part "></span>');
                                rangeActiveElement.addClass('right_arc');

                            }



                       }
                       else if (lockedStartDate = lockedEndDate)
                      {

                        // console.log('both dates are equal');
                        rangeActiveElement.addClass('single_date_locked');
                        rangeActiveElement.find('.car_status_container_inner').html('');


                     }

                    } else {

                        // console.log('both dates unequals');

                        //   overlapped lockedDate range

                        // console.warn('lockedstartDate:',lockedStartDate,'lockedEndDate:',lockedEndDate,startDate,endDate,'liDate:',liDate);
                        if (liDate != lockedEndDate) {

                            if (lockedPreviousDate.find('.links').hasClass('booked') && lockedPreviousDate.data('date-full_date') == lockedStartDate) {

                                lockedPreviousDate.find('.range_active').removeClass('right_arc');

                                lockedPreviousDate.find('.car_status_container_inner').addClass('overlap_right_yellowSqBg').html('');

                                lockedPreviousDate.find('.car_status_container_inner').html('<span class="inline-flex h-full overlapped_locked_span  overlapped_locked_left_part "></span><span class="inline-flex h-full overlapped_locked_span  overlapped_locked_right_part "></span>');

                            }

                        }
                        // else{

                        //     if(lockedStartDate==lockedEndDate){

                        //         console.log( 'both dates are same:');

                        //         rangeActiveElement.addClass('single_date_locked');
                        //         rangeActiveElement.find('.car_status_container_inner').html('');

                        //     }

                        // }


                        if (liDate === lockedStartDate) {

                            const lockedPreviousDate = $(this).closest('li').prev();
                            // console.log('lockedPreviousDate', lockedPreviousDate);
                            rangeCount++;
                            if (rangeCount > 1) {
                                firstIndex = index;
                            }
                            rangeActiveElement.addClass('left_arc');
                            rangeActiveElement.find('.car_status_container_inner').addClass('cosmicLatteBg');
                        }


                        if (liDate === lockedEndDate) {
                            rangeActiveElement.addClass('right_arc');
                            rangeActiveElement.find('.car_status_container_inner').addClass('cosmicLatteBg');

                            //  for showing difference  days for locked

                            // console.log('lockedStartDate',lockedStartDate,'lockedEndDate',lockedEndDate,'calender_start_date',calender_start_date,'calender_end_date',calender_end_date);

                            if (diffBetweenDates >= 3) {

                                if (firstIndex !== -1 && lastIndex !== -1) {


                                    if (lockedStartDate < calender_start_date) {



                                        const bookingDiff = bookedDatesDiff(calender_start_date, lockedEndDate);

                                        // console.log('bookidiff',bookingDiff);


                                        if (bookingDiff > 3) {

                                            // console.log('dates is greater than 3 ','lockedStartDate:',lockedStartDate,'lockedEndDate:',lockedEndDate);

                                            const middleIndex = Math.floor((firstIndex + lastIndex) / 2);

                                            const middleElement = liElements.eq(middleIndex);

                                            const anchor = middleElement.find('.links');
                                            anchor.find('.range_active').prepend('<div class="absolute w-[110px] text-white z-[2] right-0 left-0 top-[8px] "> <span class="inline-flex mr-[3px]"><img src="{{asset('images/locked.svg')}}"> </span> In Progress</div>');
                                        }

                                    }
                                    else {
                                        const bookedDateDiff = bookedDatesDiff(lockedStartDate, lockedEndDate);

                                        if (bookedDateDiff == 1) {

                                            // console.log('under locked bookedDatesDiff==1');

                                            const middleIndex = Math.floor((firstIndex + lastIndex) / 2);
                                            const middleElement = liElements.eq(middleIndex);

                                            // const  anchor= middleElement.find('.links');
                                            // anchor.find('.range_active').prepend('<div class="absolute w-[111px] md:text-[14px] z-[2] text-white right-0 left-0 md:top-3   top-[10px] sm:text-xs">' + diffBetweenDates + 'D</div>');

                                        }
                                        else {
                                            // console.log('under locked bookedDatesDiff not equal to 1 firstIndex',firstIndex,'lastIndex:',lastIndex);

                                            const middleIndex = Math.floor((firstIndex + lastIndex) / 2);
                                            const middleElement = liElements.eq(middleIndex);

                                            // const  anchor= middleElement.find('.links');
                                            const anchor = middleElement.find('.links');
                                            anchor.find('.range_active').prepend('<div class="absolute w-[110px] text-white z-[2] right-0 left-0 top-[8px] "> <span class="inline-flex mr-[3px]"><img src="{{asset('images/locked.svg')}}"> </span> In Progress</div>');

                                        }


                                    }




                                }

                            }

                        }

                    }




                    flag = true;
                }
            });

        }
    else{
        // console.log('one of dates are undefine ');
    }
    }

    function getAllLockedDates() {
        const carIds = $('.selectable').map(function() {
            if($(this).find('.links').hasClass('locked')){
                return $(this).data('car-id');
            }

        }).get();

        const lockedDatesByCar = [];

        carIds.forEach(function(carId) {
            const lockedDates = getLockedDatesByCarid(carId);
            lockedDatesByCar.push({ carId, lockedDates });
        });

        return lockedDatesByCar;
    }

    function handleLockedDateRangeConditions() {
        const allLockedDates = getAllLockedDates();
        let flag= false;
         for(i=0; i<allLockedDates.length;i++){
                let carId = allLockedDates[i].carId;
                let dates= allLockedDates[i].lockedDates;
                var secondDate= null;
                var firstDate= null;
                var isDateConsecutive;
                var pos = 0;
                var counterVal=0;
                for(j=pos; j<dates.length;j++){
                firstDate= dates[j];
                secondDate= dates[j+1];
                isDateConsecutive = areConsecutiveDates(firstDate,secondDate);
                counterVal++;
                if(isDateConsecutive==false){
                pos=j;
                makeRangeForLockedDates(carId,dates[j-(counterVal-1)],dates[j]);
                counterVal=0;
                    continue;
                }
            }
       }
    }

    function checkLockedAndBooked(car_id,startDate,endDate,pickTime,dropTime,action_type,callback){
      let flagBase;
        $.ajax({
            url: "{{ route('partner.checkLockedAndBooked') }}",
            method: "post",
            data: {
                'startDate': startDate,
                'endDate':endDate,
                'pickupTime':pickTime,
                'dropoffTime':dropTime,
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
                callback(flagBase);
            },

            complete: function (data) {
                $(".loader").css("display", "none");
                $(".overlay_sections").css("display", "none");
            }
        });
    }

    //for making daterange in selected dates
    function handleDateRangeConditions(elements, startDate, endDate) {
        let flag= false;
        // console.log('startDate:',startDate,'endDate',endDate);
        let diffBetweenDates= diffrenceInDates(startDate,endDate);
        const totalLiElements = elements.find('li').length;
        const liElements=elements.find('li');
        let calender_start_date = elements.find('li:first').data('date-full_date');
        let calender_end_date = elements.find('li:last').data('date-full_date');
        let firstIndex = -1;
        let lastIndex = -1;
        elements.find('li').each(function(index) {
        const liDate = $(this).data('date-full_date');
        // previous booking range
         //  && areDatesAvailable(elements, startDate, endDate)
         if (liDate >= startDate && liDate <= endDate) {
            if (firstIndex === -1) {
                firstIndex = index;
            }
            if($(this).find('.links').data('booked-startdate') && $(this).find('.links').data('booked-enddate') ){
                let bookingStartDate=$(this).find('.links').data('booked-startdate');
                let bookingEndDate=$(this).find('.links').data('booked-enddate');
                let liBookingStartDate=bookingStartDate;
                let liBookingEndDate=bookingEndDate;
                //  parsing dates
                let parsedStartDate = moment(bookingStartDate, "YYYY-MM-DD HH:mm:ss");
                let parsedEndDate = moment(bookingEndDate, "YYYY-MM-DD HH:mm:ss");
                bookingStartDate = parsedStartDate.format("YYYY-MM-DD");
                bookingEndDate = parsedEndDate.format("YYYY-MM-DD");
                // console.log('bookingStartDate:',bookingStartDate,'bookingEndDate:',bookingEndDate);
                // console.log('booked start date:',bookingStartDate,'booked end',bookingEndDate);
                // console.log('pickupDateTime:',pickupDateTime,'dateObject:',dateObject);
                // if(bookingStartDate && bookingEndDate){
                //     console.log('bookingStartDate:',bookingStartDate,'bookingEndDate:',bookingEndDate);
                //     let diffDates=bookedDatesDiff(bookingStartDate,bookingEndDate);
                //     console.log('diffDates:',diffDates);
                // }else{
                // }
            }

        lastIndex = index;
        let linksElement = $(this).find('.links');
        let rangeActiveElement =  $(this).find('.range_active');
        linksElement.addClass('selected');

        rangeActiveElement.addClass('active');

        rangeActiveElement.find('.car_status_container_inner').addClass('selectedRange');

        // check if start_date and end_date is same

        if(startDate!==endDate){

            // console.log('when start and end date not equal ');


            // $(this).closeset('')

            if($(this).find('.links').data('booked-startdate')){

                let bookingStartDate=$(this).find('.links').data('booked-startdate');
                let bookingEndDate=$(this).find('.links').data('booked-enddate');
                let liBookingStartDate=bookingStartDate;
                let liBookingEndDate=bookingEndDate;
                //  parsing dates
                let parsedStartDate = moment(bookingStartDate, "YYYY-MM-DD HH:mm:ss");
                let parsedEndDate = moment(bookingEndDate, "YYYY-MM-DD HH:mm:ss");
                bookingStartDate = parsedStartDate.format("YYYY-MM-DD");
                bookingEndDate = parsedEndDate.format("YYYY-MM-DD");



                if(liDate==bookingEndDate){

                    // console.log('indexes:',$(this));

                    $(this).find('.links').addClass('selected');
                    rangeActiveElement.addClass('right_arc');
                    // rangeActiveElement.removeClass('right_arc');

                    // rangeActiveElement.find('.car_status_container_inner').addClass('pinkishBg');
                    rangeActiveElement.find('.car_status_container_inner').addClass('overlap_right_greenSqBg').html('');

                    rangeActiveElement.find('.car_status_container_inner').html('<span class="inline-flex h-full overlapped_span  overlapped_left_part "></span><span class="inline-flex h-full overlapped_span  overlapped_right_part "></span>');

                }

            }



                if (liDate === startDate &&  !$(this).find('.links').data('booked-enddate'))
                {
                        // console.log('lidate:',liDate,'startDate:',startDate);

                        rangeActiveElement.addClass('left_arc');
                        rangeActiveElement.find('.car_status_container_inner').addClass('NyanzaBg');

                }

                if (liDate === endDate)
                {
                    // console.log('lidate:',liDate,'endDate:',endDate);
                    let overlapDateActive=$(this).find('.overlap_booking').length>0;

                    // console.log('overlapDateActive',overlapDateActive);


                        rangeActiveElement.addClass('right_arc');
                        rangeActiveElement.find('.car_status_container_inner').addClass('NyanzaBg');

                }

        }
        else{
            // run only when if overlapping date is occuring

            // console.log('when start and end date equal ');
            //  this will check the booking end date is = to the li date
            if(moment($(this).find('.links').data('booked-enddate'), "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD")== liDate && $(this).find('.links').data('booked-startdate') == $(this).find('.links').data('booked-enddate') ){
                //    console.warn('matched');

                if(liDate==startDate && liDate==endDate){

                    rangeActiveElement.find('.car_status_container_inner').addClass('selected_range overlap_right_bg');
                    rangeActiveElement.find('.car_status_container_inner').html('');

                    rangeActiveElement.find('.car_status_container_inner').html('<span class="inline-flex h-full overlapped_span  pcs_overlapped_left_part "></span><span class="inline-flex h-full overlapped_span  pcs_overlapped_right_part z-[1] "></span>');

                }

                //   console.log('overlapped');
                }
                else{

                    // console.warn('not matched',);

                    if(startDate==endDate && $(this).find('.links').hasClass('booked') ){
                        $(this).find('.links').addClass('selected');
                        rangeActiveElement.find('.car_status_container_inner').addClass('selected_range overlap_right_bg');
                        rangeActiveElement.find('.car_status_container_inner').html('');

                        rangeActiveElement.find('.car_status_container_inner').html('<span class="inline-flex h-full overlapped_span  pcs_overlapped_left_part "></span><span class="inline-flex h-full overlapped_span  pcs_overlapped_right_part z-[1] "></span>');

                    }
                    else{
                        rangeActiveElement.addClass('single_date_selection');
                    }





                // rangeActiveElement.find('.car_status_container_inner').html('');

                // rangeActiveElement.find('.car_status_container_inner').html('<span class="inline-flex h-full overlapped_span  pcs_overlapped_left_part "></span><span class="inline-flex h-full overlapped_span  pcs_overlapped_right_part "></span>');

                }


            // if (liDate === endDate) {
            //     console.log('lidate:',liDate,'endDate:',endDate);
            //     rangeActiveElement.addClass('single_date_selection');

            //     rangeActiveElement.find('.car_status_container_inner').html('');

            //     // rangeActiveElement.find('.car_status_container_inner').html('<span class="inline-flex h-full overlapped_span  pcs_overlapped_left_part "></span><span class="inline-flex h-full overlapped_span  pcs_overlapped_right_part "></span>');
            // }



            flag=true;
        }



        }

        });

        if(diffBetweenDates>=3){
            if (firstIndex !== -1 && lastIndex !== -1) {
            if(calender_end_date>endDate){
                const middleIndex = Math.floor((firstIndex + lastIndex) / 2);
                const middleElement = liElements.eq(middleIndex);
                const  anchor= middleElement.find('.links');
                anchor.find('.range_active').prepend('<div class="booking_new_content absolute text-white z-[2] right-0 left-0 top-[8px]"> New Booking </div>');
            }
            else{
            const bookingDiff = bookedDatesDiff(startDate,calender_end_date);
            if(bookingDiff>=2){
                const middleIndex = Math.floor((firstIndex + lastIndex) / 2);
                const middleElement = liElements.eq(middleIndex);
                const  anchor= middleElement.find('.links');
                anchor.find('.range_active').prepend('<div class="booking_new_content absolute text-white z-[2] right-0 left-0 top-[8px]"> New Booking </div>');
            }
            }
            }
        }
        return flag;
    }

    function dropoff_required_fun() {
     var dropoff_required = $('.dropoff_required');
     var other_dropoff_location = $('#other_dropoff_location');
     if (!dropoff_required.val()) {
          dropoff_required.closest('.inp_container').find('.required').html(dropoff_required.data("error-msg"));
          dropoff_required.closest('.inp_container').find('.required').css('display', 'inline-block');
     } else {
         if (dropoff_required.val().trim() == 'Other') {
             if (!other_dropoff_location.val().trim()) {
               other_dropoff_location.closest('.inp_container').find('.required').html(other_dropoff_location.data("error-msg"));
               other_dropoff_location.closest('.inp_container').find('.required').css('display', 'inline-block');
             } else {
               dropoff_required.closest('.inp_container').find('.required').html('');
               dropoff_required.closest('.inp_container').find('.required').css('display', 'none');
               $(".loader").css("display", "inline-flex");
               $(".overlay_sections").css("display", "block");
               afterDropOff();
             }
         } else {
          dropoff_required.closest('.inp_container').find('.required').html('');
          dropoff_required.closest('.inp_container').find('.required').css('display', 'none');
          $(".loader").css("display", "inline-flex");
          $(".overlay_sections").css("display", "block");
          afterDropOff();
         }
     }
   }


	$('.drop_save').on('click', function() {
        // $.fancybox.close('#booking_Details_showcase_popup');
        // console.log('fancy close element:',$('#booking_Details_showcase_popup .fancybox-close-small'));
        // $('#booking_Details_showcase_popup .fancybox-close-small').trigger('click');
        removeSelectedClass();
        $('.booking_new_content').remove();
		var temp_time2 = $('.datepicker_inner_b2').find(".time_hour").val() + ":" +
		$('.datepicker_inner_b2').find(".time_min").val() + " " +
		$('.datepicker_inner_b2').find(".time_am_pm").val();
        $('#datepicker_dropoff_time').val(temp_time2);
        var temp_time1=$('.datepicker_inner_b').find(".time_hour").val() + ":" +
		$('.datepicker_inner_b').find(".time_min").val() + " " +
		$('.datepicker_inner_b').find(".time_am_pm").val();
		var date = moment($(".datepicker_input").val(), "DD/MM/YYYY").format();
		var date2 = moment($(".datepicker_input2").val(), "DD/MM/YYYY").format();
        let pickupMomentTime = moment(temp_time1, 'h:mm A');
        let dropoffMomentTime = moment(temp_time2, 'h:mm A');
        // console.warn('pickupTime:',pickupMomentTime);
        // console.warn('dropoffTime:',dropoffMomentTime);
        // console.warn('check:',dropoffMomentTime.isAfter(pickupMomentTime));
        if(date==date2){
        if(dropoffMomentTime.isAfter(pickupMomentTime)){
            //   $(".loader").css("display", "inline-flex");
            //    $(".overlay_sections").css("display", "block");
               dropoff_required_fun();
                // afterDropOff();
        }else{
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
        else if(date2>date){
        // $(".loader").css("display", "inline-flex");
        // $(".overlay_sections").css("display", "block");
        dropoff_required_fun();

            // afterDropOff();
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

        function afterDropOff(){
          var date = moment($(".datepicker_input").val(), "DD/MM/YYYY").format();
		   var date2 = moment($(".datepicker_input2").val(), "DD/MM/YYYY").format();
            $.fancybox.close('[data-src="#booking_Details_showcase_popup"]');
            $.fancybox.close('[data-src="#open_popup"]');
            let trimmedfirstDate = date.slice(0, 10);
            let car_id= $('#fancyBox_car_id').val();
            let action_type=$('#fancyBox_action_type').val();
            let IsDateRangeMade=false;
            const currentUl = $('.list').filter(function() {
            return $(this).data('car-id') == car_id;
            });
            var startDate= removeExtraFromDateObject(date);
            var endDate= removeExtraFromDateObject(date2);
            $('#datepicker_first_date').val(startDate);
            $('#datepicker_last_date').val(endDate);
            let pickTime= $('#datepicker_pickup_time').val();
            let dropTime= $('#datepicker_dropoff_time').val();
             // console.log('action_type:',action_type);
            checkLockedAndBooked(car_id, startDate, endDate,pickTime,dropTime, action_type,  function(result) {
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
                isBookingCancelBtn=true;
            }
            else{
                handleDateRangeConditions(currentUl, startDate, endDate);
                IsDateRangeMade= true;
                $('#show_pickup_booking_date').text(convertdates(date));
                $('#show_dropoff_booking_date').text(convertdates(date2));
                $('#show_pickup_booking_time').text($('#datepicker_pickup_time').val());
                $('#show_dropoff_booking_time').text($('#datepicker_dropoff_time').val());

                $('#mob_show_pickup_booking_date').text(convertdates(date));
                $('#mob_show_dropoff_booking_date').text(convertdates(date2));
                $('#mob_show_pickup_booking_time').text($('#datepicker_pickup_time').val());
                $('#mob_show_dropoff_booking_time').text($('#datepicker_dropoff_time').val());
                openAddandSelectedDatePopup();
                $('.activeDate').each(function(e){
                    if($(this).closest('li').data('date-full_date')===trimmedfirstDate && $(this).closest('li').data('car-id')==car_id){
                        let selectedPopup= $(this).closest('.car_status_container').siblings('.car_showcase_cards_sec').find('.selected_dates_popup');
                        selectedPopup.toggle();
                        selectedPopup.find('.pickup_date').text(convertdates(date));
                        selectedPopup.find('.pickup_time').text($('#datepicker_pickup_time').val());
                        selectedPopup.find('.dropoff_date').text(convertdates(date2));
                        selectedPopup.find('.dropoff_time').text($('#datepicker_dropoff_time').val());
                    }
                });
               $('.fare_info_input').siblings('.dapicker_val_text').find('span').text('dd/mm/yyyy | hh:mm');
            }
            });

            $('.pickup_drop').removeClass('active');
            $('.calendar_drop').addClass('inactive');
            $('.calendar_pickup ').removeClass('inactive');
            $('.car_book__input_box').removeClass('pickup');
            $('.car_book__input_box').removeClass('drop');
            $(".datepick_area_b").removeClass('datepicker_sec_b');

            setTimeout(function() {
                    $('.calendar_drop').removeClass('inactive');
                    $('.calendar_drop').removeClass('active');
            },300);

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


 function pickup_required_fun() {
    var pickup_required = $('.pickup_required');
    var other_pickup_location = $('#other_pickup_location');

    if (!pickup_required.val()) {
        pickup_required.closest('.inp_container').find('.required').html(pickup_required.data("error-msg"));
        pickup_required.closest('.inp_container').find('.required').css('display', 'inline-block');
    } else {
        if (pickup_required.val().trim() == 'Other') {
            if (!other_pickup_location.val().trim()) {
                other_pickup_location.closest('.inp_container').find('.required').html(other_pickup_location.data("error-msg"));
                other_pickup_location.closest('.inp_container').find('.required').css('display', 'inline-block');
            } else {
                pickup_required.closest('.inp_container').find('.required').html('');
                pickup_required.closest('.inp_container').find('.required').css('display', 'none');
                $('.tab_item_drop').click();
            }
        } else {
            pickup_required.closest('.inp_container').find('.required').html('');
            pickup_required.closest('.inp_container').find('.required').css('display', 'none');
            $('.tab_item_drop').click();
        }
    }
  }






    $('.pickup_save').on('click', function() {
        let pickup_date_val = $('#pickup_date').val();
        if (pickup_date_val != ''){
            // checking for overlapped dates
            // let actionType = $('#fancyBox_action_type').val();
            let overlapped_pickupTime= $('#fancyBox_overlapped_pickupTime').val();
            // console.log('overlapped_pickupTime:',overlapped_pickupTime);
            let overlapped_dropoffTime= $('#fancyBox_overlap_previous_pickup_time').val();
            // && $('#fancyBox_action_type').val()=='overlapped_date'
            // console.warn('overlapped_pickupTime:',overlapped_pickupTime,'action_type:',$('#fancyBox_action_type').val());

            if(overlapped_pickupTime && $('#fancyBox_action_type').val()=='overlapped_date'){
                var temp_time1 = $('.datepicker_inner_b').find(".time_hour").val() + ":" +
                $('.datepicker_inner_b').find(".time_min").val() + " " +
                $('.datepicker_inner_b').find(".time_am_pm").val();
                let pickupMomentTime = moment(temp_time1, 'h:mm A');
                let overlapDropoffMomentTime = moment(overlapped_pickupTime, 'h:mm A');
                // console.warn('overlapped_dropoffTime:',overlapped_dropoffTime,'overlapDropoffMomentTime:',overlapDropoffMomentTime);
                if(pickupMomentTime.isAfter(overlapDropoffMomentTime) || pickupMomentTime.isSame(overlapDropoffMomentTime)){
                    // $('.tab_item_drop').click();
                     pickup_required_fun();
                }
                  else{
                      Toastify({
                        text: 'Pickup date must be ' + overlapped_dropoffTime + ' or  greater than ' + overlapped_dropoffTime + ' pickup date',
                        duration: 3000,
                        close: true,
                        closeOnClick: true,
                        gravity: "bottom",
                        position: "right",
                    }).showToast();
                }
                // $('#datepicker_dropoff_time').val(temp_time2);
            }
            else{
                // $('.tab_item_drop').click();
                pickup_required_fun();
            }
            // if(actionType='overlapping_date'){
            //     var temp_time1 = $('.datepicker_inner_b').find(".time_hour").val() + ":" +
            //     $('.datepicker_inner_b').find(".time_min").val() + " " +
            //     $('.datepicker_inner_b').find(".time_am_pm").val();
            //     // $('#datepicker_dropoff_time').val(temp_time2);
            //     let pickupMomentTime = moment(temp_time1, 'h:mm A');
            //     let overlapDropoffMomentTime = moment(overlapped_dropoffTime, 'h:mm A');
            //     pickupMomentTime.isAfter(overlapDropoffMomentTime);
            //     console.log('condition:',pickupMomentTime.isAfter(overlapDropoffMomentTime));
            //     if( pickupMomentTime.isAfter(overlapDropoffMomentTime)){
            //         $('.tab_item_drop').click();
            //         // console.log('pickupMomentTime is greater than overlapDropoffMomentTime');
            //     }
            //     else{
            //         console.log('pickup Time is cant be less than overlapDropoffMomentTime');
            //     }
            //     // console.log('temp_time:',temp_time2);
            //     var date = moment($(".datepicker_input").val(), "DD/MM/YYYY").format();
            //     console.log('previous date:',overlapped_dropoffTime,'temp_time1:',temp_time1);
            //     var time_check;
            //     // if (formatted < time_check && valid_date == date2)
            //     // time_check = moment("09:00 AM", "hh:mm A").format("HH:mm A");
            //     // var formatted = moment(temp_time2, "hh:mm A").format("HH:mm A");
            //     // console.log('under overlapped selected date:',date,time_check);
            // }
            // else{
            //     $('.tab_item_drop').click();
            // }
        }

	});



	var interval;
	var is_verified = false;
	function countdown() {
		clearInterval(interval);
		interval = setInterval(function() {
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

    $('.form-navigation .previous').click(function() {
        navigateTo(curIndex() - 1);
    });

	var $sections = $('.car_book_form_b');

    function checkCondtionForLocation(){
        var locFlag = false;
        var pickup_required = $('.pickup_required');
        var other_pickup_location = $('#other_pickup_location');
        if (!pickup_required.val()) {
         pickup_required.closest('.inp_container').find('.required').html(pickup_required.data("error-msg"));
         pickup_required.closest('.inp_container').find('.required').css('display', 'inline-block');
        } else {
            if (pickup_required.val().trim() == 'Other') {
                if (!other_pickup_location.val().trim()) {
                    other_pickup_location.closest('.inp_container').find('.required').html(other_pickup_location.data("error-msg"));
                    other_pickup_location.closest('.inp_container').find('.required').css('display', 'inline-block');
                } else {
                    pickup_required.closest('.inp_container').find('.required').html('');
                    pickup_required.closest('.inp_container').find('.required').css('display', 'none');
                    locFlag = true;
                    return locFlag;
                }
            } else {
                pickup_required.closest('.inp_container').find('.required').html('');
                pickup_required.closest('.inp_container').find('.required').css('display', 'none');
                locFlag = true;
                return locFlag;
            }
        }

    }

    $(document).on('click touchstart','.datepicker_tab__btn.tab_item_pickup',function(){
    // $(".datepicker_tab__btn.tab_item_pickup").on("click", function() {
		$(".datepicker_tab__inner_b").removeClass("drop_tab");
		$(".datepicker_tab__inner_b").addClass("pickup_tab");
	});



    $(document).on('click touchstart','.datepicker_tab__btn.tab_item_drop',function(){
	// $(".datepicker_tab__btn.tab_item_drop").on("click", function() {

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
        defaultDate: $('.selected_date').val(),
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

           let carId =$('#fancyBox_car_id').val();

           let disabledDates = allDisableDates;

           disabledDates = disabledDates.map(dateString => {
                return $.datepicker.formatDate('dd/mm/yy', $.datepicker.parseDate('yy-mm-dd', dateString));
           });


         var formattedDate = $.datepicker.formatDate('dd/mm/yy', date);
         var isDisabled = (disabledDates.indexOf(formattedDate) !== -1);
         return [!isDisabled];
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
           let carId =$('#fancyBox_car_id').val();

            let disabledDates = allDisableDates;

            disabledDates = disabledDates.map(dateString => {
                return $.datepicker.formatDate('dd/mm/yy', $.datepicker.parseDate('yy-mm-dd', dateString));
            });

         var formattedDate = $.datepicker.formatDate('dd/mm/yy', date);
         var isDisabled = (disabledDates.indexOf(formattedDate) !== -1);
         return [!isDisabled];
        },
	});

    $('.form_sec_1_next').on('click', function() {
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

    $('body').on('click', '.close_popup', function(e) {
		e.preventDefault();
        $.fancybox.close();
        change_number = true;
        $('.data_list_here').val('');
        $('.car_book__input_box').removeClass('pickup,drop');
        $('.datepicker_sec').removeClass('active');
        selectedClear();
        navigateTo(0);
	});
    $(document).ready(function () {
    $('#dropoff_location').on('change', function () {
        if ($(this).val() == 'Other') {
        $('#other_dropoff_location').val('');
        $('.other_dropoff_location_container').show();
        $('#other_dropoff_location').addClass('dropoff_required');
        $('#dropoff_location').closest('.inp_container').find('.required').hide().empty();
        $('.DisplayDropoffLocation').addClass('hidden');

    }else{
        $('#other_dropoff_location').removeClass('dropoff_required ');
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
               $('#other_pickup_location').addClass('pickup_required');
               $('#pickup_location').closest('.inp_container').find('.required').hide().empty();
               $('.DisplayPickupLocation').addClass('hidden');
           }
           else{
               $('#other_pickup_location').removeClass('pickup_required');
               $('.other_pickup_location_container').hide();
               $('#pickup_location').closest('.inp_container').find('.error').hide().empty();
               $('#pickup_location').closest('.inp_container').find('.required').hide().empty();
               $('.DisplayPickupLocation').removeClass('hidden').html('<span class="text-black500 capitalize">pickup: </span>'+ $(this).val());

           }
       });
    });


    $("#pickup_location, #dropoff_location").select2({
        // dropdownParent: $('#open_popup'),
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
</script>
@endsection
