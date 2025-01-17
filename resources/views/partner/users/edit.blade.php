@extends('layouts.partner')
@section('title', 'Edit User')
@section('content')
<style>
.error{ color:#ff0000;}
.required{color:#ff0000;}
.btn_confirmation button.swal2-confirm{border-radius: 25px;}
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

<div class="lg:flex hidden lg:bg-white lg:mb-[0px] lg:px-[17px] lg:py-[15px] mb-[20px] justify-start items-center">
    <div class="flex justify-start items-center w-full">
        <a href="{{route('partner.users.list')}}" class="links_item_cta inline-block p-2 mr-2 border border-[#C8C0C0] rounded-md hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
            <img class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
        </a>
        <span class="inline-block text-xl font-normal leading-normal align-middle text-black800">Edit User</span>
    </div>
</div>

<div class="pt-[42px] px-[36px] lg:px-[17px] lg:py-[25px] xl:py-10 bg-[#F6F6F6] !pb-[100px]  min-h-screen lg:flex lg:justify-center">
<!--  -->
<div class="lg:hidden mb-[36px] lg:mb-[20px] flex flex-col">
    <div class="back-button">
        <a href="{{ route('partner.users.list') }}" class=" links_item_cta inline-flex py-1 px-2 border border-[#C8C0C0] rounded-md bg-[#fff] hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
            <img class="w-[15px]" src="{{asset('images/short_arrow.svg')}}">
            <span class="inline-block text-base leading-normal align-middle text-black800 ml-[10px] font-medium">
                ALL USERS
            </span>
        </a>
    </div>
    <span class="inline-block text-[26px] font-normal leading-normal align-middle text-black800">
    Edit User
    </span>
</div>
<!--  -->


<div class="max-w-[768px] w-full bg-white lg:bg-[#F6F6F6] rounded-[10px] px-12  lg:px-[0px] py-9 lg:py-[0px]">
    <div class="booking_section">
        <form action="{{route('partner.users.edit.post', $driver->id)}}" method="post" onsubmit="return validate()">
          @csrf
                <div class="pb-5">
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full px-3 sm:w-full inp_container ">
                            <label for="name" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Name<span class="text-[#ff0000]">*</span></label>
                            <input type="text" tabindex="-1" name="name" id="name"  class="required_fields w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow alpha_validate"  data-error-msg="Name is required" placeholder="Enter name" value="{{$driver->driver_name}}">
                            <span class="hidden required text-sm"></span>
                                @if ($errors->has('name'))
                                <div class="error">
                                    <ul>
                                        <li>{{ $errors->first('name') }}</li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Mobile Number -->
                <div class="pb-5">
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full px-3 sm:w-full inp_container mobile_number mobile_number_container">
                            <label for="mobile_number" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black ">
                               Mobile Number<span class="text-[#ff0000]">*</span>
                            </label>
                            <input type="text" tabindex="-1" name="mobile_number" id="mobile_number" class="required_fields phone_validation w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow"
                            placeholder="Enter mobile number"  data-error-msg="Mobile number is required"
                            value="{{$driver->driver_mobile_country_code }} {{$driver->driver_mobile}}">
                            <input type="hidden" name="mobile_number_country_code" class="mobile_number_country_code"
                            value="{{$driver->mobile_number_country_code }}">
                            <span class="hidden required text-sm"></span>
                            @if ($errors->has('mobile_number'))
                            <div class="error">
                                <ul>
                                    <li>{{ $errors->first('mobile_number') }}</li>
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- user type -->
                <div class="pb-5">
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full px-3 sm:w-full inp_container">
                            <label for="user_type"
                            class="block pb-2.5 text-sm font-normal leading-4 text-left text-black ">
                            User Type <span class="text-[#ff0000]">*</span></label>
                            <select name="user_type" tabindex="-1" id="user_type"
                            onchange="checkBlankField(this.id,'User Type is required','errorUserType')"
                            class="required_fields w-full py-3 pl-5 pr-10 text-base font-normal leading-normal text-black bg-white
                            border rounded-md appearance-none border-black500 focus:outline-none focus:border-siteYellow drop_location_select"
                            data-error-msg="User type is required">
                            <option value="" class="capitalize text-midgray fontGrotesk" disabled>Select User Type</option>
                                <option value="driver" class="capitalize" {{ old('user_type') == 'driver' ? 'selected' : '' }}>Driver</option>
                            </select>
                            <span class="required errorUserType text-sm"></span>
                                @if ($errors->has('user_type'))
                                <div class="error">
                                    <ul>
                                        <li>{{ $errors->first('user_type') }}</li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="my-5 form_btn_sec afclr">
                    <input type="submit" value="SAVE"
                    class="inline-block w-full px-5 py-3 text-opacity-40 text-xl font-medium leading-tight transition-all
                    duration-300 border border-siteYellow rounded-[4px] cursor-pointer bg-siteYellow  md:px-[20px]
                    md:py-[14px] md:text-lg sm:text-sm sm:py-[12px] transition-all duration-500 ease-0
                    hover:bg-[#d7b50a]  disabled:opacity-40 disabled:cursor-not-allowed">
                </div>

        </form>
    </div>
</div>
@include('layouts.navigation')
</div>

<script>

    function hideLoader()
{
    $('.loader').css("display", "none");
    $('.overlay_sections').css("display", "none");
}

function checkBlankField(id, msg, msgContainer)
{
    let fieldValue = $("#" + id).val().trim();
    let $msgContainer = $("." + msgContainer);

    if (fieldValue.length < 1) {
        $msgContainer.css("display", "block");
        $msgContainer.html(msg);
    } else {
        $msgContainer.html('');
        $msgContainer.css("display", "none");
    }
}
function validate(){
     $('.loader').css("display", "inline-flex");
    $('.overlay_sections').css("display", "block");
    var hasErrors = false;
    var firstErrorElement = null;
    var required_fields = $('.required_fields');
     required_fields.each(function () {
        if (!$(this).val().trim()) {
            hasErrors = true;
            if (!firstErrorElement) {
                firstErrorElement = $(this);
            }
            $(this).closest('.inp_container').find('.required').show().html($(this).data("error-msg"));
            $(this).closest('.inp_container').find('.error').hide();

        } else {
            $(this).closest('.inp_container').find('.required').hide().empty();
            $(this).closest('.inp_container').find('.error').hide().empty();
        }

        if ($(this).hasClass('phone_validation')) {
            var inputValue = $(this).val().trim();
            var validationSpan = $(this).closest('.inp_container').find('.required');
            if (!inputValue) {
                hasErrors = true;
                validationSpan.html("Mobile number is required");
                 $(this).closest('.inp_container').find('.error').hide().empty();
            } else {
                if (inputValue.length < 10) {
                    hasErrors = true;
                    validationSpan.show().html("Mobile number must be at least 10 digits");
                    $(this).closest('.inp_container').find('.error').hide().empty();
                }
                if(inputValue.length === 10) {
                    validationSpan.empty();
                }
            }
        }

    });

    if (hasErrors) {
        hideLoader();
        Swal.fire({
            title: 'Please fill all required fields',
            icon: 'warning',
            showCancelButton: false,
            confirmButtonText: 'OK',
            customClass: {
                popup: 'popup_updated',
                title: 'popup_title',
                actions: 'btn_confirmation',
            },
        }).then((result) => {
            if (result.isConfirmed && firstErrorElement) {
                $("html, body").animate({
                    scrollTop: parseFloat(firstErrorElement.offset().top) - 150
                }, 500);
            }
        });
        hideLoader();
        return false;
     } else {
        // hideLoader();
        console.log('Validation successful!');
        return true;
    }
}

$('body').on('input', '.alpha_validate', function () {
    let alphabeticValue = $(this).val().replace(/[^a-zA-Z\s]/g, "");
    $(this).val(alphabeticValue);
});

$('body').on('input', '.phone_validation', function() {
    let numericValue = $(this).val().replace(/[^0-9+]/g, "");
    numericValue = numericValue.substring(0, 15);
    $(this).val(numericValue);
});

$('.required_fields').on('keyup', function () {
    var hasErrors = false;
    if (!$(this).val().trim()) {
        hasErrors = true;
        $(this).closest('.inp_container').find('.required').show().html($(this).data("error-msg"));
        $(this).closest('.inp_container').find('.error').hide().empty();
    } else {
        $(this).closest('.inp_container').find('.required').hide().empty();
        $(this).closest('.inp_container').find('.error').hide().empty();
    }

    if ($(this).hasClass('phone_validation')) {
        var inputValue = $(this).val().trim();
        var validationSpan = $(this).closest('.inp_container').find('.required');
        $(this).closest('.inp_container').find('.error').hide().empty();

        if (!inputValue) {
            validationSpan.show().html("Mobile number is required");
            $(this).closest('.inp_container').find('.error').hide().empty();
        } else {
            if (inputValue.length < 10) {
                validationSpan.show().html("Mobile number must be at least 10 digits");
            }
                else {
                validationSpan.empty();
            }
        }
        }
    });


    var utilsScript = "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js";
    var input = document.querySelector("#mobile_number");
    window.intlTelInput(input, {
    initialCountry: "auto",
    preferredCountries:["in", "us", "gb"],
    formatOnDisplay: false,
    separateDialCode: true,
    geoIpLookup: callback => {
    fetch("https://ipapi.co/json")
    .then(res => res.json())
    .then(data => callback(data.country_code))
    .catch(() => callback("in"));
    },
    utilsScript: utilsScript
    });

    input.addEventListener("countrychange", function() {
        var mobile_number_country_code = $(this).closest('.mobile_number_container').find('.iti__selected-dial-code').html();
        $('.mobile_number_country_code').val(mobile_number_country_code);
    });

</script>
@endsection
