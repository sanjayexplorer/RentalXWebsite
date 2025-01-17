@extends('layouts.admin')
@section('title', 'View User')
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
.main_header_container {display:none;}
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
    <div>
        <div class="lg:flex hidden lg:bg-white lg:mb-[0px] lg:px-[17px] lg:py-[15px] mb-[20px] justify-start items-center">
            <div class="flex justify-start items-center w-1/2">
                    <a href="{{route('admin.users.list')}}" class="links_item_cta inline-block p-2 mr-2 border border-[#C8C0C0] rounded-md hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
                        <img class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
                    </a>
                    <span class="inline-block text-xl font-normal leading-normal align-middle text-black800">View User</span>
                </div>


            <div class="flex justify-end items-center w-1/2">
                <a href="{{route('admin.users.edit',$user->id)}}" class="links_item_cta mr-2">
                    <span class="w-[36px] h-[36px] rounded-full bg-[#F6F6F6] inline-flex justify-center items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                        <path d="M9.81523 2.26514L0 12.0803V14.3454H2.26502L12.0803 4.53016L9.81523 2.26514Z" fill="#000"></path>
                        <path d="M12.0803 2.18641e-06L10.5703 1.51001L12.8353 3.77502L14.3453 2.26501L12.0803 2.18641e-06Z" fill="#000"></path>
                        </svg>
                    </span>
                </a>

                <!-- delete icon  -->
                <a href="javascript:void(0);" data-delete-url="{{route('admin.users.delete',$user->id)}}" class=" delete_user  desh_delate_bg dash_circle mr-2 font-medium text-gray-600 hover:underline">
                    <img src="{{asset('images/delete_icon_red.svg')}}">
                </a>

                <div class=" relative  triple_dots_main flex justify-center items-center">
                    <a href="javascript:void(0);" class=" relative  triple_dots_action_btn triple_dots ">
                        <img src="{{asset('images/triple_dottts.svg')}}" alt="More Options">
                    </a>
                    <div class="dots_hover_sec list_link_frame_section top-[45px] absolute w-[172px] rounded-[5px] py-[15px] bg-[#fff] z-[2] -left-[130px] custom-shadow afclr hidden"> <ul class="frame_list_items_popup">
                <li class="text-left  change_status_user_list">
                    <a data-src="#change_status_popup" data-fancybox href="javascript:void(0);" class=" pl-[15px] change_status_user capitalize inline-block w-full py-2 hover:bg-siteYellow text-[#000] duration-300" data-user-id="{{$user->id}}" data-status-change-url="{{route('admin.users.status.update',$user->id)}}" data-user-status="{{$user->status}}" >Change Status</a>
                </li>
                <li class="text-left"><a href="javascript:void(0);" class="pl-[15px] change_subscription_user capitalize inline-block w-full py-2 hover:bg-siteYellow text-[#000] duration-300" id="">Change Subscription</a></li></ul> </div></div>
            </div>
        </div>

        <div class="pt-[42px] px-[36px] lg:px-[17px] lg:py-[25px] xl:py-10 bg-[#F6F6F6] pb-[100px]  !pb-[100px]  min-h-screen  lg:flex lg:justify-center ">
            <div class="lg:hidden mb-[36px] lg:mb-[20px] flex flex-col">
                        <div class="back-button">
                            <a href="{{ route('admin.users.list') }}" class="links_item_cta inline-flex py-1 px-2 border border-[#C8C0C0] rounded-md bg-[#fff] hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
                                <img class="w-[15px]" src="{{asset('images/short_arrow.svg')}}">
                                <span class="inline-block text-base leading-normal align-middle text-black800 ml-[10px] font-medium">
                                    ALL USERS
                                </span>
                            </a>
                        </div>
                        <span class="inline-block text-[26px] font-normal leading-normal align-middle text-black800">
                        User View
                        </span>
            </div>
            <!--  -->
            <div class=" max-w-[768px] w-full bg-white lg:bg-[#F6F6F6] rounded-[10px] px-12  lg:px-[0px] py-9 lg:py-[0px]">
                <div class="booking_section">

                    @if($modifiedUrl!='')
                    <h4 class="inline-block pb-[13px] text-sm font-normal leading-4 text-left text-black">Company Logo</h4>

                            <div class="flex px-0 mb-[13px] overflow-hidden mb-6 image_container">
                                <div class="relative rounded-[10px] border border-[#D8D5D0] w-1/3 p-[10px]
                                w-[200px] h-[150px]
                                bg-white overflow-hidden logo_image">
                                    <div class="w-full h-full
                                        flex flex-col items-center justify-center overflow-hidden">
                                        <img src="{{ isset($modifiedUrl) ? $modifiedUrl : asset('images/User-Profile.png') }}" alt=""
                                        class="CompanyImageUrl object-contain max-h-full max-w-full">
                                    </div>
                                </div>
                            </div>
                        @endif
                        <!-- <p class="text-[12px] font-normal text-[#8B8B8B] mb-[20px]">Required image size: 150x150px.</p> -->
                        <!-- basic information  -->

                        @if(Helper::getUserMeta($user->id,'owner_name') || $user->mobile || Helper::getUserMeta($user->id,'company_name')  || Helper::getUserMeta($user->id,'company_phone_number') || Helper::getUserMeta($user->id,'email') )
                            <div class="basic_information_sec pb-[15px]">


                                <div class="basic_info_sec_head mb-5">
                                    <h4 class="capitalize text-[20px] font-normal ">basic information</h4>
                                </div>

                                @if(!empty(Helper::getUserMeta($user->id,'company_name')))

                                        <div class="basic_information_sec_inner justify-center comapny_name_left_sec flex py-[14px]">
                                            <div class="basic_information_sec_left w-1/2 text-base flex items-center"><p class="capitalize whitespace-normal break-all">company name:</p></div>
                                            <div class="basic_information_sec_right comapny_name_right_sec w-1/2 text-base pl-2 flex items-center">
                                                <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'company_name')}}</p>
                                            </div>

                                        </div>
                                @endif

                                @if(!empty(Helper::getUserMeta($user->id,'company_phone_number')))

                                    <div class="basic_information_sec_inner justify-center  flex py-[14px]">
                                        <div class="basic_information_sec_left      company_phone_number_left_sec w-1/2 text-base flex items-center"><p class="capitalize whitespace-normal break-all">company phone number:</p>
                                        </div>
                                        <div class="basic_information_sec_right company_phone_number_right_sec w-1/2 text-base pl-2 flex items-center">
                                            <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'company_phone_number_country_code')}}&nbsp;{{Helper::getUserMeta($user->id,'company_phone_number')}}</p>
                                        </div>

                                    </div>

                                @endif

                                @if(!empty(Helper::getUserMeta($user->id, 'owner_name')))

                                    <div class="basic_information_sec_inner owner_name_left_sec justify-center  flex py-[14px]">
                                        <div class="basic_information_sec_left w-1/2 text-base flex items-center">
                                            <p class="capitalize whitespace-normal break-all">Owner name:</p>
                                        </div>
                                        <div class="basic_information_sec_right owner_name_right_sec w-1/2 text-base pl-2 flex items-center">
                                            <p class="capitalize whitespace-normal break-all">{{ucwords(Helper::getUserMeta($user->id, 'owner_name'))}}</p>
                                        </div>

                                    </div>
                                @endif

                                @if(!empty($user->mobile))
                                    <div class="basic_information_sec_inner justify-center mobile_left_sec  flex py-[14px]">
                                        <div class="basic_information_sec_left w-1/2 text-base flex items-center">
                                            <p class="capitalize whitespace-normal break-all">login mobile number:</p>
                                        </div>
                                        <div class="basic_information_sec_right mobile_right_sec w-1/2 text-base pl-2 flex items-center">
                                            <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'user_mobile_country_code')}}&nbsp;{{$user->mobile}}</p>
                                        </div>

                                    </div>
                                @endif


                                @if(!empty(Helper::getUserMeta($user->id,'email')))
                                    <div class="basic_information_sec_inner email_left_sec justify-center  flex py-[14px]">
                                        <div class="basic_information_sec_left w-1/2 text-base flex items-center">
                                            <p class="capitalize whitespace-normal break-all">email address:</p>
                                            </div>
                                        <div class="basic_information_sec_right email_right_sec w-1/2 text-base pl-2 flex items-center">
                                            <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'email')}}</p>
                                        </div>

                                    </div>
                                @endif

                                <!-- status section -->
                                @if(!empty($user->status))
                                    <div class="basic_information_sec_inner justify-center  flex py-[14px]">
                                        <div class="basic_information_sec_left status_left_sec w-1/2 text-base flex items-center">
                                            <p class="capitalize whitespace-normal break-all">status:</p>
                                        </div>
                                        <div class="basic_information_sec_right status_right_sec w-1/2 text-base pl-2 flex items-center ">
                                            @if(strcmp($user->status,'inactive')==0)
                                            <div class="status status_inactive xl:inline-block text-center">
                                                <span class="status_active  capitalize lg:text-[13px]">inactive</span>
                                            </div>
                                            @else
                                                <div class="status  xl:inline-block text-center">
                                                    <span class="  capitalize lg:text-[13px]">active</span>
                                                </div>
                                            @endif
                                        </div>

                                    </div>
                                @endif

                            </div>
                        @endif

                    <!-- adress section -->
                        <div class="address_sec mt-6 md:mt-0">
                            @if( !empty(Helper::getUserMeta($user->id,'plot_shop_number')) || !empty(Helper::getUserMeta($user->id,'street_name'))  || !empty(Helper::getUserMeta($user->id,'city')) || !empty(Helper::getUserMeta($user->id,'zip')) || !empty(Helper::getUserMeta($user->id,'street_name')) )

                                <div class="address_sec_head mb-5">
                                    <h4 class="text-[20px] capitalize">address</h4>
                                </div>


                                @if(!empty(Helper::getUserMeta($user->id,'plot_shop_number')))
                                    <div class="address_sec_inner flex justify-center  py-[14px]">
                                        <div class="basic_information_sec_left plot_shop_num_left_sec  w-1/2 text-base flex items-center"><p class="capitalize whitespace-normal break-all">Plot/Shop Number:</p> </div>
                                        <div class="basic_information_sec_right plot_shop_num_right_sec w-1/2 text-base pl-2 flex items-center">
                                            <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'plot_shop_number')}}</p>
                                        </div>

                                    </div>
                                @endif

                                @if(!empty(Helper::getUserMeta($user->id,'state')))
                                    <div class="address_sec_inner justify-center  flex py-[14px]">
                                        <div class="basic_information_sec_left state_left_sec w-1/2 text-base flex items-center"><p class="capitalize whitespace-normal break-all">state:</p> </div>
                                        <div class="basic_information_sec_right state_right_sec w-1/2 text-base pl-2 flex items-center">
                                            <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'state')}}</p>
                                        </div>

                                    </div>
                                @endif

                                @if(!empty(Helper::getUserMeta($user->id,'city')))
                                <div class="address_sec_inner justify-center  flex py-[14px]">
                                    <div class="basic_information_sec_left city_left_sec w-1/2 text-base flex items-center"><p class="capitalize whitespace-normal break-all">city:</p> </div>
                                    <div class="basic_information_sec_right city_right_sec w-1/2 text-base pl-2 flex items-center">
                                        <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'city')}}</p>
                                    </div>

                                </div>
                                @endif

                                @if(!empty(Helper::getUserMeta($user->id,'zip')))
                                    <div class="address_sec_inner justify-center  flex py-[14px]">
                                        <div class="basic_information_sec_left zip_left_sec w-1/2 text-base flex items-center"><p class="capitalize whitespace-normal break-all">zip code:</p> </div>
                                        <div class="basic_information_sec_right zip_right_sec w-1/2 text-base pl-2 flex items-center">
                                            <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'zip')}}</p>
                                        </div>

                                    </div>
                                @endif

                                @if(!empty(Helper::getUserMeta($user->id,'street_name')))
                                <div class="address_sec_inner justify-center  flex py-[14px]">
                                    <div class="basic_information_sec_left street_name_left_sec w-1/2 text-base flex items-center"><p class="capitalize whitespace-normal break-all">street:</p> </div>
                                    <div class="basic_information_sec_right street_name_right_sec w-1/2 text-base pl-2 flex items-center">
                                        <p class="capitalize whitespace-normal break-all">{{Helper::getUserMeta($user->id,'street_name')}}</p>
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
                                    @foreach($drivers as $key => $item)
                                    <div class="mb-[10px]">
                                    @php $i = $key + 1; @endphp
                                    <p>Driver: {{$i}}</p>
                                    @if(!empty($item->driver_name))
                                        <div class="address_sec_inner justify-center flex py-[4px]">
                                            <div class="basic_information_sec_left street_name_left_sec w-1/2 text-base flex items-center"><p class="capitalize whitespace-normal break-all">Driver Name:</p> </div>
                                            <div class="basic_information_sec_right driver_name_right_sec w-1/2 text-base pl-2 flex items-center">
                                                <p class="capitalize whitespace-normal break-all">{{$item->driver_name}}</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if(!empty($item->driver_mobile))
                                        <div class="address_sec_inner justify-center  flex py-[4px]">
                                            <div class="basic_information_sec_left street_name_left_sec w-1/2 text-base flex items-center"><p class="capitalize whitespace-normal break-all">Driver Mobile:</p> </div>
                                            <div class="basic_information_sec_right driver_mobile_right_sec w-1/2 text-base pl-2 flex items-center">
                                                <p class="capitalize whitespace-normal break-all">{{$item->driver_mobile_country_code}}&nbsp;{{$item->driver_mobile}}</p>
                                            </div>
                                        </div>

                                    @endif
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- session message  -->
        @if (Session::has('success'))
        <div class=" session_msg_container flex items-center justify-between border border-[#478E1A] bg-[#DDEDD3] text-[#000000] text-sm font-bold px-4 py-3 rounded-lg  mx-auto bottom-3  w-3/5 sticky left-0 right-0 lg:bottom-[75px] lg:w-4/5 md:w-[93%]"
            role="alert">
            <div class="flex items-center">
                <span class="action_icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="12" viewBox="0 0 17 12" fill="none">
                        <path
                            d="M16.6443 0.351508C17.1186 0.820184 17.1186 1.58132 16.6443 2.04999L6.93109 11.6485C6.45681 12.1172 5.68659 12.1172 5.21231 11.6485L0.355708 6.84924C-0.118569 6.38057 -0.118569 5.61943 0.355708 5.15076C0.829985 4.68208 1.60021 4.68208 2.07449 5.15076L6.0736 9.09889L14.9293 0.351508C15.4036 -0.117169 16.1738 -0.117169 16.6481 0.351508H16.6443Z"
                            fill="#478E1A"></path>
                    </svg>
                </span>
                <p class="session_msg ml-[15px] text-[#000000] text-[14px] font-normal">Success.&nbsp;<span
                        class="font-normal text-[#000000]">{{ Session::get('success')}}</span></p>
            </div>
            <a href="javascript:void(0)" class="action_perform">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                    <path
                        d="M9.29074 10.3517L5.29074 6.35174L1.28574 10.3517C1.21607 10.4217 1.1333 10.4773 1.04215 10.5153C0.951005 10.5533 0.853263 10.573 0.754508 10.5732C0.655753 10.5735 0.557919 10.5543 0.466592 10.5167C0.375266 10.4791 0.292235 10.4239 0.22224 10.3542C0.152246 10.2846 0.0966583 10.2018 0.0586519 10.1107C0.0206455 10.0195 0.000964363 9.92176 0.000732217 9.82301C0.000500072 9.72425 0.0197215 9.62642 0.0572989 9.53509C0.0948764 9.44377 0.150074 9.36074 0.21974 9.29074L4.22474 5.29074L0.21974 1.28074C0.0790429 1.14004 -1.48249e-09 0.949216 0 0.75024C1.48249e-09 0.551264 0.079043 0.360438 0.21974 0.21974C0.360438 0.079043 0.551264 9.39873e-09 0.75024 7.91624e-09C0.949216 6.43375e-09 1.14004 0.0790429 1.28074 0.21974L5.28574 4.22474L9.28574 0.21974C9.42644 0.0790429 9.61726 0 9.81624 0C10.0152 0 10.206 0.0790429 10.3467 0.21974C10.4874 0.360438 10.5665 0.551264 10.5665 0.75024C10.5665 0.949216 10.4874 1.14004 10.3467 1.28074L6.34674 5.28574L10.3467 9.28574C10.4874 9.42644 10.5665 9.61726 10.5665 9.81624C10.5665 10.0152 10.4874 10.206 10.3467 10.3467C10.206 10.4874 10.0152 10.5665 9.81624 10.5665C9.61726 10.5665 9.42644 10.4874 9.28574 10.3467L9.29074 10.3517Z"
                        fill="black"></path>
                </svg>
            </a>
        </div>
        @endif


    </div>
    <!-- change status fancybox starts here -->
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
                                            <select id="user_status_change" class="w-full py-3 pl-5 pr-10 text-base font-normal leading-normal text-black bg-white border rounded-md appearance-none required_field  border-black500 focus:outline-none focus:border-siteYellow drop_location_select">
                                                <option value="0" selected disabled class="capitalize text-midgray fontGrotesk">Select status</option>
                                                <option value="active" class="capitalize">Active</option>
                                                <option value="inactive" class="capitalize">Deactivate</option>
                                            </select>
                                    </div>
                                </div>


                                <div class="flex justify-center pt-[23px] change_status_popup_action_btns">
                                    <div class="flex items-center w-1/2 mr-2">
                                        <a href=" javascript:void(0);"
                                            class="py-[8px] px-[40px] w-full text-[#000] text-center  save_status  capitalize rounded-md bg-siteYellow border border-siteYellow transition-all
                                            duration-300 hover:bg-siteYellow400" id="save_status_btn">
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



