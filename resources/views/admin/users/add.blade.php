@extends('layouts.admin')
@section('title', 'Add New User')
@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css">
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
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
@media screen and (max-width:992px) {
.main_header_container{ display:none;}
}

</style>
<div class="lg:flex hidden lg:bg-white lg:mb-[0px] lg:px-[17px] lg:py-[15px] mb-[20px] justify-start items-center">
        <a href="{{route('admin.users.list')}}" class="links_item_cta inline-block p-2 mr-2 border border-[#C8C0C0] rounded-md
        hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
            <img class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
        </a>
        <span class="inline-block text-xl font-normal leading-normal align-middle text-black800">Add New User</span>
</div>

<div class="pt-[42px] px-[36px] lg:px-[17px] lg:py-[25px] xl:py-10 bg-[#F6F6F6] pb-[100px] lg:flex lg:justify-center">
<div class="lg:hidden mb-[36px] lg:mb-[20px] flex flex-col">
            <div class="back-button">
                <a href="{{route('admin.users.list')}}" class="links_item_cta inline-flex py-1 px-2 border border-[#C8C0C0] rounded-md bg-[#fff] hover:border-[#F3CD0E] active:border-[#000] active:bg-[#F3CD0E]">
                    <img  class="w-[15px]" src="{{ asset('images/short_arrow.svg') }}">
                    <span class="inline-block text-base leading-normal align-middle text-black800 ml-[10px] font-medium">
                        ALL USERS
                    </span>
                </a>
            </div>
            <span class="inline-block text-[26px] font-normal leading-normal align-middle text-black800">
             Add New User
            </span>
        </div>


