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

    /*-------- Popup changes --------*/
    #timeslot-sp {
        color: red;
        margin-left: 10px;
        font-size: 14px;
    }
    #physician_name {
        font-size: 18px;
    }
    span#selected_datetime {
        float: left;
        margin: 10px 0 0 10px;
        color: red;
    }
    .tab-sec {
        position: initial;
        float: right;
    }
    .tab-sec ul.nav.nav-pills li {
        margin-left: -1px !important;
    }
    .btn-normal {
        padding: 5px 10px !important;
        border-radius: 0 !important;
    }
    .modal-body {
        margin-bottom: 20px;
    }
    form#form_add_patient_visit .radio {
        margin-top: 0px;
        margin-bottom: 5px;
    }
</style>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/fullcalendar.min.css"> 

<!-- Full Calendar JS-->
<script src="<?php echo base_url(); ?>assets/js/moment.min.js" ></script>
<script src="<?php echo base_url(); ?>assets/js/fullcalendar.min.js" ></script>
<script>

    //*******************************************
    // script for records
    //*******************************************



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
                            event_class = (slots === 0) ? "bg-color-grey" : "bg-color-greenLight";

                            event = {
                                title: slots + " slots available",
                                start: cur_day_ymd,
                                allDay: true,
                                className: ["event", "big-event-block", event_class]
                            };
                            global_data.events.push(event);
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
            height: 500,
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
                    }
                }
            },
            error: function () {
                error("Internal server error");
            }
        });
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

    $(document).ready(function () {
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


//        $("#form_add_patient_visit").on("change", "input[name='cell_phone_voice']", function () {
//            if ($(this).prop("checked") && !global_data.sms_notification_allowed) {
//                $(this).prop("checked", false);
//                error("Please add phone number for patient first.");
//            }
//        });
//        $("#form_add_patient_visit").on("change", "input[name='cell_phone']", function () {
//            if ($(this).prop("checked") && !global_data.sms_notification_allowed) {
//                $(this).prop("checked", false);
//                error("Please add phone number for patient first.");
//            }
//        });
//        $("#form_add_patient_visit").on("change", "input[name='email']", function () {
//            if ($(this).prop("checked") && !global_data.email_notification_allowed) {
//                $(this).prop("checked", false);
//                error("Please add email-id for patient first.");
//            }
//        });

        $("#btn_view_add_patient_visit").on("click", function () {
            $("#modal_add_patient_visit").find("#form_add_patient_visit")[0].reset();
            global_data.patient_visit_button = $("#btn_view_add_patient_visit");
            global_data.patient_visit_button.button("loading");

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
                    //console.log('response ==== ',response);
                    if (response.result === "success") {
//                        $(".modal").modal("hide");
                        let root = $("#modal_add_patient_visit");
                        var timeslotsp = "<span class='timeslot-sp' id='timeslot-sp'>60 minute time slots</span>";

                        $(root).find("span#visit_slot_1").text(response.data.allocations.slot1);
                        $(root).find("span#visit_slot_2").text(response.data.allocations.slot2);
                        $(root).find("span#visit_slot_3").text(response.data.allocations.slot3);
                        $(root).find("#record_id").val(response.data.record_id);
                        $(root).find("#physician_name").html(response.data.physician_name+timeslotsp);
                      
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
                        }, 1000);
                        view("modal_add_patient_visit");
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


    function set_id_row(id, row, row_class) {
        $(row).attr("data-id", id);
        $(row).addClass('db-table-link-row');
        $(row).addClass(row_class);
    }
    $(document).ready(function () {
        $("#btn_view_add_health_record").on("click", function () {
            view("modal_add_health_record");
            $("#form_health_record")[0].reset();
        });
        $("#btn_view_add_admin_note").on("click", function () {
            view("modal_add_admin_note");
            $("#form_add_admin_note")[0].reset();
        });

        $('#modal_add_health_record').on('hidden.bs.modal', function () {
            if (myDropzone !== undefined) {
                myDropzone.removeAllFiles();
            }
        });

        $('#modal_add_health_record').on('shown.bs.modal', function () {
            //Simple Dropzonejs 
            myDropzone = new Dropzone("#dropzone_health_record", {
                maxFilesize: 100,
                url: base + 'referral/add_health_record',
                addRemoveLinks: true,
                autoProcessQueue: false,
                uploadMultiple: true,
                paramName: 'asdqwe',
                parallelUploads: 100,
                maxFiles: 1,
                acceptedFiles: "application/pdf",
                init: function () {
                    var myDropzone = this;
                    $("#btn_save_health_record").on("click", function (e) {
                        $("#btn_save_health_record").button("loading");
                        e.preventDefault();
                        e.stopPropagation();
                        var form = $('#form_health_record');
                        if (form.valid() == true) {
                            if (myDropzone.getQueuedFiles().length > 0) {
                                myDropzone.processQueue();
                            } else {
                                var blob = new Blob();
                                blob.upload = {'chunked': myDropzone.defaultOptions.chunking};
                                myDropzone.uploadFile(blob);
                            }
                        }
                    });
                    this.on("sendingmultiple", function (files, xhr, formData) {
                        // Gets triggered when the form is actually being sent.
                        // Hide the success button or the complete form.
                        $('#form_health_record').find("#id").val(global_data.referral_id);
                        formData.append('<?php echo $this->security->get_csrf_token_name(); ?>'
                                , '<?php echo $this->security->get_csrf_hash(); ?>');
                        //add other form data
                        $("#form_health_record").find("select, textarea, input[type!='file']").each(function (index, value) {
                            name = $(value).attr("name");
                            val = ($(value).val() != null) ? $(value).val() : "";
                            formData.append(name, val);
                        });
                    });
                    this.on("successmultiple", function (files, response) {
                        // Gets triggered when the files have successfully been sent.
                        // Redirect user or notify of success.
                        $("#btn_save_health_record").button("reset");
                        myDropzone.removeAllFiles();
                        if (IsJsonString(response)) {
                            data = JSON.parse(response);
                            if (data == true) {
                                $(".modal").modal("hide");
                                success("Health record has been successfully saved", "Health record saved");
                                global_data.table_health_records.ajax.reload();
                            } else {
                                error(data);
                            }
                        } else
                            error("Unexpected Error Occured");
                    });
                }
            });
        });
        $("#add_admin_note").on("click", function () {
            form = $("#form_add_admin_note");
            form.find("#id").val(global_data.referral_id);
            data = form.serialize();
            url = base + "referral/add_admin_note";
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    if (data == true) {
                        $(".modal").modal("hide");
                        // success("Admin Note Successfully Created");
                        global_data.table_admin_notes.ajax.reload();
                    } else {
                        error(data);
                    }
                } else {
                    error("Unexpected Error Occured");
                }
            });
        });

        $("#btn_update_patient_visit").on("click", function () {
            $("#btn_update_patient_visit").button("loading");
            form = $("#form_update_patient_visit");
            form.find("#id").val(global_data.referral_id);
            data = form.serialize();
            url = base + "referral/update_patient_visit";
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                $("#btn_update_patient_visit").button("reset");
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    if (data == true) {
                        $(".modal").modal("hide");
                        success("Patient Visit Successfully Updated");
                        global_data.table_patient_visits.ajax.reload();
                    } else {
                        error(data);
                    }
                } else {
                    error("Unexpected Error Occured");
                }
            });
        });
        $("#btn_cancel_visit").on("click", function () {
            form = $("#form_update_patient_visit");
            form.find("#id").val(global_data.referral_id);
            data = form.serialize();
            url = base + "referral/cancel_patient_visit";
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    if (data == true) {
                        $(".modal").modal("hide");
                        success("Patient Visit Successfully Cancelled");
                        global_data.table_patient_visits.ajax.reload();
                    } else {
                        error(data);
                    }
                } else {
                    error("Unexpected Error Occured");
                }
            });
        });
        $("#btn_view_uploading_doc").on("click", function () {
//            debugger
            //
            // global_data.pdf_file = base + "uploads/efax/" + file_name;
            // global_data.efax_id = id;
            // PDFObject.embed(global_data.pdf_file, "#example1");
            // view("modal_document_viewer");
        });
        $("table").on("click", ".health_records_row", function () {
            form = $("#sample_form");
            form.find("#id").val(global_data.referral_id);
            form.find("#target").val($(this).data("id"));
            url = base + "referral/get_health_record_info";
            data = form.serialize();
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    data = data[0];
                    root = $("#modal_view_health_record");
                    root.find("#record_type").html(data.record_type);
                    root.find("#description").html(data.description);
                    if (data.record_file != "") {
                        global_data.uploaded_file_path = base + "uploads/clinics/" +
                                global_data.clinic_id + "/" + global_data.referral_id + "/" +
                                data.record_file + ".pdf";
                        PDFObject.embed(base + "uploads/clinics/" +
                                global_data.clinic_id + "/" + global_data.referral_id + "/" +
                                data.record_file + ".pdf", "#pdf_view_div");
                    } else {
                        root.find("#btn_view_uploaded_doc").hide();
                    }
                    view("modal_view_health_record");
                } else {
                    error("Unexpected Error Occured");
                }
            });
        });
        $("#btn_print_uploaded_pdf").on("click", function () {
            printJS(global_data.uploaded_file_path);
        });
        $("table").on("click", ".admin_notes_row", function () {
            form = $("#sample_form");
            form.find("#id").val(global_data.referral_id);
            form.find("#target").val($(this).data("id"));
            url = base + "referral/get_admin_notes_info";
            data = form.serialize();
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    data = data[0];
                    root = $("#modal_view_admin_note");
                    root.find("#note_type").val(data.note_type);
                    root.find("#description").val(data.description);
                    view("modal_view_admin_note");
                } else {
                    error("Unexpected Error Occured");
                }
            });
        });
        $("table").on("click", ".patient_visits_row", function () {
            $("#form_update_patient_visit")[0].reset();
            global_data.patient_visit_id = $(this).data("id");

            form = $("#sample_form");
            form.find("#id").val(global_data.referral_id);

            data = form.serialize();
            url = base + "referral/get_visit_allocation_for_manual_visit";

            $.post({
                url: url,
                data: data
            }).done(function (response) {
                if (IsJsonString(response)) {
                    response = JSON.parse(response);
                    if (response.result === "success") {
                        $(".modal").modal("hide");
                        let root = $("#modal_view_patient_visit");
                        $(root).find("span#visit_slot_1").text(response.data.allocations.slot1);
                        $(root).find("span#visit_slot_2").text(response.data.allocations.slot2);
                        $(root).find("span#visit_slot_3").text(response.data.allocations.slot3);
                        $(root).find("#record_id").val(response.data.record_id);
                        $(root).find("#target").val(global_data.patient_visit_id);
                        view("modal_view_patient_visit");
                    }
                } else {
                    error("Unexpected Error Occured");
                }
            });
        });

        //  *** Health Records Datatable
        global_data.table_health_records_title = "Health Records";
        global_data.table_health_records = $("#table_health_records").DataTable({
            "order": false, //[[ 2, "desc" ]],
            "processing": true,
            "serverSide": true,
            "autoWidth": false,
            "language": {
                "emptyTable": "There are no " + global_data.table_health_records_title,
                "info": "Showing _START_ to _END_ of _TOTAL_ " + global_data.table_health_records_title,
                "infoEmpty": "No results found",
                "infoFiltered": "(filtered from _MAX_ total " + global_data.table_health_records_title + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Show _MENU_ ",
                "loadingRecords": "Loading " + global_data.table_health_records_title,
                "processing": "Processing " + global_data.table_health_records_title,
                "search": "",
                "zeroRecords": "No matching " + global_data.table_health_records_title + " found"
            },
            "ajax": "<?php echo base_url(); ?>referral/ssp_health_records/" + global_data.referral_id,
            "rowCallback": function (row, data, index) {
                $('td:eq(3)', row).html(
                        set_id_row(data[3], row, "health_records_row")
                        );
            },
            "dom": get_records_dom_plan(),
            // "drawCallback": set_patients_table,
            "columnDefs": [
                {"width": "20%", "targets": 0},
                {"width": "55%", "targets": 1},
                {"width": "25%", "targets": 2}
            ]
        });
        $("#table_health_records").wrap('<div class="table-responsive"></div>');
        // $("#btn_view_add_patient").appendTo("#patients_table_wrapper .button-placeholder");
        $("#table_health_records_wrapper").find(".dataTables_filter input").before('<i class="fa fa-search datatable-search"></i>');
        $("#table_health_records_wrapper").find(".dataTables_filter input").attr('placeholder', 'Search');
        //  *** Health Records Datatable Over
        //  *** Admin Notes Datatable
        global_data.table_admin_notes_title = "Admin Notes";
        global_data.table_admin_notes = $("#table_admin_notes").DataTable({
            "processing": true,
            "serverSide": true,
            "autoWidth": false,
            "language": {
                "emptyTable": "There are no " + global_data.table_admin_notes_title,
                "info": "Showing _START_ to _END_ of _TOTAL_ " + global_data.table_admin_notes_title,
                "infoEmpty": "No results found",
                "infoFiltered": "(filtered from _MAX_ total " + global_data.table_admin_notes_title + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Show _MENU_ ",
                "loadingRecords": "Loading " + global_data.table_admin_notes_title,
                "processing": "Processing " + global_data.table_admin_notes_title,
                "search": "",
                "zeroRecords": "No matching " + global_data.table_admin_notes_title + " found"
            },
            "ajax": "<?php echo base_url(); ?>referral/ssp_admin_notes/" + global_data.referral_id,
            "rowCallback": function (row, data, index) {
                $('td:eq(3)', row).html(
                        set_id_row(data[3], row, "admin_notes_row")
                        );
            },
            "dom": get_records_dom_plan(),
            // "drawCallback": set_patients_table,
            "columnDefs": [
                {"width": "25%", "targets": 0},
                {"width": "50%", "targets": 1},
                {"width": "25%", "targets": 2}
            ]
        });
        $("#table_admin_notes").wrap('<div class="table-responsive"></div>');
        // $("#btn_view_add_patient").appendTo("#patients_table_wrapper .button-placeholder");
        $("#table_admin_notes_wrapper").find(".dataTables_filter input").before('<i class="fa fa-search datatable-search"></i>');
        $("#table_admin_notes_wrapper").find(".dataTables_filter input").attr('placeholder', 'Search');
        //  *** Admin Notes Datatable Over
        //  *** Patient Visits Datatable
        global_data.table_patient_visits_title = "Patient Visits";
        global_data.table_patient_visits = $("#table_patient_visits").DataTable({
            "processing": true,
            "serverSide": true,
            "autoWidth": false,
            "language": {
                "emptyTable": "There are no " + global_data.table_patient_visits_title,
                "info": "Showing _START_ to _END_ of _TOTAL_ " + global_data.table_patient_visits_title,
                "infoEmpty": "No results found",
                "infoFiltered": "(filtered from _MAX_ total " + global_data.table_patient_visits_title + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Show _MENU_ ",
                "loadingRecords": "Loading " + global_data.table_patient_visits_title,
                "processing": "Processing " + global_data.table_patient_visits_title,
                "search": "",
                "zeroRecords": "No matching " + global_data.table_patient_visits_title + " found"
            },
            "ajax": "<?php echo base_url(); ?>referral/ssp_patient_visits/" + global_data.referral_id,
            "rowCallback": function (row, data, index) {
                $('td:eq(4)', row).html(set_id_row(data[5], row, "patient_visits_row"));
                $('td:eq(2)', row).html(set_visit_status(data[2], data[4]));
            },
            "dom": get_records_dom_plan(),
            // "drawCallback": set_patients_table,
            "columnDefs": [
                {"width": "25%", "targets": 0},
                {"width": "25%", "targets": 1},
                {"width": "25%", "targets": 2},
                {"width": "25%", "targets": 3}
            ]
        });
        $("#table_patient_visits").wrap('<div class="table-responsive"></div>');
        // $("#btn_view_add_patient").appendTo("#patients_table_wrapper .button-placeholder");
        $("#table_patient_visits_wrapper").find(".dataTables_filter input").before('<i class="fa fa-search datatable-search"></i>');
        $("#table_patient_visits_wrapper").find(".dataTables_filter input").attr('placeholder', 'Search');
        //  *** Patient Visits Datatable Over
    });

    $(document).ready(function () {
        $("#tab_week").click(function () {

            $("#tab_month").removeClass('active');
            $(this).addClass('active');
            $("#selection_calendar_monthmode").fullCalendar("destroy");
            $("#selection_calendar_weekmode").show();

            var date = '';

            var fullDate = new Date()
            
            //convert month to 2 digits
            var twoDigitMonth = ((fullDate.getMonth().length+1) === 1)? (fullDate.getMonth()+1) : '0' + (fullDate.getMonth()+1);
             
            var currentDate = fullDate.getFullYear() + "-" + twoDigitMonth + "-" + fullDate.getDate();            

            date = currentDate;

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
                height: 500,
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
            selection_calendar_weekmode.fullCalendar("gotoDate",date);
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

            if (selection_calendar_weekmode) {
                selection_calendar_weekmode.fullCalendar("destroy");
            }
            /*if (selection_calendar_monthmode) {
                selection_calendar_monthmode.fullCalendar("destroy");
            }*/
            //setTimeout(function () {
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
            //}, 1000);

        });
    });

</script>