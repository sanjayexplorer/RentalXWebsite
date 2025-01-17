@extends('layouts.partner')
@section('title', 'Partner Dashboard')
@section('content')
<style>
    .clickable_content{
        cursor: pointer;
    }

    .viewIcon img{
        width: 30px
    }
    .viewIcon{
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .car_showcase_content{
        border-bottom: 1px solid #E7E7E7;

    }
    .car_showcase_content:last-child{
        border-bottom: 0;
    }

    .viewIcon{
    opacity: 0; visibility: hidden;
    position: absolute;
    content: '';
    width: 100%;
    height: 100%;
    transition-property: all;
    transition-timing-function: cubic-bezier(.4,0,.2,1);
    transition-duration: .15s;
    background:rgba(255, 255, 255, 0.459);
    }

    .clickable_content:hover  .viewIcon{
        transition-property: all;
        transition-timing-function: cubic-bezier(.4,0,.2,1);
        transition-duration: .15s;
        opacity: 1;
        visibility: visible
    }

    .adj_image_size{
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .img_fx_height{
        height: 80px;
    }

    @media screen and (min-width: 479px) {
    .img_fx_height{
    height: 80px;
    }

    }

    /* @media screen and (min-width: 640px) {
    .img_fx_height{
    height: 200px;
    }
    .upload_Height{
    padding:16px;
    }
    } */


</style>
<div>
    <div class="px-3 pt-2 pb-20 main_sec bg-lightgray md:px-10 tab:pt-0">

        {{-- <div class="hidden md:pt-4 top_heading md:block">
            <h4>Dashboard</h4>
        </div> --}}

        <!---- showcase cards----->
        <div class="mb-4">
            <!-- showcase_cards_box -->
            <div class="flex flex-wrap items-center pt-4 mb-4 showcase_cards_sec tab:pt-7 ">
                <div class=" showcase_cards_inner flex-auto w-[49%] pr-[5px] mb-[10px] md:w-[24%]">
                    <div class="showcase_card_main_box bg-[#DEF2F3] flex items-center  rounded-md px-5 py-5">
                        <div class="flex justify-center w-full box_item_content">
                            <div class="flex flex-col w-full">
                                <p class="text-[14px] xvs:text-base font-normal 2xl:text-xl">Total Cars added</p>
                                <h5 class="text-2xl font-semibold 2xl:text-3xl">{{count($cars)}}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class=" showcase_cards_inner flex-auto w-[49%] pl-[5px] md:pr-[5px] mb-[10px] md:w-[24%]">
                    <div class="showcase_card_main_box bg-[#FEE9E9] flex items-center  rounded-md px-5 py-5">
                        <div class="flex justify-center w-full box_item_content">
                            <div class="flex flex-col w-full">
                                <p class="text-[14px] xvs:text-base font-normal 2xl:text-xl">Cars In 7 Days</p>
                                <h5 class="text-2xl font-semibold 2xl:text-3xl">{{$sevenDaysCarsCount}}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                <div class=" showcase_cards_inner flex-auto w-[49%] pr-[5px] md:pl-[5px] mb-[10px] md:w-[24%]">
                    <div class="showcase_card_main_box bg-[#EAEEFF] flex items-center  rounded-md px-5 py-5">
                        <div class="flex justify-center w-full box_item_content">
                            <div class="flex flex-col w-full">
                                <p class="text-[14px] xvs:text-base font-normal 2xl:text-xl">Cars This Month</p>
                                <h5 class="text-2xl font-semibold 2xl:text-3xl">{{$carsThisMonthCount}}</h5>
                            </div>

                        </div>
                    </div>
                </div>
                <div class=" showcase_cards_inner flex-auto w-[49%] pl-[5px] mb-[10px] md:w-[24%]">
                    <div class="showcase_card_main_box bg-[#E2EED4] flex items-center  rounded-md px-5 py-5">
                        <div class="flex justify-center w-full box_item_content">
                            <div class="flex flex-col w-full">
                                <p class="text-[14px] xvs:text-base font-normal 2xl:text-xl">Booking Rate</p>
                                <h5 class="text-2xl font-semibold 2xl:text-3xl">80%</h5>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap justify-start">
            <div class="w-full px-3 mb-8 md:w-1/2">
                <div class="hidden w-full mb-4 title_sec md:flex ">
                    <div class="flex w-1/2 ">
                        <h4 class="text-base capitalize ">bookings</h4>
                    </div>

                    @if(false)
                    <div class="flex w-1/2 ">
                        <div class="w-full text-right">
                            <a href="javascript:void(0)" class="text-base capitalize ">view all</a>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="bg-white rounded w-full p-[10px] md:py-[10px] md:px-[20px]">
                    <div class="">
                        <div class="">


                            <div class="flex w-full mb-4 title_sec md:hidden ">
                                <div class="flex w-1/2 ">
                                    <h4 class="text-base capitalize ">bookings</h4>
                                </div>
                                @if(false)
                                <div class="flex w-1/2 ">
                                    <div class="w-full text-right">
                                        <a href="javascript:void(0)" class="text-base capitalize ">view all</a>
                                    </div>
                                </div>
                                @endif
                            </div>


                            <div class="flex justify-center cars_showcase_content_height items-center min-h-[200px]">
                                <div class="capitalize">No Bookings Found</div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full px-3 mb-8 md:w-1/2">
                <div class="hidden w-full mb-4 title_sec md:flex ">
                    <div class="flex w-1/2 ">
                        <h4 class="text-base capitalize ">cars</h4>
                    </div>
                    @if(count($cars)>=5)
                        <div class="flex w-1/2 ">
                            <div class="w-full text-right">
                                <a href="{{route('partner.car.list')}}" class="text-base capitalize ">view all</a>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded w-full p-[10px] md:py-[10px] md:px-[20px]">
                    <div class="">
                        <div class=" ">

                            <div class="flex w-full mb-4 title_sec md:hidden">
                                <div class="flex w-1/2 ">
                                    <h4 class="text-base capitalize ">cars</h4>
                                </div>
                                @if(count($cars)>=5)
                                    <div class="flex w-1/2 ">
                                        <div class="w-full text-right">
                                            <a href="{{route('partner.car.list')}}" class="text-base capitalize ">view all</a>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="cars_showcase_content_height">


                                @if(count($cars)>0)
                                @foreach($cars as $car)
                                    @php
                                        $thumbnails = Helper::getCarPhotoById($car->id);
                                        if($thumbnails)
                                        {
                                                foreach($thumbnails as $thumbnail)
                                                {
                                                    $image = Helper::getPhotoById($thumbnail->imageId);
                                                    $carImageUrl=$image->url;
                                                }
                                        }

                                        $carImageUrl = $carImageUrl ?? asset('images/no_image.svg');
                                        $modifiedImgUrl = $carImageUrl;
                                    @endphp

                            <div data-href="{{route('partner.car.view',$car->id)}}"
                                class="relative flex justify-center items-center car_showcase_content clickable_content mb-3 " >
                                <div class="viewIcon">
                                    <img src="{{asset('images/eye-svgrepo.svg')}}" alt="view">
                                </div>
                                <div class="w-full car_showcase_content_inner flex">
                                    <div class="w-1/2">
                                        <div class="img_container bg-white p-4">
                                            <div class="img_content img_fx_height">
                                                <img src="{{$modifiedImgUrl}}" alt="{{$car->name}}" class="adj_image_size">
                                             </div>
                                        </div>
                                    </div>
                                    <div class="w-1/2 flex items-center jhustify-center">
                                        <div class="car_details w-full flex flex-col py-2 items-center">
                                            <div> {{$car->name}}</div>
                                            <div> {{$car->registration_number }}</div>

                                        </div>

                                    </div>

                                </div>
                            </div>
                            @endforeach
                            </div>
                            @else
                            <div class="flex justify-center items-center min-h-[200px]">
                                <div class="capitalize ">No Pickups Found</div>
                            </div>

                            @endif

                        </div>
                    </div>
                </div>
            </div>

        </div>


    </div>

    <!-- navigation -->
    @include('layouts.navigation')

</div>

<script>

    $('body').on('click','.clickable_content',function(e){

        window.location = $(this).data("href");
    })

    $('.showcase_card_main_box').matchHeight();
    $('.cars_showcase_content_height').matchHeight();
</script>
@endsection

