@extends('layouts.partner')
@section('title', 'Invite New Agent')
@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
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
.iti--allow-dropdown input { padding-right: 20px !important;}
.container{display: flex; align-items: center; justify-content: flex-start;}
.cart_checkbox_sec , .cart_checkbox_sec_err{ margin-bottom: 12px; margin-top: 12px; position: relative; }
.cart_checkbox_sec p ,.cart_checkbox_sec_err p{ color: #000000; font-size: 14px; font-weight: 600; padding: 15px 20px; margin: 0; margin-bottom: 18px; }
/* label container */
.radio-button-container:hover{ border:1px solid #F3CD0E; }
.cart_check_input { border: 0; clip: rect(0 0 0 0); height: 1px; margin: -1px; overflow: hidden; padding: 0; position: absolute; width: 1px; }
.radio-button-container , .radio-button-container-err { display: flex; align-items: center; color: rgba(0, 0, 0, 0.75); position: relative; line-height: 25px; padding: 12px 30px; cursor: pointer;
font-size: 18px; background-color: #ffffff; }
.radiocheckmark { display: block; width: 17px; height: 17px; border-radius: 100%; background-color: transparent; margin-right: 10px;
 border: 1px solid #f3cd0e; position: relative; }
.cart_check_text { display: block; width: auto%; }
.radiocheckmark:after { content: ''; width: 11px; height: 11px; top: 0; left: 0; bottom: 0;right: 0; margin: auto; background-color: transparent;
 border-radius: 50%; position: absolute; }

 .cart_checkbox_sec input:checked ~.radio-button-container .radiocheckmark:after { background-color: #fff; }
 .cart_checkbox_sec input:checked ~.radio-button-container .radiocheckmark { border: 1px solid #ffffff; }
 .cart_checkbox_sec input:checked ~ .radio-button-container { background-color: #f3cd0e; border:1px solid #F3CD0E; }

 @media screen and (max-width: 992px){
   .main_header_container {display:none;}
}
</style>
<div><a href="javascript:void(0);"><img src="" alt="" id="loaderImage"></a></div>
    <!-- 1st Part -->
    <div class="lg:flex hidden lg:bg-white lg:mb-[0px] lg:px-[17px] lg:py-[15px] mb-[20px] justify-start items-center">
        <a href="{{route('partner.agent.list')}}" class="links_item_cta inline-block p-2 mr-2 border border-[#C8C0C0] rounded-md
        hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
            <img class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
        </a>
        <span class="inline-block text-xl font-normal leading-normal align-middle text-black800">Invite Agent</span>
    </div>

    <!-- 2nd Part -->
    <div class="pt-[42px] px-[36px] lg:px-[17px] lg:py-[25px] xl:py-10 bg-[#F6F6F6] pb-[100px] lg:mb-[60px]  min-h-screen lg:flex lg:justify-center">

        <div class="lg:hidden mb-[36px] lg:mb-[20px] flex flex-col">
            <div class="back-button">
                <a href="{{route('partner.agent.list')}}" class=" links_item_cta inline-flex py-1 px-2 border border-[#C8C0C0] rounded-md bg-[#fff] hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
                    <img  class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
                    <span class="inline-block text-base leading-normal align-middle text-black800 ml-[10px] font-medium">
                        ALL AGENTS
                    </span>
                </a>
            </div>
            <span class="inline-block text-[26px] font-normal leading-normal align-middle text-black800">
                Invite Agent
            </span>
        </div>

        <div class="max-w-[768px] w-full bg-white lg:bg-[#F6F6F6] rounded-[10px] px-12  lg:px-[0px] py-9 lg:py-[0px]">
            <div class="booking_section">
                    <!-- Agent Phone Number -->
                    <div class="lg:bg-[#F6F6F6] lg:block bg-[#ffffff] lg:py-3 lg:px-4 py-0 px-0  z-[2] sticky
                    lg:static top-0 border-[#E4E4E4] border-r-0 lg:border-l-0">
                    <label class="">Mobile number<span class="text-[#ff0000]">*</span></label>
                    <div class="flex justify-between border-[#E1D6BF] rounded-[11px] lg:right-0 bg-[#ffffff] lg:top-0 lg:left-0 lg:flex">
                        <div class="relative w-full search_sec lg:block">
                            <!-- input field -->
                            <form action="#" id="agent_search_form" method="GET" class="flex">
                                <input type="text" name="agent_search" id="agent_search" class="search w-full text-sm outline-none rounded-[4px] border border-[#898376] pt-[15px]
                                pr-[95px] pb-[15px] pl-[20px] " placeholder="Search mobile number" autocomplete="off">
                                <div class="absolute close_desktop flex align-center right-[60px] top-0 bottom-0 hidden"
                                    onclick="document.getElementById('agent_search').value = ''; clearSearch();">
                                <img src="{{asset('images/close-focus-icon.svg')}}" class="w-[12px]">
                                </div>
                            </form>
                            <!-- Profile -->
                            <div class="cursor-pointer input-group-append flex p-[10px] ml-[10px] absolute top-0 bottom-0 right-[0px] bg-[#F3CD0E] border-[1px]
                            border-[#898376] rounded-r-[5px]">
                            <button class="btn" type="submit" id="agent_search_btn">
                                <img class="w-[30px]" src="{{asset('images/black_search_icon.svg')}}" alt="user icon" class="flex">
                            </button>
                            <div class="profile">
                            </div>
                            </div>
                            <!-- </div> -->
                            </div>
                        </div>
                    </div>
                    <!--  -->
                    <div class="agent_list_for_agent_search">
                    </div>
                    </div>
                    <!--  -->
                    <div class="mt-5 form_btn_sec lg:px-4 afclr">
                     <input type="submit" value="NEXT"
                        class="next_share_btn inline-block w-full px-5 py-3 text-opacity-40 font-medium leading-tight transition-all duration-300
                        border border-[#DCDCDC] rounded-[4px] bg-[#DCDCDC] md:px-[20px] md:py-[14px] sm:py-[12px]
                        transition-all duration-500 ease-0 hover:bg-[#DCDCDC] text-[#AEAEAE] text-base md:text-sm " disabled="disabled">
                    </div>
            </div>
    </div>
    @include('layouts.navigation')
</div>
<script>
 var agentExist = false;
$('#agent_search').on('input',function(e){
    e.preventDefault();
    if($(this).val().length < 10){
        $('.cart_checkbox_sec').remove();
        $('.cart_checkbox_sec').remove();
        $('.cart_checkbox_sec_err').remove();
        $('.next_share_btn').prop('disabled', true);
        $('.next_share_btn').css('background-color', '#DCDCDC');
        $('.next_share_btn').css('color', '#AEAEAE');

    }
    if($(this).val().length > 9){
    }
});

$('body').on('click', '.cart_checkbox_sec', function() {
    $('.next_share_btn').prop('disabled', false);
    $('.next_share_btn').css('background-color', '#F3CD0E');
    $('.next_share_btn').css('color', '#000');
    $('.next_share_btn').css('cursor', 'pointer');
});

$('#agent_search_btn').on('click', function(e) {
    e.preventDefault();
    var searchAgentValue = $('#agent_search').val();
    if (searchAgentValue.length < 10){
        $('#agent_search').siblings('.error').show();
    }
    else{
        $(".loader").css("display", "inline-flex");
        $(".overlay_sections").css("display", "block");
        $.ajax({
            url: "{{ route('partner.invite.agent.search')}}",
            type: "POST",
            dataType: "JSON",
            data: {
                '_token': "{{ csrf_token() }}",
                "agent_search": searchAgentValue
            },
            success: function(data) {
                $(".agent_list_for_agent_search").html('');
                if (data.success) {
                    var listAgents = '';
                    var userName = data.userName;
                    var userMobileCountryCode = data.userMobileCountryCode;
                    listAgents += `
                        <div class="cart_checkbox_sec rounded-[4px] lg:py-3 lg:px-4 py-0 px-0 afclr">
                            <input class="cart_check_input agent_id_container" type="radio" name="radio" id="agent_id_${data.agents.id}" data-agent-id="${data.agents.id}">
                            <label class="radio-button-container border border-[#898376] rounded-[4px]" for="agent_id_${data.agents.id}">
                                <span class="radiocheckmark"></span>
                                <span class="cart_check_text">${userName} (${userMobileCountryCode} ${data.agents.mobile})</span>
                            </label>
                        </div>`;
                    $(".agent_list_for_agent_search").html(listAgents);
                    agentExist = true;
                } else {
                    var listAgents = '';
                    listAgents += `
                        <div class="cart_checkbox_sec_err rounded-[4px] lg:py-3 lg:px-4 py-0 px-0 afclr">
                            <div class="radio-button-container-err border border-[#A64F54] rounded-[4px] !bg-[#F0DCDD] !p-[12px]">
                                <span class="px-[10px] py-[5px]"><img src="{{asset('images/red_cross_icon.svg')}}" class="w-[12px]"></span>
                                <span class="font-medium text-sm">Error.&nbsp; </span>
                                <span class="text-sm ">No agent found with the mobile number</span>
                            </div>
                        </div>`;
                    $(".agent_list_for_agent_search").html(listAgents);
                    agentExist = false;
                }
            },
            error: function(error) {

            },
            complete: function(data) {
                $(".loader").css("display", "none");
                $(".overlay_sections").css("display", "none");
            }
        });
        }
    });

    function clearSearch() {
        document.getElementById('agent_search').value = '';
        $('.close_desktop').hide();
    }

    $('body').on('click','.close_desktop',function(e){
        e.preventDefault();
        $('.cart_checkbox_sec').remove();
        $('.cart_checkbox_sec_err').remove();
        $('.next_share_btn').prop('disabled', true);
        $('.next_share_btn').css('background-color', '#DCDCDC');
        $('.next_share_btn').css('cursor', 'unset');
    });

    $('body').on('input', '#agent_search', function() {
    let numericValue = $(this).val().replace(/[^0-9+]/g, "");
    numericValue = numericValue.substring(0, 15);
    $(this).val(numericValue);
    });

 $(document).ready(function() {
    $("#agent_search").keyup(function() {
        var inputVal = $(this).val().trim();
        console.log('inputVal:',inputVal);
        if (inputVal !== '') {
            $('.close_desktop').show();
        } else {
          $('.close_desktop').css('display', 'flex');
        }
    });

    $("#agent_search").blur(function() {
        var inputVal = $(this).val().trim();
        if (inputVal === '') {
            $('.close_desktop').hide();
        }
    });

    $("#agent_search").focus(function() {
      $('.close_desktop').css('display', 'flex');
    });
 });

$('.next_share_btn').on('click',function(e){
e.preventDefault();
console.log('agentExist:',agentExist);
var searchAgentValue = $('#agent_search').val();
var agentId = $('.agent_id_container').data("agent-id");
if(!agentExist){
    $('#agent_search').siblings('.error').show();
}
else{
    $(".loader").css("display", "inline-flex");
    $(".overlay_sections").css("display", "block");
    window.location.href = "share/cars/" + agentId;
}
});
</script>
@endsection
