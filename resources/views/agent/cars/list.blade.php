@extends('layouts.agent')
@section('title', 'Manage Cars')
@section('content')
<style>
.fancybox-container{ z-index: 999!important; }
.fancybox-close-small{ display:none!important; }
.popup_x_close svg{ width: 21px; }
.popup_x_close svg >path{ stroke: #fff; }
.p_apply_btn{ display: block; } .popup_header > h1{ font-size: 22px; }
.adj_image_size{ width: 100%; height:100%; object-fit: contain; }
.upload_Height{ height: 100px; }
.top-3{ top:12px; }
.top-5{ top:20px; }
.bottom-5{ bottom:20px; }
.upload_Height{ padding:16px; height: 200px; }
.out_cre_book { padding: 0px 0 25px; }
.adj_height_book_inner{ height:200px;}

/* adjust scroll */
.desktp_min_height{ min-height: 100vh; }
.adj_empty_car_sec{ height: calc(100vh - 155px); }

@media screen and (max-width: 992px){
    .desktp_min_height{ min-height: calc(100vh - 97px); }
    .adj_empty_car_sec{ height: calc(100vh - 256px); }
}
/* end */
@media screen and (max-width: 1550px) {
.upload_Height{ height: 173px;}
}
@media screen and (max-width: 1280px){
.main_sec { padding: 0 20px; }
.upload_Height { height: 150px; }
}
@media screen and (max-width: 992px){
.out_cre_book { padding: 20px 0 25px; }
.main_sec { padding: 0 16px; padding-bottom: 80px }
}
@media screen and (max-width: 767px){
.out_cre_book { padding: 10px 0 20px;}
.upload_Height { height: 107px;}
}
@media screen and (max-width: 479px){
.upload_Height { height: 95px; padding: 10px; }
}

</style>

<div class="">
    <div class="desktp_min_height py-9 px-9 lg:pt-[20px] lg:pb-[100px] lg:px-[17px] bg-[#F6F6F6]  ">

            <div class="flex items-center mb-[36px] lg:mb-[20px] md:flex-wrap">
                <div class="w-1/2 flex justify-start items-center">
                    <a href="javascript:void(0);" class="hidden py-2 mr-2">
                        <img class="w-[42px]" src="{{asset('images/panel-back-arrow.svg')}}">
                    </a>
                    <span
                        class="inline-block text-black800 text-[26px] md:text-xl font-normal leading-normal align-middle">Manage Cars</span>
                </div>

                <!-- Partner List Option -->
                <div class="w-1/2 text-right md:w-full ">
                    <!-- <a href="{{route('agent.car.add')}}" class="inline-flex rounded-[4px] items-center text-black text-base md:text-sm font-normal  bg-siteYellow px-[20px] py-2.5 md:px-[15px]">
                        <img class="mr-[8px] w-[17px] relative md:top-[-0px] top-[-2px]" src="{{asset('images/fa-add-icon.svg')}}" alt="">
                            ADD NEW CAR
                    </a> -->
                    <div class="w-full pl-3 sm:w-full flex flex-col items-end">
                        <label for="partner_id" class=" block pb-2.5 font-normal leading-4 text-left text-black text-sm">
                            Select Company Name
                        </label>
                        <select  name="partner_id"  id="partner_id"   class="w-[300px] py-3 pl-5 pr-10 font-normal leading-normal text-black bg-white border rounded-md appearance-none required_field border-black500 focus:outline-none focus:border-siteYellow drop_location_select text-base capitalize"
                        data-error-msg="Company name is required">
                            <option value="0" selected disabled class="capitalize text-midgray fontGrotesk">
                                Select Company Name
                            </option>
                            @foreach ($partners as $partner)
                                <option value="{{ $partner }}" class="capitalize fontGrotesk" {{ old('partner_id') == $partner ? 'selected' : '' }} >
                                    {{ Helper::getUserMeta($partner, 'company_name') }}
                                </option>
                            @endforeach
                        </select>
                        <span class="scrollForReq hidden errorCompanyName validateError text-sm"></span>
                        @error('partner_id')
                            <span class="inline-block validateError text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>


            <div class="cars_mn_outer">
                @if(count($initialCars)>0)
                <div class="cars_main_box flex flex-wrap -mx-4" id="cars-container">
                    @foreach ($initialCars as $car)
                            @php
                            $thumbnails = Helper::getFeaturedSetCarPhotoById($car->id);
                            $carImageUrl = asset('images/no_image.svg');
                            foreach ($thumbnails as $thumbnail) {
                                $image = Helper::getPhotoById($thumbnail->imageId);
                                $carImageUrl = $image->url;
                            }
                            $modifiedImgUrl = $carImageUrl;

                        @endphp

                        <div class="booking_img w-1/2 px-4 md:w-full mb-7" data-car-id="{{ $car->id }}" data-href="{{ route('agent.car.view', $car->id) }}">
                            <!-- Your existing car item content goes here -->
                            <div class="relative px-5 py-4 bg-white rounded-[9px]">
                                    <div class="flex py-3">
                                        <div class="w-2/5">
                                            <div class="flex items-center justify-center ">
                                                <img class="block w-[240px] h-[120px] m-auto object-contain "
                                                    src="{{ $modifiedImgUrl }}"
                                                    alt="{{ $car->name }} car">
                                            </div>
                                        </div>
                                        <div class="w-3/5">
                                            <div class="h-full xl:pl-4 xl:pr-4 pl-10 pr-6">
                                                <div class="flex flex-col ">
                                                    <h3 class="text-base font-medium leading-4 text-[#2B2B2B] pb-1">
                                                        {{ ucwords($car->name) }}</h3>
                                                    <p class="text-sm font-normal leading-none text-[#2B2B2B] pb-1.5 last:pb-0">
                                                        {{ strtoupper($car->registration_number) }}
                                                    </p>
                                                    <p class="capitalize text-sm font-medium leading-none text-[#2B2B2B] pb-1.5 last:pb-0">
                                                        {{-- {{ strtoupper($car->registration_number) }} --}}
                                                        {{ Helper::getUserMetaByCarId($car->id, 'company_name') }}
                                                    </p>
                                                    <div class="block py-4">
                                                        <p class="text-sm font-medium leading-none text-[#272522]">
                                                            ₹ {{number_format($car->price, 0, '.', ',')}} per day</p>
                                                    </div>
                                                    <div class="w-full">
                                                        <a class="inline-block links_item_cta" href="{{ route('agent.car.view', $car->id) }}" >
                                                            <div class="flex items-center">
                                                                <div class="w-auto">
                                                                    <span
                                                                        class="text-[13px] font-normal leading-none text-[#342B18]">View Details
                                                                    </span>
                                                                </div>
                                                                <div class="pl-4 min-w-12">
                                                                    <img
                                                                        src="{{ asset('images/right_arrow_cars_page.svg') }}"
                                                                        alt="arrow icon">
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div class="absolute top-4 right-4">
                                        <a href="javascript:void(0);"
                                            class=" delete_btn flex items-center justify-center w-5 h-5"
                                            data-delete-url="{{ route('agent.car.delete', $car->id) }}">
                                            <img src="{{ asset('images/delete_icon_red.svg') }}"
                                                class="delete_icon" alt="delete_icon">
                                        </a>
                                    </div> -->
                                    <!-- <div class="absolute bottom-4 right-4">
                                        <a href="{{ route('agent.car.edit', $car->id) }}"
                                            class="edit_btn flex items-center justify-center w-5 h-5">
                                            <img src="{{ asset('images/edit_icon_black.svg') }}" class="edit_icon"
                                                alt="edit_icon">
                                        </a>
                                    </div> -->
                                </div>
                        </div>
                    @endforeach
                    <!-- Include the load more content here -->
                    @include('agent.cars.load-more', ['initialCarIds' => $initialCars->pluck('id')->toArray()])
                </div>
                <!-- load more -->
                @if($countCars>10)
                <div class="text-center mt-4">
                    <button class="btn btn-primary" id="load-more">Load More...</button>
                </div>
                @endif
                @else
                <div class="empty_car_section_box bg-lightgray">
                    <div class="adj_empty_car_sec flex items-center justify-center bg-white rounded-md">
                        There is no data to show
                    </div>
                </div>
                @endif
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

$('#partner_id').change(function() {

    var selectedPartnerId = $(this).val();
    console.log("selectedPartnerId",selectedPartnerId);

    $(".loader").css("display", "inline-flex");
    $(".overlay_sections").css("display", "block");

    // Example AJAX call
    $.ajax({
        url: '{{ route('agent.car.ajaxShowCars') }}',
        method: 'POST',
        data:{
            '_token':'{{ csrf_token() }}',
              partner_id: selectedPartnerId
            },
        success: function(data) {

            $(".loader").css("display", "none");
            $(".overlay_sections").css("display", "none");

            // Handle success
            if(data.success)
            {
                $('.cars_main_box').html(data.cars_list);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX request failed:', status, error);
        }
    });

});


$(document).ready(function () {
    var page = 1;
    var loading = false;
    var initialCarIds = {!! json_encode(implode(',', $initialCars->pluck('id')->toArray())) !!};
    function loadMoreCars() {
        if (loading) {
            return;
        }
        loading = true;
        $.ajax({
            url: '{{ route('agent.cars.load-more') }}',
            type: 'GET',
            data: {
                page: page,
                initial_car_ids: initialCarIds
            },
            success: function (data) {
                if (data.trim() !== '') {
                    $('#cars-container').append(data);
                    page++;
                } else {
                    $('#load-more').hide();
                }

                loading = false;
            },
            error: function () {
                loading = false;
            }
        });
    }

    $('#load-more').on('click', function () {
        loadMoreCars();
    });


   $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 1200)
        {
            loadMoreCars();
        }
    });

});

