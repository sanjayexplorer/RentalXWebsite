@extends('layouts.partner')
@section('title', 'Agent View')
@section('content')
@php
$imageUrl = '';
if (strcmp(Helper::getUserMeta($user->id, 'CompanyImageId'), '') != 0) {
$profileImage = Helper::getPhotoById(Helper::getUserMeta($user->id, 'CompanyImageId'));
if ($profileImage) {
$imageUrl = $profileImage->url;
}
}
$modifiedUrl = $imageUrl;
@endphp
<style>
.header_bar{display:none;}
.right_dashboard_section > .right_dashboard_section_inner {padding-top: 0px;}
.booking_img:hover .booking_img_container{border: 1px solid #F3CD0E;}
.booking_img .unshare_btn{  display: inline-block; transition: .3s all ease; -webkit-transition: .3s all ease; -moz-transition: .3s all ease; -ms-transition: .3s all ease; -o-transition: .3s all ease; visibility: hidden; opacity: 0; z-index: -1;}
.booking_img:hover .unshare_btn{visibility: visible; opacity:1; z-index: 0;}
.booking_img_container{cursor: pointer;}
.booking_img_container.active{background-color: #F3CD0E;  border-color: #F3CD0E;}
.checkbox_bg_row{ position: absolute; top: 50%; left: 25px; transform: translate(-50%, -50%); display: block; }
.checkbox_bg_rowg input:checked ~ .checkmark { background-color: #fff; border: none; }
.checkbox_bg_rowg .checkmark::after { border: solid #000; border-width: 0 2px 2px 0; -webkit-transform: rotate(45deg); -ms-transform: rotate(45deg);
transform: rotate(45deg);}
.checkbox_bg_rowg .checkmark { border-radius: 4px; border: 1px solid #F3CD0E; }
.tab_container{  padding-bottom:15px; position: relative;}
.tab_container::after{position: absolute; content: ''; height: 2px; bottom: -1px; left: 0; width: 100%; background-color: #ffffff00;}
.tab_container.tab_active::after{background-color: #0F172A }
.tab_section_btns{border-bottom:1px solid #D9D9D9; }
.booking_section {display: none;}
.agent_check .checkmark{ display: block; position: static; transform:none; }
.agent_check .chckBoxes_container{ padding: 0; }
.agent_check::after{ left: 7px; top: 3px;}
 @media  screen and (max-width: 1279px){
  .booking_img .unshare_btn{visibility: visible; opacity: 1; z-index: 0;}
}
@media screen and (max-width: 992px){
.main_header_container {display:none;}
}
</style>
<div class="">
    <!-- 1st Part -->
    <div class="lg:flex hidden lg:bg-white lg:mb-[0px] lg:px-[17px] lg:py-[15px] mb-[20px] justify-start items-center">
        <div class="flex justify-start items-center w-full">
            <a href="{{route('partner.agent.list')}}" class="links_item_cta inline-block p-2 mr-2 border border-[#C8C0C0] rounded-md
        hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
            <img class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
            </a>
            <span class="inline-block text-xl font-normal leading-normal align-middle text-black800">Agent View</span>
        </div>

        <div class="flex justify-end items-center w-1/2">
            <a href="javascript:void(0);" data-user-id="{{$user->id}}" data-exit-url="{{route('partner.agent.exit',$user->id)}}" class="exit_user desh_delate_bg dash_circle font-medium text-gray-600 hover:underline">
                <img src="{{asset('images/exit_icon.svg')}}">
            </a>
        </div>
    </div>
    <!-- 2nd Part -->
    <div class="pt-[42px] px-[36px] lg:px-[17px] lg:py-[25px] xl:py-10 bg-[#F6F6F6] !pb-[100px]  min-h-screen lg:flex lg:justify-center relative">

        <div class="lg:hidden mb-[36px] lg:mb-[20px] flex flex-col">
            <div class="back-button">
                <a href="{{ route('partner.agent.list') }}" class="links_item_cta inline-flex py-1 px-2 border border-[#C8C0C0] rounded-md bg-[#fff] hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
                    <img  class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
                    <span class="inline-block text-base leading-normal align-middle text-black800 ml-[10px] font-medium">
                        ALL AGENTS
                    </span>
                </a>
            </div>
            <span class="inline-block text-[26px] font-normal leading-normal align-middle text-black800">
                Agent View
            </span>
        </div>

        <div class="w-full bg-white lg:bg-[#F6F6F6] rounded-[10px] px-12  lg:px-[0px] py-9 lg:py-[0px]">
            <div class="tab_section_btns flex items-center flex-start">
                <div class="tab_container mr-4 tab_active">
                <a href="#booking_section_1" class="tablinks">Agent Details</a>
               </div>
               <div class="tab_container">
                <a href="#booking_section_2" class="tablinks" >Shared Cars</a>
               </div>
            </div>

            <div class="booking_section max-w-[768px] mt-6 mb-6" id="booking_section_1" style="display:block;">
                    <!-- images -->
                    @if($modifiedUrl)
                    <h4 class="inline-block pb-[13px] font-normal leading-4 text-left text-black text-base md:text-sm">Company Logo</h4>
                    <div class="flex px-0 mb-[13px] overflow-hidden mb-6 image_container">
                        <div class="relative rounded-[10px] border border-[#D8D5D0] w-1/3  p-[10px]  w-[200px] h-[150px] bg-white overflow-hidden logo_image">
                            <div class="w-full h-full flex flex-col items-center justify-center overflow-hidden">
                                <img src="{{ isset($modifiedUrl) ? $modifiedUrl : asset('images/no_image.svg') }}" alt=""
                                class="CompanyImageUrl object-contain max-h-full max-w-full">
                            </div>
                        </div>
                    </div>
                    @endif

                    @if( Helper::getUserMeta($user->id,'owner_name') || ($user->mobile))
                        <div class="basic_information_sec pb-[15px]">
                            <div class="basic_info_sec_head mb-5">
                                <h4 class="capitalize text-[20px] font-normal ">basic information</h4>
                            </div>
                            <!-- Company Name -->
                            @if(!empty(Helper::getUserMeta($user->id,'owner_name')))
                                <div class="basic_information_sec_inner owner_name_left_sec justify-center  flex py-[14px]">
                                    <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">Owner Name:</p>
                                    </div>
                                    <div class="basic_information_sec_right car_name_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'owner_name')}}</p>
                                    </div>

                                </div>
                            @endif

                            @if(!empty($user->mobile))
                                <div class="basic_information_sec_inner owner_name_left_sec justify-center  flex py-[14px]">
                                    <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">Login Mobile Number:</p>
                                    </div>
                                    <div class="basic_information_sec_right login_mobile_number_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id, 'user_mobile_country_code')}}&nbsp;{{$user->mobile}}</p>
                                    </div>
                                </div>
                            @endif

                        @if(!empty(Helper::getUserMeta($user->id,'email')))
                        <div class="basic_information_sec_inner email_left_sec justify-center  flex py-[14px]">
                            <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm"><p class="capitalize whitespace-normal break-all">email address:</p> </div>
                            <div class="basic_information_sec_right email_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                <p class="whitespace-normal break-all">{{Helper::getUserMeta($user->id,'email')}}</p>
                            </div>
                        </div>
                    @endif

                    @if(!empty(Helper::getUserMeta($user->id,'company_name')))
                        <div class="basic_information_sec_inner justify-center comapny_name_left_sec flex py-[14px]">
                            <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm"><p class="capitalize whitespace-normal break-all">company name:</p> </div>
                            <div class="basic_information_sec_right comapny_name_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'company_name')}}</p>
                            </div>
                        </div>
                    @endif

                 @if(!empty(Helper::getUserMeta($user->id,'company_phone_number')))
                        <div class="basic_information_sec_inner justify-center comapny_name_left_sec flex py-[14px]">
                            <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm">
                                <p class="capitalize whitespace-normal break-all">company phone number:</p> </div>
                            <div class="basic_information_sec_right comapny_name_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'company_phone_number_country_code')}}&nbsp;{{Helper::getUserMeta($user->id,'company_phone_number')}}</p>
                            </div>

                        </div>
                    @endif

                     @if(!empty(Helper::getUserMeta($user->id,'partner_short_name')))
                            <div class="basic_information_sec_inner email_left_sec justify-center  flex py-[14px]">
                                <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm"><p class="capitalize whitespace-normal break-all">Partner Short Name:</p>
                                </div>
                                <div class="basic_information_sec_right fuel_type_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                    <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'partner_short_name')}}</p>
                                </div>
                            </div>
                    @endif
                    </div>
                    @endif

                    <div class="address_sec mt-6 md:mt-0 pb-[15px]">
                        @if( !empty(Helper::getUserMeta($user->id,'plot_shop_number')) || !empty(Helper::getUserMeta($user->id,'street_name'))  || Helper::getUserMeta($user->id,'state') || Helper::getUserMeta($user->id,'zip')   )

                        <div class="address_sec_head mb-5">
                            <h4 class="text-[20px] capitalize">address</h4>
                        </div>


                        @if(!empty(Helper::getUserMeta($user->id,'plot_shop_number')))
                                <div class="basic_information_sec_inner justify-center comapny_name_left_sec flex py-[14px]">
                                    <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">Plot/Shop number:</p>
                                    </div>
                                    <div class="basic_information_sec_right manufacturing_year_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'plot_shop_number')}}</p>
                                    </div>
                                </div>
                        @endif

                        @if(!empty(Helper::getUserMeta($user->id,'street_name')))
                            <div class="address_sec_inner justify-center  flex py-[14px]">
                                <div class="basic_information_sec_left state_left_sec w-1/2 flex items-center text-base md:text-sm"><p class="capitalize whitespace-normal break-all">Street Name:</p> </div>
                                <div class="basic_information_sec_right state_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                    <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'street_name')}}</p>
                                </div>

                            </div>
                        @endif

                        @if(!empty(Helper::getUserMeta($user->id,'state')))
                            <div class="address_sec_inner justify-center  flex py-[14px]">
                                <div class="basic_information_sec_left city_left_sec w-1/2 flex items-center text-base md:text-sm"><p class="capitalize whitespace-normal break-all">State:</p> </div>
                                <div class="basic_information_sec_right city_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                    <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'state')}}</p>
                                </div>

                            </div>
                        @endif

                        @if(!empty(Helper::getUserMeta($user->id,'zip')))
                            <div class="address_sec_inner justify-center  flex py-[14px]">
                                <div class="basic_information_sec_left zip_left_sec w-1/2 flex items-center text-base md:text-sm"><p class="capitalize whitespace-normal break-all">zip code:</p> </div>
                                <div class="basic_information_sec_right zip_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                    <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'zip')}}</p>
                                </div>
                            </div>
                        @endif


                        @if(!empty(Helper::getUserMeta($user->id,'city')))
                            <div class="address_sec_inner justify-center  flex py-[14px]">
                                <div class="basic_information_sec_left street_name_left_sec w-1/2 flex items-center text-base md:text-sm"><p class="capitalize whitespace-normal break-all">City:</p> </div>
                                <div class="basic_information_sec_right street_name_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                    <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'city')}}</p>
                                </div>
                            </div>
                        @endif

                    @endif
                    </div>


                    @if($drivers->isNotEmpty())
                    <div class="agent_sec mt-6 md:mt-0">
                        <div class="pb-5 partner_form_heading">
                            <h4 class="inline-block text-xl font-normal leading-normal align-middle text-black800">Drivers</h4>
                        </div>

                        <div class="agent_repeat_box_main">
                            @foreach($drivers as $item)
                            @php $i=0;  @endphp
                            @if(!empty($item->driver_name))
                                <div class="address_sec_inner justify-center  flex py-[14px]">
                                    <div class="basic_information_sec_left street_name_left_sec w-1/2 flex items-center text-base md:text-sm"><p class="capitalize whitespace-normal break-all">Driver Name:</p> </div>
                                    <div class="basic_information_sec_right agent_name_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">{{$item->driver_name}}</p>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($item->driver_mobile))
                                <div class="address_sec_inner justify-center  flex py-[14px]">
                                    <div class="basic_information_sec_left street_name_left_sec w-1/2 flex items-center text-base md:text-sm"><p class="capitalize whitespace-normal break-all">Driver Mobile:</p> </div>
                                    <div class="basic_information_sec_right agent_mobile_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">{{$item->driver_mobile}}</p>
                                    </div>
                                </div>
                            @endif

                               @php $i++; @endphp

                            @endforeach
                        </div>
                    </div>
                    @endif
            </div>

            <div class="booking_section " id="booking_section_2">
                <div class=" relative  mb-6 mt-6">

                    <div class="flex justify-start items-center -mx-2">
                                <div class="flex justify-end px-2">

                                    <div class="flex flex-wrap w-[130px] break-words">
                                        <label
                                            class="chckBoxes_container border border-[#F3CD0E] rounded-[4px]">
                                            <p class="text-xs mb-0 font-normal checkbox_title capitalize py-[10px] ml-[17px] mr-[20px]">
                                                Select All
                                            </p>
                                            <input type="checkbox" name="car_type"
                                                value=""
                                                class="choice_checkBox choice_checkBox_select clear_checkbox">
                                            <span class="checkmark checkbox_checkmark "></span>
                                        </label>
                                    </div>

                                </div>

                                <div class="flex justify-end px-2">

                                <div class="flex flex-wrap w-[130px] break-words unshare_section hidden">

                                    <div class="chckBoxes_container  border border-[#F3CD0E] rounded-[4px]">
                                                    <p class="text-xs mb-0 font-normal checkbox_title text-[#272522] py-[10px] ml-[17px] capitalize mr-[20px] unshare_btn_all">
                                                    Unshare
                                                    </p>

                                    </div>

                                </div>
                            </div>
                    </div>

                </div>
                <div class="booking_section_b">
                    <div class="flex px-0 mb-3 mb-6 md:block flex-col">
                  <!-- cars -->
                  @if(count($cars)>0)
                  @foreach ($cars as $car)
                  @php
                  $thumbnails = Helper::getFeaturedSetCarPhotoById($car->id);
                  $carImageUrl = asset('images/no_image.svg');
                  foreach ($thumbnails as $thumbnail) {
                      $image = Helper::getPhotoById($thumbnail->imageId);
                      $carImageUrl = $image->url;
                  }
                  $modifiedImgUrl = $carImageUrl;
                  @endphp
                    <div class="booking_img w-full mb-7 p-0">

                        <div class="relative py-5  px-6 sm:py-4 sm:px-5  bg-white rounded-[9px] border border-[#CFCFCF] booking_img_container agent_check">

                        <div class="">


                            <div class="flex items-center">
                                        <div class="w-4 block">
                                            <div class="checkbox_bg_rowg">
                                                <div class="flex flex-wrap break-words">
                                                    <label class="chckBoxes_container">
                                                        <input type="checkbox" name="car_type" class="checkbox_for_car choice_checkBox" value="{{$car->id}}">
                                                        <span class="checkmark checkbox_checkmark !ml-0 agent_check"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                @if($modifiedImgUrl)
                                <div class="w-full flex flex-wrap items-center justify-between pl-7">


                                    <div class="w-3/4 md:w-full">

                                        <div class="flex items-center flex-wrap -mx-3">
                                            <div class="flex items-center w-1/4 px-3 md:w-full md:mb-5">
                                                <img class="block  w-[120px] h-[60px] object-contain"
                                                    src="{{ $modifiedImgUrl }}"
                                                alt="car">
                                            </div>
                                            <div class="px-3 w-[35%] md:w-1/2 sm:w-full mb-2">
                                                <div class="h-full  ">
                                                    <div class="flex flex-col ">
                                                        <div class="details">
                                                        <h3 class="text-base font-medium leading-4 text-[#2B2B2B] pb-1">
                                                            {{ ucwords($car->name) }}</h3>
                                                        <p class="text-sm font-normal leading-none text-[#2B2B2B] pb-1.5 last:pb-0">
                                                            {{ strtoupper($car->registration_number) }}
                                                        </p>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="px-3 w-2/5 md:w-1/2 sm:w-full">
                                                <p class="text-base font-medium leading-normal text-[#272522] ">₹<span>{{number_format($car->price, 0, '.', ',')}}</span> per day</p>
                                            </div>
                                        </div>
                                    </div>


                                        <div class="w-1/4 md:w-full  block ">
                                        <div class=" unshare_section_container    md:pt-5 ">
                                            <div class="action_btn_c text-right md:text-center">
                                                <a href="javascript:void(0);" data-car-id="{{$car->id}}" class="inline-flex md:w-full rounded-[4px] items-center capitalize text-[#000000] text-base md:text-sm font-normal border border-[#000000] bg-[#fff] px-[20px] py-2.5 md:px-[15px] unshare_btn">
                                                Unshare
                                                </a>
                                            </div>
                                        </div>
                                        </div>

                                </div>
                                @endif

                            </div>
                                    <!--  -->
                                    {{-- <div class="action_btn_c hidden md:block text-right border-t border-[#cfcfcf] px-5 py-2">
                                        <a href="javascript:void(0);" data-car-id="{{$car->id}}" class="inline-flex rounded-[4px] items-center capitalize text-[#000000] text-base md:text-sm font-normal border border-[#000000] bg-[#fff] px-[20px] py-2.5 md:px-[15px] unshare_btn">
                                            Unshare
                                        </a>
                                    </div> --}}

                                    <!--  -->
                        </div>
                        </div>
                    </div>

                        @endforeach
                        @endif

                    </div>


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

$('body').on('click', '.action_perform',function(e){
$('.session_msg_container').slideUp();
});
$('.booking_img').on('click', function(e) {
    e.preventDefault();
    var $container = $(this).find('.booking_img_container');
    $container.toggleClass('active');
    var $checkbox = $(this).find('.checkbox_for_car');
    $checkbox.prop('checked', $container.hasClass('active'));
    choiceLength();
});

$('.choice_checkBox_select').on('change', function() {
    if($(this).prop('checked')){
        $('.checkbox_for_car').prop('checked', true);
        $('.booking_img_container').addClass('active');
        choiceLength();
    }else{
        $('.checkbox_for_car').prop('checked', false);
        $('.booking_img_container').removeClass('active');
        choiceLength();
    }
});

function choiceLength() {
    var checkedCount = 0;
    $(".checkbox_for_car").each(function () {
        if ($(this).prop('checked')) {
            checkedCount++;
        }
    });

    if (checkedCount > 0) {
        $('.unshare_section').css('display', 'block');
    } else {
        $('.unshare_section').css('display', 'none');
        $('.choice_checkBox_select').prop('checked', false);
    }
}


$('.tab_section_btns .tablinks').on('click',function(e){
    e.preventDefault();
    $('.tab_container').removeClass('tab_active');
    $('.booking_section').hide();
    var temp = $($(this).attr('href'));
    temp.show();
    $(this).closest('.tab_container').addClass('tab_active');
});


setTimeout(function() {
$('.session_msg_container').slideUp();
}, 10000);

$(".action_perform").on('click', function(e) {
    $('.session_msg_container').slideUp();
});

    $('body').on('click','.exit_user',function(){
        console.log('from exi:',$(this).data('exit-url'));
        const exitUrl = $(this).data('exit-url');
        const exitId = $(this).data('user-id');

         Swal.fire({
            title: 'Are you sure want to ended your partnership with this agent?',
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: 'YES, EXIT IT!',
            cancelButtonText: 'NO, CANCEL'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: exitUrl,
                        type: "POST",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            'ids': exitId,
                        },
                        dataType: 'json',
                        success: function (data) {
                        if (data.success) {
                        Swal.fire({
                            title: 'Partnership has been ended',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonText: 'OK',
                                customClass: {
                                        popup: 'popup_updated',
                                        title: 'popup_title',
                                        actions: 'btn_confirmation',
                                },

                        }).then((result) => {
                            if(result.isConfirmed){
                              window.location.href="{{route('partner.agent.list')}}";
                            }
                            else{
                                window.location.href="{{route('partner.agent.list')}}";
                            }
                        });
                        }
                        else {
                            alert('Error occured.');
                        }
                        },
                        error: function (data) {
                        }
                    });
                }
            })
    });
    function getCheckedCarIds() {
            var carIdArr = [];
            var checkedCheckboxes = $('.checkbox_for_car:checked');
            if (checkedCheckboxes.length === 0) {
                Swal.fire({
                    title: 'Please select a car',
                    icon: 'error',
                    showCancelButton: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'popup_updated',
                        title: 'popup_title',
                        actions: 'btn_confirmation',
                    },
                });
                return false;
            }
            checkedCheckboxes.each(function () {
                carIdArr.push($(this).val());
            });
            return carIdArr;
    }

    var agentId = '{{$user->id}}';

    $('.unshare_btn_all').on('click', function (e) {
        e.preventDefault();
        var carIds = getCheckedCarIds();
        if (!carIds) {
            return;
        }
        console.log('carIds',carIds);

        Swal.fire({
            title: 'Are you sure want to unshare car?',
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: 'YES, UNSHARE IT!',
            cancelButtonText: 'NO, CANCEL'
            }).then((result) => {
            if (result.isConfirmed) {
            $(".loader").css("display", "inline-flex");
            $(".overlay_sections").css("display", "block");
             $.ajax({
            type: "POST",
            url: "{{route('partner.unshare.cars')}}",
            data: {
                '_token':'{{ csrf_token() }}',
                'carIds': carIds,
                'agentId': agentId,
                'type': 'multipleDelete'
            },
            dataType: "json",
            success: function (data) {
             $('.checkbox_for_car:checked').each(function(){
                $(this).closest('.booking_img').remove();
             });
            if(data.success){
                Swal.fire({
                    title: 'Cars has been unshared',
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'popup_updated',
                        title: 'popup_title',
                        actions: 'btn_confirmation',
                    },
              }).then((result) => {
                    if(result.isConfirmed){
                        // window.location.href = '{{route('partner.agent.list')}}';
                    }else{
                        // window.location.href = '{{route('partner.agent.list')}}';
                    }
                });
            }else{
                Swal.fire({
                    title: 'Someting went wrong',
                    icon: 'error',
                    showCancelButton: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'popup_updated',
                        title: 'popup_title',
                        actions: 'btn_confirmation',
                    },
              }).then((result) => {
                    if(result.isConfirmed){
                        // window.location.href = '{{route('partner.agent.list')}}';
                    }
                });
            }
            },
            complete: function(data) {
                $(".loader").css("display", "none");
                $(".overlay_sections").css("display", "none");
            },
            error: function (xhr, status, error) {

            }
          });
        }
        });
    });

    $('.unshare_btn').on('click', function (e) {
    e.preventDefault();
       var that= $(this);
        Swal.fire({
           title: 'Are you sure want to unshare car?',
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: 'YES, UNSHARE IT!',
            cancelButtonText: 'NO, CANCEL'
            }).then((result) => {
                if (result.isConfirmed) {
                    var carId = $(this).data('car-id');
                    $(".loader").css("display", "inline-flex");
                    $(".overlay_sections").css("display", "block");
                    $.ajax({
                        type: "POST",
                        url: "{{route('partner.unshare.cars')}}",
                        data: {
                            '_token':'{{ csrf_token() }}',
                            'carId': carId,
                            'agentId': agentId,
                            'type': 'singleDelete'

                        },
                        dataType: "json",
                        success: function (data) {
                        if(data.success){
                            $(that).closest(".booking_img").remove();
                            $('.unshare_section').css('display', 'none');

                            Swal.fire({
                                title: 'Cars has been unshared',
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonText: 'OK',
                                customClass: {
                                    popup: 'popup_updated',
                                    title: 'popup_title',
                                    actions: 'btn_confirmation',
                                },
                        }).then((result) => {
                                if(result.isConfirmed){
                                    // window.location.href = '{{route('partner.agent.list')}}'
                                }else{
                                    // window.location.href = '{{route('partner.agent.list')}}';
                                }
                            });
                        }else{
                            Swal.fire({
                                title: 'Someting went wrong',
                                icon: 'error',
                                showCancelButton: false,
                                confirmButtonText: 'OK',
                                customClass: {
                                    popup: 'popup_updated',
                                    title: 'popup_title',
                                    actions: 'btn_confirmation',
                                },
                        }).then((result) => {
                                if(result.isConfirmed){
                                    // window.location.href = '{{route('partner.agent.list')}}';
                                }
                            });
                        }
                        },
                        complete: function(data) {
                            $(".loader").css("display", "none");
                            $(".overlay_sections").css("display", "none");
                        },
                        error: function (xhr, status, error) {

                        }
                    });
                }
            });
    });
</script>
@endsection
