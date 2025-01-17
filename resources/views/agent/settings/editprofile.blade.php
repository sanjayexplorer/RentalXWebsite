@extends('layouts.agent')
@section('title', 'Edit Profile')
@section('content')
<style>
.error { color: #ff0000; }
.required { color: #ff0000; }
.btn_confirmation button.swal2-confirm { border-radius: 25px; }
.header_bar { display: none; }
.right_dashboard_section>.right_dashboard_section_inner { padding-top: 0px; }
.eyeIconW { width: 20px }
.eye_show { display: none; }
.eyeShowing .eye_show{ display: block; }
.eyeShowing .eye_hide{ display: none; }
.iti--allow-dropdown{ width:100% !important; }
.iti--allow-dropdown input{ padding-right:20px !important; }
.profile_exist .upload_box{ display: none; }
@media screen and (max-width: 992px){
.main_header_container  {display:none;}
}
</style>
@php
    $imageUrl = '';
    if (strcmp(Helper::getUserMeta($userId, 'CompanyImageId'), '') != 0)
    {
        $profileImage = Helper::getPhotoById(Helper::getUserMeta($userId, 'CompanyImageId'));
        if ($profileImage)
        {
            $imageUrl = $profileImage->url;
        }
    }
    $modifiedUrl = $imageUrl;
@endphp

    <!-- 1st Part -->
    <div class="lg:flex hidden lg:bg-white lg:mb-[0px] lg:px-[17px] lg:py-[15px] mb-[20px] justify-start items-center">
        <div class="flex justify-start items-center w-1/2">
            <a href="{{route('agent.profile')}}" class="links_item_cta inline-block p-2 mr-2 border border-[#C8C0C0] rounded-md hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
                <img class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
            </a>
            <span class="inline-block text-xl font-normal leading-normal align-middle text-black800">Edit Profile</span>
        </div>

        <div class="flex justify-end items-center w-1/2">
            <a href="{{route('agent.profile')}}" class="links_item_cta dash_circle desh_view_bg font-medium text-gray-600 hover:underline">
                <img src="{{asset('images/eye.svg')}}">
            </a>
        </div>
    </div>

<div class="pt-[42px] px-[36px] lg:px-[17px] lg:py-[25px] xl:py-10 bg-[#F6F6F6] lg:mb-[60px] lg:flex lg:justify-center pb-[100px]  min-h-screen">

    <div class="lg:hidden mb-[36px] lg:mb-[20px] flex justify-between items-center max-w-[768px] w-full">
        <div>
            <div class="back-button">
                <a href="javascript:void(0);" class="inline-flex py-1 px-2 border border-[#C8C0C0] rounded-md bg-[#fff] hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
                    <span class="inline-block text-base leading-normal align-middle text-black800 font-medium">
                       Settings
                    </span>
                </a>
            </div>
            <div class="flex justify-between items-center">
                <span class="inline-block text-[26px] font-normal leading-normal align-middle text-black800">Edit Profile</span>
            </div>
        </div>

        <div class="flex justify-between items-center">
            <a href="{{route('agent.profile')}}" class="links_item_cta dash_circle desh_view_bg font-medium text-gray-600 hover:underline">
                <img src="{{asset('images/eye.svg')}}">
            </a>
        </div>
    </div>

    <div class="max-w-[768px] w-full bg-white lg:bg-[#F6F6F6] rounded-[10px] px-12 lg:px-[0px] py-9 lg:py-[0px]">
        <div class="booking_section">
            <form action="{{route('agent.profile.update',$userId)}}" method="post" onsubmit="return validate();">
            @csrf
            <input type="hidden" id="CompanyImageId" name="CompanyImageId" value="{{Helper::getUserMeta($userId,'CompanyImageId')}}">
            <div class="pb-5">
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full px-3 sm:w-full inp_container">
                        <label for="company_name" class="block pb-2.5 font-normal leading-4 text-left text-black text-sm">
                            Company Name<span class="text-[#ff0000]">*</span>
                        </label>
                        <input id="company_name" tabindex="-1"
                        placeholder="Enter company name"
                            class="required_fields w-full px-5 py-3 font-normal leading-normal
                        text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow text-base" type="text" name="company_name" data-error-msg="Company name is required" value="{{Helper::getUserMeta($userId,'company_name')}}">
                        <span class="required text-sm"></span>
                        @if ($errors->has('company_name'))
                        <div class="error text-sm">
                            <ul>
                                <li>{{ $errors->first('company_name') }}</li>
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <h4 class="inline-block pb-[13px] font-normal leading-4 text-left text-black text-sm">Company Logo</h4>
                <div class="flex px-0 mb-[13px] overflow-hidden mb-6 image_container">

                    <div class="mr-3 w-1/3 upload_box w-[200px] h-[150px] bg-white cursor-pointer rounded-[10px] border border-[#D8D5D0]">
                        <label for="company_logo"
                            class="flex flex-col items-center justify-center w-full h-full text-sm text-center cursor-pointer">
                            <img src="{{asset('images/add_upload_icon.svg')}}" class="upload_width sm:w-[20px]" alt="icon">
                            <h4 class="pt-4 font-normal text-blacklight sm:pt-[12px] text-sm">Upload Photo</h4>
                        </label>

                        <input type="file" id="company_logo" class="hidden" name="company_logo" accept=".png, .webp, .jpeg, .jpg">
                    </div>
                    @if($modifiedUrl!='')
                    <div class="relative rounded-[10px] border border-[#D8D5D0] w-1/3  pt-[10px] w-[200px] h-[150px] bg-white overflow-hidden logo_image">
                        <div class="pb-[45px] mr-3  w-full h-full
                            flex flex-col items-center justify-center overflow-hidden px-[10px] ">
                            <img src="{{ isset($modifiedUrl) ? $modifiedUrl : asset('images/User-Profile.png') }}" alt=""
                            class="CompanyImageUrl object-contain max-h-full max-w-full">
                        </div>
                        <div class="flex items-center absolute bottom-0 left-0 right-0 border-t border-[#DAD8D8] justify-center hover:bg-[#FFE8E8] transition-all duration-300 ease-in-out">
                            <a href="javascript:void(0);" id="removeImage" class="p-[10px] w-1/2 px-[10px] font-medium text-[#f00]">
                               <img class="mx-auto" src="{{asset('images/delete_icon_red.svg')}}" alt="">
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
                <p class="text-[12px] font-normal text-[#8B8B8B] mb-[20px]">Required image size: 150x150px.</p>
                <div class="pb-5">
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full px-3 sm:w-full inp_container">
                            <label for="owner_name" class="block pb-2.5 font-normal leading-4 text-left text-black text-sm">Name<span class="text-[#ff0000]">*</span></label>
                            <input id="owner_name" tabindex="-1"
                                placeholder="Enter owner name"
                                class="required_fields w-full px-5 py-3 font-normal leading-normal
                                text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow alpha_validate text-base " type="text" name="owner_name"  data-error-msg="Owner name is required" value="{{Helper::getUserMeta($userId,'owner_name')}}">
                                <span class="required text-sm"></span>
                                @if ($errors->has('owner_name'))
                                <div class="error text-sm">
                                    <ul>
                                        <li>{{ $errors->first('owner_name') }}</li>
                                    </ul>
                                </div>
                            @endif
                        </div>
                </div>
        </div>



      <div class="pb-5">
        <div class="flex flex-wrap -mx-3">
            <div class="w-full px-3 sm:w-full inp_container login_mobile_number">
                <label for="login_mobile_number"
                class="block pb-2.5 font-normal leading-4 text-left text-black text-sm">
                Mobile Number<span class="text-[#ff0000]">*</span></label>
                <input id="login_mobile_number" tabindex="-1"
                placeholder="Enter mobile number"
                class="required_fields mobile_num_validate  w-full px-5 py-3 font-normal leading-normal text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow text-base "
                type="text" name="login_mobile_number"
                data-error-msg="Login mobile number is required"
              value="{{Helper::getUserMeta($userId,'user_mobile_country_code')}}{{auth()->user()->mobile}}">
            <input type="hidden" name="user_mobile_country_code" class="user_mobile_country_code" value="{{Helper::getUserMeta($userId,'user_mobile_country_code')}}">
            <span class="required text-sm"></span>
                @if ($errors->has('login_mobile_number'))
                    <div class="error text-sm">
                        <ul>
                            <li>{{ $errors->first('login_mobile_number') }}</li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="pb-5">
        <div class="flex flex-wrap -mx-3">
            <div class="w-full px-3 sm:w-full inp_container">
                <label for="email" class="block pb-2.5 font-normal leading-4 text-left text-black text-sm">Email Address (optional)</label>
                <input id="email" tabindex="-1"
                    placeholder="Enter email"
                    class="w-full px-5 py-3 font-normal leading-normal
                    text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow email_alpha_validate text-base " type="text" name="email"  value="{{Helper::getUserMeta($userId,'email')}}">
            </div>
    </div>
</div>

    <div class="pb-5">
        <div class="flex flex-wrap -mx-3">
            <div class="w-full px-3 sm:w-full inp_container company_phone_number">
                <label for="company_phone_number"
                class="block pb-2.5 font-normal leading-4 text-left text-black text-sm">
                Company Phone Number<span class="text-[#ff0000]">*</span></label>
                <input id="company_phone_number" tabindex="-1"
                placeholder="Enter company phone number"
                class="required_fields mobile_num_validate  w-full px-5 py-3 font-normal leading-normal text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow text-base "
                type="text" name="company_phone_number"
                data-error-msg="Company phone number is required"
            value="{{Helper::getUserMeta($userId,'company_phone_number_country_code')}} {{Helper::getUserMeta($userId,'company_phone_number')}}">
            <input type="hidden" name="company_phone_number_country_code" class="company_phone_number_country_code" value="{{Helper::getUserMeta($userId,'company_phone_number_country_code')}}">
            <span class="required text-sm"></span>
                @if ($errors->has('company_phone_number'))
                    <div class="error text-sm">
                        <ul>
                            <li>{{ $errors->first('company_phone_number') }}</li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>


                <div class="pb-5 partner_form_heading">
                    <h4 class="inline-block text-xl font-normal leading-normal align-middle text-black800">
                        Address:</h4>
                </div>

                <div class="pb-5">
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full px-3 sm:w-full ">
                            <label for="plot_shop_number" class="block pb-2.5 font-normal leading-4 text-left text-black text-sm">Plot/Shop number</label>
                            <input type="text" tabindex="-1" id="plot_shop_number" placeholder="Enter plot/shop number"  class="w-full px-5 py-3 font-normal leading-normal text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow text-base"  name="plot_shop_number" value="{{Helper::getUserMeta($userId,'plot_shop_number')}}">
                        </div>

                    </div>

                </div>

                <div class="flex flex-wrap -mx-[9px] sm:-mx-[0px] pb-5">
                    <div class="w-1/2 px-[9px] sm:px-[0px] sm:w-full">
                        <label for="street_name"
                        class="inline-block pb-2.5 font-normal leading-4 text-left text-black text-sm">Street Name</label>
                        <input type="text" tabindex="-1" name="street_name"
                        placeholder="Enter street name" id="street_name" class="w-full px-5 py-3 font-normal leading-normal text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow text-base "
                        value="{{Helper::getUserMeta($userId,'street_name')}}">
                    </div>
                    <div class="w-1/2 sm:mt-6 px-[9px] sm:px-[0px] sm:w-full">
                    <label for="state"
                    class="inline-block pb-2.5 font-normal leading-4 text-left text-black text-sm">State</label>
                    <select name="state" id="state" class="s_tag_haf w-full pl-5 pr-10 py-3 font-normal leading-normal
                    text-black bg-white border rounded-md border-black500 focus:outline-none
                     focus:border-siteYellow appearance-none border drop_location_select text-base ">
                            <option value="" disabled>Select State</option>
                            <option value="Assam" @if(Helper::getUserMeta($userId,'state') == 'Assam') selected @endif>Assam</option>
                            <option value="Bihar" @if(Helper::getUserMeta($userId,'state') == 'Bihar') selected @endif>Bihar</option>
                            <option value="Chhattisgarh" @if(Helper::getUserMeta($userId,'state') == 'Chhattisgarh') selected @endif>Chhattisgarh</option>
                            <option value="Goa" @if(Helper::getUserMeta($userId,'state') == 'Goa') selected @endif>Goa</option>
                            <option value="Gujarat" @if(Helper::getUserMeta($userId,'state') == 'Gujarat') selected @endif>Gujarat</option>
                            <option value="Haryana" @if(Helper::getUserMeta($userId,'state') == 'Haryana') selected @endif>Haryana</option>
                            <option value="HimachalPradesh" @if(Helper::getUserMeta($userId,'state') == 'HimachalPradesh') selected @endif>Himachal Pradesh</option>
                            <option value="JammuKashmir" @if(Helper::getUserMeta($userId,'state') == 'JammuKashmir') selected @endif>Jammu Kashmir</option>
                            <option value="Jharkhand" @if(Helper::getUserMeta($userId,'state') == 'Jharkhand') selected @endif>Jharkhand</option>
                            <option value="Karnataka" @if(Helper::getUserMeta($userId,'state') == 'Karnataka') selected @endif>Karnataka</option>
                            <option value="Kerala" @if(Helper::getUserMeta($userId,'state') == 'Kerala') selected @endif>Kerala</option>
                            <option value="MadhyaPradesh" @if(Helper::getUserMeta($userId,'state') == 'MadhyaPradesh') selected @endif>Madhya Pradesh</option>
                            <option value="Maharashtra" @if(Helper::getUserMeta($userId,'state') == 'Maharashtra') selected @endif>Maharashtra</option>
                            <option value="Manipur" @if(Helper::getUserMeta($userId,'state') == 'Manipur') selected @endif>Manipur</option>
                            <option value="Meghalaya" @if(Helper::getUserMeta($userId,'state') == 'Meghalaya') selected @endif>Meghalaya</option>
                            <option value="Mizoram" @if(Helper::getUserMeta($userId,'state') == 'Mizoram') selected @endif>Mizoram</option>
                            <option value="Nagaland" @if(Helper::getUserMeta($userId,'state') == 'Nagaland') selected @endif>Nagaland</option>
                            <option value="Rajasthan" @if(Helper::getUserMeta($userId,'state') == 'Rajasthan') selected @endif>Rajasthan</option>
                            <option value="Sikkim" @if(Helper::getUserMeta($userId,'state') == 'Sikkim') selected @endif>Sikkim</option>
                            <option value="TamilNadu" @if(Helper::getUserMeta($userId,'state') == 'TamilNadu') selected @endif>TamilNadu</option>
                            <option value="Telangana" @if(Helper::getUserMeta($userId,'state') == 'Telangana') selected @endif>Telangana</option>
                            <option value="Tripura" @if(Helper::getUserMeta($userId,'state') == 'Tripura') selected @endif>Tripura</option>
                            <option value="UttarPradesh" @if(Helper::getUserMeta($userId,'state') == 'UttarPradesh') selected @endif>Uttar Pradesh</option>
                            <option value="Uttarakhand" @if(Helper::getUserMeta($userId,'state') == 'Uttarakhand') selected @endif>Uttarakhand</option>
                            <option value="WestBengal" @if(Helper::getUserMeta($userId,'state') == 'WestBengal') selected @endif>WestBengal</option>
                            <option value="AndamanNicobar" @if(Helper::getUserMeta($userId,'state') == 'AndamanNicobar') selected @endif>Andaman Nicobar</option>
                            <option value="Chandigarh" @if(Helper::getUserMeta($userId,'state') == 'Chandigarh') selected @endif>Chandigarh</option>
                            <option value="DadraHaveli" @if(Helper::getUserMeta($userId,'state') == 'DadraHaveli') selected @endif>Dadra Haveli</option>
                            <option value="DamanDiu" @if(Helper::getUserMeta($userId,'state') == 'DamanDiu') selected @endif>Daman Diu</option>
                            <option value="Delhi" @if(Helper::getUserMeta($userId,'state') == 'Delhi') selected @endif>Delhi</option>
                            <option value="Lakshadweep" @if(Helper::getUserMeta($userId,'state') == 'Lakshadweep') selected @endif>Lakshadweep</option>
                            <option value="Puducherry" @if(Helper::getUserMeta($userId,'state') == 'Puducherry') selected @endif>Puducherry</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-wrap -mx-[9px] sm:-mx-[0px] pb-5">
                    <div class="w-1/2  px-[9px] sm:px-[0px] sm:w-full inp_container">
                        <label for="city" class="inline-block pb-2.5 font-normal leading-4 text-left text-black text-sm">City</label>
                        <input type="text" tabindex="-1" name="city" id="city" class="w-full px-5 py-3 font-normal leading-normal text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow text-base " placeholder="Enter city"
                        value="{{Helper::getUserMeta($userId,'city')}}">
                    </div>
                    <div class="w-1/2  px-[9px] sm:px-[0px] sm:w-full sm:mt-5">
                        <label for="zip" class="inline-block pb-2.5 font-normal leading-4 text-left text-black text-sm">Zip</span>
                        </label>
                        <input type="text" tabindex="-1" name="zip" id="zip" class="w-full px-5 py-3 font-normal leading-normal text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow zip_validate text-base " placeholder="Enter zip"
                        value="{{Helper::getUserMeta($userId,'zip')}}">
                    </div>

                </div>

                <div class="pb-5">
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full px-3 sm:w-full">
                            <label for="password"  class="block pb-2.5 font-normal leading-4 text-left text-black text-sm">
                                Change Password
                            </label>
                            <div class="relative">
                            <input id="password" tabindex="-1"  placeholder="Enter password"  class="w-full px-5 pr-[46px] py-3 font-normal leading-normal text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow text-base " type="password" name="password"/>
                                <span class="absolute cursor-pointer btnToggle right-5 top-1/2 translate-y-[-50%] transform-50 toggle">
                                    <img src="{{asset('images/eyeIcon.svg')}}" alt="eye_icon" class="eyeIconW eye_show">
                                    <img src="{{asset('images/eys_black_password.svg')}}" alt="eye_slash_icon" class="eyeIconW eye_hide">
                                 </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-5">
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full px-3 sm:w-full inp_container">
                            <label for="agent_short_name"
                                class="block pb-2.5 font-normal leading-4 text-left text-black text-sm">
                                Short Name<span class="text-[#ff0000]">*</span>
                            </label>
                            <input id="agent_short_name" tabindex="-1"
                                class="required_fields w-full px-5 py-3 font-normal leading-normal text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow text-base "
                                type="text" name="agent_short_name" placeholder="Enter short name"
                                data-error-msg="Short name is required" value="{{strtoupper(Helper::getUserMeta($userId,'agent_short_name'))}}">
                            <span class="required text-sm"></span>
                            @if ($errors->has('agent_short_name'))
                            <div class="error text-sm">
                                <ul>
                                    <li>{{ $errors->first('agent_short_name') }}</li>
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-5 form_btn_sec afclr">
                    <input type="submit" value="UPDATE"
                        class="inline-block w-full px-5 py-3 text-opacity-40 font-medium leading-tight transition-all duration-300 border border-siteYellow rounded-[4px] cursor-pointer bg-siteYellow md:px-[20px] md:py-[14px] md:text-lg sm:text-sm sm:py-[12px] transition-all duration-500 ease-0 hover:bg-[#d7b50a]  disabled:opacity-40 disabled:cursor-not-allowed text-base md:text-sm">
                </div>
            </form>

        </div>
    </div>
    @include('layouts.navigation')
</div>
<script>

 function checkBlankField(id, msg, msgContainer){
    if ($("#" + id).val().length < 1)
    {
        $("." + msgContainer).css("display", "block");
        $("." + msgContainer).html(msg);
    }
    else
    {
        $("." + msgContainer).html('');
        $("." + msgContainer).css("display", "none");
    }
}


function hideLoader() {
            $('.loader').css("display", "none");
            $('.overlay_sections').css("display", "none");
        }

function validate() {
    $('.loader').css("display", "inline-flex");
    $('.overlay_sections').css("display", "block");
    var hasErrors = false;
    var numErrors = false;
    var firstErrorElement = null;
    var required_fields = $('.required_fields');
    required_fields.each(function () {
        if (!$(this).val().trim()) {
            hasErrors = true;
            if (!firstErrorElement) {
                firstErrorElement = $(this);
            }
            $(this).closest('.inp_container').find('.required').html($(this).data("error-msg"));
            $(this).closest('.inp_container').find('.error').hide();

        } else {
            $(this).closest('.inp_container').find('.required').empty();
            $(this).closest('.inp_container').find('.error').empty();
        }

        if ($(this).hasClass('phone_validation')) {
            var inputValue = $(this).val().trim();
            var validationSpan = $(this).closest('.inp_container').find('.required');
            if (!inputValue) {
                hasErrors = true;
                validationSpan.html("mobile number is required");
                 $(this).closest('.inp_container').find('.error').hide();
            } else {
                if (inputValue.length < 10) {
                    hasErrors = true;
                    validationSpan.html("Mobile number must be at least 10 digits");
                    $(this).closest('.inp_container').find('.error').hide();
                } else if (inputValue.length === 10) {
                    validationSpan.empty();
                } else {
                    validationSpan.empty();
                }
            }
        }
    });

    if (!hasErrors) {

    if (numErrors) {

        hideLoader();
        Swal.fire({
            title: 'Please fill a valid number',
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
       console.log('Validation successful!');
      return true;
    }
    }

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
        console.log('Validation successful!');
        return true;
    }
    }


   $('body').on('input', '.alpha_validate', function () {
    let alphabeticValue = $(this).val().replace(/[^a-zA-Z\s]/g, "");
    $(this).val(alphabeticValue);
   });

   $('body').on('input', '.email_alpha_validate', function () {
    let alphabeticValue = $(this).val().replace(/[^a-zA-Z\s.]/g, "");
    $(this).val(alphabeticValue);
   });


   $('body').on('input','.phone_validation',function(){
   let numericValue =  $(this).val().replace(/[^0-9]/g, "");
   numericValue = numericValue.substring(0, 15);
   $(this).val(numericValue);
   });

   $('body').on('input','.zip_validate',function(){
   let numericValue =  $(this).val().replace(/[^0-9]/g, "");
   numericValue = numericValue.substring(0, 6);
   $(this).val(numericValue);
   });

   $('#agent_short_name').on('input', function(){
    var inputValue = $(this).val();
    var trimmedValue = inputValue.replace(/\s/g, '');
    var truncatedValue = trimmedValue.slice(0, 3).toUpperCase();
    $(this).val(truncatedValue);
  });

    $('.required_fields').on('keyup', function () {
        var hasErrors = false;
        if (!$(this).val().trim()) {
            hasErrors = true;
            $(this).closest('.inp_container').find('.required').html($(this).data("error-msg"));
            $(this).closest('.inp_container').find('.error').hide();
        } else {
            $(this).closest('.inp_container').find('.required').empty();
            $(this).closest('.inp_container').find('.error').empty();
        }

        if ($(this).hasClass('phone_validation')) {
            var inputValue = $(this).val().trim();
            var validationSpan = $(this).closest('.inp_container').find('.required');
            $(this).closest('.inp_container').find('.error').hide();
            if (!inputValue) {
                validationSpan.html("Mobile number is required");
                $(this).closest('.inp_container').find('.error').hide();
            } else {
                if (inputValue.length < 10) {
                    validationSpan.html("Mobile number must be at least 10 digits");
                }
                 else {
                    validationSpan.empty();
                }
            }
            if(inputValue.length === 0){
                validationSpan.empty();
            }
        }
    });


    $('body').on('keyup','.not_required_fields',function(){
        var inputValue = $(this).val().trim();
        if(inputValue !=''){
            $(this).siblings('.required').empty();
        }
    });

    var partnerId = {{Auth::user()->id}};

    var fileTypesBusLicense = ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG', 'webp', 'WEBP'];
        $("#company_logo").on('change', function(e) {
            if ($(this).val() != '') {
                var that = this;
                if (this.files && this.files[0]) {
                    var fsize = this.files[0].size;
                    const file = Math.round((fsize / 1024));
                    if (file >= 10240) {
                        Swal.fire({
                        title: 'Image size is too big, please upload less then 10MB size',
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'popup_updated',
                            title: 'popup_title',
                            actions: 'btn_confirmation',
                        },

                        }).then(response => {
                        if (!response.ok) {
                        }
                        return response.json();
                    });
                    }
                    var extension = this.files[0].name.split('.').pop().toLowerCase(),
                    isSuccess = fileTypesBusLicense.indexOf(extension) > -1;
                    if (isSuccess) {
                        upload(this, extension);
                    } else {
                        Swal.fire({
                            title: 'Invalid file format, only upload (JPG, JPEG, PNG, WEBP) formats',
                            icon: 'warning',
                            showCancelButton: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                popup: 'popup_updated',
                                title: 'popup_title',
                                actions: 'btn_confirmation',
                            },

                        }).then(response => {
                        if (!response.ok) {
                        }
                        return response.json();
                    });
                    }
                }
                e.target.value='';
            }
        });

            function upload(img, extension) {
            var form_data = new FormData();
            form_data.append('photo', img.files[0]);
            console.log('uploader data:',img.files[0]);

            form_data.append('_token', '{{ csrf_token() }}');
            $(".loader").css("display", "inline-flex");
            $(".overlay_sections").css("display", "block");
            jQuery.ajax({
                url: "{{ route('agent.profile.photo.update',1) }}",
                data: form_data,
                type: 'POST',
                contentType: false,
                processData: false,
                success: function(data) {

                if (!data.errors) {
                $('.logo_image').remove();
                $(".CompanyImageUrl").attr('src', '');
                $("#CompanyImageId").val('');

                $("#CompanyImageId").val(data.imageId);
                $(".CompanyImageUrl").attr('src', data.miniImageUrl);
                $('.image_container').append(
                '<div class="relative rounded-[10px] border border-[#D8D5D0] w-1/3 pt-[10px] w-[200px] h-[150px] bg-white overflow-hidden logo_image">' +
                    '<div class="pb-[45px] mr-3  w-full h-full ' +
                        'flex flex-col items-center justify-center overflow-hidden">' +
                        '<img src="'+data.miniImageUrl+'" alt="" ' +
                        'class="CompanyImageUrl object-contain max-h-full max-w-full">' +
                    '</div>' +
                    '<div class="flex items-center absolute bottom-0 left-0 right-0 border-t border-[#DAD8D8] justify-center hover:bg-[#FFE8E8] transition-all duration-300 ease-in-out">' +
                        '<a href="javascript:void(0);" id="removeImage" class="p-[10px] w-1/2 px-[10px] font-medium text-[#f00]">' +
                            '<img class="mx-auto" src="{{asset('images/delete_icon_red.svg')}}" alt="">' +
                        '</a>' +
                    '</div>' +
                '</div>'
                );
                $('body').addClass('profile_exist');
                }
                 else {
                    Swal.fire({
                        title: 'File format not supported. Please upload a PNG, JPG or WEBP file',
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
                        }
                    });
                  }
                },
                complete: function(data) {
                     $(".CompanyImageUrl").attr('src', data.miniImageUrl);
                     $('.CompanyImageUrl').on('load', function() {
                        $(".loader").css("display", "none");
                        $(".overlay_sections").css("display", "none");
                    });
                },
                error: function(xhr, status, error) {

                }
         });
        }

    $('body').on('click', '#removeImage', function () {
         var companyImageId = $("#CompanyImageId").val();
         var userId = '{{auth()->user()->id}}';
         Swal.fire({
            title: 'Are you sure want to remove?',
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: 'YES, REMOVE IT!',
            cancelButtonText: 'NO, CANCEL'
          }).then((result) => {
            if (result.isConfirmed) {
               $(".acoda-spinner").css("display", "inline-flex");
               $(".overlay").css("display", "block");
               Swal.fire({
                        title: 'Company Logo has been deleted',
                        icon: 'success',
                        showCancelButton: false,
                        confirmButtonText: 'OK',
                        customClass: {
                                    popup: 'popup_updated',
                                    title: 'popup_title',
                                    actions: 'btn_confirmation',
                        },
                    });

                    $(".CompanyImageUrl").attr('src', '');
                    $("#CompanyImageId").val('');
                    $('.logo_image').remove();
                    $('body').removeClass('profile_exist');
            }
         });

      });

    $(".btnToggle").on('click',function(){
        var passwordInput= $(this).closest('.relative').find('#password');
        console.log('password',passwordInput);
        $(this).toggleClass('eyeShowing');
        togglePassword(passwordInput);
    });

    function togglePassword(passwordInput) {
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
        }
        else {
            passwordInput.attr('type', 'password');
        }
    }