<!-- navigation -->
<script>

    $(document).ready(function () {

        setTimeout(function() {
            $('.session_msg_container').slideUp();
            }, 10000);

    });
    //  delete user functionality
     $('body').on('click','.delete_user',function(){
        console.log('from delete:',$(this).data('delete-url'));
        const deleteUrl=$(this).data('delete-url');
        const deleteId=$(this).data('user-id');
        let listRoute= "{{route('admin.users.list')}}";
        Swal.fire({
            title: 'Are you sure want to delete?',
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: 'YES, DELETE IT!',
            cancelButtonText: 'NO, CANCEL'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                    url: deleteUrl,
                        type: "post",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            'ids': deleteId,
                        },
                        dataType: 'json',
                        success: function (data) {
                        if (data.success) {

                            Swal.fire({
                                title: 'User has been deleted',
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

                                    window.location=listRoute;
                                }else{
                                    window.location=listRoute;
                                }
                            });

                        }
                        else {

                        }
                        },
                        error: function (data) {
                        }
                    });
                }
            });
    });


    $('body').off('click','.triple_dots_action_btn');
    $('body').on('click', '.triple_dots_action_btn', function(e) {
        e.stopPropagation();
        var isActive = $(this).hasClass('active');
        $('.dots_hover_sec').not($(this).siblings('.dots_hover_sec')).slideUp();
        $(this).siblings('.dots_hover_sec').slideToggle(100);

    });

    $("body").click(function(e) {
            $(".dots_hover_sec").slideUp();
            $('.triple_dots_action_btn').removeClass('active');
    });

     $('body').on('click','.change_status_user',function(){
         let status= $(this).data('user-status');
         console.log('status_change:',status);
        $("#user_status_change option[value=" + status + "]").prop('selected', true);

    });


    // statusChange popup save btn
    $('body').on('click','#save_status_btn',function(e){
        e.preventDefault();
        const statusChangeUrl=$(this).closest('.modal_scroll_off').find('#change_statusPopup').data('status-change-url');
        let id=$(this).closest('.modal_scroll_off').find('#change_statusPopup').data('user-id');
        let status=$('#user_status_change').val();
        if(status==0){
            Swal.fire({
                        title: 'status is required',
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'popup_updated',
                            title: 'popup_title',
                            actions: 'btn_confirmation',
                        }
                    });
                    return;
        }
        else{
            Swal.fire({
                title: 'Are you sure you want to update the status ?',
                icon: 'error',
                showCancelButton: true,
                confirmButtonText: 'YES, UPDATE IT!',
                cancelButtonText: 'NO, CANCEL'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                        url: statusChangeUrl,
                            type: "post",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                'ids': id,
                                'status':status,
                            },
                            dataType: 'json',
                            success: function (data) {
                            if (data.success) {

                                $("#user_status_change option[value=" + status + "]").prop('selected', true);
                                $('.change_status_user').attr('data-user-status',status);
                                $('#change_statusPopup').attr('data-user-status',status);

                                $('.change_status_user_list').html('');

                                $('.change_status_user_list').append('<a data-src="#change_status_popup" data-fancybox href="javascript:void(0);" class="pl-[15px] change_status_user capitalize inline-block w-full py-2 hover:bg-siteYellow text-[#000] duration-300" data-user-id="{{$user->id}}" data-status-change-url="{{route('admin.users.status.update',$user->id)}}" data-user-status="'+status+'">Change Status</a>');
                                console.log('ajax_status:',status);

                                if(status==='inactive'){


                                    $('.status_right_sec').html('');
                                    $('.basic_information_sec_right.status_right_sec').append('<div class="status status_inactive xl:inline-block text-center"><span class="status_active  capitalize lg:text-[13px]">inactive</span></div> ');

                                }else{
                                    $('.status_right_sec').html('');
                                    $('.basic_information_sec_right.status_right_sec').append('<div class="status xl:inline-block text-center"><span class="status_active bg-[#ECF9E2] capitalize lg:text-[13px]">active</span></div>');
                                }

                                Swal.fire({
                                    title: 'status has been updated',
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        popup: 'popup_updated',
                                        title: 'popup_title',
                                        actions: 'btn_confirmation',
                                    },

                                });

                            }
                            else {
                                console.log('else part');
                            alert('Error occured.');
                            }
                            },
                            error: function (data) {
                            }
                        });
                    }
                });
        }
    });
    $('body').on('click','#cancel_status_btn',function(e){
        e.preventDefault();
        $.fancybox.close();
    });
</script>
@endsection
