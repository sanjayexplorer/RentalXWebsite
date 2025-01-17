@extends('layouts.agent')
@section('title', 'View Profile')
@section('content')
<style>
.error{ color:#ff0000;}
.required{color:#ff0000;}
.header_bar{display:none;}
.right_dashboard_section > .right_dashboard_section_inner {padding-top: 0px;}
.eyeIconW{ width: 20px }
.eye_show { display: none; }
.eyeShowing .eye_show { display: block; }
.eyeShowing .eye_hide { display: none; }
.iti--allow-dropdown{ width:100% !important; }
@media screen and (max-width: 992px){
.main_header_container  {display:none;}
}
</style>
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
@if(session('success'))
    <div class="alert alert-success">
        <script>
            Swal.fire({
                title: 'Profile has been updated',
                icon: 'success',
                showCancelButton: false,
                confirmButtonText: 'OK',
                customClass: {
                            popup: 'popup_updated',
                            title: 'popup_title',
                            actions: 'btn_confirmation',
                            },
                });
        </script>
    </div>
@endif
<div>
    <!-- 1st Part -->
    <div class="lg:flex hidden lg:bg-white lg:mb-[0px] lg:px-[17px] lg:py-[15px] mb-[20px] justify-start items-center">
        <div class="flex justify-start items-center w-1/2">
            <a href="{{route('agent.booking.list')}}" class="inline-block p-2 mr-2 border border-[#C8C0C0] rounded-md hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
                <img class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
            </a>
            <span class="inline-block text-xl font-normal leading-normal align-middle text-black800">View Profile</span>
        </div>

        <div class="flex justify-end items-center w-1/2">
            <a href="{{route('agent.edit.profile')}}" class="links_item_cta dash_circle desh_edit_bg font-medium text-gray-600 hover:underline">
                <img src="{{asset('images/edit_icon.svg')}}">
            </a>
        </div>
    </div>

    <!-- 2nd Part -->
    <div class="pt-[42px] px-[36px] lg:px-[17px] lg:py-[25px] xl:py-10 bg-[#F6F6F6] !pb-[100px] min-h-screen lg:flex lg:justify-center">
        <div class="lg:hidden mb-[36px] lg:mb-[20px] flex justify-between items-center max-w-[768px] w-full">
            <div>
                <div class="back-button">
                    <a href="javascript:void(0);" class="inline-flex py-1 px-2 border border-[#C8C0C0] rounded-md bg-[#fff] hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">

                        <span class="inline-block text-base leading-normal align-middle text-black800 font-medium">
                           Settings
                        </span>
                    </a>
                </div>
                <div class="flex justify-between items-center">
                    <span class="inline-block text-[26px] font-normal leading-normal align-middle text-black800">View Profile</span>
                </div>
            </div>

            <div class="flex justify-between items-center">
                <a href="{{route('agent.edit.profile')}}" class="links_item_cta dash_circle desh_edit_bg font-medium text-gray-600 hover:underline">
                    <img src="{{asset('images/edit_icon.svg')}}">
                </a>
            </div>
        </div>


    <div class="max-w-[768px] w-full bg-white lg:bg-[#F6F6F6] rounded-[10px] px-12  lg:px-[0px] py-9 lg:py-[0px]">
        <div class="booking_section">
                @if($modifiedUrl!='')
                    <div class="flex px-0 mb-[13px] overflow-hidden mb-6 image_container">
                        <div class="relative rounded-[10px] border border-[#D8D5D0] w-1/3 p-[10px] w-[200px] h-[150px] bg-white overflow-hidden logo_image">
                            <div class="w-full h-full
                                flex flex-col items-center justify-center overflow-hidden">
                                <img src="{{ isset($modifiedUrl) ? $modifiedUrl : asset('images/User-Profile.png') }}" alt=""
                                class="CompanyImageUrl object-contain max-h-full max-w-full">
                            </div>
                        </div>
                    </div>
                @endif

                <!-- basic information  -->
                @if(Helper::getUserMeta($user->id,'owner_name') || $user->mobile || Helper::getUserMeta($user->id,'company_name')  || Helper::getUserMeta($user->id,'company_phone_number') || Helper::getUserMeta($user->id,'email') )
                    <div class="basic_information_sec pb-[15px]">
                        <div class="basic_info_sec_head mb-5">
                            <h4 class="capitalize text-[20px] font-normal ">basic information</h4>
                        </div>
                        @if(!empty(Helper::getUserMeta($user->id, 'owner_name')))
                            <div class="basic_information_sec_inner owner_name_left_sec justify-center  flex py-[14px]">
                                <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm">
                                    <p class="capitalize whitespace-normal break-all">name:</p>
                                </div>
                                <div class="basic_information_sec_right owner_name_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                    <p class="capitalize whitespace-normal break-all">{{ucwords(Helper::getUserMeta($user->id, 'owner_name'))}}</p>
                                </div>

                            </div>
                        @endif

                        @if(!empty($user->mobile))
                            <div class="basic_information_sec_inner justify-center mobile_left_sec  flex py-[14px]">
                                <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm"><p class="capitalize whitespace-normal break-all">mobile number:</p> </div>
                                <div class="basic_information_sec_right mobile_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
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
                        <!-- status section  -->
                        @if(!empty($user->status))
                            <div class="basic_information_sec_inner justify-center  flex py-[14px]">
                                <div class="basic_information_sec_left status_left_sec w-1/2 flex items-center text-base md:text-sm">
                                    <p class="capitalize whitespace-normal break-all">status:</p>
                                </div>
                                <div class="basic_information_sec_right status_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                    @if(strcmp($user->status,'inactive')==0)
                                    <div class="status status_inactive xl:inline-block text-center">
                                        <span class="status_active  capitalize lg:text-[13px]">inactive</span>
                                    </div>
                                    @else
                                    <div class="status  xl:inline-block text-center"><span class="  capitalize lg:text-[13px]">active</span></div>
                                    @endif
                                </div>

                            </div>
                        @endif
                    </div>

                @endif


                @if( !empty(Helper::getUserMeta($user->id,'plot_shop_number')) || !empty(Helper::getUserMeta($user->id,'street_name'))  || !empty(Helper::getUserMeta($user->id,'city')) || !empty(Helper::getUserMeta($user->id,'zip')) || !empty(Helper::getUserMeta($user->id,'street_name')) )
                <div class="address_sec mt-6 md:mt-0 pb-[15px]">
                    <div class="address_sec_head mb-5">
                        <h4 class="text-[20px] capitalize">address</h4>
                    </div>
                    @if(!empty(Helper::getUserMeta($user->id,'plot_shop_number')))
                        <div class="address_sec_inner flex justify-center  py-[14px]">
                            <div class="basic_information_sec_left plot_shop_num_left_sec w-1/2 flex items-center text-base md:text-sm"><p class="capitalize whitespace-normal break-all">Plot/Shop Number:</p> </div>
                            <div class="basic_information_sec_right plot_shop_num_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'plot_shop_number')}}</p>
                            </div>

                        </div>
                    @endif

                    @if(!empty(Helper::getUserMeta($user->id,'state')))
                        <div class="address_sec_inner justify-center  flex py-[14px]">
                            <div class="basic_information_sec_left state_left_sec w-1/2 flex items-center text-base md:text-sm"><p class="capitalize whitespace-normal break-all">state:</p> </div>
                            <div class="basic_information_sec_right state_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'state')}}</p>
                            </div>

                        </div>
                    @endif

                    @if(!empty(Helper::getUserMeta($user->id,'city')))
                    <div class="address_sec_inner justify-center  flex py-[14px]">
                        <div class="basic_information_sec_left city_left_sec w-1/2 flex items-center text-base md:text-sm"><p class="capitalize whitespace-normal break-all">city:</p> </div>
                        <div class="basic_information_sec_right city_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                            <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'city')}}</p>
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

                    @if(!empty(Helper::getUserMeta($user->id,'street_name')))
                    <div class="address_sec_inner justify-center  flex py-[14px]">
                        <div class="basic_information_sec_left street_name_left_sec w-1/2 flex items-center text-base md:text-sm"><p class="capitalize whitespace-normal break-all">street:</p> </div>
                        <div class="basic_information_sec_right street_name_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                            <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'street_name')}}</p>
                        </div>
                    </div>
                    @endif
                </div>
                @endif
                @if(!empty(Helper::getUserMeta($user->id,'agent_short_name')))
                    <div class="basic_information_sec_inner justify-center comapny_name_left_sec flex py-[14px]">
                        <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm"><p class="capitalize whitespace-normal break-all">short name:</p>
                        </div>
                        <div class="basic_information_sec_right comapny_name_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                            <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'agent_short_name')}}</p>
                        </div>
                    </div>
                @endif
        </div>
    </div>

</div>
@include('layouts.navigation')
</div>
<div id="change_status_popup" style="display:none" class="status_change_popup w-[90%]">
    <div class="model_content">
        <div class="rounded-tl-10 rounded-tr-10 rounded-bl-0 rounded-br-0">
            <div class="flex items-center px-5 py-5 flex-nowrap bg-siteYellow ">
                <div class="popup_heading w-1/2 text-[18px] text-[#000000] font-normal">
                    <h3 class="text-lg capitalize">Change Status </h3>
                </div>
            </div>

            <div class="flex items-center px-4 flew-row py-7 popup_outer">
                <div class="w-full modal_scroll_off search_bar_btn afclr">
                    <form action="">

                        <input type="hidden" name="" class="" id="change_statusPopup" data-user-id="{{$user->id}}" data-status-change-url="{{route('admin.users.status.update',$user->id)}}" data-user-status="{{$user->status}}">
                        <div class="popup_content_item ">

                            <div class="pb-2 form_main_src_full afclr">
                                <div class="form_main_src_h form_main_src_active popup__form__src_half_inner afclr">
                                    <label for="user_status_change" class="block mb-2 capitalize">user status
                                    </label>
                                        <select id="user_status_change" class="w-full py-3 pl-5 pr-10 font-normal leading-normal text-black bg-white border rounded-md appearance-none required_field  border-black500 focus:outline-none focus:border-siteYellow drop_location_select text-base md:text-sm">
                                            <option value="0" selected disabled class="capitalize text-midgray fontGrotesk">Select status</option>
                                            <option value="active" class="capitalize">Active</option>
                                            <option value="inactive" class="capitalize">Deactivate</option>
                                        </select>
                                </div>
                            </div>


                            <div class="flex justify-center pt-[23px] change_status_popup_action_btns">
                                <div class="flex items-center w-1/2 mr-2">
                                    <a href="javascript:void(0);"
                                        class="py-[8px] px-[40px] w-full text-[#000] text-center save_status  capitalize rounded-md bg-siteYellow border border-siteYellow transition-all
                                        duration-300 hover:bg-siteYellow400 " id="save_status_btn">
                                        save</a>
                                </div>


                                <div class="flex w-1/2 mr-2">
                                    <a href="javascript:void(0);"
                                        class="ml-2 w-full py-[8px] px-[40px] popupcancelBtn border border-siteYellow  text-center capitalize rounded-md text-[#000] bg-[#fff] transition-all
                                        duration-300 border-siteYellow hover:bg-siteYellow400" id="cancel_status_btn">
                                        cancel</a>
                                </div>

                            </div>

                        </div>
                    </form>


                </div>


            </div>



        </div>

    </div>
</div>
@endsection
