@extends('layouts.partner')
@section('title', 'Add New Car')
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
.fancybox-content { padding: 0px 0px; /* width: 20%; */ width: 320px; border-radius: 6px; }
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
.car_box{ /* width:200px; */ width:100%; height: 150px; }
@media screen and (max-width: 992px){
    .main_header_container{ display:none; }
}
@media screen and (min-width:767px) {
.car_box{ width:100%; height: 150px; }
}


</style>

<div>
    <!-- 1st Part -->
    <div class="lg:flex hidden lg:bg-white lg:mb-[0px] lg:px-[17px] lg:py-[15px] mb-[20px] justify-start items-center">
        <a href="{{ route('partner.car.list') }}" class="links_item_cta inline-block p-2 mr-2 border border-[#C8C0C0] rounded-md
        hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
            <img class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
        </a>
        <span class="inline-block text-xl font-normal leading-normal align-middle text-black800">Add New Car</span>
    </div>

    <!-- 2nd Part -->
    <div class="pt-[42px] px-[36px] lg:px-[17px] lg:py-[25px] xl:py-10 bg-[#F6F6F6] !pb-[100px]  min-h-screen lg:flex lg:justify-center">

        <div class="lg:hidden mb-[36px] lg:mb-[20px] flex flex-col">
            <div class="back-button">
                <a href="{{ route('partner.car.list') }}" class="links_item_cta inline-flex py-1 px-2 border border-[#C8C0C0] rounded-md bg-[#fff] hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
                    <img  class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
                    <span class="inline-block text-base leading-normal align-middle text-black800 ml-[10px] font-medium">
                        ALL CARS
                    </span>
                </a>
            </div>
            <span class="inline-block text-[26px] font-normal leading-normal align-middle text-black800">
                Add New Car
            </span>
        </div>

        <div class="max-w-[768px] w-full bg-white lg:bg-[#F6F6F6] rounded-[10px] px-12 lg:px-[0px] py-9 lg:py-[0px]">
            <div class="booking_section">
                <form action="{{ route('partner.car.add.post') }}" method="POST" onsubmit="return checkForm()">
                    @csrf

                    <!-- Car Name -->
                    <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                            <div class="w-full px-3 sm:w-full ">
                                <label class="block pb-2.5 font-normal leading-4 text-left text-black text-sm "
                                    for="car_name">Car Name<span class="text-[#ff0000]">*</span></label>
                                <input type="text" tabindex="-1" name="car_name" value="{{ old('car_name') }}"
                                    onkeyup="checkBlankField(this.id,'Car name is required','errorName')"
                                    onchange="checkBlankField(this.id,'Car name is required','errorName')"
                                    class="w-full px-5 py-3 font-normal leading-normal text-black bg-white border rounded-md required_field border-black500 focus:outline-none focus:border-siteYellow text-base "
                                    placeholder="Enter Car Name" id="car_name" data-error-msg="Car name is required">
                                <span class="scrollForReq hidden validateError errorName text-sm"></span>
                                @error('car_name')
                                    <span class="required inline-block validateError text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="CompanyImageId" name="CompanyImageId">

                    <div class="flex px-0 mb-3 mb-6 image_container md:block">

                        <div class=" w-[100%]  md:flex-wrap md:w-full scroller_cars lg:overflow-unset ">


                            <div class=" flex flex-wrap image_content md:flex-wrap -mx-2.5 ">


                                <div class="uploader_container w-1/3 sm:w-1/2 md:mb-2.5 px-2.5 py-2.5">
                                    <div  class="upload_height bg-white flex items-center justify-center booking_inner_b cursor-pointer border  min-w-[100px] !w-full ">
                                        <label for="car_photos"
                                            class="flex flex-col items-center justify-center w-full
                                            text-center cursor-pointer py-[42px]">
                                            <img src="{{ asset('images/add_upload_icon.svg') }}" class="upload_width"
                                                alt="icon">
                                            <h4 class="pt-4 text-sm font-normal text-blacklight">Upload Photo</h4>
                                        </label>
                                        <input type="file" id="car_photos" class="hidden" name="car_photos[]" multiple
                                            accept="image/*">
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- Uploader Message -->
                    <div class="mb-6">
                        <p class="text-orange flex items-center text-sm pr-1 sm:text-base font-normal text-[#EF6A00] ">*Click on star to make image featured, featured image will appear first on the car listing to
                            customers
                        </p>
                    </div>

                    <!-- Registration number -->
                    <div class="pb-5">
                        <div class="flex flex-wrap -mx-3">
                            <div class="registration_number_container w-full px-3 sm:w-full ">
                                    <label class="block pb-2.5 font-normal leading-4 text-left text-black text-sm"
                                        for="car_number"> Registration Number<span class="text-[#ff0000]">*</span>
                                    </label>
                                    <div class="flex relative ">
                                        <!-- Format selection dropdown -->
                                        <div class="absolute inline-flex h-[100%] w-[135px] rounded-l-md bg-[rgba(0, 0, 0, 0.05)] top-0 bottom-0 p-[1px] " style="">
                                            <select class="w-full rounded-l-md focus:outline-none pl-[16px] appearance-none reg_arrow text-base" id="formatSelect" onchange="updateCleaveFormat();" style="background-color: rgba(0, 0, 0, 0.05);">
                                                <option value="general">General</option>
                                                <option value="bh">Bharat (BH)</option>
                                                <option value="other">Other</option>
                                            </select>
                                        </div>

                                        <input type="text" tabindex="-1" name="registration_number" value="{{ old('registration_number') }}"
                                            onkeyup="checkBlankField(this.id,'Registration number is required','errorRegistrationNumber')"
                                            onchange="checkBlankField(this.id,'Registration number is required','errorRegistrationNumber')"
                                            class=" w-full px-5 py-3 font-normal leading-normal text-black bg-white border rounded-md required_field border-black500 focus:outline-none focus:border-siteYellow pl-[150px] text-base " placeholder="GA-01-CP-1234" id="car_number" data-error-msg="Registration number is required" >
                                    </div>

                                    <span class="scrollForReq hidden validateError errorRegistrationNumber text-sm"></span>
                                    @if($errors->has('registration_number'))
                                    <span class="inline-block validateError text-sm">{{ $errors->first('registration_number') }}</span>
                                @endif


                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap -mx-[9px] sm:-mx-[0px] pb-5">

                        <!-- Segment -->
                        <div class="w-1/2 px-[9px] sm:px-[0px] sm:w-full">
                            <label for="car_type"
                                class="inline-block pb-2.5 font-normal leading-4 text-left text-black text-sm">Segment<span class="text-[#ff0000]">*</span></label>
                            <select name="car_type" id="car_type"
                                onchange="checkBlankField(this.id,'Segment is required','errorCartype')"
                                class="w-full py-3 pl-5 pr-10 font-normal leading-normal text-black bg-white border rounded-md appearance-none required_field s_tag_haf border-black500 focus:outline-none focus:border-siteYellow drop_location_select text-base "
                                data-error-msg="Segment is required">
                                <option value="0" selected disabled class="capitalize text-midgray fontGrotesk">
                                    Select Segment
                                </option>
                                <option value="sedan" class="capitalize fontGrotesk" {{ old('car_type') == 'sedan' ? 'selected' : '' }}>  Sedan </option>
                                <option value="hatchback" class="capitalize fontGrotesk" {{ old('car_type') == 'hatchback' ? 'selected' : '' }}>  Hatchback </option>
                                <option value="compact_suv" class="capitalize fontGrotesk" {{ old('car_type') == 'compact_suv' ? 'selected' : '' }}>   Compact SUV </option>
                                <option value="suv" class="capitalize fontGrotesk" {{ old('car_type') == 'suv' ? 'selected' : '' }}> SUV </option>
                                <option value="luxury" class="capitalize fontGrotesk" {{ old('car_type') == 'luxury' ? 'selected' : '' }}> Luxury </option>
                                <option value="off_road" class="capitalize fontGrotesk" {{ old('car_type') == 'off_road' ? 'selected' : '' }}>Off Road</option>
                            </select>

                            <span class="scrollForReq hidden errorCartype validateError text-sm"></span>
                            @error('car_type')
                                <span class="inline-block validateError text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Fuel type -->
                        <div class="w-1/2 sm:mt-5 px-[9px] sm:px-[0px] sm:w-full">
                            <label for="fuel_type"
                                class="inline-block pb-2.5 font-normal leading-4 text-left text-black text-sm">Fuel
                                Type<span class="text-[#ff0000]">*</span></label>
                            <select name="fuel_type" id="fuel_type"
                                onchange="checkBlankField(this.id,'Fuel type is required','errorCarfuel')"
                                class="w-full py-3 pl-5 pr-10 font-normal leading-normal text-black bg-white border rounded-md appearance-none required_field s_tag_haf border-black500 focus:outline-none focus:border-siteYellow drop_location_select text-base"
                                data-error-msg="Fuel type is required ">

                                <option value="0" selected disabled class="capitalize text-midgray fontGrotesk">
                                    Select Fuel Type
                                </option>
                                <option value="petrol" class="capitalize" {{ old('fuel_type') == 'petrol' ? 'selected' : '' }}>
                                    Petrol
                                </option>
                                <option value="diesel" class="capitalize" {{ old('fuel_type') == 'diesel' ? 'selected' : '' }}>
                                    Diesel
                                </option>
                                <option value="cng" class="capitalize" {{ old('fuel_type') == 'cng' ? 'selected' : '' }}>
                                    CNG
                                </option>
                                <option value="electric" class="capitalize" {{ old('fuel_type') == 'electric' ? 'selected' : '' }}>
                                    Electric
                                </option>
                                <option value="hybrid" class="capitalize" {{ old('hybrid') == 'hybrid' ? 'selected' : '' }}>
                                    Hybrid
                                </option>
                                <option value="other" class="capitalize" {{ old('fuel_type') == 'other' ? 'selected' : '' }}>
                                    Other
                                </option>
                            </select>
                            <span class="scrollForReq hidden errorCarfuel validateError text-sm"></span>
                            @error('fuel_type')
                                <span class="inline-block validateError text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>


                    <div class="flex flex-wrap -mx-[9px] sm:-mx-[0px] pb-5">

                        <!-- Manufacturing year -->
                        <div class="w-1/2 px-[9px] sm:px-[0px] w-full">
                            <label for="manufacturing_year" class="inline-block pb-2.5 font-normal leading-4 text-left text-black text-sm">
                                Manufacturing Year<span class="text-[#ff0000]">*</span>
                            </label>
                            <select name="manufacturing_year"  id="manufacturing_year" onchange="checkBlankField(this.id, 'Manufacturing year is required', 'errorCarmanufacturing')"  class="w-full py-3 pl-5 pr-10 font-normal leading-normal text-black bg-white border rounded-md appearance-none required_field border-black500 focus:outline-none focus:border-siteYellow drop_location_select text-base" data-error-msg="Manufacturing year is required" >
                                <option value="0" selected disabled class="capitalize text-midgray">Select Manufacturing Year</option>
                                @php
                                    $currentYear = date('Y');
                                @endphp
                                @for ($year = $currentYear; $year >= $currentYear - 20; $year--)
                                    <option value="{{ $year }}" {{ old('manufacturing_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endfor
                            </select>
                            <span class="scrollForReq hidden errorCarmanufacturing validateError text-sm"></span>
                            @error('manufacturing_year')
                                <span class="inline-block validateError text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>


                    <div class="flex flex-wrap -mx-[9px] sm:-mx-[0px] pb-5">

                        <!-- Price -->
                        <div class="price_container w-1/2 px-[9px] sm:px-[0px] sm:w-full">
                                <label for="price"
                                    class="inline-block pb-2.5 font-normal leading-4 text-left text-black text-sm">
                                    Price(per day)<span class="text-[#ff0000]">*</span>
                                </label>
                                <div class="rupee_price_field">
                                    <input type="text" tabindex="-1" name="price"  id="price"
                                    onkeyup = "checkBlankField(this.id,'Price is required','errorCarprice')"
                                    onchange = "checkBlankField(this.id,'Price is required','errorCarprice')"
                                    class="required_field w-full py-3 pl-[33px] pr-10 font-normal leading-normal text-black bg-white border rounded-md appearance-none required_field s_tag_haf border-black500 focus:outline-none focus:border-siteYellow drop_location_select text-base" placeholder="Enter Price"  data-error-msg="Price is required" value="{{ old('price') }}">
                                    <span class="rupee_icon">₹</span>
                                </div>
                                <span class="scrollForReq hidden errorCarprice validateError text-sm"></span>
                                @error('price')
                                    <span class="inline-block validateError text-sm">{{ $message }}</span>
                                @enderror
                        </div>

                        <!-- Transmission type -->
                        <div class="w-1/2 sm:mt-5 px-[9px] sm:px-[0px] sm:w-full">
                            <label for="transmission_type"
                                class="inline-block pb-2.5 font-normal leading-4 text-left text-black text-sm">Transmission
                                Type<span class="text-[#ff0000]">*</span></label>
                            <select name="transmission_type" id="transmission_type"
                                onchange="checkBlankField(this.id,'Transmission type is required','errorTransmission')"
                                class="w-full py-3 pl-5 pr-10 font-normal leading-normal text-black bg-white border rounded-md appearance-none required_field s_tag_haf border-black500 focus:outline-none focus:border-siteYellow drop_location_select text-base"
                                data-error-msg="Transmission type is required">
                                <option value="0" selected disabled class="capitalize text-midgray">
                                    Select Transmission Type
                                </option>
                                <option value="manual" class="capitalize" {{ old('transmission_type') == 'manual' ? 'selected' : '' }}>
                                    Manual
                                </option>
                                <option value="auto" class="capitalize" {{ old('transmission_type') == 'auto' ? 'selected' : '' }}>
                                    Automatic
                                </option>
                            </select>
                            <span class="scrollForReq hidden errorTransmission validateError text-sm"></span>
                            @error('transmission_type')
                                <span class="inline-block validateError text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>


                    <div class="flex flex-wrap -mx-[9px] sm:-mx-[0px] pb-5">

                        <!-- Roof Type -->
                        <div class="w-1/2 px-[9px] sm:px-[0px] sm:w-full">
                            <label class="inline-block pb-2.5 font-normal leading-4 text-left text-black text-sm"
                                for="roof_type">Roof Type<span class="text-[#ff0000]">*</span></label>
                            <select name="roof_type" id="roof_type" onchange="checkBlankField(this.id,'Car roof type is required','errorRoofType')" class="w-full py-3 pl-5 pr-10 font-normal leading-normal text-black bg-white border rounded-md appearance-none required_field s_tag_haf border-black500 focus:outline-none focus:border-siteYellow drop_location_select text-base"
                                data-error-msg="Roof type is required">
                                <option value="0" selected disabled class="capitalize text-midgray fontGrotesk">
                                Select Roof Type</option>
                                <option value="normal" class="capitalize fontGrotesk" {{ old('roof_type') == 'normal' ? 'selected' : '' }}>Normal</option>
                                <option value="sunroof" class="capitalize fontGrotesk" {{ old('roof_type') == 'sunroof' ? 'selected' : '' }}>Sunroof</option>
                                <option value="moonroof" class="capitalize fontGrotesk" {{ old('roof_type') == 'moonroof' ? 'selected' : '' }}>Moonroof</option>
                                <option value="convertible" class="capitalize fontGrotesk" {{ old('roof_type') == 'convertible' ? 'selected' : '' }}>Convertible</option>

                            </select>
                            <span class="scrollForReq hidden errorRoofType validateError text-sm"></span>
                            @error('roof_type')
                            <span class="inline-block validateError text-sm">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Plate Type -->
                        <div class="w-1/2 sm:mt-5 px-[9px] sm:px-[0px] sm:w-full">
                            <label class="inline-block pb-2.5 font-normal leading-4 text-left text-black text-sm"
                                for="plate_type">Plate Type<span class="text-[#ff0000]">*</span></label>
                                <select  name="plate_type" id="plate_type" onchange="checkBlankField(this.id, 'Plate type is required', 'errorPlateType')" class="w-full py-3 pl-5 pr-10 font-normal leading-normal text-black bg-white border rounded-md appearance-none required_field s_tag_haf border-black500 focus:outline-none focus:border-siteYellow drop_location_select text-base"
                                    data-error-msg="Plate type is required"   >
                                    <option value="0" selected disabled class="capitalize text-midgray fontGrotesk">
                                        Select Plate Type
                                    </option>
                                    <option value="white" class="capitalize fontGrotesk" {{ old('plate_type') == 'white' ? 'selected' : '' }}>White</option>
                                    <option value="black" class="capitalize fontGrotesk" {{ old('plate_type') == 'black' ? 'selected' : '' }}>Black</option>
                                    <option value="yellow" class="capitalize fontGrotesk" {{ old('plate_type') == 'yellow' ? 'selected' : '' }}>Yellow</option>
                                    <option value="green" class="capitalize fontGrotesk" {{ old('plate_type') == 'green' ? 'selected' : '' }}>Green</option>
                                </select>

                            <span class="scrollForReq hidden errorPlateType validateError text-sm"></span>
                            @error('plate_type')
                                <span class="inline-block validateError text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>


                    <div class="mt-5 form_btn_sec afclr">
                        <input type="submit" value="ADD NEW CAR" class="inline-block w-full px-5 py-3 text-opacity-40 font-medium leading-tight transition-all duration-300 border border-siteYellow rounded-[4px] cursor-pointer bg-siteYellow  md:px-[20px] md:py-[14px] sm:py-[12px] transition-all duration-500 ease-0 hover:bg-[#d7b50a]  disabled:opacity-40 disabled:cursor-not-allowed text-base md:text-sm">
                    </div>

                </form>

            </div>
        </div>
    </div>
    <!-- navigation -->
    @include('layouts.navigation')
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
<script>
    var cleave;
    function updateCleaveFormat() {
        var formatSelect = document.getElementById('formatSelect');
        var selectedFormat = formatSelect.value;
        var blocks;
        var placeholder;
        switch (selectedFormat) {
            case 'general':
                blocks = [2, 2, 2, 4]; // Adjust blocks for General format
                placeholder = 'GA-01-CP-1234';
                break;
            case 'bh':
                blocks = [2, 2, 4, 2]; // Adjust blocks for BH format
                placeholder = '12-BH-3456-AA';
                break;
            case 'other':
                blocks = [4, 4, 4]; // Adjust blocks for Other format
                placeholder = '1234-1234-1234';
                break;
            default:
                blocks = [3, 2, 4]; // Default format
                placeholder = '123-12-1234'; // Default placeholder
                break;
        }

        // Set the value and placeholder of the input field
        document.getElementById('car_number').value = ''; // Clear the value
        document.getElementById('car_number').placeholder = placeholder;

        // Destroy and reinitialize Cleave.js with the updated format
        cleave.destroy();
        cleave = new Cleave('#car_number', {
            delimiter: '-',
            blocks: blocks,
            uppercase: true,
        });
    }
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        cleave = new Cleave('#car_number', {
            delimiter: '-',
            blocks: [2, 2, 2, 4],
            uppercase: true,
        });
    });
</script>


<script>

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


function hideLoader()
{
    $('.loader').css("display", "none");
    $('.overlay_sections').css("display", "none");
}


// on sumbit
  function checkForm()
{
    $('.loader').css("display", "inline-flex");
    $('.overlay_sections').css("display", "block");

    var flag=false;
    var msg = 'Please fill all required fields';
    let required_fields =$('.required_field');

    required_fields.each(function(e){
        if($(this).val()){
            $(this).siblings('.validateError').html('');
            $(this).siblings('.validateError').hide();

            $(this).closest('.price_container').find('.validateError').html('');
            $(this).closest('.price_container').find('.validateError').hide();

            $(this).closest('.registration_number_container').find('.validateError').html('');
            $(this).closest('.registration_number_container').find('.validateError').hide();

        }
        else
        {
            flag=true;
            $(this).siblings('.validateError').html($(this).data('error-msg'));
            $(this).siblings('.validateError').css('display','block');

            $(this).closest('.price_container').find('.validateError').html($(this).data('error-msg'));
            $(this).closest('.price_container').find('.validateError').css('display','block');

            $(this).closest('.registration_number_container').find('.validateError').html($(this).data('error-msg'));
            $(this).closest('.registration_number_container').find('.validateError').css('display','block');
        }

    });

    if (flag) {
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
else
    {
        if(checkImageCount()&&checkFeatureImage()){
            // hideLoader();
            return true;
        }else{
            hideLoader();
            return false;
        }
    }
}

function checkImageCount()
{
    var inputCount = $('.image_content').find('.car_photos_hidden').length;

    if(inputCount===0){
        Swal.fire({
                    title: 'Car images is required',
                    icon: 'warning',
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
    else
    {
        return true;

    }
}

function checkFeatureImage()
{
    if($('#featured_img').length>0)
    {
        return true;
    }
    else
    {
        Swal.fire({
            title: 'Please select a feature image, click on star image',
            icon: 'warning',
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
}


var imgArray = [];
var counter = 0;
$("#car_photos").on('change', function(e) {
    var files = $(this)[0].files;
    if (files.length > 0) {
        const maxFileSize = 10 * 1024 * 1024;
        var allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        for (var i = 0; i < files.length; i++)
        {
            var file = files[i];
            var fileName = file.name;
            var fileExtension = fileName.split('.').pop();
            imgArray.push(files[i]);

            if (allowedExtensions.indexOf(fileExtension.toLowerCase()) === -1) {
                Swal.fire({
                    title: 'Invalid file format. Supported formats: JPG, JPEG, PNG WEBP',
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
                    $(".loader").css("display", "none");
                    $(".overlay_sections").css("display", "none");
                    e.target.value = '';
                }
            });
            return false;
            }

            if (file.size > maxFileSize) {
                Swal.fire({
                    title: 'Image size is too big, please upload files smaller than 10MB',
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
                    $(".loader").css("display", "none");
                    $(".overlay_sections").css("display", "none");
                    e.target.value = '';

                }
            });
            return false;
            }

            var form_data = new FormData();
            form_data.append('_token', '{{ csrf_token() }}');
            form_data.append('car_photos[]', file);
            $(".loader").css("display", "inline-flex");
            $(".overlay_sections").css("display", "block");
            $.ajax({
                url: "{{ route('partner.upload.car.images') }}",
                data: form_data,
                type: 'POST',
                contentType: false,
                processData: false,
                success: function(data)
                {
                    if (data.success) {
                        var modifiedImgUrl = data.url;
                        //  if (modifiedImgUrl.includes('index.php/')) {
                        //     modifiedImgUrl = modifiedImgUrl.replace('index.php/', '');
                        // }

                        $('.image_content').prepend(`
                        <div class="main_car_image_container w-1/3 sm:w-1/2 md:mb-2.5 px-2.5 py-2.5">
                            <div class=" rounded-[12px]  ">
                                <div class="car_box relative border flex flex-col justify-between h-full bg-[#fff] rounded-[12px] ">
                                        <div class="booking_inner_bb logo_image w-full h-full flex flex-col items-center justify-center overflow-hidden ">

                                            <a class="w-full h-full" href="${modifiedImgUrl}" data-fancybox="editgallery">
                                                <img class="p-[10px] cursor-pointer block w-full main_img imageProfileUrl toZoom" src="${modifiedImgUrl}" alt="Car Image">
                                                <input type="hidden" class="car_photos_hidden" name="photoId[]" value="${data.imageId}" id="imageId_${data.ImageUniqueId}"/>
                                            </a>
                                        </div>

                                        <div class="relative flex">
                                            <a href="javascript:void(0);" class="clicked_area flex justify-center w-1/2 p-2 border  featured_picture_anchor item-center border-l-none border-b-none border-r-none" href="#">
                                                <img class="block w-4 h-4" src="{{ asset('images/blank_star_img.svg') }}" alt="icon">
                                            </a>
                                            <a href="javascript:void(0);" data-remove-id="${modifiedImgUrl}" class="flex justify-center w-1/2 p-2 border removeProfileBtn item-center border-r-none border-b-none " href="#">
                                                <img class="block w-4 h-4" src="{{ asset('images/delete_icon_red.svg') }}" alt="icon">
                                            </a>
                                        </div>

                                </div>
                            </div>
                        </div>
                        `);
                        counter++;
                    }
                    countImageContainer();
                    $(".loader").css("display", "none");
                    $(".overlay_sections").css("display", "none");
                },
                complete: function(data) {
                    if (counter === imgArray.length) {
                        $('.main_img').each(function(index, element) {
                            var $element = $(element);
                            $element.attr('src', data.url);
                            $element.on('load', function() {
                                $(".loader").css("display", "none");
                                $(".overlay_sections").css("display", "none");
                            });
                        });
                    }
                    e.target.value = '';
                },
                error: function(xhr, status, error) {

                    e.target.value = '';
                }
            });
        }

    }
    e.target.value = '';
});


$('body').on('click', '.clicked_area', function() {
    var that = $(this);

    var imageId = that.closest('.car_box').find('.car_photos_hidden').val();

    console.log(imageId);

    if (that.find('.featured_picture_sec').length > 0)
    {

    }
    else
    {

        var star_icon = '<img class="block w-4 h-4" src="{{ asset('images/blank_star_img.svg') }}" alt="icon">';

        var featured_html = '<div class="featured_picture_sec">' +
            '<input type="hidden" name="featured_check" id="featured_img" class="featured_check" value="' + imageId + '">' +
            '<img src="{{ asset('images/featured_icon2.svg') }}" class="featured_icon w-4 h-4">' +
            '</div>';
        that.html(featured_html);

        $('.clicked_area').not(that).html(star_icon);
        $('.clicked_area').not(that).find('.featured_picture_sec').remove();

    }
});

$('body').on('click', '.removeProfileBtn', function() {
    console.log("remove Profile");
    $(this).closest('.main_car_image_container').remove();
    imgArray.pop();
    counter--;
    countImageContainer();
});

    function countImageContainer(){
        var carContainerCount = $('.main_car_image_container').length;
        if(carContainerCount==0)
        {
            $('.uploader_container').css('min-width','200px');
        }
        else if(carContainerCount>0)
        {
            $('.uploader_container').css('min-width','0');
        }
    }

    countImageContainer();


    $('#seats').on('input', function() {
        let numericValue = this.value.replace(/[^0-9]/g, "");
        numericValue = numericValue.substring(0, 2);
        $('#seats').val(numericValue);
    });

    $('#car_number').on('input', function() {
        let carNum = this.value.replace(/[!@#$%^&*()_+\=\[\]{};':"\\|,.<>\/?]+/g, "");
        $('#car_number').val(carNum);
    });

function formatIndianCurrency(value) {
    value = value.replace(/\$/g, '').replace(/,/g, '');
    if (!isNaN(value) && value !== '') {
        return parseFloat(value).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
    return '';
}

function convertRateToIndianCurrency(inputId) {
    var $input = $("#" + inputId);
    var inputValue = $input.val();
    var formattedValue = formatIndianCurrency(inputValue);

    $input.val(formattedValue);
}

var inputIds = ['price'];

inputIds.forEach(function(inputId) {
    $('#' + inputId).on('keyup', function() {
        convertRateToIndianCurrency(inputId);
    });
    $('#' + inputId).on('blur', function() {
        var $input = $(this);
        var value = $input.val();

        if (value !== '') {
            var numericValue = parseFloat(value.replace(/[^\d.]/g, ''));
            $input.val(numericValue.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ','));
        }
    });
});
</script>
@endsection
