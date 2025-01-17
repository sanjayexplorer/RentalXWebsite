@extends('layouts.agent')
@section('title', 'Leads List')
@section('content')
<style>
.fancybox-bg { background: #B1B1B1; }

.error{ color:#ff0000; }
.required{ color:#ff0000; }
 #delete_all_partner{background-color: #E12919; margin-right: 10px;}
.dataTables_paginate.paging_simple_numbers{ margin-top: 30px; display:flex; justify-content: center; align-items: center; padding: 15px 0; width:100% }
/* hidden pagination arrow icon  */
.dataTables_paginate.paging_simple_numbers .pagination_link{ display: inline-flex; justify-content: center; align-items: center; display: none; }
#leads_list_paginate a{ color: #BEBEBE; /* margin: 0 15px; */}
.pagination_link svg{ margin: 0 5px;}
#leads_list_paginate .paginate_button.previous~span>a,#leads_list_paginate .page_name{font-size: 18px;}
#leads_list_paginate .paginate_button.current{ background:#F3CD0E; color: #000;}
#leads_list_paginate .page_name{ -webkit-transition: background-color 0.3s ease;
-moz-transition: background-color 0.3s ease;
-o-transition: background-color 0.3s ease;
transition: background-color 0.3s ease; margin: 0 10px; cursor: pointer; color: #000; }
#leads_list_paginate .page_name:hover{ color: #F3CD0E; }
#leads_list_paginate .paginate_button.previous~span>a{ -webkit-transition: background-color 0.3s ease;
-moz-transition: background-color 0.3s ease;
-o-transition: background-color 0.3s ease;
transition: background-color 0.3s ease; margin: 0 5px; display: inline-flex;
cursor: pointer; width: 43px; height: 43px; justify-content: center;
align-items: center; transition: all 0.3s ;
border-radius: 5px }

#leads_list_paginate .paginate_button.previous~span>a:hover{ color: #000; background:#F3CD0E; }

.checkmark_list::after{ left:6px; }

/* for remove the by default styling of datatable   */
.ta_scrollbar_out .dataTables_wrapper .dataTables_paginate .paginate_button:hover{ border: none; color:#000; background:none; }

@media (min-width: 767px)
{
    .extraBottomPadding
    {
        padding-bottom:140px;
    }
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover .ta_scrollbar_out .dataTables_wrapper .dataTables_paginate .paginate_button.current, .ta_scrollbar_out .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { border: none; }

.row_class:hover .test_child{
    opacity:1;
}

@media (min-width: 479px){
    #leads_list_paginate .paginate_button.previous~span>a,#leads_list_paginate .page_name{font-size: 16px;}
    #leads_list_paginate  .dataTables_paginate a.paginate_button{font-size: 16px;}
}

@media screen and (max-width:479px) {
    .fancybox-content { width: 90%; }
    #leads_list_paginate .paginate_button.previous~span>a{ width: 35px; height: 35px; }
}

@media (max-width: 767px){
    #leads_list_paginate .paginate_button.previous~span>a{ width: 40px; height: 40px; }
    .fancybox-content { width: 60%; }
}

@media screen and (max-width:992px) {
    .fancybox-content { width: 45%; }
}

</style>

<div>
    <div class="right_dashboard_section_inner py-9 px-9 lg:py-[20px] lg:px-[17px] bg-[#F6F6F6] min-h-screen">

            <div class="flex items-center mb-[36px] lg:mb-[20px]">
                <div class="w-1/2 flex justify-start items-center">
                    <a href="javascript:void(0);" class="hidden py-2 mr-2">
                        <img class="w-[42px]" src="{{asset('images/panel-back-arrow.svg')}}">
                    </a>
                    <span class="inline-block text-black800 text-[26px] md:text-xl font-normal leading-normal align-middle">Leads</span>
                </div>
                <div class="w-1/2 text-right flex items-center justify-end ">
                    <a href="{{route('agent.leads.add')}}" class="links_item_cta inline-flex rounded-[4px] items-center text-black text-base md:text-sm font-normal bg-siteYellow px-[20px] py-2.5 md:px-[15px] ">
                        <img class="mr-[8px] w-[17px] relative md:top-[-0px] top-[-2px]" src="{{asset('images/fa-add-icon.svg')}}" alt="">New Lead
                    </a>
                </div>
            </div>

            @if(count($leads)>0)
                <div class="relative ta_scrollbar_out bg-[#fff] md:pb-[30px] pb-[55px] md:pb-[30px] md:mb-[60px] overflow-x-auto overflow-y-hidden rounded-[10px] shadow-md sm:rounded-lg">
                    <table id="leads_list" class=" border-spacing-0 border-collapse-separate  w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 md:hidden">
                            <tr class="bg-white border-b ">

                                {{-- <th scope="col" class=" px-3  pt-[20px] md:pt-[14px] pb-3 text-[#000] font-bold">
                                id
                                </th> --}}

                                <th scope="col" class="pl-5 sm:pl-[10px] px-3 pt-[20px]  md:pt-[14px] pb-3 text-[#000] font-bold">
                                Customer Name
                                </th>

                                {{-- <th scope="col" class="px-3 pt-[20px] md:pt-[14px] pb-3 text-[#000] font-bold">
                                Contact No.
                                </th> --}}

                                <th scope="col" class="lg:hidden xl:hidden  px-3  pt-[20px] md:pt-[14px] pb-3 text-[#000] font-bold">
                                Pick up Date & TIme
                                </th>

                                <th scope="col" class="lg:hidden xl:hidden px-3  pt-[20px] md:pt-[14px] pb-3 text-[#000] font-bold">
                                Pick up Location
                                </th>

                                <th scope="col" class="lg:hidden xl:hidden px-3  pt-[20px] md:pt-[14px] pb-3 text-[#000] font-bold">
                                Car Model
                                </th>

                                {{-- <th scope="col" class=" lg:hidden px-3 pt-[20px] md:pt-[14px] pb-3 text-[#000] font-bold">
                                car_type
                                </th>

                                <th scope="col" class="lg:hidden px-3 pt-[20px] pb-3 text-[#000] font-bold">
                                lead_source
                                </th> --}}

                                <th scope="col" class=" px-3 pt-[20px] pb-3 text-[#000] font-bold">
                                    Status
                                </th>

                                {{-- <th scope="col" class="  px-3 pt-[20px] pb-3 text-[#000] font-bold
                                ">
                                    action
                                </th> --}}

                            </tr>
                        </thead>

                        <tbody class="md:p-3">
                        </tbody>

                    </table>
                </div>
                @else
                <div class="empty_car_section_box bg-lightgray">
                    <div class="flex items-center justify-center min-h-screen bg-white rounded-md">
                        There is no data to show
                    </div>
                </div>
            @endif
    </div>
    <!-- session message  -->
    @if (Session::has('success'))
        <div class="session_msg_container flex items-center justify-between border border-[#478E1A] bg-[#DDEDD3] text-[#000000] text-sm font-bold px-4 py-3 rounded-lg  mx-auto bottom-3 w-3/5 sticky left-0 right-0 lg:bottom-[75px] lg:w-4/5 md:w-[93%]" role="alert">
            <div class="flex items-center">
            <span class="action_icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="12" viewBox="0 0 17 12" fill="none">
            <path d="M16.6443 0.351508C17.1186 0.820184 17.1186 1.58132 16.6443 2.04999L6.93109 11.6485C6.45681 12.1172 5.68659 12.1172 5.21231 11.6485L0.355708 6.84924C-0.118569 6.38057 -0.118569 5.61943 0.355708 5.15076C0.829985 4.68208 1.60021 4.68208 2.07449 5.15076L6.0736 9.09889L14.9293 0.351508C15.4036 -0.117169 16.1738 -0.117169 16.6481 0.351508H16.6443Z" fill="#478E1A"></path>
            </svg>
            </span>
            <p class="session_msg ml-[15px] text-[#000000] text-[14px] font-normal">Success.&nbsp;<span class="font-normal text-[#000000]">{{ Session::get('success')}}</span></p>
            </div>
            <a href="javascript:void(0);"class="action_perform">
            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
            <path d="M9.29074 10.3517L5.29074 6.35174L1.28574 10.3517C1.21607 10.4217 1.1333 10.4773 1.04215 10.5153C0.951005 10.5533 0.853263 10.573 0.754508 10.5732C0.655753 10.5735 0.557919 10.5543 0.466592 10.5167C0.375266 10.4791 0.292235 10.4239 0.22224 10.3542C0.152246 10.2846 0.0966583 10.2018 0.0586519 10.1107C0.0206455 10.0195 0.000964363 9.92176 0.000732217 9.82301C0.000500072 9.72425 0.0197215 9.62642 0.0572989 9.53509C0.0948764 9.44377 0.150074 9.36074 0.21974 9.29074L4.22474 5.29074L0.21974 1.28074C0.0790429 1.14004 -1.48249e-09 0.949216 0 0.75024C1.48249e-09 0.551264 0.079043 0.360438 0.21974 0.21974C0.360438 0.079043 0.551264 9.39873e-09 0.75024 7.91624e-09C0.949216 6.43375e-09 1.14004 0.0790429 1.28074 0.21974L5.28574 4.22474L9.28574 0.21974C9.42644 0.0790429 9.61726 0 9.81624 0C10.0152 0 10.206 0.0790429 10.3467 0.21974C10.4874 0.360438 10.5665 0.551264 10.5665 0.75024C10.5665 0.949216 10.4874 1.14004 10.3467 1.28074L6.34674 5.28574L10.3467 9.28574C10.4874 9.42644 10.5665 9.61726 10.5665 9.81624C10.5665 10.0152 10.4874 10.206 10.3467 10.3467C10.206 10.4874 10.0152 10.5665 9.81624 10.5665C9.61726 10.5665 9.42644 10.4874 9.28574 10.3467L9.29074 10.3517Z" fill="black"></path>
            </svg>
            </a>
        </div>
    @endif
    <!-- navigation -->
    @include('layouts.navigation')
</div>

<!-- FancyBox -->
<div id="change_status_popup" style="display:none" class="status_change_popup w-[90%]">
    <div class="model_content">
        <div class="rounded-tl-10 rounded-tr-10 rounded-bl-0 rounded-br-0">
            <div class="flex items-center px-5 py-5 flex-nowrap bg-siteYellow ">
                <div class="popup_heading w-1/2 text-[18px] text-[#000000] font-normal">
                    <h3 class="text-lg capitalize">Change Status </h3>
                </div>
            </div>

            <div class="flex items-center px-4 flew-row py-7 popup_outer">
                <div class="w-full modal_scroll_off search_bar_btn afclr">
                    <form action="">

                        <input type="hidden" name="" class="" id="change_statusPopup">
                        <div class="popup_content_item ">

                            <div class="pb-2 form_main_src_full afclr">
                                <div class="form_main_src_h form_main_src_active popup__form__src_half_inner afclr">
                                    <label for="user_status_change" class="block mb-2 capitalize">user status
                                    </label>
                                        <select id="user_status_change" class="w-full py-3 pl-5 pr-10 text-base font-normal leading-normal text-black bg-white border rounded-md appearance-none required_field  border-black500 focus:outline-none focus:border-siteYellow drop_location_select">
                                            <option value="0" selected disabled class="capitalize text-midgray fontGrotesk">Select status</option>
                                            <option value="new" class="capitalize">new </option>
                                            <option value="attempted_to_contacted" class="capitalize">attempted to contacted</option>
                                            <option value="confirmed" class="capitalize">confirmed</option>
                                            <option value="cancelled" class="capitalize">cancelled</option>
                                            <option value="lost_lead" class="capitalize">lost lead</option>
                                            <option value="junk_lead" class="capitalize">junk lead</option>
                                        </select>
                                </div>
                            </div>


                            <div class="flex justify-center pt-[23px] change_status_popup_action_btns">
                                <div class="flex items-center w-1/2 mr-2">
                                    <a href=" javascript:void(0);"
                                        class="py-[8px] px-[40px] w-full text-[#000] text-center  save_status  capitalize rounded-md bg-siteYellow border border-siteYellow transition-all
                                        duration-300 hover:bg-siteYellow400" id="save_status_btn">
                                        save</a>
                                </div>


                                <div class="flex w-1/2 mr-2">
                                    <a href="javascript:void(0);"
                                        class="ml-2 w-full py-[8px] px-[40px] popupcancelBtn border border-siteYellow  text-center capitalize rounded-md text-[#000] bg-[#fff] transition-all
                                        duration-300 border-siteYellow hover:bg-siteYellow400" id="cancel_status_btn">
                                        cancel
                                    </a>
                                </div>

                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<input type="hidden" name="" id="statusChangeId"  >

<script>


    $('body').on('click', '.test_child .dash_circle', function(e) {
        e.stopPropagation();
    });

    function hideLoader()
    {
        $('.loader').css("display", "none");
        $('.overlay_sections').css("display", "none");
    }

    $('body').on('click','.clickableRow',function()
    {
        let viewUrl=$(this).data('preview-url');
        $('.loader').css("display", "inline-flex");
        $('.overlay_sections').css("display", "block");
        $('body').removeClass('sidebar_open');
        window.location = viewUrl;

        $(window).on('pageshow', function(event)
        {
            if (event.originalEvent.persisted)
            {
                hideLoader();
            }
        });

    });

    function checkTableLength()
    {
            var table = $("#leads_list");
            console.warn('leads_list:',table);
            // Check if the table has more than 2 rows
            if (table.find("tr").length >0) {
                // Add classes to the table element
                $('.ta_scrollbar_out').addClass('extraBottomPadding');
            }
    }

    $(document).ready(function()
    {
        checkTableLength();

        setTimeout(function() {
            $('.session_msg_container').slideUp();
        }, 10000);

        $(".action_perform").on('click', function(e) {
            $('.session_msg_container').slideUp();
        });

    });

    var dataTable = $('#leads_list').DataTable({
        // processing: true,
        serverSide: true,
        responsive: false,
        autoWidth: false,
        searching: false,
        sortable:true,
        // ordering:true,
        lengthChange: false,
        info: false,
        order: [[0, "desc"]],
        ajax: {
        url: "{{route('agent.leads.list.ajax')}}",
        },
        columns: [
            // { data: 'id', name: 'id', orderable: false, searchable: false },
            // { data: 'id', name: 'id', orderable: true  },
            { data: 'customer_name', name: 'customer_name', orderable: true  },
            // { data: 'contact_number', name: 'contact_number',orderable: true },

            { data: 'pick_up_date_time', name: 'pick_up_date_time',orderable: true },

            { data: 'pick_up_location', name: 'pick_up_location',orderable: true },
            { data: 'car_model', name: 'car_model',orderable: true },
            // { data: 'car_type', name: 'car_type',orderable: true },
            // { data: 'lead_source', name: 'lead_source',orderable: true },
            { data: 'status', name: 'status',orderable: true },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],

        createdRow: function (row, data, dataIndex)
        {
            $(row).addClass('row_class bg-white hover:bg-whitesmoke400 text-[#000] lg:border-b lg:borer-b-[#444444]');

            // $(row).find('td:eq(0)').addClass('px-3 py-4 md:py-2.5 md:hidden');
            // $(row).find('td:eq(0)').addClass('px-3 py-4 md:py-2.5 font-medium text-gray-900 whitespace-nowrap sm:pr-0 sm:pl-[10px] ');
            // $(row).find('td:eq(0)').attr('data-view-user-url',data.action.view_url);

            $(row).attr('data-user-id', data.id);
            $(row).attr('data-view-url',data.action.view_url);

            // var pickupDateTime = data.pick_up_date_time;
            // // Parse the date string into a JavaScript Date object
            // var date = new Date(pickupDateTime);
            // // Using Moment.js for date formatting
            // var formattedDate = moment(date).format('DD MMMM, YYYY - hh:mma');

            $('td:eq(0)', row).html(
                ' <div class=""> <div class="mb-[10px] md:block hidden">'+ (data.status === 'new' ?
                    '<div class="inline-block py-[2px] px-[20px] bg-[#E7F8FF] text-[#198DE1]  xl:inline-block rounded-[39px] text-left">' +
                        '<span class="status_active capitalize lg:text-[13px]">' + data.status + '</span>' +
                    '</div>' :
                (data.status === 'attempted_to_contacted' ?
                    '<div class="inline-block py-[2px] px-[20px] bg-[#FFFCF4] text-[#FD9154]  xl:inline-block p-[6px] rounded-[39px] text-left">' +
                        '<span class="status_active capitalize lg:text-[13px]">attempted to contacted</span>' +
                    '</div>' :
                (data.status === 'confirmed' ?
                    '<div class="inline-block py-[2px] px-[20px] bg-[#EDFAEE] text-[#25A02A]  xl:inline-block p-[6px] rounded-[39px] text-left">' +
                        '<span class="status_active capitalize lg:text-[13px]">' + data.status + '</span>' +
                    '</div>' :
                (data.status === 'cancelled' ?
                    '<div class="inline-block py-[2px] px-[20px] bg-[#FFE6E6] text-[#E12919]  xl:inline-block   p-[6px] rounded-[39px] text-left">' +
                        '<span class="status_active capitalize lg:text-[13px]">' + data.status + '</span>' +
                    '</div>' :
                (data.status === 'lost_lead' ?
                    '<div class="inline-block py-[2px] px-[20px] bg-[#FFF2F2] text-[#E12919]  xl:inline-block   p-[6px] rounded-[39px] text-left">' +
                        '<span class="status_active capitalize lg:text-[13px]">lost lead</span>' +
                    '</div>' :
                (data.status === 'junk_lead' ?
                    '<div class="inline-block py-[2px] px-[20px] bg-[#FFEEE9] text-[#FF471F]  xl:inline-block   p-[6px] rounded-[39px] text-left">' +
                        '<span class="status_active capitalize lg:text-[13px]">junk lead</span>' +
                    '</div>' :
                ''))))))
                +' </div> <div class=" sm:text-sm sm:text-[#000000] font-medium flex items-center justify-start">' + data.customer_name + ' <span class="test_child opacity-0 transition-all duration-300 flex ml-[30px]"><a href="tel:'+data.action.call+'" class="mr-2 dash_circle font-medium text-gray-600 hover:underline flex md:hidden hover:bg-[#F3CD0E] " data-img-url="{{ asset('images/black_call.svg') }}">' +
                '<img src="{{ asset('images/black_call.svg') }}" alt="Call">' +
                '<span class="tooltiptext">Call</span>' +
                '</a><a href="javascript:void(0);" class=" dash_circle font-medium text-gray-600 hover:underline flex md:hidden hover:bg-[#F3CD0E] ">' +
                    '<img src="{{asset('images/black_message.svg')}}" alt="Message">' +
                '<span class="tooltiptext">Message</span>' +
                '</a>  </span></div> <div class="hidden md:block sm:text-xs text-[#444444] ">' + data.pick_up_date_time + ' <span class="sm:text-xs md:text-sm sm:block">&nbsp;|&nbsp;' + data.car_model + '</span> </div> </div>'
            );

            $(row).find('td:eq(0)').addClass('clickableRow px-3 capitalize py-4 pl-5 sm:pl-[10px]');
            // $(row).find('td:eq(1)').addClass('clickableRow md:hidden capitalize px-3 py-4');
            $(row).find('td:eq(1)').addClass('clickableRow xl:hidden capitalize px-3 py-4');
            $(row).find('td:eq(2)').addClass('clickableRow xl:hidden capitalize px-3 py-4');
            $(row).find('td:eq(3)').addClass('clickableRow xl:hidden capitalize px-3 py-4');
            $(row).find('td:eq(4)').addClass('clickableRow md:hidden capitalize px-3 py-4');

            // $(row).find('td:eq(6)').addClass('lg:hidden  px-3  py-4');
            // $(row).find('td:eq(9)').attr('data-view-user-url',data.action.view_url);

            // $(row).find('td:eq(4)').addClass(' xl:text-right lg:px-3  py-4 sm:pl-0 sm:pr-[10px]');

            if(window.matchMedia("(max-width: 992px)").matches)
            {
                $(row).find('td:eq(0)').addClass('clickableRow');
                $(row).find('td:eq(1)').addClass('clickableRow');
                $(row).find('td:eq(2)').addClass('clickableRow');
                $(row).find('td:eq(3)').addClass('clickableRow');
                $(row).find('td:eq(4)').addClass('clickableRow');
                $(row).find('td:eq(5)').addClass('clickableRow');
                // $(row).find('td:eq(6)').addClass('clickableRow');
            }

            // $('td:eq(5)', row).html(
            //         (data.status === 'new' ?
            //             '<div class="status xl:inline-block text-center">'
            //             '<span class=" status_active bg-[#ECF9E2] capitalize">'+ data.status+'</span>' '</div>' :
            //             (data.status === 'deactivate'? '<div class="status xl:inline-block text-center">'
            //             '<span class=" status_active bg-[#ECF9E2] capitalize">'+ data.status+'</span>' '</div>' ):
            //             // '<span class="">'+ data.status+'</span>'
            //         ) +
            // );

            $('td:eq(4)', row).html(
                (data.status === 'new' ?
                    '<div class="inline-block py-[5px] px-[20px] bg-[#E7F8FF] text-[#198DE1]  xl:inline-block rounded-[39px] text-left">' +
                        '<span class="status_active capitalize lg:text-[13px]">' + data.status + '</span>' +
                    '</div>' :
                (data.status === 'attempted_to_contacted' ?
                    '<div class="inline-block py-[5px] px-[20px] bg-[#FFFCF4] text-[#FD9154]  xl:inline-block p-[6px] rounded-[39px] text-left">' +
                        '<span class="status_active capitalize lg:text-[13px]">attempted to contacted</span>' +
                    '</div>' :
                (data.status === 'confirmed' ?
                    '<div class="inline-block py-[5px] px-[20px] bg-[#EDFAEE] text-[#25A02A]  xl:inline-block   p-[6px] rounded-[39px] text-left">' +
                        '<span class="status_active capitalize lg:text-[13px]">' + data.status + '</span>' +
                    '</div>' :
                (data.status === 'cancelled' ?
                    '<div class="inline-block py-[5px] px-[20px] bg-[#FFE6E6] text-[#E12919]  xl:inline-block   p-[6px] rounded-[39px] text-left">' +
                        '<span class="status_active capitalize lg:text-[13px]">' + data.status + '</span>' +
                    '</div>' :
                (data.status === 'lost_lead' ?
                    '<div class="inline-block py-[5px] px-[20px] bg-[#FFF2F2] text-[#E12919]  xl:inline-block   p-[6px] rounded-[39px] text-left">' +
                        '<span class="status_active capitalize lg:text-[13px]">lost lead</span>' +
                    '</div>' :
                (data.status === 'junk_lead' ?
                    '<div class="inline-block py-[5px] px-[20px] bg-[#FFEEE9] text-[#FF471F]  xl:inline-block   p-[6px] rounded-[39px] text-left">' +
                        '<span class="status_active capitalize lg:text-[13px]">junk lead</span>' +
                    '</div>' :
                ''))))))
            );


        $(row).find('td:eq(5)').addClass('pl-3 pr-5 py-4  text-right md:py-2.5 md:pr-3 ');

        $('td:eq(5)', row).html(
            '<div class="action_sec_view">' +
                '<div class="relative triple_dots_main border border-transparent flex pt-2 pb-2">' +

                    '<a href="tel:'+data.action.call+'"  class="mr-2 dash_circle font-medium text-gray-600 hover:underline hidden md:flex hover:bg-[#F3CD0E] ">' +
                        '<img src="{{asset('images/black_call.svg')}}" alt="Call">' +
                    '<span class="tooltiptext">Call</span>' +
                    '</a>' +

                    '<a href="javascript:void(0);" class="mr-2 dash_circle font-medium text-gray-600 hover:underline hidden md:flex hover:bg-[#F3CD0E] ">' +
                        '<img src="{{asset('images/black_message.svg')}}" alt="Message">' +
                    '<span class="tooltiptext">Message</span>' +
                    '</a>' +

                    '<a href="' + data.action.view_url + '" class="mr-2 dash_circle font-medium text-gray-600 hover:underline hidden md:flex hover:bg-[#F3CD0E] ">' +
                        '<img src="{{asset('images/black_eye.svg')}}" alt="Eye">' +
                    '<span class="tooltiptext">Eye</span>' +
                    '</a>' +

                    '<a href="javascript:void(0);" class="relative triple_dots_action_btn triple_dots delete_partner font-medium text-gray-600 hover:underline md:hidden">' +
                        '<img src="{{asset('images/triple_dottts.svg')}}" alt="More Options">' +
                    '</a>' +

                    '<div class="dots_hover_sec list_link_frame_section top-[42px] absolute w-[165px] rounded-[5px] py-[15px] bg-[#fff] z-[2] -left-[129px] border border-[#D1D1D1] rounded-[5px] rounded-tr-none custom-shadow afclr hidden">' +
                        '<ul class="frame_list_items_popup">' +
                            '<li class="text-left">' +
                                '<a data-src="#change_status_popup" data-fancybox href="javascript:void(0);" class="pl-[15px] change_status_user capitalize inline-block w-full py-2 hover:bg-siteYellow text-[#000] duration-300" data-user-id="'+data.id+'" data-status-change-url="'+data.action.status_change_url+'" data-user-status="'+ data.status +'">Change Status</a>' +
                            '</li>' +
                        '</ul>' +
                    '</div>' +

                '</div>' +
            '</div>'
            );

        },
        language:{
            oPaginate:{
            sPrevious: '<span class="hidden pagination_link" aria-hidden="true">\
        <svg width="40" height="15" viewBox="0 0 40 15" fill="none" xmlns="http://www.w3.org/2000/svg">\
            <path fill-rule="evenodd" clip-rule="evenodd"\
                d="M8.4799 0.000511718C8.5734 0.00614315 8.663 0.0401363 8.73686 0.0980374C8.81072 0.155939 8.86536 0.235024 8.89357 0.324802C8.92178 0.414581 8.92222 0.510847 8.89484 0.600884C8.86746 0.690921 8.81355 0.770484 8.74022 0.829069L1.71152 6.91993H39.5306C39.5919 6.91905 39.6528 6.93044 39.7097 6.95343C39.7666 6.97641 39.8184 7.01054 39.8621 7.05382C39.9058 7.0971 39.9405 7.14868 39.9641 7.20555C39.9878 7.26242 40 7.32345 40 7.3851C40 7.44674 39.9878 7.50778 39.9641 7.56465C39.9405 7.62152 39.9058 7.67309 39.8621 7.71637C39.8184 7.75965 39.7666 7.79378 39.7097 7.81677C39.6528 7.83975 39.5919 7.85114 39.5306 7.85027H1.63923L8.37864 14.1882C8.42377 14.2301 8.46021 14.2805 8.48586 14.3366C8.51151 14.3927 8.52588 14.4533 8.52811 14.515C8.53035 14.5767 8.52043 14.6382 8.49891 14.696C8.47739 14.7538 8.4447 14.8068 8.40273 14.8519C8.36076 14.8969 8.31033 14.9332 8.25435 14.9586C8.19837 14.984 8.13795 14.998 8.07656 14.9998C8.01518 15.0016 7.95404 14.9912 7.89667 14.9692C7.8393 14.9472 7.78684 14.9139 7.7423 14.8714L0.149597 7.73397C0.101248 7.6893 0.0629115 7.63479 0.0371357 7.57407C0.0113599 7.51336 -0.00126492 7.4478 9.99709e-05 7.3818C0.00146486 7.3158 0.0167883 7.25085 0.0450521 7.19127C0.0733158 7.13168 0.113872 7.07884 0.164027 7.03622L8.14724 0.116804C8.22739 0.0456103 8.3296 0.00451701 8.43649 0.000511718C8.45095 -0.000170573 8.46544 -0.000170573 8.4799 0.000511718Z"\
                fill="black" />\
        </svg>\
        <span class="page_name hidden sm:hidden ">Prev</span>\
        <span class="" aria-disabled="true" aria-label="« Previous">\
        </span>\
        </span>',
        sNext: '<span class="pagination_link hidden">\
        <span class="page_name hidden sm:hidden  ">Next</span>\
        <svg width="40" height="15" viewBox="0 0 40 15" fill="none" xmlns="http://www.w3.org/2000/svg">\
            <path fill-rule="evenodd" clip-rule="evenodd"\
            d="M31.5201 0.000511718C31.4266 0.00614315 31.337 0.0401363 31.2631 0.0980374C31.1893 0.155939 31.1346 0.235024 31.1064 0.324802C31.0782 0.414581 31.0778 0.510847 31.1052 0.600884C31.1325 0.690921 31.1865 0.770484 31.2598 0.829069L38.2885 6.91993H0.469383C0.408058 6.91905 0.347172 6.93044 0.290264 6.95343C0.233353 6.97641 0.181557 7.01054 0.137882 7.05382C0.0942078 7.0971 0.0595284 7.14868 0.0358582 7.20555C0.012188 7.26242 0 7.32345 0 7.3851C0 7.44674 0.012188 7.50778 0.0358582 7.56465C0.0595284 7.62152 0.0942078 7.67309 0.137882 7.71637C0.181557 7.75965 0.233353 7.79378 0.290264 7.81677C0.347172 7.83975 0.408058 7.85114 0.469383 7.85027H38.3608L31.6214 14.1882C31.5762 14.2301 31.5398 14.2805 31.5141 14.3366C31.4885 14.3927 31.4741 14.4533 31.4719 14.515C31.4696 14.5767 31.4796 14.6382 31.5011 14.696C31.5226 14.7538 31.5553 14.8068 31.5973 14.8519C31.6392 14.8969 31.6897 14.9332 31.7456 14.9586C31.8016 14.984 31.862 14.998 31.9234 14.9998C31.9848 15.0016 32.046 14.9912 32.1033 14.9692C32.1607 14.9472 32.2132 14.9139 32.2577 14.8714L39.8504 7.73397C39.8988 7.6893 39.9371 7.63479 39.9629 7.57407C39.9886 7.51336 40.0013 7.4478 39.9999 7.3818C39.9985 7.3158 39.9832 7.25085 39.9549 7.19127C39.9267 7.13168 39.8861 7.07884 39.836 7.03622L31.8528 0.116804C31.7726 0.0456103 31.6704 0.00451701 31.5635 0.000511718C31.549 -0.000170573 31.5346 -0.000170573 31.5201 0.000511718Z"\
            fill="black" />\
        </svg>\
        </span>',
            sFirst: '',
            sLast: ''
        }
        },
        drawCallback: function(settings) {
            var api = this.api();
            var recordsTotal = api.page.info().recordsTotal;
            if (recordsTotal > 10) {
                $('#' + api.table().node().id + '_paginate').show();
            } else {
                $('#' + api.table().node().id + '_paginate').hide();
            }
        }
    });

    dataTable.on('preXhr.dt', function ( e, settings, data ) {
        $(".loader").css("display", "inline-flex");
        $(".overlay_sections").css("display", "block");
        }).on('draw.dt', function () {
        $(".loader").css("display", "none");
        $(".overlay_sections").css("display", "none");
    });


    // $('body').on('click','.clickableRow',function(){
    //     let viewUrl=$(this).data('view-user-url');
    //     window.location = viewUrl;
    // });


    $('body').off('click','.triple_dots_action_btn');

    $('body').on('click', '.triple_dots_action_btn', function(e) {
        e.stopPropagation();
        $(this).closest('.triple_dots_main').toggleClass('active');

        $('.triple_dots_main').not($(this).closest('.triple_dots_main')).removeClass('active');
        var isActive = $(this).hasClass('active');
        $('.dots_hover_sec').not($(this).siblings('.dots_hover_sec')).slideUp();
        $(this).siblings('.dots_hover_sec').slideToggle(100);

    });

    $("body").click(function(e) {
            $(".dots_hover_sec").slideUp();
            $('.triple_dots_action_btn').removeClass('active');
            $('.triple_dots_main').removeClass('active');
    });


    $('body').on('click','.delete_user',function(){
        console.log('from delete:',$(this).data('delete-url'));
        const deleteUrl=$(this).data('delete-url');
        const deleteId=$(this).data('user-id');

        Swal.fire({
            title: 'Are you sure want to delete?',
            icon: 'error',
            showCancelButton: true,
            confirmButtonText: 'YES, DELETE IT!',
            cancelButtonText: 'NO, CANCEL'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                    url: deleteUrl,
                        type: "post",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            'ids': deleteId,
                        },
                        dataType: 'json',
                        success: function (data) {
                        if (data.success) {
                           console.log('under success part',data);

                        $(this).parents("tr").remove();

                        Swal.fire({
                            title: 'User has been deleted',
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonText: 'OK',
                                customClass: {
                                        popup: 'popup_updated',
                                        title: 'popup_title',
                                        actions: 'btn_confirmation',
                                },

                        }).then((result) => {
                            if(result.isConfirmed){
                                checkTableLength();
                                $('#leads_list').DataTable().ajax.reload();
                            }
                            else{
                                checkTableLength();
                                $('#leads_list').DataTable().ajax.reload();

                            }
                        });
                        }
                        else {
                            console.log('else part');
                        alert('Error occured.');
                        }
                        },
                        error: function (data) {
                        }
                    });
                }
            })
    });


    $('body').on('click','.change_status_user',function()
    {
        console.log("clicked");

        $(".change_status_user").fancybox({
                beforeShow: function(data, item) {
            }
        });

        let status= $(this).data('user-status');

        $("#user_status_change option[value=" + status + "]").prop('selected', true);

        const statusChangeUrl = $(this).data('status-change-url');

        let id = $(this).data('user-id');

        $('#change_statusPopup').attr('data-status-change-url', statusChangeUrl);

        console.warn("click status change iddd", id);

        $('#change_statusPopup').attr('data-user-id', id);

        $('#statusChangeId').val(id);

        $('#change_statusPopup').attr('data-user-status', status);

    });


    $('body').on('click','#save_status_btn',function(e)
    {
        e.preventDefault();
        // console.warn('current save button',$(this) );
        const statusChangeUrl = $(this).closest('.modal_scroll_off').find('#change_statusPopup').data('status-change-url');

        // console.log("statusChangeUrl",statusChangeUrl);

        // let id = $(this).closest('.modal_scroll_off').find('#change_statusPopup').data('user-id');
        let id =$('#statusChangeId').val();

        // console.warn('current data ',$(this).closest('.modal_scroll_off').find('#change_statusPopup'));

        // console.warn('save_status_btn id',id);

        let status=$('#user_status_change').val();

        if(status==0){
            Swal.fire({
                        title: 'status is required',
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'popup_updated',
                            title: 'popup_title',
                            actions: 'btn_confirmation',
                        }

                    });
              return;
        }
        else{
            Swal.fire({
                title: 'Are you sure you want to update the status ?',
                icon: 'error',
                showCancelButton: true,
                confirmButtonText: 'YES, UPDATE IT!',
                cancelButtonText: 'NO, CANCEL'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.fancybox.close();
                        $.ajax({
                        url: statusChangeUrl,
                            type: "post",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                'ids': id,
                                'status':status,
                            },
                            dataType: 'json',
                            success: function (data) {
                            if (data.success) {
                                Swal.fire({
                                    title: 'status has been updated',
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        popup: 'popup_updated',
                                        title: 'popup_title',
                                        actions: 'btn_confirmation',
                                    },
                                }).then((result) => {
                                    if(result.isConfirmed){
                                        $.fancybox.close();
                                        $('#leads_list').DataTable().ajax.reload();
                                        checkTableLength();
                                    }
                                    else{
                                        $.fancybox.close();
                                        $('#leads_list').DataTable().ajax.reload();
                                        checkTableLength();
                                    }
                                });
                            }
                            else {
                                console.log('else part');
                                alert('Error occured.');
                            }
                            },
                            error: function (data) {
                            }
                        });
                    }
                });
        }

    });

    $('body').on('click','#cancel_status_btn',function(e)
    {
        e.preventDefault();
        $.fancybox.close();
    });

    $('body').on('click','.clickableRow',function()
    {
        let viewUrl = $(this).parent().data('view-url');
        window.location = viewUrl;
    });

</script>
@endsection

