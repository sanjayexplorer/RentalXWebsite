@extends('layouts.partner')
@section('title', 'Add New Lead')
@section('content')
<style>
/* rs icon */
.rupee_price_field{position: relative;}
.rupee_price_field .rupee_icon {display: flex;align-items: center;}
span.rupee_icon {position: absolute !important; top: 0; bottom: 0; margin: auto; left: 9px; height: 100%; text-align: center; font-size: 16px; color: #000; font-weight: 500;}
/* end */
.scroller_cars::-webkit-scrollbar{ border-radius: 8px; height: 9px; background-color: #e7e3e3; }
.scroller_cars::-webkit-scrollbar-track{-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); border-radius: 8px; background-color: #F5F5F5; }
.scroller_cars::-webkit-scrollbar-thumb{ border-radius: 8px; -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3); background-color: #cecaca; }
 /* fancybox */
.fancybox-bg { background: #B1B1B1; }
.fancybox-content { padding: 0px 0px; width: 320px; border-radius: 6px; }
body .change_color_popup .fancybox-close-small { width: 70px; height: 70px; color: #2a2a2a; opacity: 1; padding: 18px;
top: -2px; right: 5px; }
/* end */
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
span.rupee_icon{ left:22px;}
.car_box{ width:100%; height: 150px; }
.iti.iti--allow-dropdown{ width: 100%;}
@media screen and (max-width: 992px){
    .main_header_container{ display: none; }
}
@media screen and (min-width:767px) {
    .car_box{ width:100%; height: 150px; }
}
</style>
<div>
    <!-- 1st Part -->
    <div class="lg:flex hidden lg:bg-white lg:mb-[0px] lg:px-[17px] lg:py-[15px] mb-[20px] justify-start items-center">
        <a href="{{ route('partner.leads.list') }}" class="links_item_cta inline-block p-2 mr-2 border border-[#C8C0C0] rounded-md
        hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
            <img class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
        </a>
        <span class="inline-block text-xl font-normal leading-normal align-middle text-black800">Add New Lead</span>
    </div>

    <!-- 2nd Part -->
    <div class="pt-[42px] px-[36px] lg:px-[17px] lg:py-[25px] xl:py-10 bg-[#F6F6F6] !pb-[100px]  min-h-screen lg:flex lg:justify-center">

        <div class="lg:hidden mb-[36px] lg:mb-[20px] flex flex-col">
            <div class="back-button">
                <a href="{{ route('partner.leads.list') }}" class="links_item_cta inline-flex py-1 px-2 border border-[#C8C0C0] rounded-md bg-[#fff] hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
                    <img  class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
                    <span class="inline-block text-base leading-normal align-middle text-black800 ml-[10px] font-medium">
                        ALL LEADS
                    </span>
                </a>
            </div>
            <span class="inline-block text-[26px] font-normal leading-normal align-middle text-black800">
               Add New Lead
            </span>
        </div>

        <div class="max-w-[768px] w-full bg-white lg:bg-[#F6F6F6] rounded-[10px] px-12 lg:px-[0px] py-9 lg:py-[0px]">
            <div class="booking_section">
                <form action="{{ route('partner.leads.add.post') }}" method="POST" onsubmit="return checkForm()">
                    @csrf

                    <!-- Customer Name -->
                    <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full px-3 sm:w-full ">
                                <label class="block pb-2.5 font-normal leading-4 text-left text-black text-sm "
                                    for="customer_name">Customer Name<span class="text-[#ff0000]">*</span></label>
                                <input type="text" tabindex="-1"  name="customer_name" value="{{ old('customer_name') }}"
                                    onkeyup="checkBlankField(this.id,'Customer name is required','errorName')"
                                    onchange="checkBlankField(this.id,'Customer name is required','errorName')"
                                    class="w-full px-5 py-3 font-normal leading-normal text-black bg-white border rounded-md required_field border-black500 focus:outline-none focus:border-siteYellow text-base "
                                    placeholder="Enter Customer Name" id="customer_name" data-error-msg="Customer name is required">
                                    <span class="scrollForReq hidden validateError errorName text-sm"></span>
                                @error('customer_name')
                                    <span class="required inline-block validateError text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Contact no -->
                    <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full px-3 sm:w-full contact_number_container">
                                <label class="block pb-2.5 font-normal leading-4 text-left text-black text-sm" for="contact_number">Contact No.<span class="text-[#ff0000]">*</span>
                                </label>
                                <input type="tel" tabindex="-1" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" onkeyup="validateContactNumber(this); checkBlankField(this.id,'Contact number is required','errorNumber')" onchange="checkBlankField(this.id,'Contact number is required','errorNumber')" class="w-full px-5 py-3 font-normal leading-normal text-black bg-white border rounded-md required_field border-black500 focus:outline-none focus:border-siteYellow text-base" placeholder="Enter Contact Number" data-error-msg="Contact number is required">
                                <input type="hidden" name="contact_mobile_country_code" class="contact_mobile_country_code">
                                    <span class="contactValidateError scrollForReq hidden validateError errorNumber text-sm"></span>
                                @error('contact_number')
                                    <span class="required inline-block validateError text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Pick up Date & Time -->
                    <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full px-3 sm:w-full ">
                                <label class="block pb-2.5 font-normal leading-4 text-left text-black text-sm"
                                    for="pick_up_date_time">Pick up Date & Time<span class="text-[#ff0000]">*</span>
                                </label>
                                <input type="datetime-local" tabindex="-1" name="pick_up_date_time" value="{{ old('pick_up_date_time') }}" onkeyup="checkBlankField(this.id,'Pick up date time is required','errorPickUpDateTime')" onchange="checkBlankField(this.id,'Pick up date time is required','errorPickUpDateTime')" class="w-full px-5 py-3 font-normal leading-normal text-black bg-white border rounded-md required_field border-black500 focus:outline-none focus:border-siteYellow text-base"  placeholder="Enter Pick up Date & Time" id="pick_up_date_time" data-error-msg="Pick up date & time is required">
                                    <span class="scrollForReq hidden validateError errorPickUpDateTime text-sm"></span>
                                @error('pick_up_date_time')
                                    <span class="required inline-block validateError text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- pick_up_location -->
                    <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full px-3 sm:w-full ">
                                <label class="block pb-2.5 font-normal leading-4 text-left text-black text-sm "
                                    for="pick_up_location">Pick Up Location<span class="text-[#ff0000]">*</span></label>
                                <input type="text" tabindex="-1" name="pick_up_location" value="{{ old('car_name') }}"
                                    onkeyup="checkBlankField(this.id,'Pick up location is required','errorPickUpLocation')"
                                    onchange="checkBlankField(this.id,'Pick up location is required','errorPickUpLocation')"
                                    class="w-full px-5 py-3 font-normal leading-normal text-black bg-white border rounded-md required_field border-black500 focus:outline-none focus:border-siteYellow text-base "
                                    placeholder="Enter Pick up location" id="pick_up_location" data-error-msg="Pick up location is required">
                                    <span class="scrollForReq hidden validateError errorPickUpLocation text-sm"></span>
                                @error('pick_up_location')
                                    <span class="required inline-block validateError text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Drop off Date & Time -->
                    <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full px-3 sm:w-full ">
                                <label class="block pb-2.5 font-normal leading-4 text-left text-black text-sm "
                                    for="drop_off_date_time">Drop Off Date & Time<span class="text-[#ff0000]">*</span></label>
                                <input type="datetime-local" tabindex="-1" name="drop_off_date_time" value="{{ old('drop_off_date_time') }}"
                                    onkeyup="checkBlankField(this.id,'Drop off date time is required','errorDropOffDateTime')"
                                    onchange="checkBlankField(this.id,'Drop off date time is required','errorDropOffDateTime')"
                                    class="w-full px-5 py-3 font-normal leading-normal text-black bg-white border rounded-md required_field border-black500 focus:outline-none focus:border-siteYellow text-base "
                                    placeholder="Enter Drop Off Date & Time" id="drop_off_date_time" data-error-msg="Drop off date & time is required">
                                    <span class="scrollForReq hidden validateError errorDropOffDateTime text-sm"></span>
                                @error('pick_up_date_time')
                                    <span class="required inline-block validateError text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- drop_off_location -->
                    <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full px-3 sm:w-full ">
                                <label class="block pb-2.5 font-normal leading-4 text-left text-black text-sm "
                                    for="pick_up_location">Drop Off Location<span class="text-[#ff0000]">*</span></label>
                                <input type="text" tabindex="-1" name="drop_off_location" value="{{ old('car_name') }}"
                                    onkeyup="checkBlankField(this.id,'Drop off location is required','errorDropOffLocation')"
                                    onchange="checkBlankField(this.id,'Drop off location is required','errorDropOffLocation')"
                                    class="w-full px-5 py-3 font-normal leading-normal text-black bg-white border rounded-md required_field border-black500 focus:outline-none focus:border-siteYellow text-base "
                                    placeholder="Enter Drop off location" id="drop_off_location" data-error-msg="Pick up location is required">
                                    <span class="scrollForReq hidden validateError errorDropOffLocation text-sm"></span>
                                @error('drop_off_location')
                                    <span class="required inline-block validateError text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- car model -->
                    <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full px-3 sm:w-full ">
                                <label class="block pb-2.5 font-normal leading-4 text-left text-black text-sm "
                                    for="car_model">Car Model<span class="text-[#ff0000]">*</span></label>
                                <input type="text" tabindex="-1" name="car_model" value="{{ old('car_model') }}"
                                    onkeyup="checkBlankField(this.id,'Car model is required','errorCarModel')"
                                    onchange="checkBlankField(this.id,'Car_model is required','errorCarModel')"
                                    class="w-full px-5 py-3 font-normal leading-normal text-black bg-white border rounded-md required_field border-black500 focus:outline-none focus:border-siteYellow text-base "
                                    placeholder="Enter Car model" id="car_model" data-error-msg="Car model is required">
                                    <span class="scrollForReq hidden validateError errorCarModel text-sm"></span>
                                @error('car_model')
                                    <span class="required inline-block validateError text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- car type -->
                    <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full px-3 sm:w-full ">
                                <label class="block pb-2.5 font-normal leading-4 text-left text-black text-sm "
                                    for="car_type">Car Type<span class="text-[#ff0000]">*</span>
                                </label>
                                    <input type="text" tabindex="-1" name="car_type" value="{{ old('car_model') }}"
                                    onkeyup="checkBlankField(this.id,'Car type is required','errorCarType')"
                                    onchange="checkBlankField(this.id,'Car typel is required','errorCarType')"
                                    class="w-full px-5 py-3 font-normal leading-normal text-black bg-white border rounded-md required_field border-black500 focus:outline-none focus:border-siteYellow text-base "
                                    placeholder="Enter Car Type" id="car_type" data-error-msg="Car type is required">
                                    <span class="scrollForReq hidden validateError errorCarType text-sm"></span>
                                @error('car_type')
                                    <span class="required inline-block validateError text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap -mx-[9px] sm:-mx-[0px] pb-5">

                        <!-- Status -->
                        <div class="w-1/2 px-[9px] sm:px-[0px] sm:w-full">
                            <label for="car_type"  class="inline-block pb-2.5 font-normal leading-4 text-left text-black text-sm">Status<span class="text-[#ff0000]">*</span></label>
                            <select name="status" id="status"  onchange="checkBlankField(this.id,'Status is required','errorStatus')" class="w-full py-3 pl-5 pr-10 font-normal leading-normal text-black bg-white border rounded-md appearance-none required_field s_tag_haf border-black500 focus:outline-none focus:border-siteYellow drop_location_select text-base" data-error-msg="Status is required">
                                <option value="0" selected disabled class="capitalize text-midgray fontGrotesk">
                                    Select Status
                                </option>
                                <option value="new" class="capitalize fontGrotesk" {{ old('new') == 'sedan' ? 'new' : '' }}>  new </option>
                                <option value="attempted_to_contacted" class="capitalize fontGrotesk" {{ old('attempted_to_contacted') == 'attempted_to_contacted' ? 'selected' : '' }}>  attempted to contacted </option>
                                <option value="confirmed" class="capitalize fontGrotesk" {{ old('confirmed') == 'confirmed' ? 'selected' : '' }}>   confirmed </option>
                                <option value="cancelled" class="capitalize fontGrotesk" {{ old('cancelled') == 'cancelled' ? 'selected' : '' }}> cancelled </option>
                                <option value="lost_lead" class="capitalize fontGrotesk" {{ old('lost_lead') == 'lost_lead' ? 'selected' : '' }}> lost lead </option>
                                <option value="junk_lead" class="capitalize fontGrotesk" {{ old('junk_lead') == 'junk_lead' ? 'selected' : '' }}>junk lead</option>
                            </select>

                                <span class="scrollForReq hidden errorStatus validateError text-sm"></span>
                            @error('car_type')
                                <span class="inline-block validateError text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Lead Source -->
                        <div class="w-1/2 sm:mt-5 px-[9px] sm:px-[0px] sm:w-full">
                            <label for="lead_source" class="inline-block pb-2.5 font-normal leading-4 text-left text-black text-sm">Lead Source<span class="text-[#ff0000]">*</span></label>
                            <input type="text" tabindex="-1" name="lead_source" id="lead_source"
                                onkeyup="checkBlankField(this.id,'Lead source is required','errorLeadSource')"
                                onchange="checkBlankField(this.id,'Lead source is required','errorLeadSource')"
                                class="w-full py-3 pl-5 pr-10 font-normal leading-normal text-black bg-white border rounded-md appearance-none required_field s_tag_haf border-black500 focus:outline-none focus:border-siteYellow drop_location_select text-base"
                                data-error-msg="Lead source is required ">
                                <span class="scrollForReq hidden errorLeadSource validateError text-sm"></span>
                            @error('lead_source')
                                <span class="inline-block validateError text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-5 form_btn_sec afclr">
                        <input type="submit" value="ADD NEW LEAD" class="inline-block w-full px-5 py-3 text-opacity-40 font-medium leading-tight transition-all duration-300 border border-siteYellow rounded-[4px] cursor-pointer bg-siteYellow  md:px-[20px] md:py-[14px] sm:py-[12px] transition-all duration-500 ease-0 hover:bg-[#d7b50a]  disabled:opacity-40 disabled:cursor-not-allowed text-base md:text-sm">
                    </div>

                </form>

            </div>
        </div>

    </div>
    <!-- navigation -->
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
    }
    else
    {
        $msgContainer.html('');
        $msgContainer.css("display", "none");
    }

    // For Contact Number
    if ( ($('#contact_number').val().length > 0) && ($('#contact_number').val().length < 10) )
    {
        $('.contactValidateError').html("Contact number must be at least 10 digits");
        $('.contactValidateError').css('display','block');
    }

}

// on sumbit
function checkForm()
{
    $('.loader').css("display", "inline-flex");
    $('.overlay_sections').css("display", "block");

    var flag=false;
    var msg = 'Please fill all required fields';
    let required_fields =$('.required_field');

    // console.log('required_fields',required_fields);

    required_fields.each(function(e){
        if($(this).val())
        {
            $(this).siblings('.validateError').html('');
            $(this).siblings('.validateError').hide();

            $(this).closest('.contact_number_container').find('.validateError').html('');
            $(this).closest('.contact_number_container').find('.validateError').hide();
        }
        else
        {
            flag=true;
            $(this).siblings('.validateError').html($(this).data('error-msg'));
            $(this).siblings('.validateError').css('display','block');

            $(this).closest('.contact_number_container').find('.validateError').html($(this).data('error-msg'));
            $(this).closest('.contact_number_container').find('.validateError').css('display','block');
        }

        if ( $('#contact_number').val().length > 1 && $('#contact_number').val().length < 10  ) {
            flag = true;
            $(this).closest('.contact_number_container').find('.validateError').html("Contact number must be at least 10 digits");
            $(this).closest('.contact_number_container').find('.validateError').css('display','block');
        }

    });

    if (flag)
    {
        hideLoader();
        Swal.fire({
            title: msg,
            icon: 'warning',
            showCancelButton: false,
            confirmButtonText: 'OK',
            customClass: {
                popup: 'popup_updated',
                title: 'popup_title',
                actions: 'btn_confirmation',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                result.dismiss = Swal.DismissReason.cancel;
                $("html, body").animate({
                    scrollTop: parseFloat($(".scrollForReq:visible:first").offset().top) - 150
                }, 1000);
            }
        });
        hideLoader();
        return false;
    }
}

var utilsScript = "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js";
var input = document.querySelector("#contact_number");
    window.intlTelInput(input, {
    initialCountry: "auto",
    preferredCountries:["in", "us", "gb"],
    formatOnDisplay: true,
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
    var contact_mobile_country_code = $(this).closest('.contact_number_container').find('.iti__selected-dial-code').html();
    $('.contact_mobile_country_code').val(contact_mobile_country_code);
});

function validateContactNumber(input)
{
    // Remove any non-numeric characters
    input.value = input.value.replace(/\D/g, '');
}


</script>
@endsection
