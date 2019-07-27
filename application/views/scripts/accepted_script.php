<!-- Full Calendar CSS -->

<style>
    .fc-toolbar.fc-header-toolbar:first-child,
    .fc-toolbar .fc-right {
        display: block;
    }
    .bg-color-red { 
        color: white !important;
        background-color: #D3D3D3 !important;
    }
    .bg-color-red .fc-event-dot {
        background-color: #D3D3D3 !important;
    }
    .bg-color-greenLight {
        color: white !important;
        background-color: #03b6a2 !important;
    }
    .bg-color-greenLight .fc-event-dot {
        background-color: #03b6a2 !important;
    }
    .bg-color-grey {
        color: white !important;
        background-color: #D3D3D3 !important;
    }
    .bg-color-grey  .fc-event-dot {
        background-color: #03b6a2 !important;
    }
    .big-event-block {
        height: 35px;
    }

    /*customize calendar color*/
    .fc-unthemed .fc-divider, .fc-unthemed .fc-list-heading td, .fc-unthemed .fc-popover .fc-header {
        background: #fff !important;
    }
    @media only screen and (min-width: 768px){
        .pl0 {
            padding-left: 0;
        }
        .pmainsec{
            padding-bottom: 30px;
        }
        .sec-rowm{
            margin: 0 -4px;
        }
    }
    @media only screen and (max-width: 767px){
        .mob-no-padding{
            padding-left: 0;
            padding-right: 0;
        }
        .pdinput{
            padding-bottom: 15px;
        }
        .pmainsec{
            padding-bottom: 20px;
        }
        .pl25 {
            padding-left: 15px !important;
        }
        button#btn_confirm_update_weekdays {
            display: block;
        }
    }
    .pt12 {
        padding-top: 12px;
    }
    .mb15{
        margin-bottom: 15px;
    }
    .p15 {
        padding-top: 15px;
    }
    .blocked-time-ttl {
        color: #08b5a2;
        font-weight: 600;
        font-size: 16px;
        padding: 15px 0 0;
    }
    .ptrowblock {
        padding: 20px 0 0;
    }
    .blocked-time-close {
        color: #08b5a2;
        font-size: 16px;
        font-weight: 600;
        margin: 6px 0 0;
    }
    .pl25 {
        padding-left: 25px;
    }
    .bold-txt{
        font-weight: 600;
    }
    p.blk-div {
        margin: 8px 0 0;
    }
    .remove_timeslot {      
        margin: 20px 0px;       
        color: #03b6a2;     
    }
    .checkbox .cr {
        float: right;
        margin-left: 5px;
    }


    .selected-event {
        background-color: #e8696b !important;
    }

    span.timeslot-sp {
        color: red;
        margin-left: 10px;
    }    
    span#selected_datetime {
        float: left;
        margin: 10px 0 0 10px;
        color: red;
    } 
    span#physician_name {
        font-size: 18px;
    }
    button.fc-agendaWeek-button.fc-corner-left.fc-corner-right {
        border-radius: 0;
    }
    button.fc-month-button.fc-corner-right {
        margin-left: 0;
        border-radius: 0;
    }
    .fc-time-grid .fc-slats td {
        height: .9em !important;
    }
    .backbtn-sec{
        display: inline-block;
        float: right;
        margin-right: 10px;
    }
    form#form_add_patient_visit .radio {
        margin-top: 0px;
        margin-bottom: 5px;
    }
    .tab-sec {
        position: initial;
        float: right;
    }
    .btn-normal {
        padding: 5px 10px !important;
        border-radius: 0 !important;
    }
    .tab-sec ul.nav.nav-pills li {
        margin-left: -1px !important;
    }
    .modal-body{
        margin-bottom: 20px;
    }
</style>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/fullcalendar.min.css"> 