<div class="max-w-[768px] w-full bg-white lg:bg-[#F6F6F6] rounded-[10px] px-12  lg:px-[0px] py-9 lg:py-[0px]">
    <div class="booking_section">
        <form action="{{route('admin.users.add.post')}}" method="post" onsubmit="return validate()">
          @csrf
          <!-- company user type -->
           <div class="pb-5">
            <div class="flex flex-wrap -mx-3">
                <div class="w-full px-3 sm:w-full inp_container">
                    <label for="company_user_type"
                    class="block pb-2.5 text-sm font-normal leading-4 text-left text-black ">
                    User Type <span class="text-[#ff0000]">*</span></label>
                    <select name="user_type" id="company_user_type" tabindex="-1"
                    onchange="checkBlankField(this.id,'User Type is required','errorUserType')"
                    class="required_fields w-full py-3 pl-5 pr-10 text-base font-normal leading-normal text-black bg-white
                    border rounded-md appearance-none border-black500 focus:outline-none focus:border-siteYellow drop_location_select"
                    data-error-msg="User type is required">
                    <option value="" class="capitalize text-midgray fontGrotesk">Select User Type</option>
                        <option value="agent" class="capitalize" {{ old('user_type') == 'agent' ? 'selected' : '' }}>Agent</option>
                        <option value="partner" class="capitalize" {{ old('user_type') == 'partner' ? 'selected' : '' }}>Rental Company</option>
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

            <h4 class="inline-block pb-[13px] text-sm font-normal leading-4 text-left text-black">Company Logo</h4>
             <!--  -->
            <div class="flex px-0 mb-[13px] overflow-hidden mb-6 image_container">
                <div class="mr-3 w-1/3 w-[200px] h-[150px] bg-white cursor-pointer rounded-[10px] border border-[#D8D5D0] upload_box">
                    <label for="company_logo"
                        class="flex flex-col items-center justify-center w-full h-full  text-center cursor-pointer">
                        <img src="{{asset('images/add_upload_icon.svg')}}" class="upload_width sm:w-[20px]" alt="icon">
                        <h4 class="pt-4 text-sm font-normal text-blacklight sm:pt-[12px]">Upload Photo</h4>
                    </label>
                    <input type="file" name="company_logo" tabindex="-1" id="company_logo" class="hidden"  accept=".png, .webp, .jpeg, .jpg">
                </div>
            </div>
            <!--  -->
            <p class="text-[12px] font-normal text-[#8B8B8B] mb-[20px]">Required image size: 150x150px.</p>
            <div class="basic_info_sec_head mb-5">
                <h4 class="capitalize text-[20px] font-normal ">basic information</h4>
            </div>

            <div class="pb-5">
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full px-3 sm:w-full inp_container">
                       <input type="hidden" id="CompanyImageId" name="CompanyImageId">
                        <label for="company_name"
                        class="company_name block pb-2.5 text-sm font-normal leading-4 text-left text-black ">
                        Company Name<span class="text-[#ff0000]">*</span></label>
                        <input id="company_name" tabindex="-1"
                            class="required_fields w-full px-5 py-3 text-base font-normal leading-normal
                            text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow" placeholder="Enter company name"
                            type="text" name="company_name"  data-error-msg="Company name is required" value="{{old('company_name')}}">
                            <span class="hidden required text-sm"></span>
                            @if ($errors->has('company_name'))
                            <div class="error">
                                <ul>
                                    <li>{{ $errors->first('company_name') }}</li>
                                </ul>
                            </div>

                        @endif
                    </div>
                </div>
            </div>
            <!-- Company Phone Number -->
                  <div class="pb-5">
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full px-3 sm:w-full inp_container company_phone_number">
                            <label for="company_phone_number"
                                class="block pb-2.5 text-sm font-normal leading-4 text-left text-black ">
                                Company Phone Number<span class="text-[#ff0000]">*</span>
                            </label>

                            <input type="text" tabindex="-1" name="company_phone_number" id="company_phone_number"
                            class="required_fields company_phone_validation w-full px-5 py-3 text-base font-normal leading-normal text-black
                            bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow"
                            data-error-msg="Company phone number is required"
                            value="{{old('company_phone_number')}}" placeholder="Enter company phone number">
                            <input type="hidden" name="company_phone_number_country_code" class="company_phone_number_country_code">
                            <span class="hidden required text-sm"></span>
                            @if ($errors->has('company_phone_number'))
                            <div class="error">
                                <ul>
                                    <li>{{ $errors->first('company_phone_number') }}</li>
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="pb-5">
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full px-3 sm:w-full inp_container">
                            <label for="owner_name" tabindex="-1" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Owner Name<span class="text-[#ff0000]">*</span></label>
                            <input type="text" tabindex="-1" name="owner_name" id="owner_name"
                             class="required_fields w-full px-5 py-3 text-base font-normal leading-normal
                             text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow alpha_validate"
                             data-error-msg="Owner name is required" placeholder="Enter owner name" value="{{old('owner_name')}}">
                             <span class="hidden required text-sm"></span>
                                @if ($errors->has('owner_name'))
                                <div class="error">
                                    <ul>
                                        <li>{{ $errors->first('owner_name') }}</li>
                                    </ul>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>

                <!-- Login Mobile Number -->
                <div class="pb-5">
                    <div class="flex flex-wrap -mx-3">
                        <div class="w-full px-3 sm:w-full inp_container mobile_number">
                            <label for="mobile_number"
                                class="block pb-2.5 text-sm font-normal leading-4 text-left text-black ">
                               Login Mobile Number<span class="text-[#ff0000]">*</span>
                            </label>
                            <input type="text" tabindex="-1" name="mobile_number" id="mobile_number"
                            class="required_fields phone_validation w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white
                            border rounded-md border-black500 focus:outline-none focus:border-siteYellow"
                            placeholder="Enter login mobile number"
                            data-error-msg="Login mobile number is required"
                            value="{{old('mobile_number')}}">
                            <input type="hidden" name="user_mobile_country_code" class="user_mobile_country_code">
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
            <div class="pb-5">
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full px-3 sm:w-full inp_container">
                        <label for="email" class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Email Address (optional)</label>
                        <input type="text" tabindex="-1" name="email" id="email"
                        class="w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white
                        border rounded-md border-black500 focus:outline-none focus:border-siteYellow"
                        placeholder="Enter email"
                        value="{{old('email')}}">
                    </div>
                </div>
            </div>

            <div class="pb-5 partner_form_heading">
                <h4 class="inline-block text-xl font-normal leading-normal align-middle text-black800">Address:</h4>
            </div>

            <div class="pb-5">
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full px-3 sm:w-full">
                        <label for="plot_shop_number"class="block pb-2.5 text-sm font-normal leading-4 text-left text-black">Plot/Shop number</label>
                        <input id="plot_shop_number" tabindex="-1"
                        placeholder="Enter plot/shop number"
                        class="w-full px-5 py-3 text-base font-normal leading-normal
                        text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow"
                        type="text" name="plot_shop_number" value="{{old('plot_shop_number')}}">
                    </div>

                </div>

            </div>

            <div class="flex flex-wrap -mx-[9px] sm:-mx-[0px] pb-5">
                <div class="w-1/2 px-[9px] sm:px-[0px] sm:w-full">
                    <label for="street_name"
                    class="inline-block pb-2.5 text-sm font-normal leading-4 text-left text-black">Street Name</label>
                    <input type="text" tabindex="-1" name="street_name" id="street_name"
                    class="w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border rounded-md
                     border-black500 focus:outline-none focus:border-siteYellow"
                    placeholder="Enter street name" value="{{old('street_name')}}">
                </div>
                <div class="w-1/2 sm:mt-5 px-[9px] sm:px-[0px] sm:w-full">
                    <label for="state"
                    class="inline-block pb-2.5 text-sm font-normal leading-4 text-left text-black">State</label>
                    <select name="state" tabindex="-1" id="state" class="s_tag_haf w-full pl-5 pr-10 py-3 text-base font-normal leading-normal
                    text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow appearance-none border drop_location_select">
                    <option value="" disabled>Select State</option>
                    <option value="Assam" {{ old('state') == 'Assam' ? 'selected' : '' }}>Assam</option>
                    <option value="Bihar" {{ old('state') == 'Bihar' ? 'selected' : '' }}>Bihar</option>
                    <option value="Chhattisgarh" {{ old('state') == 'Chhattisgarh' ? 'selected' : '' }}>Chhattisgarh</option>
                    <option value="Goa" {{ old('state') == 'Goa' ? 'selected' : '' }}>Goa</option>
                    <option value="Gujarat" {{ old('state') == 'Gujarat' ? 'selected' : '' }}>Gujarat</option>
                    <option value="Haryana" {{ old('state') == 'Haryana' ? 'selected' : '' }}>Haryana</option>
                    <option value="HimachalPradesh" {{ old('state') == 'HimachalPradesh' ? 'selected' : '' }}>Himachal Pradesh</option>
                    <option value="JammuKashmir" {{ old('state') == 'JammuKashmir' ? 'selected' : '' }}>Jammu Kashmir</option>
                    <option value="Jharkhand" {{ old('state') == 'Jharkhand' ? 'selected' : '' }}>Jharkhand</option>
                    <option value="Karnataka" {{ old('state') == 'Karnataka' ? 'selected' : '' }}>Karnataka</option>
                    <option value="Kerala" {{ old('state') == 'Kerala' ? 'selected' : '' }}>Kerala</option>
                    <option value="MadhyaPradesh" {{ old('state') == 'MadhyaPradesh' ? 'selected' : '' }}>Madhya Pradesh</option>
                    <option value="Maharashtra" {{ old('state') == 'Maharashtra' ? 'selected' : '' }}>Maharashtra</option>
                    <option value="Manipur" {{ old('state') == 'Manipur' ? 'selected' : '' }}>Manipur</option>
                    <option value="Meghalaya" {{ old('state') == 'Meghalaya' ? 'selected' : '' }}>Meghalaya</option>
                    <option value="Mizoram" {{ old('state') == 'Mizoram' ? 'selected' : '' }}>Mizoram</option>
                    <option value="Nagaland" {{ old('state') == 'Nagaland' ? 'selected' : '' }}>Nagaland</option>
                    <option value="Rajasthan" {{ old('state') == 'Rajasthan' ? 'selected' : '' }}>Rajasthan</option>
                    <option value="Sikkim" {{ old('state') == 'Sikkim' ? 'selected' : '' }}>Sikkim</option>
                    <option value="TamilNadu" {{ old('state') == 'TamilNadu' ? 'selected' : '' }}>Tamil Nadu</option>
                    <option value="Telangana" {{ old('state') == 'Telangana' ? 'selected' : '' }}>Telangana</option>
                    <option value="Tripura" {{ old('state') == 'Tripura' ? 'selected' : '' }}>Tripura</option>
                    <option value="UttarPradesh" {{ old('state') == 'UttarPradesh' ? 'selected' : '' }}>Uttar Pradesh</option>
                    <option value="Uttarakhand" {{ old('state') == 'Uttarakhand' ? 'selected' : '' }}>Uttarakhand</option>
                    <option value="WestBengal" {{ old('state') == 'WestBengal' ? 'selected' : '' }}>West Bengal</option>
                    <option value="AndamanNicobar" {{ old('state') == 'AndamanNicobar' ? 'selected' : '' }}>Andaman Nicobar</option>
                    <option value="Chandigarh" {{ old('state') == 'Chandigarh' ? 'selected' : '' }}>Chandigarh</option>
                    <option value="DadraHaveli" {{ old('state') == 'DadraHaveli' ? 'selected' : '' }}>Dadra Haveli</option>
                    <option value="DamanDiu" {{ old('state') == 'DamanDiu' ? 'selected' : '' }}>Daman Diu</option>
                    <option value="Delhi" {{ old('state') == 'Delhi' ? 'selected' : '' }}>Delhi</option>
                    <option value="Lakshadweep" {{ old('state') == 'Lakshadweep' ? 'selected' : '' }}>Lakshadweep</option>
                    <option value="Puducherry" {{ old('state') == 'Puducherry' ? 'selected' : '' }}>Puducherry</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap -mx-[9px] sm:-mx-[0px] pb-5">

                <div class="w-1/2 sm:mt-5 sm:mt-0 px-[9px] sm:px-[0px] sm:w-full">
                    <label for="city" class="inline-block pb-2.5 text-sm font-normal leading-4 text-left text-black">City</label>
                      <input type="text" tabindex="-1" name="city" id="city"
                      class="w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border
                      rounded-md border-black500 focus:outline-none focus:border-siteYellow"
                      placeholder="Enter city" value="{{old('city')}}">
                </div>

                <div class="w-1/2 px-[9px] sm:px-[0px] sm:pt-5 sm:w-full">
                    <label for="zip"
                    class="inline-block pb-2.5 text-sm font-normal leading-4 text-left text-black">Zip</label>
                    <input type="text" tabindex="-1" name="zip" id="zip"
                    class="w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border
                    rounded-md border-black500 focus:outline-none focus:border-siteYellow zip_validate"
                    placeholder="Enter zip" value="{{old('zip')}}">
                </div>

            </div>

            <div class="pb-5">
                <div class="flex flex-wrap -mx-3">
                    <div class="w-full px-3 sm:w-full inp_container">
                        <label for="password"
                            class="block pb-2.5 text-sm font-normal leading-4 text-left text-black ">
                            Password<span class="text-[#ff0000]">*</span>
                        </label>
                        <div class="relative">
                        <input id="password" tabindex="-1"
                            class="required_fields w-full px-5 py-3 text-base font-normal leading-normal
                             text-black bg-white border rounded-md border-black500 pr-[46px] focus:outline-none focus:border-siteYellow"
                             placeholder="Enter password"
                            type="password" name="password" data-error-msg="Password is required" value="{{old('password')}}">
                         <span class="absolute cursor-pointer btnToggle right-5 top-1/2 translate-y-[-50%] transform-50 toggle">
                            <img src="{{asset('images/eyeIcon.svg')}}" alt="eye_icon" class="eyeIconW eye_show">
                            <img src="{{asset('images/eys_black_password.svg')}}" alt="eye_slash_icon" class="eyeIconW eye_hide">
                         </span>
                         </div>
                            <span class="hidden required text-sm"></span>
                            @if ($errors->has('password'))
                            <div class="error">
                                <ul>
                                    <li>{{ $errors->first('password') }}</li>
                                </ul>
                            </div>
                            @endif
                    </div>
                </div>
            </div>

            <!--  -->
            <div class="pb-5 partner_form_heading delivery_drivers_head">
                <h4 class="inline-block text-xl font-normal leading-normal align-middle text-black800">Drivers:</h4>
            </div>
            <div class="driver_repeat_box_main">
            </div>
            <div class="text-left pt-[8px] pb-[21px]">
                <a href="javascript:void(0)" id="add_more_agent" class="inline-flex items-center pl-[10px] pr-[16px] py-2.5  font-normal leading-4 text-black rounded-[4px] hover:border-siteYellow transition-all duration-300 ease-in-out hover:bg-siteYellow border border-black500 text-sm"
                   > <span class="mr-[15px]"><svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23" fill="none">
                    <circle cx="11.5" cy="11.5" r="11.5" fill="#898376"/>
                    <path d="M11.8489 17.4244V4.87891M18.1216 11.1516H5.57617H18.1216Z" stroke="white" stroke-width="2" stroke-linecap="square"/>
                </svg></span>ADD DRIVER</a>
            </div>
            <!--  -->

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
var utilsScript = "https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js";
    $('#company_user_type').on('input',function(e){
        e.preventDefault();
        console.warn('value:',$(this).val());
        if($(this).val()==='agent'){
           $('.delivery_drivers_head, .driver_repeat_box_main').hide();
           $('#add_more_agent').closest('div').hide();
           $('.driver_repeat_box_main').html('');
        }else if($(this).val()==='partner'){
            $('.delivery_drivers_head, .driver_repeat_box_main').show();
            $('#add_more_agent').closest('div').show();
        }
    });
    var driverNo = 1;
    $("#add_more_agent").on('click', function(e) {
        e.preventDefault();
        $('.driver_repeat_box_main').append(`<div class="mb-5 driver_repeat_box relative pr-[40px] sm:pr-[0px] sm:bg-white rounded-[5px]">
                    <div class="flex flex-wrap -mx-3 sm:pt-[18px] sm:pb-[18px] sm:px-[22px]">
                        <div class="w-1/2 px-3 sm:w-full sm:pb-5">
                            <label for="driver_name_${driverNo}"
                                class="block pb-2.5 text-sm font-normal leading-4 text-left text-black ">
                                Driver Name
                            </label>
                            <input id="driver_name_${driverNo}"
                            placeholder="Enter driver name"
                                class="not_required_fields w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border
                                rounded-md border-black500 focus:outline-none focus:border-siteYellow alpha_validate text-base hhhhhh"
                                type="text" name="driver_name[]" value="{{ is_array(old('driver_name')) ? '' : old('driver_name') }}">
                                <span class="required text-sm"></span>

                        </div>
                        <div class="w-1/2 px-3 sm:w-full not_require_container driver_mobile">
                            <label for="driver_mobile_${driverNo}"
                                class="block pb-2.5 text-sm font-normal leading-4 text-left text-black ">
                                Driver Mobile
                            </label>
                            <input id="driver_mobile_${driverNo}"
                            placeholder="Enter driver mobile"
                                class="not_required_fields phone_validation_manager w-full px-5 py-3 text-base font-normal leading-normal text-black bg-white border rounded-md border-black500 focus:outline-none focus:border-siteYellow text-base"  type="text" name="driver_mobile[]" data-error-msg="Agent mobile is required" value="{{ is_array(old('driver_mobile')) ? '' : old('driver_mobile') }}" minlength="10" >
                                <input type="hidden" name="mobile_number_country_code[]" class="mobile_number_country_code" value="+91">

                                <span class="required text-sm"></span>

                        </div>
                    </div>
                    <div class="text-right absolute right-0 top-[32px] sm:static border-[F6F6F6] sm:border-t sm:py-[12px] sm:px-[20px]">
                        <a class="inline-block removeDriver" href="javascript:void(0)">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-[26px] sm:[28px]">
                                    <svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="16.5" cy="16.5" r="16.5" fill="#F3CD0E"/>
                                    <path d="M22.1257 11.5H10.875L12.1251 24.0008H20.8757L22.1257 11.5Z" stroke="#242323" stroke-miterlimit="10" stroke-linejoin="round"/>
                                    <path d="M9 11.5H24.001" stroke="#242323" stroke-miterlimit="10" stroke-linejoin="round"/>
                                    <path d="M14.625 24.0012V13.3755" stroke="#242323" stroke-miterlimit="10" stroke-linejoin="round"/>
                                    <path d="M18.375 24.0012V13.3755" stroke="#242323" stroke-miterlimit="10" stroke-linejoin="round"/>
                                    <path d="M14.625 11.5002V9H18.3752V11.5002" stroke="#242323" stroke-miterlimit="10" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                <script>
                    var input_x_${driverNo} = document.querySelector("#driver_mobile_${driverNo}");
                    window.intlTelInput(input_x_${driverNo}, {
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

                    input_x_${driverNo}.addEventListener("countrychange", function() {
                    var mobile_number_country_code = $(this).closest('.driver_mobile').find('.iti__selected-dial-code').html();
                    console.log('mobile_number_country_code:',mobile_number_country_code);
                    $(this).closest('.driver_mobile').find('.mobile_number_country_code').val(mobile_number_country_code);
                    });
                <\/script>

                `);
        driverNo++;
    });

    $('body').on('click','.removeDriver',function(){
    var driver_repeat_box = $('.driver_repeat_box').length;
    if(driver_repeat_box<=1){
        $('.delivery_drivers_head').css('display','none');
    }
    $(this).closest('.driver_repeat_box').remove();
 });
