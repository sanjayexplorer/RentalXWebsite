@extends('layouts.partner')
@section('title','Share Cars')
@section('content')
<style>
/* swalfire */
.btn_confirmation button.swal2-confirm{border-radius: 25px;}
/* end */
/*for header none of search  */
.header_bar { display: none;  }
.right_dashboard_section>.right_dashboard_section_inner { padding-top: 0px;}
.booking_img_container{cursor: pointer;}
.booking_img_container.active{background-color: #F3CD0E}
.checkbox_bg_row{ position: absolute; top: 50%; left: 25px; transform: translate(-50%, -50%); display: block; }
.checkbox_bg_rowg input:checked ~ .checkmark { background-color: #fff; border: none; }
.checkbox_bg_rowg .checkmark::after { border: solid #F3CD0E; border-width: 0 2px 2px 0; -webkit-transform: rotate(45deg); -ms-transform: rotate(45deg);
transform: rotate(45deg);}
.checkbox_bg_rowg .checkmark { border-radius: 4px; border: 1px solid #F3CD0E; }
 @media screen and (max-width: 992px) {
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
            <span class="inline-block text-xl font-normal leading-normal align-middle text-black800">Select cars to share</span>
        </div>
    </div>
        <!--  -->

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
            Select cars to share
            </span>
        </div>
        <!--  -->

            <div class="w-full bg-white lg:bg-[#F6F6F6] rounded-[10px] px-12 lg:px-[0px] py-9 lg:py-[0px]">
                <div class="flex justify-start items-center lg:w-full w-1/2 relative px-4 mb-6">
                    <div class="flex justify-end mr-[15px]">

                        <div class="flex flex-wrap w-[130px] break-words">
                            <label
                                class="chckBoxes_container border border-[#F3CD0E] rounded-[4px]">
                                <p class="text-xs mb-0 font-normal checkbox_title capitalize py-[10px] ml-[17px] mr-[20px]">
                                    Select All
                                </p>
                                <input type="checkbox" name="car_type"
                                    value=""
                                    class="choice_checkBox choice_checkBox_select clear_checkbox">
                                <span class="checkmark checkbox_checkmark"></span>
                            </label>
                        </div>

                    </div>

                    <div class="flex justify-end ml-[15px]">

                      <div class="flex flex-wrap w-[130px] break-words">
                        <label
                            class="chckBoxes_container  border border-[#F3CD0E] rounded-[4px]">
                            <p class="text-xs mb-0 font-normal checkbox_title text-[#272522] capitalize py-[10px] ml-[17px] mr-[20px]">
                                Deselect All
                            </p>
                            <input type="checkbox" name="car_type"
                                value=""
                                class="choice_checkBox choice_checkBox_deselect clear_checkbox">
                            <span class="checkmark checkbox_checkmark"></span>
                        </label>
                    </div>
                  </div>
                </div>

                <div class="booking_section">
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
                                                    <input type="checkbox" name="car_type" class="checkbox_for_car choice_checkBox"
                                                        value="{{$car->id}}">
                                                    <span class="checkmark checkbox_checkmark !ml-0"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @if($modifiedImgUrl)
                                    <div class="w-full flex flex-wrap items-center justify-between pl-7 sm:pl-0">
                                        <div class="w-3/4 md:w-full">
                                            <div class="flex items-center flex-wrap -mx-3">
                                                <div class="flex items-center w-1/4 px-3 md:w-full md:mb-5 sm:justify-center">
                                                        <img class="block w-[120px] h-[60px] object-contain" src="{{ $modifiedImgUrl }}" alt="car">
                                                </div>
                                                <div class="px-3 w-[35%] md:w-1/2 sm:w-full mb-2 sm:text-center">
                                                    <div class="h-full">
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
                                                <div class="px-3 w-2/5 md:w-1/2 sm:w-full sm:text-center">
                                                    <p class="text-base font-medium leading-normal text-[#272522] ">
                                                        ₹<span>{{number_format($car->price, 0, '.', ',')}}</span> per day</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <!--  -->
                            </div>
                        </div>
                    </div>
                @endforeach
                @endif
                </div>
                </div>
                <div class="mt-5 form_btn_sec w-1/2 px-4 md:w-full mb-5 afclr">
                    <input type="submit" id="share_car_btn" class="inline-block w-full px-5 py-3 text-opacity-40 font-medium leading-tight transition-all
                    duration-300 border border-siteYellow rounded-[4px] cursor-pointer bg-siteYellow  md:px-[20px] md:py-[14px] sm:py-[12px] transition-all
                    duration-500 ease-0 hover:bg-[#d7b50a]  disabled:opacity-40 disabled:cursor-not-allowed text-base md:text-sm" value="SHARE SELECTED CARS">
                </div>
            </div>
        </div>
        <!-- navigation -->
        @include('layouts.navigation')
    </div>

    <script>
     $('.booking_img').on('click', function(e) {
            e.preventDefault();
            var $container = $(this).find('.booking_img_container');
            $container.toggleClass('active');
            var $checkbox = $(this).find('.checkbox_for_car');
            $checkbox.prop('checked', $container.hasClass('active'));
        });

        $('.choice_checkBox_select').on('change', function() {
            if($(this).prop('checked')){
                $('.checkbox_for_car').prop('checked', true);
                $('.booking_img_container').addClass('active');
                $('.choice_checkBox_deselect').prop('checked', false);
            }else{
                $('.checkbox_for_car').prop('checked', false);
                $('.booking_img_container').removeClass('active');
            }
        });

        $('.choice_checkBox_deselect').on('change', function() {
            if($(this).prop('checked')){
                $('.checkbox_for_car').prop('checked', false);
                $('.choice_checkBox_select').prop('checked', false);
                $('.booking_img_container').removeClass('active');
            }
        });

        var agentId = '{{$agentId}}';

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

    $('#share_car_btn').on('click', function (e) {
        e.preventDefault();
        var carIds = getCheckedCarIds();
        if (!carIds) {
            return;
        }
        console.log('carIdArr', carIds);
        $(".loader").css("display", "inline-flex");
        $(".overlay_sections").css("display", "block");
        $.ajax({
            type: "POST",
            url: "{{route('partner.share.cars')}}",
            data: {
                '_token':'{{ csrf_token() }}',
                'carIds': carIds,
                'agentId': agentId
            },
            dataType: "json",
            success: function (data) {
            if(data.success){
                Swal.fire({
                    title: 'Cars has been shared',
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
                        window.location.href = '{{route('partner.agent.list')}}';

                    }else{
                        window.location.href = '{{route('partner.agent.list')}}';
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
                        window.location.href = '{{route('partner.agent.list')}}';
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
    });
</script>
@endsection