<!-- Full Calendar JS-->
<script src="<?php echo base_url(); ?>assets/js/moment.min.js" ></script>
<script src="<?php echo base_url(); ?>assets/js/fullcalendar.min.js" ></script>
<script>
    function set_row4(id, row) {
//    debugger
        $(row).find("td:not(:last-child)").each(function (index, td) {
            $(td).addClass("db-table-link-row");
            $(td).attr("data-id", id);
            $(td).attr("data-href", base + "accepted/referral_details/" + id);
        });
        // $(row).addClass("db-table-link-row");
        // $(row).addClass("accepted_row");
        return '<a class="btn btn-theme bttn-circle view_add_patient" data-id="' + id + '" ' +
                'data-toggle="modal" data-target="#modal_add_patient_visit"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp;Patient Visit</a>';
    }


    //for patient visit script starts
    function set_missing_status(status, dot) {
        if (dot === "red") {
            return '<span class="fc-event-dot" style="background-color:#f74444"></span>  ' + status;
        } else if (dot === "green") {
            return '<span class="fc-event-dot" style="background-color:#88b794"></span>  ' + status;
        } else if (dot === "yellow") {
            return '<span class="fc-event-dot" style="background-color:#9da1c3"></span>  ' + status;
        } else if (dot === "blue") {
            return '<span class="fc-event-dot" style="background-color:#e7e92a"></span>  ' + status;
        } else {
//            console.log("empty color");
//            console.log(status, dot);
            return status;
        }
    }


    // *************************************
    //     for patient visit script starts
    // *************************************


    selection_calendar_monthmode = null;
    selection_calendar_weekmode = null;
    global_data.weekdays = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];

    function set_blocks_in_calendar() {

        selection_calendar_monthmode.fullCalendar("removeEvents");
        view_mode = selection_calendar_monthmode.fullCalendar('getView');
        start = view_mode.start.format("YYYY-MM-DD");
        end = view_mode.end.format("YYYY-MM-DD");

        form = $("form#sample_form");
        form.find("#target").val(start);
        form.find("#param").val(end);
        form.find("#id").val(global_data.referral_id);

        data = form.serialize();
        url = base + "referral/get_patient_visit_calendar_month_view";

        $.post({
            url: url,
            data: data,
            success: function (response) {
                if (IsJsonString(response)) {
                    response = JSON.parse(response);
                    if (response.result === "success") {
                        data = response.data;
                        selection_calendar_monthmode.fullCalendar("removeEvents");
                        view_mode = selection_calendar_monthmode.fullCalendar('getView');
//
                        //set events here
                        global_data.events = [];
                        start = moment(view_mode.start.format("YYYY-MM-DD"));
                        end = moment(view_mode.end.format("YYYY-MM-DD"));
                        cur_day = start;

                        counter = 1;

                        break_while = false;
                        global_data.events = [];
                        do {
                            cur_day_ymd = cur_day.format("YYYY-MM-DD");
                            slots = data[cur_day_ymd];
//                            debugger
                            if (slots !== undefined) {
                                event_class = (slots === 0) ? "bg-color-grey" : "bg-color-greenLight";

                                event = {
                                    title: slots + " slots available",
                                    start: cur_day_ymd,
                                    allDay: true,
                                    className: ["event", "big-event-block", event_class]
                                };
                                global_data.events.push(event);
                            }
                            cur_day.add(1, 'days');
                            counter++;
                        } while (cur_day < end && counter < 100);

                        for (i = 0; i < global_data.events.length; i++) {
                            selection_calendar_monthmode.fullCalendar('renderEvent', global_data.events[i]);
                        }

                        setTimeout(function () {
                            $("#selection_calendar_monthmode .fc-today-button").on("click", function () {
                                set_blocks_in_calendar();
                            });
                        }, 1000);

                    } else {
                        error(response.message);
                    }
                }
            },
            error: function () {
                error("Internal server error");
            }
        });
    }
    function set_weekday_calendar(date) {

        $("#tab_month").removeClass('active');
        $("#tab_week").addClass('active');
        $("#selection_calendar_weekmode").show();

        //remove month calendar
        if (selection_calendar_monthmode) {
            selection_calendar_monthmode.fullCalendar("destroy");
        }

        //add week calendar
        selection_calendar_weekmode = $("#selection_calendar_weekmode").fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            defaultView: 'agendaWeek',
            eventClick: function (param) {
                start_datetime = param.start.format("YYYY-MM-DD kk:mm:ss");
                end_datetime = param.end.format("YYYY-MM-DD kk:mm:ss");
                display_datetime = param.start.format("dddd, MMMM Do") + " at " + param.start.format("LT");
                $("#selected_datetime").text(display_datetime);
                $("#selected_starttime").val(start_datetime);
                $("#selected_endtime").val(end_datetime);
                $(".selected-event").removeClass("selected-event");
                $(this).addClass("selected-event");
            },
            navLinks: true, // can click day/week names to navigate views
            eventLimit: true // allow "more" link when too many events
        });
        selection_calendar_weekmode.fullCalendar("gotoDate", date);
        selection_calendar_weekmode.fullCalendar("changeView", "agendaWeek");
        set_weekly_calendar2();
    }

    function set_weekly_calendar2() {

        view_mode = selection_calendar_weekmode.fullCalendar('getView');
        start = view_mode.start.format("YYYY-MM-DD");
        end = view_mode.end.format("YYYY-MM-DD");


        //fetch and plot events
        form = $("form#sample_form");
        form.find("#id").val($("select#physicians").val());
        form.find("#target").val(start);
        form.find("#param").val(end);
        form.find("#id").val(global_data.referral_id);

        data = form.serialize();
        url = base + "referral/get_patient_visit_calendar_week_view";

        $.post({
            url: url,
            data: data,
            success: function (response) {
                if (IsJsonString(response)) {
                    response = JSON.parse(response);
                    if (response.result === "success") {
                        $("#btn_back_to_month_view").show();
                        data = response.data;
                        selection_calendar_weekmode.fullCalendar("removeEvents");
                        global_data.events = [];
                        for (i = 0; i < data.length; i++) {
                            event_date = data[i].start_time.substr(0, 10);
                            event_start = data[i].start_time.substr(11, 8);
                            event_end = data[i].end_time.substr(11, 8);

                            event = {
                                title: "",
                                start: event_date + "T" + event_start,
                                end: event_date + "T" + event_end,
                                allDay: false,
                                className: ["event", "bg-color-greenLight"]
                            };
                            global_data.events.push(event);
                        }
                        for (i = 0; i < global_data.events.length; i++) {
                            selection_calendar_weekmode.fullCalendar('renderEvent', global_data.events[i]);
                        }
                        setTimeout(function () {
                            $("#selection_calendar_weekmode .fc-today-button").on("click", function () {
                                set_weekly_calendar2();
                            });
                        }, 1000);
                    }
                }
            },
            error: function () {
                error("Internal server error");
            }
        });
    }

    function get_location_and_custom() {
        url = base + "referral/get_location_and_custom";
        $.post({
            url: url,
            data: $("#sample_form").serialize()
        }).done(function (response) {
            if (IsJsonString(response)) {
                response = JSON.parse(response);
                if (response.result === "success") {
                    data = response.data;
                    //patient location
                    options = "<option disabled>Select Patient Location</option>";
                    data.locations.forEach(function (value, index) {
                        options += "<option value='" + value.id + "'>" + value.name + "</option>";
                    });
                    $("#form_add_patient").find("#patient_location").html(options);
                    //custom
                    options = "<option selected disabled>Custom Fields</option>";
                    data.customs.forEach(function (value, index) {
                        options += "<option value='" + value.id + "'>" + value.name + "</option>";
                    });
                    $("#form_add_patient").find("#custom").html(options);
                } else {
                    error("Error getting locations and customs");
                }
            } else {
                error("Error getting locations and customs");
            }
        });
    }

    $(document).ready(function () {



        get_location_and_custom();

        $("table").on("click", ".view_add_patient", function () {
            global_data.referral_id = $(this).data("id");
            $("#viewpatient_id").val(global_data.referral_id);
            global_data.patient_visit_button = $(this);
            $(global_data.patient_visit_button).button("loading");

            form = $("#sample_form");
            form.find("#id").val(global_data.referral_id);

            data = form.serialize();
            url = base + "referral/get_visit_allocation_for_manual_visit";

            $.post({
                url: url,
                data: data
            }).done(function (response) {
                $(global_data.patient_visit_button).button("reset");
                if (IsJsonString(response)) {
                    response = JSON.parse(response);
                    if (response.result === "success") {
                        let root = $("#modal_add_patient_visit");

                        $(root).find("span#visit_slot_1").text(response.data.allocations.slot1);
                        $(root).find("span#visit_slot_2").text(response.data.allocations.slot2);
                        $(root).find("span#visit_slot_3").text(response.data.allocations.slot3);
                        $(root).find("#record_id").val(response.data.record_id);
                        $(root).find("#physician_name").html(response.data.physician_name);
                        setTimeout(function () {
                            if (selection_calendar_monthmode) {
                                selection_calendar_monthmode.fullCalendar("destroy");
                            }
                            selection_calendar_monthmode = $("#selection_calendar_monthmode").fullCalendar({
                                header: {
                                    left: 'prev,next today',
                                    center: 'title',
                                    right: ''
                                },
                                height: 500,
                                defaultView: 'month',
                                dayClick: function (date, jsEvent, view) {
//                                    alert("day is clicked");
                                    date = date.format();
                                    set_weekday_calendar(date);
                                },
                                eventClick: function (param) {
//                                    alert("event is clicked");
                                    date = param.start.format("YYYY-MM-DD");
                                    set_weekday_calendar(date);
                                },
                                navLinks: true, // can click day/week names to navigate views
                                eventLimit: true // allow "more" link when too many events
                            });
                            set_blocks_in_calendar();
                        }, 1000);
//                        view("modal_add_patient_visit");
                    } else {
                        $(".modal").modal("hide");
                        error(response.message);
                    }
                } else {
                    error("Unexpected Error Occured");
                }
            });
        });

        $("#btn_add_patient_visit").on("click", function () {
            form = $("#form_add_patient_visit");
            form.find("#id").val(global_data.referral_id);
            data = form.serialize();
            url = base + "referral/add_patient_visit";
            $("#btn_add_patient_visit").button("loading");
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                $("#btn_add_patient_visit").button("reset");
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    if (data == true) {
                        $(".modal").modal("hide");
                        success("Patient visit has been successfully added", "Visit added");
                        get_latest_dashboard_counts();
                        $("#form_add_patient_visit")[0].reset();
                    } else {
                        error(data);
                    }
                } else {
                    error("Unexpected Error Occured");
                }
            });
        });

        $("#btn_back_to_month_view").on("click", function () {
            $(this).hide();
            if (selection_calendar_weekmode) {
                selection_calendar_weekmode.fullCalendar("destroy");
            }
            if (selection_calendar_monthmode) {
                selection_calendar_monthmode.fullCalendar("destroy");
            }
            setTimeout(function () {
                selection_calendar_monthmode = $("#selection_calendar_monthmode").fullCalendar({
                    header: {
                        left: 'prev,next today',
                        center: 'title',
                        right: ''
                    },
                    defaultView: 'month',
                    dayClick: function (date, jsEvent, view) {
                        date = date.format();
                        set_weekday_calendar(date);
                    },
                    eventClick: function (param) {
                        date = param.start.format("YYYY-MM-DD");
                        set_weekday_calendar(date);
                    },
                    navLinks: true, // can click day/week names to navigate views
                    eventLimit: true // allow "more" link when too many events
                });
                set_blocks_in_calendar();
            }, 1000);
        });


        $("#selection_calendar_monthmode").on("click", ".fc-button", function () {
            set_blocks_in_calendar();
        });

        $("#selection_calendar_weekmode").on("click", ".fc-button", function () {
            set_weekly_calendar2();
        });
    });

    // *************************************
    //     for patient visit script over
    // *************************************

    //for reason referral
    var x_reason = 0; //initlal text box count
    function add_reason(text) {
        if (x_reason < 1) { //max input box allowed
            x_reason++; //text box increment
            wrapper = $("#btn_add_reason").closest("div.wrapper_div").find(".edit_reasons");

            $(wrapper).append('<div><div class="checkbox"><label><input type="checkbox" name="reasons[]" value="' + text + '" checked><span class="cr"><i class="cr-icon fa fa-check"></i><i class="bully" style="display: none;">-</i></span><input type="text" placeholder="Type your text" value="' + text + '" class="dummy_checkbox"/></label><a href="#" class="remove_field edit_reason">&nbsp;&nbsp;<i class="fa fa-minus-circle" aria-hidden="true"></i></a></div></div>'); //add input box
        }
    }

    function get_clinic_physicians() {
        form = $("#sample_form");
        url = base + "referral/get_clinic_physicians";
        data = form.serialize();
        $.post({
            url: url,
            data: data
        }).done(function (response) {
            if (IsJsonString(response)) {
                data = JSON.parse(response);
                options = "<option value='unassign'>Unassigned</option>";
                for (i = 0; i < data.length; i++) {
                    options += "<option value='" + data[i].id + "'>" + data[i].physician_name + "</option>";
                }
                $("#assigned_physician").html(options);
            }
        });
    }

    $(document).ready(function () {
        $("#li_accepted").addClass("active");

        get_clinic_physicians();

        $("table").on("click", ".db-table-link-row", function () {
            location.href = $(this).data("href");
        });

        $("#btn_view_add_new_patient").on("click", function () {
            $("#modal_add_new_patient").modal("show");
        });

        $("#form_add_patient").on("click", "#btn_add_reason", function (e) {
            //on add input button click
            add_reason("");
        });
        $("#form_add_patient").on("click", ".edit_reason.remove_field", function (e) {
            //user click on remove text
            $(this).closest("div.checkbox").remove();
            x_reason--;
        });

        $("#btn_add_new_patient").on("click", function () {
            form = $("#form_add_patient");
            data = $(form).serialize();
            url = base + "accepted/new_referral";

            $.post({
                url: url,
                data: data
            }).done(function (response) {
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    if (data == true) {
                        $(".modal").modal("hide");
                        success("Patient Successfully Added");
                        global_data.table_accepted.ajax.reload();
                        get_latest_dashboard_counts();
                        $("#form_add_patient")[0].reset();
                    } else {
                        error(data);
                    }
                } else {
                    error("Unexpected Error Occured");
                }
            });
        });



        $.dobPicker({
            daySelector: '#form_add_patient #pat_dob_day', /* Required */
            monthSelector: '#form_add_patient #pat_dob_month', /* Required */
            yearSelector: '#form_add_patient #pat_dob_year', /* Required */
            dayDefault: 'Day', /* Optional */
            monthDefault: 'Month', /* Optional */
            yearDefault: 'Year', /* Optional */
            minimumAge: 0, /* Optional */
            maximumAge: 120 /* Optional */
        });

        // $('table#table_accepted').on("click", ".accepted_row", function() {
        //     let id = $(this).data('id');
        //     location.href = base + "accepted/referral_details/" + id;
        // });
        $('#modal_add_patient_visit').on('hidden.bs.modal', function () {
            $("#modal_add_patient_visit").find("#form_add_patient_visit")[0].reset();
            $("#btn_back_to_month_view").hide();
            if (selection_calendar_monthmode) {
                selection_calendar_monthmode.fullCalendar("destroy");
            }
            if (selection_calendar_weekmode) {
                selection_calendar_weekmode.fullCalendar("destroy");
            }
            $("#selected_datetime").html("");
        });

        //  *** Accepted Datatable
        global_data.table_accepted_title = "Accepted Referral";
        global_data.table_accepted = $("#table_accepted").DataTable({
            "processing": true,
            "serverSide": true,
            "autoWidth": false,
            "pageLength": 50,
            "language": {
                "emptyTable": "There are no patients to be scheduled",
                "info": "Showing _START_ to _END_ of _TOTAL_ " + global_data.table_accepted_title,
                "infoEmpty": "No results found",
                "infoFiltered": "(filtered from _MAX_ total " + global_data.table_accepted_title + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Show _MENU_ ",
                "loadingRecords": "Loading " + global_data.table_accepted_title,
                "processing": "Processing " + global_data.table_accepted_title,
                "search": "",
                "zeroRecords": "No matching " + global_data.table_accepted_title + " found"
            },
            "ajax": "<?php echo base_url(); ?>accepted/ssp_accepted",
            "rowCallback": function (row, data, index) {
                $('td:eq(4)', row).html(set_row4(data[5], row));
                $('td:eq(3)', row).html(set_missing_status(data[3], data[4]));
//                $('td:eq(3)', row).html(set_missing_status(data[3], data[4]) + data[7]);
            },
            "dom": get_dom_plan(),
            // "drawCallback": set_patients_table,
//            "columnDefs": [
//                {"width": "20%", "targets": 0},
//                {"width": "20%", "targets": 1},
//                {"width": "20%", "targets": 2},
//                {"width": "20%", "targets": 3},
//                {"width": "20%", "targets": 4}
//            ],
            "order": []
        });
        $("#table_accepted").wrap('<div class="table-responsive"></div>');
        // $("#btn_view_add_patient").appendTo("#patients_table_wrapper .button-placeholder");
        $(".dataTables_filter input").before('<i class="fa fa-search datatable-search"></i>');
        $(".dataTables_filter input").attr('placeholder', 'Search');
        //  *** Accepted Datatable Over

    });

    $(document).ready(function () {
        $("#tab_week").click(function () {
            $("#tab_month").removeClass('active');
            $(this).addClass('active');
            $("#selection_calendar_monthmode").fullCalendar("destroy");
            $("#selection_calendar_weekmode").show();

            var date = '';

            //remove month calendar
            if (selection_calendar_monthmode) {
                selection_calendar_monthmode.fullCalendar("destroy");
            }

            //add week calendar
            selection_calendar_weekmode = $("#selection_calendar_weekmode").fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                defaultView: 'agendaWeek',
                height: 500,

                eventClick: function (param) {
                    start_datetime = param.start.format("YYYY-MM-DD kk:mm:ss");
                    end_datetime = param.end.format("YYYY-MM-DD kk:mm:ss");
                    display_datetime = param.start.format("dddd, MMMM Do") + " at " + param.start.format("LT");
                    $("#selected_datetime").text(display_datetime);
                    $("#selected_starttime").val(start_datetime);
                    $("#selected_endtime").val(end_datetime);
                    $(".selected-event").removeClass("selected-event");
                    $(this).addClass("selected-event");
                },
                navLinks: true, // can click day/week names to navigate views
                eventLimit: true // allow "more" link when too many events
            });
            selection_calendar_weekmode.fullCalendar("gotoDate");
            selection_calendar_weekmode.fullCalendar("changeView", "agendaWeek");
            view_mode = selection_calendar_weekmode.fullCalendar('getView');
            start = view_mode.start.format("YYYY-MM-DD");
            end = view_mode.end.format("YYYY-MM-DD");


            //fetch and plot events
            form = $("form#sample_form");
            form.find("#id").val($("select#physicians").val());
            form.find("#target").val(start);
            form.find("#param").val(end);
            form.find("#id").val(global_data.referral_id);

            data = form.serialize();
            url = base + "referral/get_patient_visit_calendar_week_view";

            $.post({
                url: url,
                data: data,
                success: function (response) {
                    if (IsJsonString(response)) {
                        response = JSON.parse(response);
                        if (response.result === "success") {
                            $("#btn_back_to_month_view").show();
                            data = response.data;
                            selection_calendar_weekmode.fullCalendar("removeEvents");
                            global_data.events = [];
                            for (i = 0; i < data.length; i++) {
                                event_date = data[i].start_time.substr(0, 10);
                                event_start = data[i].start_time.substr(11, 8);
                                event_end = data[i].end_time.substr(11, 8);

                                event = {
                                    title: "",
                                    start: event_date + "T" + event_start,
                                    end: event_date + "T" + event_end,
                                    allDay: false,
                                    className: ["event", "bg-color-greenLight"]
                                };
                                global_data.events.push(event);
                            }
                            for (i = 0; i < global_data.events.length; i++) {
                                selection_calendar_weekmode.fullCalendar('renderEvent', global_data.events[i]);
                            }

                            setTimeout(function () {
                                $("#selection_calendar_weekmode .fc-today-button").on("click", function () {
                                    set_weekly_calendar2();
                                });
                            }, 1000);
                        }
                    }
                },
                error: function () {
                    error("Internal server error");
                }
            });
        });

        $("#tab_month").click(function () {
            $("#tab_week").removeClass('active');
            $(this).addClass('active');
            $("#selection_calendar_weekmode").hide();

            //$("#selection_calendar_weekmode").fullCalendar("destroy");

            /*if (selection_calendar_weekmode) {
             selection_calendar_weekmode.fullCalendar("destroy");
             }*/
            if (selection_calendar_monthmode) {
                selection_calendar_monthmode.fullCalendar("destroy");
            }

            selection_calendar_monthmode = $("#selection_calendar_monthmode").fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                height: 500,
                defaultView: 'month',
                dayClick: function (date, jsEvent, view) {
                    date = date.format();
                    set_weekday_calendar(date);
                },
                eventClick: function (param) {
                    date = param.start.format("YYYY-MM-DD");
                    set_weekday_calendar(date);
                },
                navLinks: true, // can click day/week names to navigate views
                eventLimit: true // allow "more" link when too many events
            });
            set_blocks_in_calendar();

            setTimeout(function () {

            }, 1000);
        });
    });
</script> 