$('#add_car_btn').on('click',function(){

        var user_id = $('#user_id').val();
        if(user_id == null || '') {
            Swal.fire({
            title: 'Please select partner',
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
        else{
          var url = "{{route('agent.car.add', '')}}"+"/"+user_id;
          window.location.href = url;
        }
});

    $('.popup_x_close').on('click', function() {
            $.fancybox.close();
    });

$('#partnerName').on('change', function () {
    var partnerId = $(this).val();
    $(".loader").css("display", "inline-flex");
    $(".overlay_sections").css("display", "block");

    $.ajax({
        type: "POST",
        url: "{{ route('agent.partner.search') }}",
        data: {
            '_token': '{{ csrf_token() }}',
            'partnerId': partnerId
        },
        dataType: "json",
        success: function (data) {
            $(".loader").css("display", "none");
            $(".overlay_sections").css("display", "none");

            if (data.success) {
                if (data.cars_list.length > 0) {
                    $('.cars_main_box').show().html(data.cars_list);
                    $('.empty_car_section_box').empty();
                } else {
                    $('.cars_main_box').hide().empty();
                    $('.empty_car_section_box').html('<div class="flex items-center justify-center min-h-screen bg-white rounded-md">There is no data to show</div>');
                }
            } else {
                console.error('Server responded with an error:', data);
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX request failed:', status, error);
        }
    });
});

$('body').on('click','.delete_btn',function(e){
    var carId = $(this).data('delete-id');
});

$(".add_car_popup").fancybox({
        beforeShow: function(data,item){
    }
});

$('body').on('click', '.action_perform',function(e){
    $('.session_msg_container').slideUp();
});

setTimeout(function() {
    $('.session_msg_container').slideUp();
}, 10000);

$(".action_perform").on('click', function(e) {
    $('.session_msg_container').slideUp();
});


$('body').on('click','.delete_btn',function(e){

    var that=$(this);
    var deleteUrl = $(this).data('delete-url');
    Swal.fire({
        title: 'Are you sure want to delete?',
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'YES, DELETE IT!',
        cancelButtonText: 'NO, CANCEL'
    }).then((result) => {
        if(result.isConfirmed){
            $(".loader").css("display", "inline-flex");
            $(".overlay_sections").css("display", "block");
            $.ajax({
            url: deleteUrl,
            type: 'POST',
            data: {
                '_token':'{{ csrf_token() }}',
            },
            dataType: "json",
            success: function(data) {
                $(that).closest('.booking_img').remove();
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
                })
            },
            complete: function(data) {
            $(".loader").css("display", "none");
            $(".overlay_sections").css("display", "none");
            },
            error: function(xhr, status, error) {}
        });
        }
        });

 });


</script>
@endsection
