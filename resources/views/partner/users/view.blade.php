@extends('layouts.partner')
@section('title', 'User View')
@section('content')
<style>
.header_bar{display:none;}
.right_dashboard_section > .right_dashboard_section_inner { padding-top: 0px; }
@media screen and (max-width: 992px){
.main_header_container  {display:none;}
}
</style>
<div class="">
    <!-- 1st Part -->
    <div class="lg:flex hidden lg:bg-white lg:mb-[0px] lg:px-[17px] lg:py-[15px] mb-[20px] justify-start items-center">
        <div class="flex justify-start items-center w-full">
            <a href="{{route('partner.users.list')}}" class="links_item_cta inline-block p-2 mr-2 border border-[#C8C0C0] rounded-md 
             hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
            <img class="w-[15px]" src="{{ asset('public/images/short_arrow.svg') }}">
            </a>
            <span class="inline-block text-xl font-normal leading-normal align-middle text-black800">User View</span>
        </div>
    
        <div class="flex justify-end items-center w-1/2">
            <a href="javascript:void(0);" data-user-id="{{$driver->id}}" data-delete-url="{{route('partner.users.delete',$driver->id)}}" class="delete_user desh_delate_bg dash_circle font-medium text-gray-600 hover:underline">
                <img src="{{asset('public/images/delete_icon_red.svg')}}">
            </a>
        </div>
    </div>

    <!-- 2nd Part -->
    <div class="pt-[42px] px-[36px] lg:px-[17px] lg:py-[25px] xl:py-10 bg-[#F6F6F6] !pb-[100px]  min-h-screen lg:flex lg:justify-center relative">
        <!--  -->
        <div class="lg:hidden mb-[36px] lg:mb-[20px] flex flex-col">
            <div class="back-button">
                <a href="{{ route('partner.users.list') }}" class=" links_item_cta inline-flex py-1 px-2 border border-[#C8C0C0] rounded-md bg-[#fff] hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
                    <img class="w-[15px]" src="{{asset('public/images/short_arrow.svg')}}">
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

        <div class="max-w-[768px] w-full bg-white lg:bg-[#F6F6F6] rounded-[10px] px-12  lg:px-[0px] py-9 lg:py-[0px]">
            <div class="booking_section">
                    @if( $driver->driver_name || $driver->driver_mobile )
                        <div class="basic_information_sec pb-[15px]">
                            <div class="basic_info_sec_head mb-5">
                                <h4 class="capitalize text-[20px] font-normal">basic information</h4>
                            </div>
                            @if(!empty($driver->driver_name))
                                <div class="basic_information_sec_inner owner_name_left_sec justify-center  flex py-[14px]">
                                    <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">Name:</p> 
                                    </div>
                                    <div class="basic_information_sec_right login_mobile_number_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">{{$driver->driver_name}}</p>
                                    </div>
                                </div>
                            @endif

                            @if(!empty($driver->driver_mobile))
                                <div class="basic_information_sec_inner owner_name_left_sec justify-center  flex py-[14px]">
                                    <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">Mobile Number:</p> 
                                    </div>
                                    <div class="basic_information_sec_right login_mobile_number_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                        <p class="capitalize whitespace-normal break-all">{{$driver->driver_mobile_country_code	}} {{$driver->driver_mobile}}</p>
                                    </div>
                                </div>
                            @endif
                         <div class="basic_information_sec_inner justify-center comapny_name_left_sec flex py-[14px]">
                            <div class="basic_information_sec_left w-1/2 flex items-center text-base md:text-sm"><p class="capitalize whitespace-normal break-all">User Type:</p> </div>
                            <div class="basic_information_sec_right comapny_name_right_sec w-1/2 pl-2 flex items-center text-base md:text-sm">
                                <p class="capitalize whitespace-normal break-all">Driver</p>
                            </div>
                        </div>
                     </div>
                    @endif
            </div>
        </div>
    </div>
    <!-- session message  -->
    @if (Session::has('success'))
    <div class="session_msg_container flex items-center justify-between border border-[#478E1A] bg-[#DDEDD3]
     text-[#000000] text-sm font-bold px-4 py-3 rounded-lg  mx-auto bottom-3 w-3/5 sticky left-0 right-0 lg:bottom-[75px] lg:w-4/5 md:w-[93%]" role="alert">
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

$('body').on('click','.delete_user',function(){
         console.log('from exi:',$(this).data('delete-url'));
         const exitUrl = $(this).data('delete-url');
         const exitId = $(this).data('user-id');
         Swal.fire({
            title: 'Are you sure want to delete this user?',
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: 'YES, DELETE IT!',
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
                              window.location.href="{{route('partner.users.list')}}";
                            }
                            else{
                                window.location.href="{{route('partner.users.list')}}";
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
</script>
@endsection


