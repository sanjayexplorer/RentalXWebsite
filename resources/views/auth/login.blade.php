<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <meta name="theme-color" content="#F3CD0E"/>
    <link rel="apple-touch-icon" href="{{ asset('images/logo.svg') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="stylesheet" href="{{asset('css/tailwind-output.css' . generateRandomVersion()) }}">
    <link rel="stylesheet" href="{{asset('css/main.css' . generateRandomVersion()) }}">
    <link rel="stylesheet" href="{{asset('css/style.css' . generateRandomVersion()) }}">
  <style>
  .error{ color:#ff0000; }
  .required{ color:#ff0000; }
  .eyeIconW{ width: 20px }
  .eye_show { display: none; }
  .eyeShowing .eye_show { display: block; }
  .eyeShowing .eye_hide { display: none; }
  /* recaptcha styling */
   @media only screen and (max-width:479px) {
    .g-recaptcha{ transform:scale(0.77); transform-origin:0 0; }
   }
 </style>
</head>
@php
function generateRandomVersion() {
return '?v=' . substr(md5(time()), 0, 8);
}
@endphp
<body>
    <div class="hidden overlay_sections afclr"></div>
    <div class="fixed top-0 left-0 z-[1000] flex items-center justify-center hidden w-full h-screen loader">
        <img src="{{asset('images/loader.svg')}}" class="spinner " alt="loader gif">
    </div>

    <div class="site_area afclr">
        <div class="site_area_inner afclr ">
            <div class="login_section afclr">
                <!-- 3xl:h-full -->
                <div
                    class="login_section h-screen flex flex-wrap p-14 3xl:p-6 2xl:p-[25px] sm:p-[20px]  lg:h-screen lg:items-center lg:justify-center afclr">
                    <div
                        class="login_left_sec matchSection_height lg:hidden w-2/4 bg-siteYellow flex flex-col relative p-12 3xl:p-7 rounded-[19px]">
                        <div class="flex flex-col items-center justify-center h-full mt-auto text-center">
                            <div
                                class="mb-[145px] 2xl:max-w-[237px] lg:max-w-[237px] 3xl:mb-[100px] 3xl:max-w-[296px] max-w-[380px] mx-auto">
                                <a href="javascript:void(0);"><img src="{{asset('images/rental-logo.svg')}}" alt="main_logo"></a>
                            </div>
                        </div>
                        <div
                            class="absolute bottom-0 left-0 right-0 block w-full p-12 mx-auto login_box_sec 3xl:p-12 afclr">
                            <img class="mx-auto" src="{{asset('images/rental-img-l.svg')}}" alt="">
                        </div>
                    </div>

                    <div class="w-2/4 login_right_sec matchSection_height lg:w-full">
                        <div
                            class="login_main_sec flex items-center h-full 2xl:pl-[50px] 2xl:pr-[25px] 3xl:pl-[100px]
                            pl-[126px] pr-[78px] 3xl:pr-[60px] py-4 lg:px-[60px] md:px-[0px] afclr">
                            <div class="w-full max-w-[400px] lg:max-w-[423px] md:max-w-[382px] mx-auto">
                                <div class="hidden lg:block lg:mb-[27px] md:mb-[25px] max-w-[220px] mx-auto">
                                    <a href="javascript:void(0);"><img class="mx-auto" src="{{asset('images/rentalx_logoMob.svg')}}" alt="rentalx logo"></a>
                                </div>
                                <div class="mb-[31px] 3xl:mb-[20px] md:mb-[15px] sm:mb-[14px]">
                                    <h2
                                        class="text-black text-center mx-auto md:max-w-[640px] lg:max-w-[500px] max-w-[386px]  text-4xl font-normal leading-14 md:text-3xl">
                                        Login to your RentalX Account
                                    </h2>
                                </div>
                                <!-- <div class="pb-[37px] 2xl:pb-[25px] max-w-[330px] mx-auto sm:pb-[20px]">
                                    <p class="text-black text-center text-lg md:text-[17px] font-normal leading-6 sm:text-sm sm:text-black500">
                                        Please enter your mobile number to receive One Time Password</p>
                                </div> -->
                                <form method="POST" action="{{route('login.post')}}" onsubmit="return validate()">
                                    @csrf
                                    @if(session('errorMessage'))
                                        <div class="error  text-base font-normal block w-full mb-[8px]  sm:text-sm sm:leading-[16px] errorMsg">
                                            {{ session('errorMessage') }}
                                        </div>
                                    @endif
                                    <div class="form_out_l mt-[30px] 3xl:mt-[30px] sm:mt-[20px]">
                                        <div class="mb-[20px]  3xl:mb-[15px] 2xl:mb-[16px] inp_container  afclr">
                                            <label for="mobile"
                                                class="text-sm font-normal block w-full mb-[8px] text-black500  sm:leading-[16px]">
                                                Mobile<span class="text-[#ff0000]">*</span></label>
                                            <input type="text" id="mobile" name="mobile" value="{{ old('mobile') }}"
                                                class="sm:text-base text-sm font-normal leading-normal bg-[#fff] text-black border required_fields
                                                border-black500 px-[16px] py-[12px] rounded-[4px] w-full focus:outline-none focus:border-siteYellow  3xl:py-[12px]"
                                                placeholder="Enter Your Mobile No." data-error-msg="Mobile number is required">
                                                <span class="text-base font-normal leading-normal required sm:text-sm"></span>
                                                @error('mobile')
                                                <span class="text-base font-normal error sm:text-sm">{{$message}}</span>
                                                @enderror
                                        </div>
                                        <div class="mb-[20px] 3xl:mb-[15px] 2xl:mb-[16px]  inp_container relative afclr">
                                            <label for="password"
                                                class="font-normal block w-full mb-[8px] text-black500 text-sm sm:leading-[16px] ">
                                                Password<span class="text-[#ff0000]">*</span></label>

                                             <div class="relative">
                                            <input type="password" id="password" name="password"
                                                class="required_fields text-sm sm:text-base relative font-normal leading-normal bg-[#fff] text-black border
                                                border-black500 pl-[16px] pr-[46px] py-[12px] rounded-[4px] w-full focus:outline-none
                                                focus:border-siteYellow md:pl-[16px] md:pr-[46px] 3xl:py-[10px] md:py-[12px]"
                                                placeholder="Enter Your password" data-error-msg="Password is required">
                                                <span class="absolute cursor-pointer btnToggle right-5  top-1/2 translate-y-[-50%] transform-50 toggle">
                                                    <img src="{{asset('images/eyeIcon.svg')}}" alt="eye_icon" class="eyeIconW eye_show">
                                                    <img src="{{asset('images/eys_black_password.svg')}}" alt="eye_slash_icon" class=" eyeIconW eye_hide">
                                                </span>
                                            </div>
                                            <span class="text-base font-normal leading-normal required sm:text-sm" ></span>
                                                @error('password')
                                                <span class="text-base error sm:text-sm">{{$message}}</span>
                                                @enderror
                                        </div>

                                        <div class="mb-[20px] 3xl:mb-[15px] 2xl:mb-[10px]  afclr">
                                            <div class="g-recaptcha captcha_responsive"
                                            {{-- data-sitekey="6Lcll68qAAAAANRWmpq7paanAdhevAdKSCJ7iHLY" --}}
                                            data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}">

                                            </div>
                                            <span class="required sm:relative sm:-top-[14px]  sm:text-sm text-base font-normal leading-normal"></span>
                                            @error('g-recaptcha-response')
                                            <span class="text-base font-normal error sm:text-sm">{{$message}}</span>
                                            @enderror
                                        </div>


                                        <div class="mb-[20px]  md:mb-[15px] afclr">
                                            <input type="submit" value="Login"
                                                class="inline-block w-full px-6 py-[12px] text-opacity-40 text-lg font-medium leading-tight
                                                 transition-all duration-300 border border-siteYellow rounded-[4px]
                                                 cursor-pointer bg-siteYellow  md:px-[20px] md:py-[12px] md:text-lg sm:text-sm transition-all duration-500 ease-0
                                                 hover:bg-[#d7b50a]">
                                        </div>

                                        <!-- <div class="form_b_sec md:pb-[20px] afclr">
                                            <p class="text-lg font-normal text-center text-black sm:text-sm md:text-base">
                                            Don’t have an account
                                            Yet?
                                            <a href="javascript:void(0);" class="text-black hover:text-[#d7b50a] transition-all duration-500 ease-0 underline">Sign
                                           Up</a>
                                        </p>
                                        </div> -->

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="{{asset('js/jquery-3.7.0.min.js')}}"></script>
<script src="{{ asset('js/jquery.matchHeight-min.js') }}"></script>
<script defer async src="https://www.google.com/recaptcha/api.js"></script>

<script>


function hideLoader() {
    $('.loader').css("display", "none");
    $('.overlay_sections').css("display", "none");
}

function validate() {
    $('.loader').css("display", "inline-flex");
    $('.overlay_sections').css("display", "block");

    // console.log('validate');
    var required_fields = $('.required_fields');
    var hasErrors = false;
    required_fields.each(function() {
        var fieldValue = $(this).val().trim();
        var errorContainer = $(this).closest('.inp_container').find('.required');

        if (!fieldValue) {
            hasErrors = true;
            // console.log('errorContainer:',errorContainer)

            errorContainer.html($(this).data("error-msg"));
        } else {
            errorContainer.empty();
        }

        if ($(this).attr('id') === 'mobile') {

            if(fieldValue.length>=1){

                if (fieldValue.length !== 10 ) {
                // console.log('from fieldValue');
                hasErrors = true;
                errorContainer.html('Oh. Looks like that mobile number is not valid. Check again?');
            }

            }
        }

        if (grecaptcha.getResponse().length===0) {
            console.log('from res');
                hasErrors = true;
                $('.g-recaptcha').siblings('.required').html('Please complete the reCAPTCHA');
        }
        else{
            $('.g-recaptcha').siblings('.required').html('');
        }


    });

    // console.log('hasErrors:',hasErrors);
    if (hasErrors) {
        hideLoader();
    return false;
    } else {
        return true;
    }
   }

    $("#mobile").on('input', function() {
    var enteredNum = $(this).val();
    var requiredMessage = $(this).siblings('.required');
    enteredNum = enteredNum.replace(/\D/g, '');
    if (enteredNum.length > 10) {
        enteredNum = enteredNum.slice(0, 10);
    }
    $(this).val(enteredNum);
    if (/^\d{10}$/.test(enteredNum)) {
        requiredMessage.empty();
    }
   });


$("#password").on('input', function() {
    if($(this).val().trim()>0){
        $(this).siblings('.required').html('');
    }
});
$(".btnToggle").on('click',function(){
    console.log('hello there');
    var passwordInput= $(this).closest('.relative').find('#password');
    console.log('password',passwordInput);
    $(this).toggleClass('eyeShowing');

    togglePassword(passwordInput);
});

function togglePassword(passwordInput) {
    if (passwordInput.attr('type') === 'password') {
        passwordInput.attr('type', 'text');

    } else {

        passwordInput.attr('type', 'password');
    }
}

setTimeout(function() {
$('.errorMsg').slideUp();
}, 10000);


</script>
<script>
   if ("serviceWorker" in navigator) {
      // Register a service worker hosted at the root of the
      // site using the default scope.
      navigator.serviceWorker.register("{{ asset('/sw.js') }}").then(
      (registration) => {
        //  console.log("Service worker registration succeeded:", registration);
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
