<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="theme-color" content="#F3CD0E"/>
    <link rel="apple-touch-icon" href="{{ asset('images/logo.svg') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="stylesheet" href="{{asset('css/sanjay-style.css?v=8.7')}} ">
    <link rel="stylesheet" type="text/css" href="{{asset('css/fancybox.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/build.css?v=2')}}">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="{{asset('css/build2.css?v=4')}}">
    <link rel="stylesheet" href="{{asset('css/build3.css?v9.3')}}">
    <link rel="shortcut icon" href="{{asset('images/favicon.png')}}" type="image/x-icon">
    <title>@yield('title')</title>
</head>
<style>
    @media screen and (max-width: 992px) {
    .layout_main_top_header{ display: none; }
    }
    .error{ color:#ff0000; }
    .required{ color:#ff0000; }
    .booking_inner_b{ overflow: hidden; width: 200px; height: 200px; }
    .imageProfileUrl{ width: 100%; object-fit: contain; height: 100%; }
</style>

    <div class="">
        <div class="bg-white shadow-sm tab:bg-transparent tab:z-0 tab:border-0 tab:shadow-none">
            <div class="px-3  md:px-10 ">
                <div class="flex justify-start items-center">
                     <a href="javascript:void(0)" onclick="event.preventDefault(); document.getElementsByClassName('logout_form')[0].submit();"  class="inline-block py-4">
                        <img class="inline-block mr-2" src="{{asset('images/panel-back-arrow.svg')}}">
                    </a>
                    <form action="{{ route('partner.logout') }}" class="logout_form" method="post">
                        @csrf
                    </form>
                    <span class="inline-block text-xl font-normal align-middle">Complete Your Profile Details</span>
                </div>

            </div>
        </div>

        @php
        $imageUrl = asset('images/User-Profile.png');
         if (strcmp(Helper::getUserMeta(auth()->user()->id, 'profileImageId'), '') != 0) {
            $profileImage = Helper::getPhotoById(Helper::getUserMeta(auth()->user()->id, 'profileImageId'));
            if ($profileImage) {
                $imageUrl = $profileImage->url;
            }
          }
        $modifiedUrl = $imageUrl;
        @endphp
        @if (Session::has('success'))
            <div class="w-[300px] my-0 mx-auto render">
                <p class="text-[#fff] text-[18px] py-[10px] bg-[#008000] text-center">{{Session::get('success')}}</p>
            </div>
        @endif


        <div class="main_sec  px-3 pt-3 pb-20 bg-[#F6F6F6] md:px-10 tab:pt-0">
            <div class="pt-4 pb-10 booking_section ">

        <!-- imageProfileUrl -->
        <div class="flex px-0 pt-2 mb-3 overflow-hidden">
            <div class=" tab:mr-3 w-1/3  max-h-[200px] min-w-[150px] upload_height
                 bg-white flex flex-col items-center justify-center booking_inner_b cursor-pointer">
                <label for="myfile"
                    class="flex flex-col items-center justify-center w-full py-[72px]  text-center cursor-pointer">
                    <img src="{{asset('images/add_upload_icon.svg')}}" class="upload_width" alt="icon">
                    <h4 class="pt-4 text-sm font-normal text-blacklight">Upload logo</h4>
                </label>
                <input type="file" id="myfile" class="hidden" name="myfile">
            </div>

            <div class="flex image_content  tab:flex-wrap ">
           <div class="pb-5 pl-2 img_container">
          <div class="group">
          <a href="javascript:void(0)">
        <div class="bg-white upload_height booking_inner_b flex relative flex-col items-center justify-center p-4 h-[200px] w-[200px] overflow-hidden">
        <img src="{{ isset($modifiedUrl) ? $modifiedUrl : asset('images/User-Profile.png') }}" alt="Profile Image" class="imageProfileUrl">
          </div>
            </a>
         </div>
        <a href="javascript:void(0)" class="inline-block text-sm w-full text-center pt-2 font-normal
        text-[#B3AFA7] cursor-pointer removeProfileBtn">Remove</a>
        </div>
        </div>
        </div>
        <!-- imageProfileUrl  -->
        <form action="{{route('partner.complete.profile.update',$userId)}}" method="post" onsubmit="return validate();">
        @csrf
        <input type="hidden" id="profileImageId" name="profileImageId"
        value="@if (strcmp(Helper::getUserMeta(auth()->user()->id, 'profileImageId'), '') != 0)
         {{ Helper::getUserMeta(auth()->user()->id, 'profileImageId') }}@else{{ '0' }} @endif">
                    <div class="tab:flex">
                        <div class="mt-6 mb-6 text-sm font-normal tab:w-1/2 tab:mr-2">
                            <label for="name" class="block w-full mb-[5px] text-black500">Name*</label>
                            <input type="text" name="name" id="name" class="input_tag required_fields
                             focus:border-siteYellow w-full py-[10px] pl-3 text-sm rounded-[10px] border
                              border-midgray  outline-none text-black500"
                                placeholder="Enter Name" data-error-msg="Name is required"
                                 value="{{ Helper::getUserMeta(auth()->user()->id, 'name') }}">
                            <span class="required"></span>
                            @if ($errors->has('name'))
                            <div class="error">
                                <ul>
                                    <li>{{ $errors->first('name') }}</li>
                                </ul>
                            </div>
                            @endif
                        </div>
                        <div class="mt-6 mb-6 text-sm font-normal tab:w-1/2 tab:ml-2">
                            <label for="company_name" class="block w-full mb-[5px] text-black500">Company Name*</label>
                    <input type="text" name="company_name" id="company_name" class="input_tag w-full required_fields
                 focus:border-siteYellow py-[10px] pl-3 text-sm rounded-[10px] border border-midgray
                    outline-none text-black500" value="{{ Helper::getUserMeta(auth()->user()->id, 'company_name') }}"
                    placeholder="Enter Company Name"
                                data-error-msg="Company Name is required">
                            <span class="required"></span>
                            @if ($errors->has('company_name'))
                            <div class="error">
                                <ul>
                                    <li>{{ $errors->first('company_name') }}</li>
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>


                    <div class="tab:flex">
                        <div class="mt-6 mb-6 text-sm font-normal tab:w-1/2 tab:mr-2">
                            <label for="mobile" class="block w-full mb-[5px] text-black500">Mobile Number*</label>
                            <input type="text" name="mobile" id="mobile" class="input_tag focus:border-siteYellow
                             w-full py-[10px] pl-3 text-sm rounded-[10px] border
                                 border-midgray outline-none text-black500 required_fields" value="{{Auth::user()->mobile}}"
                                placeholder="Enter Mobile Number" disabled>

                        </div>
                        <div class="mt-6 mb-6 text-sm font-normal tab:w-1/2 tab:ml-2">
                            <label for="serving_area" class="block mb-[5px] text-black500">Serving Area</label>
                            <input type="text" name="serving_area" id="serving_area"
                            class="input_tag focus:border-siteYellow w-full py-[10px] pl-3 text-sm
                                rounded-[10px] border border-midgray outline-none text-black500"
                            value="{{ Helper::getUserMeta(auth()->user()->id, 'serving_area') }}"placeholder="Enter Serving Area">
                        </div>
                    </div>


                    <div class="tab:flex">
                        <div class="mt-6 mb-6 text-sm font-normal tab:w-1/2 tab:mr-2">
                            <label for="advance_b_amount" class="block w-full mb-[5px] text-black500">Email Id*</label>
                            <input type="text" name="email" id="email" class="input_tag w-full text-sm py-[10px]
                             pl-3 focus:border-siteYellow rounded-[10px]  outline-none border border-midgray
                             text-black500 required_fields"   value="{{ Helper::getUserMeta(auth()->user()->id, 'email') }}"
                              placeholder="Enter Email Id"
                            data-error-msg="Email is required" data-not-valid ="Email is not valid">
                            <span class="required"></span>
                            @if ($errors->has('email'))
                            <div class="error">
                                <ul>
                                    <li>{{ $errors->first('email') }}</li>
                                </ul>
                            </div>
                            @endif
                        </div>
                        <div class="mt-6 mb-6 text-sm font-normal tab:w-1/2 tab:ml-2">
                        <label for="address" class="block w-full mb-[5px] text-black500">Address</label>
                        <input type="text" name="address" id="address"
                    class="input_tag w-full text-sm py-[10px] pl-3 focus:border-siteYellow rounded-[10px] outline-none border
                         border-midgray text-black500"
                         value="{{ Helper::getUserMeta(auth()->user()->id, 'address') }}" placeholder="Enter Address">
                        </div>
                    </div>
                 <!--  -->
                <div class="tab:flex">
                    <div class="mt-6 mb-6 text-sm font-normal tab:w-1/2 tab:mr-2">
                        <label for="location" class="block w-full mb-[5px] text-black500">Location*</label>
                        <input type="text" name="location" id="location" class="input_tag w-full text-sm py-[10px]
                         pl-3 focus:border-siteYellow rounded-[10px] outline-none border border-midgray
                        text-midgray required_fields" value="{{ Helper::getUserMeta(auth()->user()->id, 'location') }}" placeholder="Enter location"
                        data-error-msg="Location is required">
                        <span class="required"></span>
                        @if ($errors->has('location'))
                        <div class="error">
                            <ul>
                                <li>{{ $errors->first('location') }}</li>
                            </ul>
                        </div>
                        @endif
                                            </div>
                    <div class="flex mt-6 mb-6 tab:w-1/2 tab:ml-2">
                        <div class="w-1/2 mr-2 text-sm font-normal">
                        <label class="block mb-1 text-black500" for="drop_charges">Drop charges*</label>
                        <input type="text" name="drop_charges" id="drop_charges" value="{{ Helper::getUserMeta(auth()->user()->id, 'drop_charges') }}" data-error-msg="Drop charges is required" class="input_tag required_fields focus:border-siteYellow w-full py-[10px] pl-3 text-sm rounded-[10px] border border-midgray  outline-none text-black500" placeholder="200">
                        <span class="required"></span>
                        @if ($errors->has('drop_charges'))
                        <div class="error">
                            <ul>
                                <li>{{ $errors->first('drop_charges') }}</li>
                            </ul>
                        </div>
                        @endif
                    </div>
                    <div class="w-1/2 text-sm font-normal">
                            <label class="block mb-[5px] text-black500" for="pickup_charges">Pickup charges*</label>
                            <input type="text" name="pickup_charges" id="pickup_charges" value="{{ Helper::getUserMeta(auth()->user()->id, 'pickup_charges') }}" data-error-msg="Pickup charges is required" class="input_tag required_fields focus:border-siteYellow w-full py-[10px] pl-3 text-sm rounded-[10px] border border-midgray  outline-none text-black500" placeholder="200">
                            <span class="required"></span>
                            @if ($errors->has('pickup_charges'))
                            <div class="error">
                                <ul>
                                    <li>{{ $errors->first('pickup_charges') }}</li>
                                </ul>
                            </div>
                            @endif
                    </div>
                    </div>
                </div>
                <!--  -->

                    <div class="w-full mt-8 mb-8 text-center sm:my-14">
                        <input type="submit"
                            class="inline-flex items-center justify-center
                             w-full px-5 py-3 text-base font-bold text-center
                              text-white capitalize ease-in-out cursor-pointer sm:w-auto bg-siteYellow sm:px-14 rounded-xl hover:bg-[#e4b130]"
                            value="SAVE">
                    </div>
                </form>
            </div>
        </div>


    </div>
    <script src="{{asset('js/jquery-3.7.0.min.js')}}"></script>
    <script src="{{ asset('js/jquery.matchHeight-min.js') }}"></script>
    <script type="text/javascript" src="{{asset('js/fancybox.min.js')}}"></script>
    <script src="{{ asset('js/sweetalert2@11.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


    <script>
        $('.upload_Height').matchHeight();

         if($('#profileImageId').val()!=0){
              $('.removeProfileBtn').show();
         }
         else{
             $('.removeProfileBtn').hide();
         }
        var fileTypesBusLicense = ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'];
            $("#myfile").on('change', function(e)
            {
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
                                 title: 'Invalid file format',
                                 icon: 'warning',
                                 showCancelButton: false,
                                 confirmButtonText: 'OK',
                             }).then(response => {
                             if (!response.ok) {
                             }
                             return response.json()
                         });
                         }

                     }

                 }
             });
             function upload(img, extension) {
                 var form_data = new FormData();
                 form_data.append('photo', img.files[0]);
                 form_data.append('_token', '{{ csrf_token() }}');
                 jQuery.ajax({
                     url: "{{ route('partner.complete.profile.photo.update',$userId) }}",
                     data: form_data,
                     type: 'POST',
                     contentType: false,
                     processData: false,
                     success: function(data) {
                     if (!data.errors) {
                     $("#profileImageId").val(data.imageId);
                     console.log('profile_images:',$("#profileImageId").val());
                     $(".imageProfileUrl").attr('src', data.miniImageUrl);
                     if($("#profileImageId").val()>0){
                         $('.removeProfileBtn').show();
                     }
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
                          $(".imageProfileUrl").attr('src', data.miniImageUrl);
                     },
                     error: function(xhr, status, error) {}
              });
        }

         $('body').on('click', '.removeProfileBtn', function() {
           Swal.fire({
             title: 'Are you sure you want to remove?',
             icon: 'warning',
             showCancelButton: true,
             }).then((result) => {
             if (result.isConfirmed) {
                 $.ajax({
                     type: "POST",
                     url: "{{ route('partner.complete.profile.photo.remove', $userId) }}",
                     data: {
                         "_token": "{{ csrf_token() }}"
                     },
                     dataType: "json",
                     success: function (data) {
                     if (data.success) {

                         $(".imageProfileUrl").attr('src', '{{ asset('images/User-Profile.png') }}');
                         $('.removeProfileBtn').hide();
                     }
                     }
                 });
              }
            });
         });

         function validate() {
         var required_fields = $('.required_fields');
         var hasErrors = false;

         required_fields.each(function() {
             if (!$(this).val()) {
                 hasErrors = true;
                 $(this).siblings('.required').html($(this).data("error-msg"));
             } else {
                 $(this).siblings('.required').empty();
             }
         });

         var emailField = $('#email');
         var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
         if (!emailField.val()) {
             hasErrors = true;
             emailField.siblings('.required').html(emailField.data("error-msg"));
         } else if (!validRegex.test(emailField.val())) {
             hasErrors = true;
             emailField.siblings('.required').html(emailField.data("not-valid"));
         } else {
             emailField.siblings('.required').empty();
         }

         if (hasErrors) {
             Swal.fire({
                 title: 'Please fill all required fields',
                 icon: 'warning',
                 showCancelButton: false,
                 confirmButtonText: 'OK'
             }).then((result) => {
                 if (result.isConfirmed) {
                     $("html, body").animate({
                       scrollTop: parseFloat($(".required:visible:first").offset().top) - 150
                      }, 500);
                 }
             });
         return false;
         } else {
             return true;
         }
     }


         $('.required_fields').on('keyup',function(){
             if($(this).val()){
               $(this).siblings('.required').empty();
               $('.error').empty();
             }
             else{
              $(this).siblings('.required').html($(this).data("error-msg"));
           }
         });

         $('#mobile').on('input', function(){
         let numericValue = this.value.replace(/[^0-9]/g, "");
         numericValue = numericValue.substring(0, 10);
         $('#mobile').val(numericValue);
         });

         $('#drop_charges').on('input', function(){
         let numericValue = this.value.replace(/[^0-9]/g, "");
         numericValue = numericValue.substring(0, 4);
         $('#drop_charges').val(numericValue);
         });

         $('#pickup_charges').on('input', function(){
         let numericValue = this.value.replace(/[^0-9]/g, "");
         numericValue = numericValue.substring(0, 4);
         $('#pickup_charges').val(numericValue);
         });

         $('#email').on('keyup', function() {
         var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
         if (validRegex.test($(this).val())) {
              $(this).siblings('.required').empty();
              $('.error').empty();
         } else {
             $(this).siblings('.required').html($(this).data("not-valid"));
         }
         });
         setTimeout(function() {
                 $('.render').slideUp('5000');
         }, 500);

</script>
<script src="{{ asset('/sw.js') }}"></script>
<script>
   if ("serviceWorker" in navigator) {
      // Register a service worker hosted at the root of the
      // site using the default scope.
      navigator.serviceWorker.register("/sw.js").then(
      (registration) => {
         console.log("Service worker registration succeeded:", registration);
      },
      (error) => {
         console.error(`Service worker registration failed: ${error}`);
      },
    );
  } else {
     console.error("Service workers are not supported.");
  }
</script>

</body>
</html>
