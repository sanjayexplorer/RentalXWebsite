@extends('layouts.agent')
@section('title', 'Agent Dashboard')
@section('content')
<style>
    .adj_image_size{
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
</style>

<div>
    <div class="px-3 pt-2 pb-20 main_sec bg-lightgray md:px-10 tab:pt-0">
        <div class="mb-4">
            <div class="flex flex-wrap items-center pt-4 mb-4 showcase_cards_sec tab:pt-7 ">
                <div class=" showcase_cards_inner flex-auto w-[49%] pr-[5px] mb-[10px] md:w-[24%]">
                    <div class="showcase_card_main_box bg-[#DEF2F3] flex items-center  rounded-md px-5 py-5">
                        <div class="flex justify-center w-full box_item_content">
                            <div class="flex flex-col w-full">
                                <p class="text-[14px] xvs:text-base font-normal 2xl:text-xl">Partners added</p>
                                <h5 class="text-2xl font-semibold 2xl:text-3xl">{{count($partners)}}</h5>
                            </div>
                        </div>
        
                    </div>
                </div>
                <div class=" showcase_cards_inner flex-auto w-[49%] pl-[5px] md:pr-[5px] mb-[10px] md:w-[24%]">
                    <div class="showcase_card_main_box bg-[#FEE9E9] flex items-center  rounded-md px-5 py-5">
                        <div class="flex justify-center w-full box_item_content">
                            <div class="flex flex-col w-full">
                                <p class="text-[14px] xvs:text-base font-normal 2xl:text-xl">Partners In 7 Days</p>
                                <h5 class="text-2xl font-semibold 2xl:text-3xl">{{$sevenDaysUsersCount}}</h5>
                            </div>
        
                        </div>
        
                    </div>
                </div>
                <div class=" showcase_cards_inner flex-auto w-[49%] pr-[5px] md:pl-[5px] mb-[10px] md:w-[24%]">
                    <div class="showcase_card_main_box bg-[#EAEEFF] flex items-center  rounded-md px-5 py-5">
                        <div class="flex justify-center w-full box_item_content">
                            <div class="flex flex-col w-full">
                                <p class="text-[14px] xvs:text-base font-normal 2xl:text-xl">Partners This Month</p>
                                <h5 class="text-2xl font-semibold 2xl:text-3xl">{{$usersThisMonthCount}}</h5>
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
                        <h4 class="text-base capitalize ">bookings </h4>
                    </div>
                    @if(count($partners)>5)
                    <div class="flex w-1/2 ">
                        <div class="w-full text-right">
                            <a href="javascript:void(0)" class="text-base capitalize ">view all</a>
                        </div>
                    </div>
                    @endif
                </div>
        
                <div class="bg-white rounded w-full p-[10px] md:py-[10px] md:px-[20px]">
                    <div class="">
                        <div class="empty_data ">
        
                            <div class="flex w-full mb-4 title_sec md:hidden ">
                                <div class="flex w-1/2 ">
                                    <h4 class="text-base capitalize ">bookings</h4>
                                </div>
                                @if(count($partners)>5)
                                <div class="flex w-1/2 ">
                                    <div class="w-full text-right">
                                        <a href="javascript:void(0)" class="text-base capitalize ">view all</a>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="flex justify-center items-center min-h-[200px]">
                                <div class="capitalize ">No Bookings Found</div>
                            </div>
                         
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full px-3 mb-8 md:w-1/2">
                <div class="hidden w-full mb-4 title_sec md:flex ">
                    <div class="flex w-1/2 ">
                        <h4 class="text-base capitalize ">pickups</h4>
                    </div>
                    @if(count($partners)>5)
                    <div class="flex w-1/2 ">
                        <div class="w-full text-right">
                            <a href="javascript:void(0)" class="text-base capitalize ">view all</a>
                        </div>
                    </div>
                    @endif
                </div>
        
                <div class="bg-white rounded w-full p-[10px] md:py-[10px] md:px-[20px]">
                    <div class="">
                        <div class="empty_data ">
        
                            <div class="flex w-full mb-4 title_sec md:hidden">
                                <div class="flex w-1/2 ">
                                    <h4 class="text-base capitalize ">pickups</h4>
                                </div>
                                <div class="flex w-1/2 ">
                                    <div class="w-full text-right">
                                        <a href="javascript:void(0)" class="text-base capitalize ">view all</a>
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-center items-center min-h-[200px]">
                                <div class="capitalize ">No Pickups Found</div>
                            </div>
        
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
    $('.showcase_card_main_box').matchHeight();
</script>
@endsection

