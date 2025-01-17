@extends('layouts.admin')
@section('title', 'View Setting')
@section('content')
<style>
.header_bar{display:none;}
.right_dashboard_section > .right_dashboard_section_inner {padding-top: 0px;}
.iti--allow-dropdown{ width:100% !important; }

@media screen and (max-width: 992px){
.main_header_container {display:none;}
}

</style>

<!-- 1st Part -->
    <div class="lg:flex hidden lg:bg-white lg:mb-[0px] lg:px-[17px] lg:py-[15px] mb-[20px] justify-start items-center">
        <div class="flex justify-start items-center w-1/2">
            <a href="{{route('admin.users.list')}}" class="links_item_cta inline-block p-2 mr-2 border border-[#C8C0C0] rounded-md hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
                <img class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
            </a>
            <span class="inline-block text-xl font-normal leading-normal align-middle text-black800">My Settings</span>
        </div>

        <div class="flex justify-end items-center w-1/2">
        <a href="javascript:void(0);" class="mr-2">
            <span class="w-[36px] h-[36px] rounded-full bg-[#F6F6F6] inline-flex justify-center items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                <path d="M9.81523 2.26514L0 12.0803V14.3454H2.26502L12.0803 4.53016L9.81523 2.26514Z" fill="#000"></path>
                <path d="M12.0803 2.18641e-06L10.5703 1.51001L12.8353 3.77502L14.3453 2.26501L12.0803 2.18641e-06Z" fill="#000"></path>
                </svg>
            </span>
        </a>
        </div>
</div>


<div class="pt-[42px] px-[36px] lg:px-[17px] lg:py-[25px] xl:py-10 bg-[#F6F6F6] pb-[100px] min-h-screen lg:flex lg:justify-center">
<div class="lg:hidden mb-[36px] lg:mb-[20px] flex justify-between items-center max-w-[768px] w-full">
            <div>
                <div class="back-button">
                    <a href="javascript:void(0);" class="inline-flex py-1 px-2 border border-[#C8C0C0] rounded-md bg-[#fff] hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
                        <span class="inline-block text-base leading-normal align-middle text-black800  font-medium">
                        My Settings
                        </span>
                    </a>
                </div>
                <div class="flex justify-between items-center">
                    <span class="inline-block text-[26px] font-normal leading-normal align-middle text-black800">View Profile</span>
                </div>
            </div>
        </div>


<div class="max-w-[768px] w-full bg-white lg:bg-[#F6F6F6] rounded-[10px] px-12  lg:px-[0px] py-9 lg:py-[0px]">
    <div class="booking_section">

        <div class="booking_section_inner flex justify-between ">

            <div class="basic_information_sec w-full pb-[15px]">

                    <div class="basic_information_sec_inner flex mb-4">
                        <div class="basic_information_sec_left w-1/2 text-base"><p class="capitalize">mobile number:</p> </div>
                        <div class="basic_information_sec_right w-1/2 text-base pl-2">
                            <p class="capitalize">{{auth()->user()->mobile}}</p>
                        </div>

                    </div>


                    <div class="basic_information_sec_inner flex mb-4">
                        <div class="basic_information_sec_left w-1/2 text-base"><p class="capitalize">password :</p> </div>
                        <div class="basic_information_sec_right w-1/2 text-base pl-2">
                            <p class="capitalize">***********</p>
                        </div>

                    </div>

            </div>

            <div class="edit_profile_icon lg:hidden">
                <a href="javascript:void(0);" class="edit_profile" id="edit_profileBtn">
                    <span class="w-[36px] h-[36px] rounded-full bg-[#F6F6F6] inline-flex justify-center items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                        <path d="M9.81523 2.26514L0 12.0803V14.3454H2.26502L12.0803 4.53016L9.81523 2.26514Z" fill="#000"/>
                        <path d="M12.0803 2.18641e-06L10.5703 1.51001L12.8353 3.77502L14.3453 2.26501L12.0803 2.18641e-06Z" fill="#000"/>
                        </svg>
                    </span>
                </a>
            </div>
        </div>
    </div>
</div>
</div>
<script>
</script>
@endsection


