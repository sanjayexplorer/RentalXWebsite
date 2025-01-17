<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#F3CD0E"/>
    <link rel="apple-touch-icon" href="{{ asset('images/logo.svg') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&family=Space+Grotesk&display=swap"rel="stylesheet">
  <link rel="stylesheet" href="{{asset('css/air-datepicker.min.css')}}">
  <link rel="stylesheet" href="{{asset('css/jquery-ui.css')}}">
  <link rel="stylesheet" type="text/css" href="{{asset('css/fancybox.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{asset('css/toastify.min.css')}}">
  <link rel="stylesheet" href="{{asset('css/custom.css')}}">
  <link rel="stylesheet" href="{{asset('css/main.css' . generateRandomVersion()) }}">
  <script src="{{ asset('js/sweetalert2@11.js') }}"></script>
  <link rel="stylesheet" href="{{asset('css/tailwind-output.css' . generateRandomVersion()) }}">
  <link rel="stylesheet" href="{{asset('css/style.css' . generateRandomVersion())}}">
  <link rel="stylesheet" href="{{asset('css/popup.css'. generateRandomVersion())}}">
  <link rel="shortcut icon" href="{{asset('images/favicon.png')}}" type="image/x-icon">
  <title>@yield('title')</title>
  <script src="{{asset('js/jquery-3.7.0.min.js')}}"></script>
  <script src="{{asset('js/jquery.matchHeight-min.js')}}"></script>
  <script type="text/javascript" src="{{asset('js/fancybox.min.js')}}"></script>
  <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
  <script src="{{asset('js/dataTables.rowReorder.min.js')}}"></script>
  <script src="{{ asset('js/jquery.cookie.min.js') }}"></script>
  <script src="{{ asset('js/toastify.min.js') }}"></script>
  <script src="{{asset('js/moment.min.js')}}"></script>
  <script type="text/javascript" src="{{asset('js/air-datepicker.min.js')}}"></script>
  <script src="{{ asset('js/jquery-ui.js') }}"></script>
  <style>

  body.sidebar_open .main_header_container{
  z-index: 0;
  }
    .main_header_container { position: sticky; position: -webkit-sticky; right: 0; background: #fff; z-index: 11; top: 0; left: 0; }
  .swal2-container{ z-index: 99999 !important; }
  .swal2-shown.swal2-height-auto .swal2-popup{ font-size: 16px !important; }
  .swal2-popup .swal2-title { font-size: 23px; font-weight: 500; color: #000; }
  .fancybox-container, .fancybox-stage { z-index: 999; }
  .ui-autocomplete { max-height: 250px; overflow-y: auto; }
  .list_title{ color: #000; font-size: 15px; font-weight: 600; padding: 5px 60px !important; font-family: 'Roboto', sans-serif; /* border-bottom: 1px solid #c5c5c5;  */ }
  .list_content{ display: block; padding: 3px 60px!important; font-family: 'Roboto', sans-serif; /* border-bottom: 1px solid #c5c5c5; */ }
  /* for scroll problem in autocomplete */
  li.ui-menu-item { width: 99.5%; }
  /* .mob_autocomplete_ul{ padding: 0 55px;} */
  /* end */
  .navigation_exist{z-index: 0;}
  .sidebar_open .overlay_sections{ display: block !important; }
  @media screen and (max-width: 479px){
    .list_title{ padding: 5px 50px !important; }
    .list_content{ padding: 3px 50px!important; }
  }
  </style>
</head>
@php
function generateRandomVersion() {
    return '?v=' . substr(md5(time()), 0, 8);
}
$routeArray = app('request')
->route()
->getAction();
$controllerAction = class_basename($routeArray['controller']);
[$controller, $action] = explode('@', $controllerAction);



@endphp
<body class="relative afclr">

  <div class="hidden overlay_sections afclr"></div>

  <div class="fixed top-0 left-0 z-[1000] flex items-center justify-center hidden w-full h-screen loader">
    <img src="{{asset('images/rounded_loader.svg')}}" class="spinner w-20 h-20" alt="loader gif">
 </div>

  <div class="flex flex-wrap layout_outer">

    <div class="sidebar xsm:w-[83%] lg:w-[90%] md:w-[83%] lg:fixed sticky z-[11]  top-0 h-screen
     xsm:left-[-400px] md:left-[-675px] lg:left-[-950px] sm:left-[-400px]
      overflow-hidden lg:overflow-y-scroll  bg-[#FFFFFF]
      xl:w-[20%]  w-[16%] 3xl:w-[18%] 2xl:w-[20%]">


      <div class="website_logo_sec items-center lg:hidden border-b flex justify-center py-[20px] px-[25px] xsm:hidden:border-r border-[#DAD8D8] afclr !h-[65px] !min-h-[65px]">
        <div class="logo">
          <a href="{{route('admin.users.list')}}" class="links_item_cta"><img class="w-full h-[45px]" src="{{asset('images/logo.svg')}}" alt=""></a>
        </div>
      </div>

      <div class="relative border-r border-[#DAD8D8] h-full">
        <a href="javascript:void(0);" class="close_menu p-[5px] absolute top-[15px] right-[13px] lg:block hidden">
          <img class="w-[24px] sm:w-[21px]" src="{{asset('images/cross.svg')}}">
        </a>
        <div class="items-center hidden px-5 pt-8 user_info_deatils lg:flex afclr">
          <div class="logo">
            <a href="{{route('admin.users.list')}}" class="links_item_cta"><img class="w-full" src="{{asset('images/logo.svg')}}" alt=""></a>
          </div>
        </div>
        <div class="relative sidebar_l">
          <div
            class="info_m_bar pt-[30px] px-[25px]  lg:pl-[0px] lg:pr-[13px] 2xl:pr-[15px] 3xl:px-[15px] md:pb-[50px] pb-[100px] afclr">
            <ul class="layout_sidebar_listing list-none   2xl:pt-[0px] pt-[15px] afclr">
              <li class="relative mb-[40px] 3xl:mb-[35px] align-middle link_listing">
                <a href="{{route('admin.users.list')}}"
                  class="links_item_cta @if (strcmp($controller, 'UserController') == 0) bg-[#F3CD0E] @else bg-[#fff] @endif hover:bg-[#F3CD0E] transition-all duration-300 ease-out text-black text-lg 2xl:text-base font-normal flex items-center py-[10px] px-[11px] lg:px-[20px] rounded-lg lg:rounded-l-none min-h-[45px] list_item_link">
                  <span class="svg_mob 2xl:w-[41px]  w-[45px] flex items-center relative top-[-2px] pt-[3px] pr-[10px]">
                    <svg width="26" height="20" viewBox="0 0 26 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <circle cx="18.5337" cy="7.68359" r="3.88085" stroke="black"/>
                      <circle cx="7.95703" cy="4.50488" r="3.88085" stroke="black"/>
                      <path d="M25.0128 18.5146H12.0548C12.2965 14.9133 15.1286 12.1128 18.5338 12.1128C21.939 12.1128 24.7712 14.9133 25.0128 18.5146Z" stroke="black"/>
                      <path d="M12.4214 16.7101C6.95737 16.7101 0.860352 16.7168 0.860352 16.7168C0.860352 12.6289 3.99245 9.31494 7.85608 9.31494C10.2564 9.31494 12.3744 10.594 13.6344 12.5432C13.8009 12.8006 14.0712 13.3198 14.2103 13.6088" stroke="black"/>
                      </svg>

                  </span>
                  Users </a>
              </li>
              <li class="relative mb-[40px] 3xl:mb-[35px] align-middle link_listing">
                <a href="{{route('admin.users.settings.profile.view')}}"class="links_item_cta hover:bg-[#F3CD0E] transition-all duration-300 ease-out text-black text-lg 2xl:text-base font-normal flex items-center py-[10px] px-[11px] lg:px-[20px] rounded-lg lg:rounded-l-none min-h-[45px] list_item_link">
                  <span class="svg_mob 2xl:w-[41px]  w-[45px] flex items-center relative top-[-2px] pt-[3px] pr-[10px]">

                    <img src="{{asset('images/setting_icon.svg')}}" alt="setting icon" width="35" height="21">
                  </span>
                  My Settings </a>
              </li>

            </ul>
          </div>
          <div class="cover-bar lg:hidden"></div>
        </div>
      </div>
    </div>

    <div class="layout_main_sec xl:w-[80%] lg:w-[100%]  3xl:w-[82%] 2xl:w-[80%] w-[84%] afclr">


      <div class="right_dashboard_section">

        <div
            class="main_header_container   flex items-center justify-between py-[8px] px-[25px] border-b border-[#DAD8D8] lg:border-none !h-[65px] !min-h-[65px] lg:px-4">
            <div class="header_t_left hidden items-center lg:flex lg:w-[30%] sm:w-[50%]">
              <div class="logo">
                <a href="{{route('partner.booking.list')}}" class="links_item_cta">
                  <img class="w-full h-[45px]" src="{{asset('images/logo.svg')}}" alt=""></a>
              </div>
            </div>
            <div class="header_t_right flex items-center justify-end lg:w-[70%] sm:w-[50%] w-full">
              <div class="icons mr-[10px]">
                <div class="list_items_popup_container relative">
                  <a href="javascript:void(0);"
                    class="inline-flex rounded-[4px] items-center  text-black text-base md:text-sm font-normal bg-siteYellow px-[10px] py-2  triple_dots_action_btn header_act_button  add_new_sec  ">
                    <span>
                      <img class="w-[17px] relative" src="{{asset('images/fa-add-icon.svg')}}" alt="">
                    </span>
                  </a>

                  <div
                    class="dots_hover_sec list_link_frame_section header_act_container top-[40px] absolute md:w-[250px] w-[250px] border border-[#D1D1D1] rounded-[5px] py-[20px] sm:py-[10px] bg-[#fff] z-[2] right-0 custom-shadow afclr hidden">
                    <ul class="frame_list_items_popup">
                      <li class="relative align-middle link_listing ">
                        <a href="{{route('admin.users.add')}}"
                          class="links_item_cta  bg-[#fff] hover:bg-[#F3CD0E] transition-all duration-300 ease-out text-black text-lg 2xl:text-base sm:text-sm font-normal flex items-center py-[8px] px-[11px] lg:px-[20px] min-h-[45px] list_item_link">
                          <span class="svg_mob 2xl:w-[41px]  w-[45px] flex items-center relative top-[-2px] pt-[3px] pr-[10px]">

                            <svg width="26" height="25" viewBox="0 0 26 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                              <path
                                d="M15.6356 16.2087C15.5633 16.1384 15.4665 16.0991 15.3656 16.0991C15.2648 16.0991 15.168 16.1384 15.0957 16.2087L12.6082 18.6962C11.0577 20.2465 8.49931 20.2107 6.9051 18.6163C5.37492 17.0859 5.27829 14.4604 6.82531 12.9132L9.31271 10.4258C9.38312 10.3536 9.42253 10.2568 9.42253 10.1559C9.42253 10.0551 9.38312 9.9582 9.31271 9.88599L6.50263 7.07582C6.42967 7.00686 6.33309 6.96844 6.2327 6.96844C6.13231 6.96844 6.03573 7.00686 5.96277 7.07582L3.47528 9.56331C0.116861 12.9217 0.184437 18.4539 3.62609 21.8955C4.45255 22.7264 5.43486 23.3861 6.51674 23.8367C7.59862 24.2873 8.75879 24.52 9.93076 24.5215C11.049 24.5266 12.1573 24.3106 13.1917 23.8857C14.2262 23.4609 15.1664 22.8357 15.9583 22.0461L18.4457 19.5588C18.5164 19.4867 18.556 19.3898 18.556 19.2888C18.556 19.1879 18.5164 19.091 18.4457 19.0189L15.6356 16.2087ZM15.4184 21.5062C12.3575 24.5668 7.30989 24.4993 4.16594 21.3556C1.02208 18.2118 0.954416 13.1639 4.01513 10.1032L6.2327 7.8856L8.50284 10.1559L6.28546 12.3733C4.43736 14.2214 4.47315 17.264 6.36523 19.1561C8.25695 21.0482 11.2999 21.0842 13.1481 19.2361L15.3656 17.0185L17.6358 19.2889L15.4184 21.5062Z"
                                fill="black" stroke="black" stroke-width="0.2"></path>
                              <path
                                d="M7.86671 6.66409C7.9675 6.6651 8.06457 6.62607 8.13661 6.55558L9.3139 5.37839L9.48121 5.94117C9.50007 6.00454 9.53508 6.06193 9.58279 6.10771C9.6305 6.1535 9.68928 6.1861 9.75338 6.20234C9.81748 6.21857 9.8847 6.21788 9.94845 6.20034C10.0122 6.18279 10.0703 6.14898 10.1171 6.10223L11.9543 4.26494C11.9898 4.22949 12.0179 4.18741 12.0371 4.1411C12.0563 4.09478 12.0662 4.04514 12.0662 3.99501C12.0662 3.94488 12.0563 3.89525 12.0371 3.84893C12.0179 3.80262 11.9898 3.76054 11.9543 3.72509C11.9189 3.68964 11.8768 3.66152 11.8305 3.64234C11.7842 3.62316 11.7345 3.61328 11.6844 3.61328C11.6343 3.61328 11.5846 3.62316 11.5383 3.64234C11.492 3.66152 11.4499 3.68964 11.4145 3.72509L10.0304 5.1092L9.86307 4.54624C9.8442 4.48286 9.8092 4.42547 9.76149 4.37969C9.71378 4.33391 9.65499 4.30131 9.5909 4.28507C9.5268 4.26883 9.45958 4.26952 9.39583 4.28707C9.33208 4.30462 9.27398 4.33843 9.22721 4.38518L7.59675 6.01573C7.54502 6.06948 7.51012 6.13717 7.49634 6.21049C7.48256 6.28381 7.49049 6.35955 7.51916 6.42842C7.54784 6.49729 7.59601 6.55628 7.65775 6.59815C7.71949 6.64002 7.79212 6.66294 7.86671 6.66409Z"
                                fill="black" stroke="black" stroke-width="0.2"></path>
                              <path
                                d="M21.4022 13.6022L19.565 15.4395C19.5182 15.4862 19.4844 15.5443 19.4668 15.6081C19.4493 15.6718 19.4486 15.7391 19.4649 15.8032C19.4811 15.8673 19.5137 15.9261 19.5595 15.9738C19.6053 16.0215 19.6627 16.0565 19.7261 16.0753L20.2888 16.2425L19.1116 17.4199C19.0761 17.4553 19.048 17.4974 19.0288 17.5437C19.0096 17.5901 18.9998 17.6397 18.9998 17.6898C18.9998 17.74 19.0096 17.7896 19.0288 17.8359C19.048 17.8822 19.0761 17.9243 19.1116 17.9598C19.1839 18.03 19.2807 18.0693 19.3815 18.0693C19.4823 18.0693 19.5792 18.03 19.6515 17.9598L21.2819 16.3292C21.3287 16.2824 21.3625 16.2243 21.38 16.1606C21.3976 16.0968 21.3983 16.0296 21.382 15.9655C21.3658 15.9014 21.3331 15.8426 21.2874 15.7949C21.2416 15.7472 21.1842 15.7122 21.1208 15.6933L20.5581 15.5261L21.9421 14.142C21.9775 14.1066 22.0057 14.0645 22.0249 14.0182C22.044 13.9719 22.0539 13.9222 22.0539 13.8721C22.0539 13.822 22.044 13.7723 22.0249 13.726C22.0057 13.6797 21.9775 13.6376 21.9421 13.6022C21.9067 13.5667 21.8646 13.5386 21.8183 13.5194C21.7719 13.5002 21.7223 13.4904 21.6722 13.4904C21.622 13.4904 21.5724 13.5002 21.5261 13.5194C21.4798 13.5386 21.4377 13.5667 21.4022 13.6022Z"
                                fill="black" stroke="black" stroke-width="0.2"></path>
                              <path
                                d="M13.5681 7.628C13.3668 6.63824 13.4399 5.61228 13.7795 4.66106C14.1191 3.70985 14.7124 2.86959 15.495 2.23116C15.5688 1.72903 15.7503 1.24878 16.0269 0.823303C14.5212 1.60664 13.3869 2.95441 12.8723 4.57182C12.3576 6.18923 12.5045 7.94464 13.2807 9.45407L13.5681 7.628Z"
                                fill="black" stroke="black" stroke-width="0.2"></path>
                              <path
                                d="M21.9724 0.823303C22.2491 1.24878 22.4305 1.72903 22.5043 2.23116C23.287 2.86959 23.8802 3.70985 24.2198 4.66106C24.5595 5.61227 24.6326 6.63824 24.4313 7.628L24.7187 9.45407C25.4949 7.94463 25.6417 6.18923 25.1271 4.57182C24.6124 2.9544 23.4782 1.60663 21.9724 0.823303Z"
                                fill="black" stroke="black" stroke-width="0.2"></path>
                              <path
                                d="M18.9995 12.0645C17.9342 12.0654 16.8915 11.7573 15.9977 11.1777H14.5723C15.7651 12.3162 17.3506 12.9514 18.9995 12.9514C20.6484 12.9514 22.2339 12.3162 23.4267 11.1777H22.0012C21.1074 11.7573 20.0648 12.0654 18.9995 12.0645Z"
                                fill="black" stroke="black" stroke-width="0.2"></path>
                              <path
                                d="M23.4545 7.12589C23.415 6.90846 23.3062 6.70966 23.1443 6.55924C22.9825 6.40882 22.7762 6.31487 22.5565 6.29144L20.6097 5.98528C20.578 5.98068 20.5477 5.96948 20.5206 5.95241C20.2452 6.08457 19.9536 6.18021 19.6534 6.23692L19.6564 6.24401C19.7287 6.40766 19.8415 6.55019 19.9842 6.65819C20.1268 6.76618 20.2946 6.83608 20.4718 6.8613L22.4284 7.16896C22.5257 7.18195 22.5742 7.23414 22.5774 7.25622L22.9153 9.40384H15.0841L15.4231 7.24886C15.4251 7.23413 15.4736 7.18195 15.5806 7.16745L17.5278 6.8613C17.705 6.83603 17.8727 6.76609 18.0154 6.65805C18.158 6.55002 18.2708 6.40746 18.3431 6.24379L18.346 6.23691C18.0458 6.1802 17.7542 6.08455 17.4788 5.95239C17.4518 5.96945 17.4215 5.98066 17.3899 5.98528L15.4525 6.28991C15.2322 6.31167 15.0249 6.40413 14.8615 6.55348C14.6981 6.70283 14.5874 6.90105 14.546 7.11852L14.1275 9.7784C14.1175 9.84168 14.1214 9.90636 14.1388 9.96799C14.1563 10.0296 14.1868 10.0868 14.2285 10.1354C14.2701 10.1841 14.3218 10.2232 14.3799 10.25C14.4381 10.2768 14.5014 10.2907 14.5655 10.2907H23.4339C23.498 10.2907 23.5613 10.2768 23.6195 10.25C23.6776 10.2232 23.7293 10.1841 23.7709 10.1354C23.8126 10.0868 23.8432 10.0296 23.8606 9.96799C23.878 9.90636 23.8819 9.84168 23.8719 9.7784L23.4545 7.12589Z"
                                fill="black" stroke="black" stroke-width="0.2"></path>
                              <path
                                d="M18.9996 5.41312C19.5259 5.41312 20.0402 5.25708 20.4778 4.96473C20.9153 4.67239 21.2563 4.25687 21.4577 3.77072C21.659 3.28457 21.7117 2.74963 21.6091 2.23353C21.5064 1.71744 21.253 1.24338 20.8809 0.871295C20.5089 0.499213 20.0348 0.245821 19.5187 0.143164C19.0026 0.0405063 18.4677 0.0931938 17.9815 0.294564C17.4954 0.495933 17.0798 0.836941 16.7875 1.27446C16.4951 1.71199 16.3391 2.22637 16.3391 2.75258C16.3399 3.45796 16.6205 4.13421 17.1192 4.63299C17.618 5.13177 18.2943 5.41233 18.9996 5.41312ZM18.9996 0.978882C19.3505 0.978882 19.6934 1.08291 19.9851 1.2778C20.2767 1.4727 20.5041 1.74971 20.6383 2.07381C20.7726 2.39791 20.8077 2.75454 20.7393 3.0986C20.6708 3.44267 20.5019 3.75871 20.2538 4.00676C20.0058 4.25482 19.6897 4.42375 19.3457 4.49219C19.0016 4.56062 18.645 4.5255 18.3209 4.39125C17.9968 4.25701 17.7198 4.02967 17.5249 3.73799C17.33 3.44631 17.226 3.10338 17.226 2.75258C17.2265 2.28233 17.4135 1.8315 17.7461 1.49898C18.0786 1.16647 18.5294 0.97942 18.9996 0.978882Z"
                                fill="black" stroke="black" stroke-width="0.2"></path>
                            </svg>
                          </span>
                          New Users
                        </a>
                      </li>
                    </ul>
                  </div>

                  <div>
                  </div>
                </div>
              </div>
              <div class="icons pl-[10px]">
                <div class="list_items_popup_container flex justify-center items-center relative">
                  <a href="javascript:void(0);"
                    class=" inline-flex rounded-[4px] items-center
                                                  text-black text-base md:text-sm font-normal py-2 header_act_button user_info_sec_btn">
                    <span class="svg_mob 2xl:w-[35px] w-[41px] relative top-[-2px] pt-[3px] flex items-center justify-start">
                      <svg width="30" height="30" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="13.0359" cy="12.6296" r="11.9939" stroke="black"></circle>
                        <circle cx="12.8785" cy="8.52693" r="3.85505" stroke="black"></circle>
                        <path
                          d="M19.4685 19.229H6.60377C6.84553 15.6582 9.6564 12.8818 13.0361 12.8818C16.4159 12.8818 19.2267 15.6582 19.4685 19.229Z"
                          stroke="black"></path>
                      </svg>
                    </span>
                  </a>
                  <div
                    class="  dots_hover_sec list_link_frame_section header_act_container top-[46px] absolute md:w-[280px] w-[280px] border border-[#D1D1D1] rounded-[5px]  bg-[#fff] z-[2] right-2 custom-shadow afclr hidden">
                    <div class="user_info_details flex py-[30px] px-[20px] items-center ">
                      <div class="user_info_details_left_sec ">
                        <div class="user_profile_inner_sec w-[50px] h-[50px] mr-[10px]">

                          <a href="javascript:void(0); "
                            class="  w-full h-full inline-flex items-center ">
                            <img
                              src="{{asset('images/small_avtar_img.jpg') }}"
                              alt="user icon">
                          </a>



                        </div>

                      </div>
                      <div class="user_info_details_right_sec">
                        <h5 class="text-[14px] text-[#000] font-medium leading-[1.4] capitalize">
                          Super Admin
                        </h5>
                        <p class="text-xs text-[#000000] font-medium">
                          {{Helper::getUserMeta(auth()->user()->id,
                          'user_mobile_country_code')}}&nbsp;{{Auth::user()->mobile;}}
                          </p>

                      </div>

                    </div>
                    <ul class="frame_list_items_popup">

                      <li class="relative align-middle link_listing border-b border-[#DAD8D8]">
                        <a href="{{route('admin.users.settings.profile.view')}}"
                          class="links_item_cta   bg-[#fff] hover:bg-[#F3CD0E] transition-all duration-300 ease-out text-black text-lg 2xl:text-base sm:text-sm font-normal flex items-center py-[8px] px-[11px] lg:px-[20px] min-h-[45px] list_item_link">
                          <span class="svg_mob 2xl:w-[35px] w-[41px] flex items-center relative top-[-2px] pt-[3px] pr-[10px]">
                            <img src="{{asset('images/setting_icon.svg')}}" alt="setting icon" width="35" height="21">
                          </span>
                          My Settings
                        </a>
                      </li>
                    </ul>

                    <div class="w-full  p-[11px] lg:p-[20px]">

                      <a href="javascript:void(0);" id="logout_link"
                        class="links_item_cta  bg-[#fff] hover:text-[#F3CD0E] ease-out text-black text-lg 2xl:text-base sm:text-sm font-normal flex items-center list_item_link">
                        Logout
                        <form action="{{ route('admin.logout') }}" class="logout_form" method="post">
                          @csrf
                        </form>
                      </a>

                    </div>

                  </div>

                </div>

              </div>
            </div>
        </div>
         <!-- SEARCH BAR -->
        <div class="hidden lg:bg-[#F6F6F6] lg:block bg-[#ffffff] lg:py-3 lg:px-4 py-0 px-0  z-[15] sticky
               lg:static top-0 border-[#E4E4E4] border-r-0 lg:border-l-0 header_bar">
          <div class="flex justify-between border-[#E1D6BF] rounded-[11px] lg:right-0 bg-[#ffffff] lg:top-0 lg:left-0 lg:flex">
            <div class="relative hidden w-full search_sec lg:block">
              <!-- menu icon -->
              <div
                class="menu_expend flex items-center justify-start mr-2 layout_header_left absolute top-0 bottom-0 left-[5px]">
                <div class="layout_menu_sec py-[17px] px-[10px] afclr">
                  <div class="hidden mob_content_box_inner_item lg:block afclr">
                    <a href="javascript:void(0);" class="no-underline ">
                      <img src="{{asset('images/toggle_menu_icon.svg')}}">
                    </a>
                  </div>
                </div>
              </div>

              <!-- input field -->
              <form class="flex " id="search-form" action="#" method="GET">
                <input type="text" name="query" id="search"
                  class="search w-full capitalize text-sm outline-none  rounded-[10px] lg:border  lg:border-[#E4E4E4] pt-[15px] pr-[60px] pb-[15px] pl-[60px] sm:pl-[50px] sm:pr-[50px]"
                  placeholder="Search Users" autocomplete="off">
                <div class="absolute close_desktop flex align-center right-[35px] sm:right-[20px] top-0 bottom-0 hidden"
                  onclick="document.getElementById('search').value = ''; clearSearch();">
                  <img class="" src="{{asset('images/close-focus-icon.svg')}}" class="w-[12px]">
                </div>
              </form>

            </div>
          </div>
        </div>

        <div class="right_dashboard_section_inner lg:pt-0 sm:py-0 lg:py-5 xl:py-0  bg-[#F6F6F6]  min-h-screen">
            @yield('content')
        </div>
      </div>

    </div>
  </div>

<script>

function hideLoader() {
$('.loader').css("display", "none");
$('.overlay_sections').css("display", "none");
}
$(document).ready(function() {
    // Function to hide loader and overlay
        function hideLoader() {
            $('.loader').css("display", "none");
            $('.overlay_sections').css("display", "none");
        }

       $('body').on('click','.links_item_cta',function(e){
            e.preventDefault();
            console.log('under newBookingCta',$(this).attr("href"));
            $('.loader').css("display", "inline-flex");
            $('.overlay_sections').css("display", "block");
            $('body').removeClass('sidebar_open');

            // let anchorLink = $(this).find("a").attr("href");
            let anchorLink = $(this).attr("href");
            window.location.href = anchorLink;

        });


        $(window).on('pageshow', function(event) {
            // If the persisted property is false, it means the page is not cached
            if (event.originalEvent.persisted) {
                // Hide loader and overlay
                hideLoader();
            }
        });

});
 var clickedCount=0
document.getElementById('logout_link').addEventListener('click', function(event) {
        event.preventDefault();
        clickedCount++;
        if(clickedCount>1){
          this.classList.add('disabled');
        this.setAttribute('disabled', true);
        }
        else{
          document.getElementsByClassName('logout_form')[0].submit();
        }

});
// Autocomplete Input cross and search
function clearSearch() {
        document.getElementById('search').value = '';
        $('.close_desktop').hide();

    }

$(document).ready(function() {

    $("#search").keyup(function() {
        var inputVal = $(this).val().trim();
        if (inputVal !== '') {
            $('.close_desktop').show();
        } else {
          $('.close_desktop').css('display', 'flex');
        }
    });

    $("#search").blur(function() {
        var inputVal = $(this).val().trim();
        if (inputVal === '') {
            $('.close_desktop').hide();
        }
    });

    $("#search").focus(function() {
      $('.close_desktop').css('display', 'flex');
    });

});


    var path = "{{ route('admin.autocomplete') }}";
    $("#search").autocomplete({
    source: function(request, response) {
        $.ajax({
            url: path,
            type: 'GET',
            dataType: "json",
            data: {
                search: request.term
            },
            success: function(data) {
              $('#search').css('border-bottom-left-radius', '0px');
              $('#search').css('border-bottom-right-radius', '0px');
                if (data.length === 0) {
                    response(["No Data Found!!"]);
                } else {
                    var groupedResults = {};
                    data.forEach(function(item) {
                        if (!groupedResults[item.header]) {
                            groupedResults[item.header] = [];
                        }
                        groupedResults[item.header].push(item);
                    });
                    console.log('groupedResults:',groupedResults);
                    var formattedData = [];
                    for (var header in groupedResults) {
                        formattedData.push({
                            label: header,
                            header: true
                        });
                        formattedData = formattedData.concat(groupedResults[header].slice(0, 7));
                    }
                    console.log('formattedData :',formattedData);
                    response(formattedData);
                }
            }
        });
    },
    select: function(event, ui) {
        if (ui.item.label === "No Data Found!!") {
            event.preventDefault();
        }
        else {
            console.log('label:',ui.item.header);
            console.log('',ui.item.label);
            if ((ui.item.label != 'PARTNERS') ) {
                $('#search').val(ui.item.label);
                window.location.href = "{{ route('admin.search') }}?id=" + ui.item.id + "&name=" + encodeURIComponent(ui.item.label) + "&header=" + encodeURIComponent(ui.item.header);
            } else {
                console.log("header clicked");
            }
        }
        return false;
    },
    open: function (event, ui) {
        $(".ui-autocomplete").addClass("mob_autocomplete_ul");
    },
    close: function (event, ui) {
        $('#search').css('border-bottom-left-radius', '10px');
        $('#search').css('border-bottom-right-radius', '10px');
    },
}).bind('focus', function(){ $(this).autocomplete("search"); })
.data("ui-autocomplete")._renderItem = function(ul, item) {
    console.log('item.label', item.label);
    if ((item.label == 'PARTNERS') || (item.label == 'CARS')) {
        console.log('appending',item.label);
        return ul;
    } else {
        return $("<li>").append('<span class="list_content capitalize text-sm font-medium leading-none text-[#272522]">' + item.label + (item.registration_number ? ' (' + item.registration_number + ')' : '') + '</span>').appendTo(ul);
    }
};

  $('.close_search_icon').on('click', function (e) {
      $('#search_inp_box').val('');
      $('.close_search_icon').hide();
  });

    $('.menu_expend').on('click', function () {
      $('body').addClass('sidebar_open');
      $('body').removeClass('sidebar_close');
      $('.navigation').addClass('navigation_exist');
    });

    $('.close_menu').on('click', function () {
      $('body').removeClass('sidebar_open');
      $('body').addClass('sidebar_close');
      $('.navigation').show();
      $('.navigation').removeClass('navigation_exist');
    });

   $('body').on('click', '.overlay_sections', function () {
      $('body').removeClass('sidebar_open');
      $('body').addClass('sidebar_close');
      $('.navigation').removeClass('navigation_exist');
   });

  $(".user_info_sec").click(function (e) {
      e.stopPropagation();
      $(".dropdown_info").slideToggle();
      $('.drop_down').slideUp('1000');
      $('.drop_down_lang').slideUp('1000');
  });

  $("body").click(function (e) {
      $(".dropdown_info").slideUp();
  });
  $(".dropdown_info").click(function (e) {
      e.stopPropagation();
  });



    $('.close_desktop').on('click', function() {
        $('.s_search_result_block').slideUp();
        $('.s_search_desktop').removeClass('f_filter_active');
    });

    $('.header_act_container').on('click',function(e){
      // stop propagation only if click isnt on anchor
      if (!$(e.target).is('a')) {
      e.stopPropagation();
      }
    });



     $("body").click(function (e) {
      e.stopPropagation();
        // console.warn('under the header_act_container');
        $('.add_new_sec').closest('.list_items_popup_container').find('.header_act_container').slideUp('1000');
        $('.user_info_sec_btn').closest('.list_items_popup_container').find('.header_act_container').slideUp('1000');


    });


    $(".add_new_sec").click(function(e) {
    e.stopPropagation();
     $(this).closest('.list_items_popup_container').find(".header_act_container").slideToggle();
    $('.user_info_sec_btn').closest('.list_items_popup_container').find('.header_act_container').slideUp('1000');

    });

    $(".user_info_sec_btn").click(function(e) {
      e.stopPropagation();
      $(this).closest('.list_items_popup_container').find(".header_act_container").slideToggle();
      $('.add_new_sec').closest('.list_items_popup_container').find('.header_act_container').slideUp('1000');

    });

  </script>
<script>
   if ("serviceWorker" in navigator) {
      navigator.serviceWorker.register('{{ asset('/sw.js') }}').then(
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
