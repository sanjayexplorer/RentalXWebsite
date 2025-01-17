<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="theme-color" content="#F3CD0E"/>
    <link rel="apple-touch-icon" href="{{ asset('images/logo.svg') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <title>Otp</title>
    <link rel="stylesheet" href="{{asset('css/build.css')}}">
    <style>
        .error{
            color:#ff0000;
        }
        #required{
            color:#ff0000;
        }
    </style>
</head>
<body>
    <div class="get_strt_login_section flex items-center h-screen">
        <div class="w-full tab:w-[600px] mx-auto px-5 pb-10 pt-14 md:py-28 lg:py-20">
            <div class="mb-16 flex items-center flex-col">
                <div class="website_logo"><img src="{{asset('images/rentalx_logo.svg')}}" alt=""></div>
                <div class="web_moto_text pt-[21px]">
                    <h5 class="text-[18px] font-normal text-[#342B18] text-center">Almost There!</h5>
                    <p class="text-[14px] font-normal text-[#898376] text-center pt-[12px]">Please enter the one-time password sent on to access your Fastrental Acccount.</p>
                </div>
            </div>
            <div class="pt-5">
                <form method="POST" action="{{route('otp.post')}}" autocomplete="on" onsubmit="return validate();">
                    @csrf
                    <div class="form_section  my-0">
                    <div class="relative mb-8">
                     <input type="text" name="otp" id="otp"
                        value="{{ old('otp') }}"

                         class="block w-full p-4 m-0 text-base  2xl:text-lg  font-normal
                            leading-none text-black  transition ease-in-out border
                             border-gray-300 rounded form-control
                               bg-clip-padding bg-[#F5F5F5] focus:text-gray-700
                               focus:border-siteYellow focus:bg-white focus:outline-none" placeholder="Enter OTP"/>
                               <span id="required"></span>
                               @if (session('errorMessage'))
                               <span class="error">{{ session('errorMessage') }}</span>
                               @endif
                    <label for="otp"
                class="absolute top-[-0.87rem] left-0 text-sm sm:text-base
                text-black transition-all duration-100 ease-in-out origin-left transform -translate-y-1/2 opacity-75">Enter your Otp</label>
                </div>
                <div class="mt-5 text-center">
                <input type="submit" value="VERIFY"
                 class="inline-block w-full px-6 py-4 text-xl font-medium leading-tight
                  text-[#FFFFFF] transition-all duration-300 border border-transparent
                  rounded cursor-pointer bg-siteYellow focus:shadow-lg focus:outline-none focus:ring-0 active:shadow-lg">
                </div>
                </div>
                </form>
            </div>
        </div>
    </div>
<script src="{{asset('js/jquery-3.7.0.min.js')}}"></script>
<script>
    function validate() {
        var enteredNum = $('#otp').val();
        var requiredMessage = $('#required');
        var errorMessage = $('.error');

        if (enteredNum.length === 0) {
            requiredMessage.html('Please enter OTP');
            return false;
        } else if (enteredNum.length !== 6 || !/^\d+$/.test(enteredNum)) {
            requiredMessage.html('Please enter a valid 6-digit number');
            return false;
        } else {
            requiredMessage.empty();
            errorMessage.empty();
            return true;
        }
    }

    $("#otp").on('input', function() {
    var enteredNum = $(this).val();
    var requiredMessage = $('#required');
    enteredNum = enteredNum.replace(/\D/g, '');
    if (enteredNum.length > 6) {
        enteredNum = enteredNum.slice(0, 6);
    }
    $(this).val(enteredNum);
    if (/^\d{6}$/.test(enteredNum)) {
        requiredMessage.empty();
    }
});
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