</script>
<script>
$('body').on('input', '.phone_validation', function() {
   let numericValue = $(this).val().replace(/[^0-9+]/g, "");
   $(this).val(numericValue);
});

    var utilsScript = "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js";
    var input = document.querySelector("#login_mobile_number");
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
        var user_mobile_country_code = $(this).closest('.login_mobile_number').find('.iti__selected-dial-code').html();
        console.log('user_mobile_country_code:',user_mobile_country_code);
        $('.user_mobile_country_code').val(user_mobile_country_code);

    });

    var utilsScript = "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js";
    var input4 = document.querySelector("#company_phone_number");
    window.intlTelInput(input4, {
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

    input4.addEventListener("countrychange", function() {
        var company_phone_number_country_code = $(this).closest('.company_phone_number').find('.iti__selected-dial-code').html();
        console.log('company_phone_number_country_code:',company_phone_number_country_code);
        $('.company_phone_number_country_code').val(company_phone_number_country_code);

    });

    $(document).ready(function () {
    if ($('#company_phone_number').val().trim() === '' || $('#company_phone_number').val().trim() === '0') {
        $('#company_phone_number').val('').click();
    }

    if($('#CompanyImageId').val() !=''){
        $('body').addClass('profile_exist');
    }
  });

</script>
@endsection
