@extends('layouts.partner')
@section('title', 'Lead View')
@section('content')
<style>
.header_bar{display:none;}
.right_dashboard_section > .right_dashboard_section_inner {padding-top: 0px;}
.tab_container{  padding-bottom:15px; position: relative;}
.tab_container::after{position: absolute; content: ''; height: 2px; bottom: -1px; left: 0; width: 100%; background-color: #ffffff00;}
.tab_container.tab_active::after{background-color: #0F172A }
.tab_section_btns{border-bottom:1px solid #D9D9D9; }
.booking_section {display: none;}
@media screen and (max-width: 992px){
    .main_header_container{ display: none; }
}
@media  screen and (max-width: 767px){
  .booking_img .unshare_btn{visibility: visible; opacity: 1; z-index: 0;}
}
</style>

@php
$leads_pickup_time= $leads->pick_up_date_time;
$pickupTimestamp = strtotime($leads_pickup_time);
$formattedDatePickup = date("F j, Y - h:ia", $pickupTimestamp);


$leads_dropoff_time=$leads->drop_off_date_time;
$dropoffTimestamp = strtotime($leads_dropoff_time);
$formattedDateDropoff = date("F j, Y - h:ia", $dropoffTimestamp);

$datetime = new DateTime($leads->created_at);
$month_year = $datetime->format('F Y');


$formatted_date = $datetime->format('M d,Y \a\t h:ia T');



$today = new DateTime('today');
$yesterday = new DateTime('yesterday');

// Compare the provided datetime with today's date and yesterday's date
if ($datetime->format('Y-m-d') == $today->format('Y-m-d')) {
    $date_string = 'today';
} elseif ($datetime->format('Y-m-d') == $yesterday->format('Y-m-d')) {
    $date_string = 'yesterday';
} else {
    $date_string = $datetime->format('M d,Y');
}

// Format the output accordingly
$formatted_date = $date_string . ', at ' . $datetime->format('h:ia T');


@endphp
<div class="">

    <!-- 1st Part -->
    <div class="lg:flex hidden lg:bg-white lg:mb-[0px] lg:px-[17px] lg:py-[15px] mb-[20px] justify-start items-center">
        <div class="flex justify-start items-center w-full">
            <a href="{{route('partner.leads.list')}}" class="links_item_cta inline-block p-2 mr-2 border border-[#C8C0C0] rounded-md hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
                <img class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
            </a>
            <span class="inline-block text-xl font-normal leading-normal align-middle text-black800">Lead View</span>
        </div>

        <div class="flex justify-end items-center w-1/2 hidden">
            <a href="javascript:void(0);" class="exit_user desh_delate_bg dash_circle font-medium text-gray-600 hover:underline">
                <img src="{{asset('images/exit_icon.svg')}}">
            </a>
        </div>
    </div>

    <!-- 2nd Part -->
    <div class="pt-[42px] px-[36px] lg:px-[17px] lg:py-[25px] xl:py-10 bg-[#F6F6F6] !pb-[100px]  min-h-screen lg:flex lg:justify-center relative">

        <div class="lg:hidden mb-[36px] lg:mb-[20px] flex flex-col">
            <div class="back-button">
                <a href="{{route('partner.leads.list')}}" class="links_item_cta inline-flex py-1 px-2 border border-[#C8C0C0] rounded-md bg-[#fff] hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
                    <img  class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
                    <span class="inline-block text-base leading-normal align-middle text-black800 ml-[10px] font-medium">
                        ALL LEADS
                    </span>
                </a>
            </div>
            <span class="inline-block text-[26px] font-normal leading-normal align-middle text-black800">
                Lead View
            </span>
        </div>

        <div class="w-full bg-white lg:bg-[#F6F6F6] rounded-[10px] px-12  lg:px-[0px] py-9 lg:py-[0px]">
        <div class="basic_info_sec_head mb-[30px]">
           <h4 class="capitalize text-[20px] font-normal pb-[10px]">{{($leads->customer_name)?$leads->customer_name:''}}</h4>
            <div class="flex items-center">

                <div class="contact_number_sec flex border pr-2  border-t-0 border-b-0 border-l-0 border-r-[#000]">
                    <div class="contact_no_left_sec flex justify-center items-center ">
                        <div class="img_container w-[16px] h-[16px]">
                            <img src="{{asset('images/callIcon.svg')}}" alt="" class="w-[16px]">
                        </div>
                        {{-- <span><img src="{{asset('images/callIcon.svg')}}" alt=""></span> --}}
                    </div>

                    <div class="contact_no_right_sec pl-2"><span class="contact_no.">{{$leads->contact_number}}</span></div>
                </div>

                <div class="contact_mail_sec flex pl-2">
                    <div class="contact_no_left_sec flex justify-center items-center ">
                        <div class="img_container w-[16px] h-[16px]">
                            <img src="{{asset('images/mailIcon.svg')}}" alt="" class="w-[16px]">
                        </div>
                        {{-- <span></span> --}}
                    </div>

                    <div class="contact_no_right_sec px-2"><span class="contact_no.">johndoe@gmail.com</span></div>
                </div>


            </div>
        </div>
            <div class="tab_section_btns flex items-center flex-start pt-[30px]">
                <div class="tab_container mr-4 tab_active">
                <a href="#booking_section_1" class="tablinks">Details</a>
               </div>
               <div class="tab_container">
                <a href="#booking_section_2" class="tablinks" >Activity</a>
               </div>
            </div>

            <div class="booking_section max-w-[768px] mt-6 mb-6" id="booking_section_1" style="display:block;">
                        <div class="basic_information_sec pb-[15px]">
                            <div class="basic_info_sec_head mb-5">
                                <h4 class="capitalize text-[20px] font-normal ">basic information</h4>
                            </div>
                            <!-- Company Name -->

                                <div class="basic_information_sec_inner owner_name_left_sec justify-center  flex py-[14px]">
                                    <div class="basic_information_sec_left w-[35%] sm:w-2/5 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all font-medium">Pick Up Date & time:</p>
                                    </div>
                                    <div class="basic_information_sec_right car_name_right_sec w-[65%] sm:w-3/5   px-2 flex items-center text-base md:text-sm ">
                                        <p class="capitalize whitespace-normal break-all">{{$formattedDatePickup}}</p>
                                    </div>

                                </div>

                                <div class="basic_information_sec_inner owner_name_left_sec justify-center  flex py-[14px]">
                                    <div class="basic_information_sec_left w-[35%] sm:w-2/5 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all font-medium">Drop Off Date & time:</p>
                                    </div>
                                    <div class="basic_information_sec_right car_name_right_sec w-[65%] sm:w-3/5 px-2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">{{$formattedDateDropoff}}</p>
                                    </div>

                                </div>

                                <div class="basic_information_sec_inner owner_name_left_sec justify-center  flex py-[14px]">
                                    <div class="basic_information_sec_left w-[35%] sm:w-2/5 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all font-medium">Pick Up Location:</p>
                                    </div>
                                    <div class="basic_information_sec_right login_mobile_number_right_sec w-[65%]  sm:w-3/5 px-2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">{{$leads->pick_up_location}}</p>
                                    </div>
                                </div>


                                <div class="basic_information_sec_inner justify-center comapny_name_left_sec flex py-[14px]">
                                    <div class="basic_information_sec_left w-[35%] sm:w-2/5 flex items-center text-base md:text-sm"><p class="capitalize whitespace-normal break-all font-medium">Drop off Location:</p> </div>
                                    <div class="basic_information_sec_right comapny_name_right_sec w-[65%] sm:w-3/5  px-2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">{{$leads->drop_off_location}}</p>
                                    </div>
                                </div>


                                <div class="basic_information_sec_inner justify-center comapny_name_left_sec flex py-[14px]">
                                    <div class="basic_information_sec_left w-[35%] sm:w-2/5 flex items-center text-base md:text-sm"><p class="capitalize whitespace-normal break-all font-medium">lead status:</p> </div>
                                    <div class="basic_information_sec_right comapny_name_right_sec w-[65%] sm:w-3/5 px-2 flex items-center text-base md:text-sm">
                                        {{-- <p class="capitalize whitespace-normal break-all">{{$leads->drop_off_location}}</p> --}}

                                        @if(strcmp($leads->status,'new')==0)

                                        <div class="bg-[#E7F8FF] text-[#198DE1]  xl:inline-block text-center w-full px-3 py-[5px] rounded-[39px] min-w-[75px] w-max"><span class="status_active capitalize lg:text-[13px] ">new</span></div>



                                        @elseif(strcmp($leads->status,'attempted_to_contacted')==0)

                                        <div class="bg-[#FFFCF4] text-[#FD9154]  xl:inline-block text-center w-full px-3 py-[5px] rounded-[39px] min-w-[75px] w-max"><span class="status_active bg-[#FFFCF4] capitalize lg:text-[13px] ">attempted to contacted</span></div>


                                        @elseif(strcmp($leads->status,'confirmed')==0)

                                        <div class="bg-[#EDFAEE] text-[#25A02A]  xl:inline-block text-center w-full px-3 py-[5px] rounded-[39px] min-w-[75px] w-max"><span class="status_active  capitalize lg:text-[13px]">confirmed</span></div>

                                        @elseif(strcmp($leads->status,'lost_lead')==0)
                                        <div class="bg-[#FFF2F2] text-[#E12919]  xl:inline-block text-center w-full px-3 py-[5px] rounded-[39px] min-w-[75px] w-max "><span class="status_active bg-[#FFF2F2] capitalize lg:text-[13px]"></span>lost lead</div>

                                        @elseif(strcmp($leads->status,'junk_lead')==0)
                                        <div class="inline-block py-[5px] px-[20px] bg-[#FFEEE9] text-[#FF471F]  xl:inline-block text-center px-3 py-[5px] rounded-[39px] min-w-[75px] w-max"><span class="status_active capitalize lg:text-[13px]">junk lead</span></div>

                                        @elseif(strcmp($leads->status,'cancelled')==0)

                                        <div class="inline-block py-[5px] px-[20px] bg-[#FFE6E6] text-[#E12919]  xl:inline-block text-center px-3 py-[5px] rounded-[39px] min-w-[75px] w-max"><span class="status_active capitalize lg:text-[13px]">cancelled</span></div>

                                        @endif
                                    </div>
                                </div>

                        </div>
            </div>

            <div class="booking_section  mt-6 mb-6 " id="booking_section_2">

                <div class="booking_section_b w-full">
                    <div class="flex ">
                        <div class="left_part w-1/2 flex justify-start">
                            <div class="month_activity_title pb-[15px]">
                            <h4 class="capitalize text-[#A5A5A5] font-normal text-base ">{{$month_year}}</h4>
                                </div>

                        </div>
                        <div class="right_part hidden  w-1/2 flex justify-end sm:block">
                            <div class="activities_right_sec w-full text-right flex justify-end items-start ">
                                            <div class="activities_timestamp">
                                                <p class="font-normal text-[14px] text-[#A3A3A3]">{{$formatted_date}}</p>
                                            </div>
                            </div>

                        </div>
                    </div>




                    <div class="activities__outer">

                        <div class="activities_main_container pb-4">
                                <div class="activities_inner_content p-4 bg-[#F6F6F6] flex  justify-center sm:justify-start ">

                                    <div class="activities_left_sec w-[75%] text-left  sm:w-full">
                                        <div class="activities_title pb-1">
                                            <h4 class="capitalize font-medium text-[14px] text-[#000]">form submission</h4>
                                        </div>

                                        <div class="activities_description">
                                            <p class="capitalize font-normal text-[14px] text-[#000]">John Doe submitted form on your <span class="font-medium">Instagram Lead Ads </span></p>
                                        </div>
                                    </div>

                                    <div class="activities_right_sec w-[25%] text-right flex justify-end items-start sm:hidden ">
                                        <div class="activities_timestamp">
                                            <p class="font-normal text-[14px] text-[#A3A3A3]">{{$formatted_date}}</p></div>
                                    </div>


                                </div>
                        </div>

                        <!-- <div class="activities_main_container pb-4">
                                <div class="activities_inner_content p-4 bg-[#F6F6F6] flex  justify-center ">

                                    <div class="activities_left_sec w-[75%] text-left ">
                                        <div class="activities_title pb-1">
                                            <h4 class="capitalize font-medium text-[14px] text-[#000]">form submission</h4>
                                        </div>

                                        <div class="activities_description">
                                            <p class="capitalize font-normal text-[14px] text-[#000]">John Doe submitted form on your <span class="font-medium">Instagram Lead Ads </span></p>
                                        </div>
                                    </div>

                                    <div class="activities_right_sec w-[25%] text-right flex justify-end items-start">
                                        <div class="activities_timestamp">
                                            <p class="font-normal text-[14px] text-[#A3A3A3]">Today at 12:30 pm ISD</p></div>
                                    </div>


                                </div>
                        </div> -->

                    </div>

                </div>
            </div>


    </div>

    <!-- session message  -->
    @if (Session::has('success'))
    <div class="session_msg_container flex items-center justify-between border border-[#478E1A] bg-[#DDEDD3] text-[#000000] text-sm font-bold px-4 py-3 rounded-lg  mx-auto bottom-3 w-3/5 sticky left-0 right-0 lg:bottom-[75px] lg:w-4/5 md:w-[93%]" role="alert">
        <div class="flex items-center">
        <span class="action_icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="12" viewBox="0 0 17 12" fill="none">
        <path d="M16.6443 0.351508C17.1186 0.820184 17.1186 1.58132 16.6443 2.04999L6.93109 11.6485C6.45681 12.1172 5.68659 12.1172 5.21231 11.6485L0.355708 6.84924C-0.118569 6.38057 -0.118569 5.61943 0.355708 5.15076C0.829985 4.68208 1.60021 4.68208 2.07449 5.15076L6.0736 9.09889L14.9293 0.351508C15.4036 -0.117169 16.1738 -0.117169 16.6481 0.351508H16.6443Z" fill="#478E1A"></path>
        </svg>
        </span>
        <p class="session_msg ml-[15px] text-[#000000] text-[14px] font-normal">Success.&nbsp;<span class="font-normal text-[#000000]">{{ Session::get('success')}}</span></p>
        </div>
        <a href="javascript:void(0)" class="action_perform">
        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
        <path d="M9.29074 10.3517L5.29074 6.35174L1.28574 10.3517C1.21607 10.4217 1.1333 10.4773 1.04215 10.5153C0.951005 10.5533 0.853263 10.573 0.754508 10.5732C0.655753 10.5735 0.557919 10.5543 0.466592 10.5167C0.375266 10.4791 0.292235 10.4239 0.22224 10.3542C0.152246 10.2846 0.0966583 10.2018 0.0586519 10.1107C0.0206455 10.0195 0.000964363 9.92176 0.000732217 9.82301C0.000500072 9.72425 0.0197215 9.62642 0.0572989 9.53509C0.0948764 9.44377 0.150074 9.36074 0.21974 9.29074L4.22474 5.29074L0.21974 1.28074C0.0790429 1.14004 -1.48249e-09 0.949216 0 0.75024C1.48249e-09 0.551264 0.079043 0.360438 0.21974 0.21974C0.360438 0.079043 0.551264 9.39873e-09 0.75024 7.91624e-09C0.949216 6.43375e-09 1.14004 0.0790429 1.28074 0.21974L5.28574 4.22474L9.28574 0.21974C9.42644 0.0790429 9.61726 0 9.81624 0C10.0152 0 10.206 0.0790429 10.3467 0.21974C10.4874 0.360438 10.5665 0.551264 10.5665 0.75024C10.5665 0.949216 10.4874 1.14004 10.3467 1.28074L6.34674 5.28574L10.3467 9.28574C10.4874 9.42644 10.5665 9.61726 10.5665 9.81624C10.5665 10.0152 10.4874 10.206 10.3467 10.3467C10.206 10.4874 10.0152 10.5665 9.81624 10.5665C9.61726 10.5665 9.42644 10.4874 9.28574 10.3467L9.29074 10.3517Z" fill="black"></path>
        </svg>
        </a>
    </div>
    @endif
    <!-- navigation -->
    @include('layouts.navigation')
</div>

<script>
$('.tab_section_btns .tablinks').on('click',function(e){
    e.preventDefault();
    $('.tab_container').removeClass('tab_active');
    $('.booking_section').hide();
    var temp = $($(this).attr('href'));
    temp.show();
    $(this).closest('.tab_container').addClass('tab_active');
});

</script>
@endsection