function checkBlankField(id, msg, msgContainer){
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

function hideLoader() {
        $('.loader').css("display", "none");
        $('.overlay_sections').css("display", "none");
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
                validationSpan.html("Login mobile number is required");
                 $(this).closest('.inp_container').find('.error').hide().empty();
            } else {
                if (inputValue.length < 10) {
                    hasErrors = true;
                    validationSpan.show().html("Login mobile number must be at least 10 digits");
                    $(this).closest('.inp_container').find('.error').hide().empty();
                }
                if(inputValue.length === 10) {
                    validationSpan.empty();
                }
            }
        }

        if ($(this).hasClass('company_phone_validation')) {
            var inputValue = $(this).val().trim();
            var validationSpan = $(this).closest('.inp_container').find('.required');
            if (!inputValue) {
                hasErrors = true;
                validationSpan.show().html($(this).data("error-msg"));
                 $(this).closest('.inp_container').find('.error').hide().empty();
            } else {
                if (inputValue.length < 10) {
                    hasErrors = true;
                    console.log('fff');
                    validationSpan.show().html("Company phone number must be at least 10 digits");
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
        console.log('Validation successful!');
        return true;
    }
}

$('body').on('input', '.alpha_validate', function () {
    let alphabeticValue = $(this).val().replace(/[^a-zA-Z\s]/g, "");
    $(this).val(alphabeticValue);
});

$('body').on('input', '.phone_validation,.company_phone_validation', function() {
    let numericValue = $(this).val().replace(/[^0-9+]/g, "");
    numericValue = numericValue.substring(0, 15);
    $(this).val(numericValue);
});




$('body').on('input','.zip_validate',function(){
    let numericValue =  $(this).val().replace(/[^0-9]/g, "");
    numericValue = numericValue.substring(0, 6);
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
            validationSpan.show().html("Login mobile number is required");
            $(this).closest('.inp_container').find('.error').hide().empty();
        } else {
            if (inputValue.length < 10) {
                validationSpan.show().html("Login mobile number must be at least 10 digits");
            }
                else {
                validationSpan.empty();
            }
        }

        }

        if ($(this).hasClass('company_phone_validation')) {
        var inputValue = $(this).val().trim();
        var validationSpan = $(this).closest('.inp_container').find('.required');
        $(this).closest('.inp_container').find('.error').hide().empty();
        if (!inputValue) {
            validationSpan.show().html($(this).data("error-msg"));
            $(this).closest('.inp_container').find('.error').hide().empty();
        } else {
            if (inputValue.length < 10) {
                validationSpan.show().html("Company phone number must be at least 10 digits");
            }
                else {
                validationSpan.empty();
            }
        }
        }

    });

    var partnerId = {{ Auth::user()->id }};
    var fileTypesBusLicense = ['jpg', 'jpeg', 'png','webp', 'JPG', 'JPEG', 'PNG', 'WEBP'];
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
                        }
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
                            title: 'Invalid file format, only upload(jpg, jpeg, png, webp, JPG, JPEG, PNG, WEBP) formats',
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
                e.target.value = ''

            }
        });
        function upload(img, extension) {
            var form_data = new FormData();
            form_data.append('photo', img.files[0]);
            form_data.append('_token', '{{ csrf_token() }}');
            $(".loader").css("display", "inline-flex");
            $(".overlay_sections").css("display", "block");
            jQuery.ajax({
                url: "{{ route('admin.user.company.logo',1) }}",
                data: form_data,
                type: 'POST',
                contentType: false,
                processData: false,
                success: function(data) {
                if (!data.errors) {
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

                $('.upload_box').hide();
                console.log('CompanyImageId:',$("#CompanyImageId").val());
                }
                 else {
                    Swal.fire({
                        title: 'File format not supported. Please upload a PNG or JPG file',
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonText: 'OK',
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
                error: function(xhr, status, error) {}
         });

        }
        $('body').on('click','#removeImage', function () {
            $(".CompanyImageUrl").attr('src', '');
             $("#CompanyImageId").val('');
            $('.logo_image').remove();
            $('.upload_box').show();
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

 $('#company_user_type').on('change', function(e) {
    e.preventDefault();
    if ($(this).val() == 'agent') {
        $('#company_name').removeClass('required_fields');
        $('.company_name').children('span').hide();
        $('#company_name').closest('.inp_container').find('.required').hide();
    }

    if ($(this).val() == 'partner') {
        if (!$('#company_name').hasClass('required_fields')) {
            $('#company_name').addClass('required_fields');
            $('.company_name').children('span').show();
            $('#company_name').closest('.inp_container').find('.required').show();
        }
    }
});
    var input = document.querySelector("#mobile_number");
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
        var user_mobile_country_code = $(this).closest('.mobile_number').find('.iti__selected-dial-code').html();
        $('.user_mobile_country_code').val(user_mobile_country_code);
    });



    var input2 = document.querySelector("#company_phone_number");
    window.intlTelInput(input2, {
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

    input2.addEventListener("countrychange", function() {
    var company_phone_number_country_code = $(this).closest('.company_phone_number').find('.iti__selected-dial-code').html();
    $('.company_phone_number_country_code').val(company_phone_number_country_code);
    });

</script>

@endsection
