@extends('layouts.partner')
@section('title', 'Car Details')
@section('content')
<style>
/* swalfire */
.btn_confirmation button.swal2-confirm{border-radius: 25px;}
/* end */
/* rs icon */
.rupee_price_field{position: relative;}
.rupee_price_field .rupee_icon {display: flex;align-items: center;}
span.rupee_icon {position: absolute !important; top: 0; bottom: 0; margin: auto; left: 9px; height: 100%; text-align: center; font-size: 16px; color: #000; font-weight: 500;}
/* end */
.image_content::-webkit-scrollbar{ border-radius: 8px; height: 9px; background-color: #e7e3e3; }
.image_content::-webkit-scrollbar-track{-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); border-radius: 8px; background-color: #F5F5F5; }
.image_content::-webkit-scrollbar-thumb{ border-radius: 8px; -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3); background-color: #cecaca; }
 /* fancybox */
.fancybox-bg { background: #B1B1B1; }
.fancybox-content { padding: 0px 0px; /* width: 20%; */ width: 320px; border-radius: 6px; }
body .change_color_popup .fancybox-close-small { width: 70px; height: 70px; color: #2a2a2a; opacity: 1; padding: 18px;
top: -2px; right: 5px; }
/* end */
.booking_inner_b {  overflow: hidden; width: 200px; height: 150px;}
.imageProfileUrl { width: 100%; object-fit: contain; height: 100%;}
.featured_picture_sec { left: 10px;z-index: 9; top: 8px; }
.featured_picture_sec img { width: 21px; }
/*  */
.border-l-none{  border-left:none; }
.border-b-none{ border-bottom:none;  }
.border-r-none{ border-right:none; }
/*for header none of search  */
.header_bar { display: none;  }
.right_dashboard_section>.right_dashboard_section_inner { padding-top: 0px;}
.validateError{ color:#ff0000;}
.featured_picture_sec img { width: 20px; }
.featured_picture_sec {left: 40px; z-index: 9; top: 7px; }
.car_box{ /* width:200px; */ width:100%; height: 150px; }
@media screen and (min-width:767px) {
.car_box{ width:100%; height: 150px; }
}
 @media screen and (max-width: 992px) {
.main_header_container{ display: none; }
}



</style>

<div class="">

    <!-- 1st Part -->
    {{-- <div class="lg:flex hidden lg:bg-white lg:mb-[0px] lg:px-[17px] lg:py-[15px] mb-[20px] justify-start items-center">
        <a href="{{ route('partner.car.list') }}" class="inline-block p-2 mr-2 border border-[#C8C0C0] rounded-md hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
            <img class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
        </a>
        <span class="inline-block text-xl font-normal leading-normal align-middle text-black800">Car Details</span>
    </div> --}}
    <div class="lg:flex hidden lg:bg-white lg:mb-[0px] lg:px-[17px] lg:py-[15px] mb-[20px] justify-start items-center">
        <div class="flex justify-start items-center w-1/2">
            <a href="{{ route('partner.car.list') }}" class="links_item_cta inline-block p-2 mr-2 border border-[#C8C0C0] rounded-md hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
                <img class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
            </a>
            <span class="inline-block text-xl font-normal leading-normal align-middle text-black800">Car Details</span>
        </div>

        <div class="flex justify-end items-center w-1/2">
            <a href="{{ route('partner.car.edit', $car->id) }}" class="links_item_cta dash_circle desh_edit_bg font-medium text-gray-600 hover:underline">
                <img src="{{asset('images/edit_icon.svg')}}">
            </a>
        </div>
    </div>

    <!-- 2nd Part -->
    <div class="pt-[42px] px-[36px] lg:px-[17px] lg:py-[25px] xl:py-10 bg-[#F6F6F6] !pb-[100px]  min-h-screen lg:flex lg:justify-center relative">

        <div class="lg:hidden mb-[36px] lg:mb-[20px] flex justify-between items-center max-w-[768px] w-full">
            <div>
                <div class="back-button">
                    <a href="{{route('partner.car.list')}}" class="links_item_cta inline-flex py-1 px-2 border border-[#C8C0C0] rounded-md bg-[#fff] hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
                        <img  class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
                        <span class="inline-block text-base leading-normal align-middle text-black800 ml-[10px] font-medium">
                            ALL CARS
                        </span>
                    </a>
                </div>
                <div class="flex justify-between items-center">
                    <span class="inline-block text-[26px] font-normal leading-normal align-middle text-black800">Car Details</span>
                </div>
            </div>

            <div class="flex justify-between items-center">
                <a href="{{ route('partner.car.edit', $car->id) }}" class="links_item_cta dash_circle desh_edit_bg font-medium text-gray-600 hover:underline">
                    <img src="{{asset('images/edit_icon.svg')}}">
                </a>
            </div>
        </div>

        <div class="max-w-[768px] w-full bg-white lg:bg-[#F6F6F6] rounded-[10px] px-12  lg:px-[0px] py-9 lg:py-[0px]">
            <div class="booking_section">

                        @php
                        $imgArray=[];
                        $thumbnails = Helper::getCarPhotoById($car->id);

                        @endphp

                        @if(count($thumbnails)>0)

                            <!-- images -->
                            <div class="flex px-0 mb-3 mb-6 image_container md:block" >
                                <div class=" w-[100%] md:flex-wrap md:w-full scroller_cars lg:overflow-unset ">
                                    <div class=" flex flex-wrap image_content md:flex-wrap -mx-2.5 ">
                                        @foreach($thumbnails as $thumbnail)
                                            @php
                                                $image = Helper::getPhotoById($thumbnail->imageId);
                                                // echo "<pre>";
                                                // print_r($image);
                                                // die;
                                                $carImageUrl=$image->url;
                                                $featuredCarImage=$thumbnail->featured;
                                                $imgArray[]=$carImageUrl;

                                                $modifiedImgUrl =  $carImageUrl;
                                                $carImageuniqueid=$image->ImageUniqueId;
                                            @endphp

                                            <div class="main_car_image_container w-1/3 sm:w-1/2 px-2.5 py-2.5">
                                            <div class=" rounded-[12px]  ">
                                                <div class="car_box relative border flex flex-col justify-between h-full bg-[#fff] rounded-[12px] ">

                                                    <div class="booking_inner_bb logo_image w-full h-full flex flex-col items-center justify-center overflow-hidden ">
                                                        <a class="w-full h-full" href="{{$modifiedImgUrl}}" data-fancybox="editgallery">
                                                            <img class="p-[10px]  block w-full main_img imageProfileUrl toZoom cursor-pointer" src="{{$modifiedImgUrl}}" alt="img">
                                                            <input type="hidden" class="car_photos_hidden" name="photoId[]" value="{{$thumbnail->imageId}}" id="imageId_{{$carImageuniqueid}}"/>
                                                        </a>

                                                    </div>

                                                    <div class=" flex relative">
                                                        @if(strcmp($featuredCarImage,'set')==0)
                                                        <a class="clicked_area featured_picture_anchor flex item-center justify-center p-2 w-full border border-l-none border-r-none border-b-none cursor-default" href="javascript:void(0);">
                                                            <div class="featured_picture_sec">
                                                                <input type="hidden" id="featured_img" name="featured_check" class="featured_check"  value="{{$thumbnail->imageId}}">
                                                                <img src="{{ asset('images/featured_icon.svg') }}" class="featured_icon w-4 h-4">
                                                            </div>
                                                        </a>
                                                        @else
                                                            <a class="clicked_area featured_picture_anchor flex item-center justify-center p-2 w-full border border-r-none border-l-none border-b-none cursor-default" href="javascript:void(0);">
                                                            <img class="w-4 h-4 block" src="{{ asset('images/blank_star_img.svg') }}" alt="icon">
                                                            </a>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>
                                            </div>

                                        @endforeach

                                    </div>
                                </div>
                            </div>
                        @endif

                    {{-- basic information  --}}
                    @if(($car->name) || ($car->registration_number) || ($car->car_type) || ($car->fuel_type)  || ($car->manufacturing_year) || ($car->price) || ($car->transmission) || ($car->roof_type) || (Helper::getCarMeta($car->id, 'plate_type')) )

                        <div class="basic_information_sec pb-[15px]">

                            <div class="basic_info_sec_head mb-5">
                                <h4 class="capitalize text-[20px] font-normal ">basic information</h4>
                            </div>

                            <!-- Car Name -->
                            @if(!empty($car->name))
                                <div class="basic_information_sec_inner owner_name_left_sec justify-center  flex py-[14px]">
                                    <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">Car Name:</p>
                                    </div>
                                    <div class="basic_information_sec_right car_name_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">{{ucwords($car->name)}}</p>
                                    </div>

                                </div>
                            @endif

                            @if(!empty($car->registration_number))
                                <div class="basic_information_sec_inner owner_name_left_sec justify-center  flex py-[14px]">
                                    <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">Registration Number:</p>
                                    </div>
                                    <div class="basic_information_sec_right registration_number_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">{{ucwords($car->registration_number)}}</p>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($car->car_type))
                                <div class="basic_information_sec_inner justify-center mobile_left_sec  flex py-[14px]">
                                    <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">Segment:</p>
                                    </div>
                                    <div class="basic_information_sec_right car_type_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">
                                            @if(strcmp($car->car_type,'sedan')==0)Sedan @elseif(strcmp($car->car_type,'hatchback')==0)Hatchback @elseif(strcmp($car->car_type,'compact_suv')==0)Compact Suv @elseif(strcmp($car->car_type,'suv')==0)Suv @elseif(strcmp($car->car_type,'luxury')==0)Luxury @elseif(strcmp($car->car_type,'off_road')==0)Off Road @endif
                                        </p>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($car->fuel_type))
                                <div class="basic_information_sec_inner email_left_sec justify-center  flex py-[14px]">
                                    <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm"><p class="capitalize whitespace-normal break-all">Fuel type:</p>
                                    </div>
                                    <div class="basic_information_sec_right fuel_type_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">{{$car->fuel_type}}</p>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($car->manufacturing_year))
                                <div class="basic_information_sec_inner justify-center comapny_name_left_sec flex py-[14px]">
                                    <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">Manufacturing Year:</p>
                                    </div>
                                    <div class="basic_information_sec_right manufacturing_year_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">{{$car->manufacturing_year}}</p>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($car->price))
                                <div class="basic_information_sec_inner justify-center comapny_name_left_sec flex py-[14px]">
                                    <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">Price(per day):</p>
                                    </div>
                                    <div class="basic_information_sec_right price_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">₹ {{number_format($car->price, 0, '.', ',')}}</p>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($car->transmission))
                                <div class="basic_information_sec_inner justify-center comapny_name_left_sec flex py-[14px]">
                                    <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">Transmission Type:</p>
                                    </div>
                                    <div class="basic_information_sec_right transmission_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">{{$car->transmission}}</p>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($car->roof_type))
                                <div class="basic_information_sec_inner justify-center comapny_name_left_sec flex py-[14px]">
                                    <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">Roof Type:</p>
                                    </div>
                                    <div class="basic_information_sec_right roof_type_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">{{$car->roof_type}}</p>
                                    </div>
                                </div>
                            @endif

                            @if(!empty(Helper::getCarMeta($car->id, 'plate_type')))
                                <div class="basic_information_sec_inner justify-center comapny_name_left_sec flex py-[14px]">
                                    <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm"><p class="capitalize whitespace-normal break-all">Plate Type:</p>
                                    </div>
                                    <div class="basic_information_sec_right plate_type_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">{{Helper::getCarMeta($car->id, 'plate_type')}}</p>
                                    </div>
                                </div>
                            @endif

                        </div>

                    @endif

            </div>
        </div>
        {{--  --}}

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

setTimeout(function() {
$('.session_msg_container').slideUp();
}, 10000);

$(".action_perform").on('click', function(e) {
    $('.session_msg_container').slideUp();
});

</script>
@endsection
