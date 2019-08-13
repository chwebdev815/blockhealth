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
</style>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/fullcalendar.min.css"> 

<!-- Full Calendar JS-->
<script src="<?php echo base_url(); ?>assets/js/moment.min.js" ></script>
<script src="<?php echo base_url(); ?>assets/js/fullcalendar.min.js" ></script>
<script>
    availability_calendar = null;
    $(document).ready(function () {
        $("#second_menu_list").find("#li_schedule_settings").addClass("active");
        $("#submitButton").click();
        get_clinic_physicians();

        $("#clinic_physician_container").find("#physicians").on("change", function () {
            get_physician_weekdays();
        });

        $("#btn_confirm_update_weekdays").on("click", function () {
            view("modal_confirm_update_physician_weekday");
        });

        $("#btn_update_physician_weekday").on("click", function () {
            set_calendar_retrieve();
            
            $("form#form_physician_weekdays").find("#id").val($("select#physicians").val());
            data = $("form#form_physician_weekdays").serialize();
            url = base + "schedule_settings/update_physician_weekdays";
            $.post({
                url: url,
                data: data,
                success: function (response) {
                    if (IsJsonString(response)) {
                        response = JSON.parse(response);
                        if (response.result === "success") {
                            get_physician_weekdays();
//                            success("Weekdays for selected physician are updated");
                        } else {
                            error(response.message);
                        }
                    }
                },
                error: function () {
                    error("Internal server error");
                }
            });
        });

        $("#weekday").on("change", function () {
            get_weekday_timing();
        });

        $("#btn_confirm_apply_time").on("click", function () {
            view("modal_confirm_update_weekday_timing");
        });
        $("#btn_confirm_apply_time_all").on("click", function () {
            view("modal_confirm_update_weekday_timing_all");
        });

        $("#btn_update_weekday_timing").on("click", function () {
            form = $("#form_physician_timing");
            global_data.calendar_retrieve = {};
            global_data.calendar_retrieve.weekday = form.find("#weekday").val();
            global_data.calendar_retrieve.calendar_start_date =
                    availability_calendar.fullCalendar('getView').start.format("YYYY-MM-DD");
            global_data.calendar_retrieve.calendar_end_date =
                    availability_calendar.fullCalendar('getView').end.format("YYYY-MM-DD");
            global_data.calendar_retrieve.calendar_view =
                    availability_calendar.fullCalendar('getView').name;
            console.log("sending data = ", global_data.calendar_retrieve);

            form.find("#id").val($("select#physicians").val());
            data = form.serialize();
            url = base + "schedule_settings/update_weekday_timing";
            $.post({
                url: url,
                data: data,
                success: function (response) {
                    if (IsJsonString(response)) {
                        response = JSON.parse(response);
                        if (response.result === "success") {
                            if (global_data.calendar_retrieve) {
//                                availability_calendar.fullCalendar("changeView", "agendaDay", "2017-06-01");
//                                availability_calendar.fullCalendar('option', 'visibleRange', {
//                                    "start": '01-04-2019',
//                                    "end": '05-04-2019'
//                                });

                            }
                            get_physician_weekdays();
//                            success("Timing for selected day is updated");
                        } else {
                            error(response.message);
                        }
                    }
                },
                error: function () {
                    error("Internal server error");
                }
            });
        });

        $("#btn_update_weekday_timing_all").on("click", function () {
            set_calendar_retrieve();
            form = $("#form_physician_timing");
            form.find("#id").val($("select#physicians").val());
            data = form.serialize();
            url = base + "schedule_settings/update_weekday_timing_all";
            $.post({
                url: url,
                data: data,
                success: function (response) {
                    if (IsJsonString(response)) {
                        response = JSON.parse(response);
                        if (response.result === "success") {
                            get_physician_weekdays();
//                            success("Timings for all days are updated");
                        } else {
                            error(response.message);
                        }
                    }
                },
                error: function () {
                    error("Internal server error");
                }
            });
        });

        $("#btn_add_blocked_time_fields").on("click", function () {
            add_blocked_time_fields();
        });

        $("#availability_calendar").on("click", ".fc-button", function () {
            set_blocks_in_calendar();
        });

        $("#btn_apply_blocked_time_for_day").on("click", function () {
            set_calendar_retrieve();
            
            form = $("form#form_day_specific_blocking");
            form.find("#id").val($("select#physicians").val());
            form.find("#type").val("timeblock");
            data = form.serialize();
            url = base + "schedule_settings/set_day_specific_blocking";
            $.post({
                url: url,
                data: data,
                success: function (response) {
                    if (IsJsonString(response)) {
                        response = JSON.parse(response);
                        if (response.result === "success") {
                            $("#modal_set_day_blocks").modal("hide");
                            get_physician_weekdays();
                        } else {
                            error(response.message);
                        }
                    }
                },
                error: function () {
                    error("Internal server error");
                }
            });
        });

        $("#btn_block_day").on("click", function () {
            set_calendar_retrieve();
            
            form = $("form#form_day_specific_blocking");
            form.find("#id").val($("select#physicians").val());
            form.find("#type").val("dayblock");
            data = form.serialize();
            url = base + "schedule_settings/set_day_specific_blocking";
            $.post({
                url: url,
                data: data,
                success: function (response) {
                    if (IsJsonString(response)) {
                        response = JSON.parse(response);
                        if (response.result === "success") {
                            $("#modal_set_day_blocks").modal("hide");
                            get_physician_weekdays();
                        } else {
                            error(response.message);
                        }
                    }
                },
                error: function () {
                    error("Internal server error");
                }
            });
        });

        $("form#form_day_specific_blocking").on("click", ".remove_timeslot", function () {
            $(this).closest(".row").remove();
        });


        //weekday template script starts
        $("#btn_add_weekday_block").on("click", function () {
            add_weekday_blocked_time_fields();
            set_weekday_block_counter();
        });

        $("#weekday_additional_blocks").on("click", ".btn_remove_weekday_block", function () {
            $(this).closest(".ptrowblock").remove();
            set_weekday_block_counter();
        });
    });

    function set_weekday_block_counter() {
        $(".lbl_weekday_block_counter").each(function (index, element) {
            $(element).html(index + 1);
        });
    }

    function set_calendar_retrieve() {
        form = $("#form_physician_timing");
        global_data.calendar_retrieve = {};
        global_data.calendar_retrieve.weekday = form.find("#weekday").val();
        global_data.calendar_retrieve.calendar_start_date =
                availability_calendar.fullCalendar('getView').start.format("YYYY-MM-DD");
        global_data.calendar_retrieve.calendar_end_date =
                availability_calendar.fullCalendar('getView').end.format("YYYY-MM-DD");
        global_data.calendar_retrieve.calendar_view =
                availability_calendar.fullCalendar('getView').name;
        console.log("sending data = ", global_data.calendar_retrieve);
    }

    global_data.weekdays = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
    global_data.events = [];
    function set_availability_calendar() {
        form = $("form#form_physician_timing");
        form.find("#id").val($("select#physicians").val());
        data = form.serialize();
        url = base + "schedule_settings/get_all_weekday_timing";
        $.post({
            url: url,
            data: data,
            success: function (response) {
                global_data.events = [];
                if (IsJsonString(response)) {
                    response = JSON.parse(response);
                    if (response.result === "success") {
                        data = response.data[0];
                        global_data.calendar_reference_data = data;
                        if (availability_calendar) {
                            availability_calendar.fullCalendar("destroy");
                        }
                        availability_calendar = $("#availability_calendar").fullCalendar({
                            header: {
                                left: 'prev,next today',
                                center: 'title',
                                right: 'month,agendaWeek,agendaDay,listWeek'
                            },
                            defaultView: 'listWeek',
                            dayClick: function (param) {
                                console.log("Day is clicked ");
                                console.log(param);
                            },
                            eventClick: function (param) {
                                console.log("Event is clicked ");
                                console.log(param);

                                if (param) {
                                    day = param.start.format("MMM DD, YYYY");
                                    day2 = param.start.format("YYYY-MM-DD");
                                    weekday = global_data.weekdays[param.start.weekday()].substr(0, 3);
                                    enabled_day = global_data.calendar_reference_data[weekday];

                                    modal = $("#modal_set_day_blocks");
                                    modal.find("span#blocking_date_title").html(day);
                                    modal.find("#blocking_date").val(day2);
                                    $("#additional_blocks").html("");
                                    set_day_block_popup(day2, enabled_day);
                                }
                            },
                            navLinks: true, // can click day/week names to navigate views
                            eventLimit: true // allow "more" link when too many events
                        });
                        set_blocks_in_calendar();
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

    function set_day_block_popup(day, enabled_day) {
        form = $("#sample_form");
        form.find("#id").val($("select#physicians").val());
        form.find("#param").val(day);
        //btn_block_day
        global_data.enabled_day = enabled_day;
        console.log("enabled_day 1 = " + global_data.enabled_day);

        data = form.serialize();
        url = base + "schedule_settings/get_all_blocks";

        $.post({
            url: url,
            data: data,
            success: function (response) {
                if (IsJsonString(response)) {
                    response = JSON.parse(response);
                    if (response.result === "success") {
                        data = response.data;
                        root = $("#modal_set_day_blocks");
                        root.find("#day_start_time").val(data.day_data.timings.start_time.substr(0, 5));
                        root.find("#day_end_time").val(data.day_data.timings.end_time.substr(0, 5));

                        blocks_to_show = [];

                        day_block_data = data.day_block_data;
                        for (i = 0; i < day_block_data.length; i++) {
                            if (day_block_data[i].type === "timeblock") {
                                blocks_to_show.push({
                                    "start_time": day_block_data[i].start_time.substr(0, 5),
                                    "end_time": day_block_data[i].end_time.substr(0, 5)
                                });
                            } else if (day_block_data[i].type === "dayblock") {
                                global_data.enabled_day = "no";
                            }
                        }

                        weekly_block_data = data.weekly_block_data;
                        for (i = 0; i < weekly_block_data.length; i++) {
                            blocks_to_show.push({
                                "start_time": weekly_block_data[i].start_time.substr(0, 5),
                                "end_time": weekly_block_data[i].end_time.substr(0, 5)
                            });
                        }

                        //reorder blocks, sort the array
                        for (i = 0; i < blocks_to_show.length; i++) {
                            for (j = i + 1; j < blocks_to_show.length; j++) {
                                console.log("comparing " + blocks_to_show[i].start_time + " with " +
                                        blocks_to_show[j].start_time);
                                if (blocks_to_show[i].start_time > blocks_to_show[j].start_time) {
                                    tmp = blocks_to_show[i];
                                    blocks_to_show[i] = blocks_to_show[j];
                                    blocks_to_show[j] = tmp;
                                }
                            }
                        }

                        //manage continuous blocks
                        tmp_blocks_to_show = [];
                        tmp_start = null;
                        tmp_end = null;
                        take_new_start = true;
                        for (i = 0; i < blocks_to_show.length; i++) {
                            if (take_new_start) {
                                tmp_start = blocks_to_show[i].start_time;
                                take_new_start = false;
                            }
                            if (i + 1 === blocks_to_show.length ||
                                    (blocks_to_show[i].end_time !== blocks_to_show[i + 1].start_time)) {
                                tmp_end = blocks_to_show[i].end_time;
                                tmp_blocks_to_show.push({
                                    "start_time": tmp_start,
                                    "end_time": tmp_end
                                });
                                take_new_start = true;
                            }
                        }
                        blocks_to_show = tmp_blocks_to_show;


                        //show blocks
                        for (i = 0; i < blocks_to_show.length; i++) {
                            add_blocked_time_fields(blocks_to_show[i].start_time,
                                    blocks_to_show[i].end_time);
                        }

                        if (global_data.enabled_day === "no") {
                            global_data.enabled_day = (data.day_data.type === "customized") ? "yes" : "no";
                            if (global_data.enabled_day === "no") {
                                modal.find("#btn_block_day").fadeOut();
                            } else {
                                modal.find("#btn_block_day").fadeIn();
                            }
                        } else {
                            modal.find("#btn_block_day").fadeIn();
                        }
                        view("modal_set_day_blocks");
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

    function set_blocks_in_calendar() {
        //get blocked time for the start and end days

        availability_calendar.fullCalendar("removeEvents");
        view_mode = availability_calendar.fullCalendar('getView');
        start = view_mode.start.format("YYYY-MM-DD");
        end = view_mode.end.format("YYYY-MM-DD");

        form = $("form#sample_form");
        form.find("#id").val($("select#physicians").val());
        form.find("#target").val(start);
        form.find("#param").val(end);

        data = form.serialize();
        url = base + "schedule_settings/get_physician_blocks";

        $.post({
            url: url,
            data: data,
            success: function (response) {
                if (IsJsonString(response)) {
                    response = JSON.parse(response);
                    if (response.result === "success") {
                        day_blocks = response.data.day_blocks;
                        weekly_blocks = response.data.weekly_blocks;
                        customized_data = response.data.customized_data;

                        availability_calendar.fullCalendar("removeEvents");
                        view_mode = availability_calendar.fullCalendar('getView');

                        //set events here
                        global_data.events = [];
                        start = moment(view_mode.start.format("YYYY-MM-DD"));
                        end = moment(view_mode.end.format("YYYY-MM-DD"));
                        cur_day = start;

                        counter = 1;

                        break_while = false;
                        do {

                            break_while = false;

                            //get weekday info
                            weekday = global_data.weekdays[cur_day.weekday()];
                            console.log("for weekday = " + weekday + " answer is " +
                                    global_data.calendar_reference_data[weekday]);



                            console.log("processing day = " + cur_day.format("YYYY-MM-DD"));
                            for (i = 0; i < day_blocks.length; i++) {
                                if (day_blocks[i].for_date === cur_day.format("YYYY-MM-DD")) {

                                    if (day_blocks[i].type === "dayblock") {
                                        event = {
                                            title: "Day blocked off",
                                            start: cur_day.format("YYYY-MM-DD"),
                                            allDay: true,
                                            className: ["event", "bg-color-red"]
                                        };
                                        global_data.events.push(event);
                                        break_while = true;
                                    }
                                }
                            }


                            //process and place blocks
                            if (!break_while) {

                                //divide and set events for blocks of  a day

                                //if customized day start and end time then check
                                day_start_time = null;
                                day_end_time = null;
                                day_enabled = false;
                                customization = false;

                                //add customization for day start time end time
                                cur_ymd = cur_day.format("YYYY-MM-DD");
                                for (i = 0; i < customized_data.length; i++) {
                                    console.log("comparing " + cur_ymd + " with " +
                                            customized_data[i].for_date);
                                    if (cur_ymd === customized_data[i].for_date) {
                                        console.log("customization is there at " + cur_ymd);
                                        day_start_time = customized_data[i].start_time;
                                        day_end_time = customized_data[i].end_time;
                                        day_enabled = customized_data[i];
                                        customization = true;
                                    }
                                }

                                //get blocks for day from data
                                blocks_for_day = [];

                                if (!customization) {
                                    console.log("processing weekday = " + weekday);
                                    for (i = 0; i < weekly_blocks.length; i++) {
                                        if (weekly_blocks[i].for_weekday === weekday) {
                                            blocks_for_day.push(weekly_blocks[i]);
                                        }
                                    }
                                }

                                for (i = 0; i < day_blocks.length; i++) {

                                    if (day_blocks[i].for_date === cur_day.format("YYYY-MM-DD")) {
                                        if (day_blocks[i].type === "timeblock") {
                                            blocks_for_day.push(day_blocks[i]);
                                        }
                                    }
                                }

                                console.log("blocks for day ==============> ", blocks_for_day);
                                //sort the array
                                for (i = 0; i < blocks_for_day.length; i++) {
                                    for (j = i + 1; j < blocks_for_day.length; j++) {
                                        console.log("comparing " + blocks_for_day[i].start_time + " with " +
                                                blocks_for_day[j].start_time);
                                        if (blocks_for_day[i].start_time > blocks_for_day[j].start_time) {
                                            tmp = blocks_for_day[i];
                                            blocks_for_day[i] = blocks_for_day[j];
                                            blocks_for_day[j] = tmp;
                                        }
                                    }
                                }

                                console.log("after blocks for day ==============> ", blocks_for_day);

                                //combine those where no timespan gap
                                console.log("before combining ===> ", blocks_for_day);
                                tmp_blocks_for_day = [];
                                tmp_start = null;
                                tmp_end = null;
                                take_new_start = true;
                                for (i = 0; i < blocks_for_day.length; i++) {
                                    if (take_new_start) {
                                        tmp_start = blocks_for_day[i].start_time;
                                        take_new_start = false;
                                    }
                                    if (i + 1 === blocks_for_day.length ||
                                            (blocks_for_day[i].end_time !== blocks_for_day[i + 1].start_time)) {
                                        tmp_end = blocks_for_day[i].end_time;
                                        tmp_blocks_for_day.push({
                                            "start_time": tmp_start,
                                            "end_time": tmp_end
                                        });
                                        take_new_start = true;
                                    }
                                }
                                blocks_for_day = tmp_blocks_for_day;
                                console.log("after combine ===> ", blocks_for_day);




                                //if allowed by weekday or day_enabled set
                                if (global_data.calendar_reference_data[weekday] === "yes" || day_enabled) {
                                    if (!day_start_time) {
                                        day_start_time = global_data.calendar_reference_data[weekday + "_start_time"];
                                        day_end_time = global_data.calendar_reference_data[weekday + "_end_time"];
                                    }

                                    pointer_available = day_start_time;
                                    pointer_unavailable = null;

                                    console.log("starting pointer_available = " + pointer_available);
                                    tmp_events = [];
                                    dayblock = false;

                                    if (blocks_for_day.length === 0) {
                                        console.log("available full day");
                                        event = {
                                            title: "Available",
                                            className: ["event", "bg-color-greenLight"],
                                            start: cur_day.format("YYYY-MM-DD") + "T" + day_start_time,
                                            end: cur_day.format("YYYY-MM-DD") + "T" + day_end_time
                                        };
                                        global_data.events.push(event);
                                    } else {
                                        for (i = 0; i < blocks_for_day.length; i++) {
                                            console.log("looping block ", blocks_for_day[i]);
                                            pointer_unavailable = blocks_for_day[i].start_time;
                                            console.log("pointer unavailable = " + pointer_unavailable);
                                            if (blocks_for_day[i].type === "dayblock") {
                                                console.log("day block found");
                                                event = {
                                                    title: "Day blocked off",
                                                    className: ["event", "bg-color-red"],
                                                    start: cur_day.format("YYYY-MM-DD") + "T" + day_start_time,
                                                    end: cur_day.format("YYYY-MM-DD") + "T" + day_end_time
                                                };
                                                global_data.events.push(event);
                                                dayblock = true;
                                                break;
                                            }


                                            if (pointer_available === pointer_unavailable) {
                                                console.log("pointers matched at " + pointer_available);
                                                event = {
                                                    title: "Unavailable",
                                                    className: ["event", "bg-color-red"],
                                                    start: cur_day.format("YYYY-MM-DD") + "T" + pointer_unavailable,
                                                    end: cur_day.format("YYYY-MM-DD") + "T" +
                                                            blocks_for_day[i].end_time
                                                };
                                                tmp_events.push(event);
                                                console.log("pA = pU" + pointer_available, event);

                                            } else {
                                                event = {
                                                    title: "Available",
                                                    className: ["event", "bg-color-greenLight"],
                                                    start: cur_day.format("YYYY-MM-DD") + "T" + pointer_available,
                                                    end: cur_day.format("YYYY-MM-DD") + "T" + pointer_unavailable
                                                };
                                                tmp_events.push(event);
                                                console.log("else add before blocked time" +
                                                        pointer_available + " to " + pointer_unavailable, event);

                                                event = {
                                                    title: "Unavailable",
                                                    className: ["event", "bg-color-red"],
                                                    start: cur_day.format("YYYY-MM-DD") + "T" + pointer_unavailable,
                                                    end: cur_day.format("YYYY-MM-DD") + "T" +
                                                            blocks_for_day[i].end_time
                                                };
                                                tmp_events.push(event);
                                                console.log("else add the blocked time" +
                                                        pointer_available + " to " +
                                                        blocks_for_day[i].end_time, event);
                                            }


                                            console.log("pointer changing from " + pointer_available + ", " +
                                                    pointer_unavailable + ", to => " + blocks_for_day[i].end_time);
                                            pointer_available = pointer_unavailable = blocks_for_day[i].end_time;
                                        }
                                        if (!dayblock) {
                                            if (pointer_unavailable !== day_end_time) {
                                                event = {
                                                    title: "Available",
                                                    className: ["event", "bg-color-greenLight"],
                                                    start: cur_day.format("YYYY-MM-DD") + "T" + pointer_unavailable,
                                                    end: cur_day.format("YYYY-MM-DD") + "T" + day_end_time
                                                };
                                                tmp_events.push(event);
                                            }
                                            console.log("last add available time" +
                                                    pointer_unavailable + " to " + day_end_time, event);
                                            console.log("pushing day events = ", tmp_events);
                                            for (i = 0; i < tmp_events.length; i++) {
                                                global_data.events.push(tmp_events[i]);
                                            }
                                        }
                                    }
                                } else {
                                    event = {
                                        title: "Day blocked off",
                                        start: cur_day.format("YYYY-MM-DD"),
                                        allDay: true,
                                        className: ["event", "bg-color-red"]
                                    };
                                    global_data.events.push(event);
                                }
                            }
                            cur_day.add(1, 'days');
                            counter++;
                        } while (cur_day < end && counter < 100);

                        for (i = 0; i < global_data.events.length; i++) {
                            availability_calendar.fullCalendar('renderEvent', global_data.events[i]);
                        }

                        setTimeout(function () {
                            $(".fc-today-button").on("click", function () {
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

    function get_clinic_physicians() {
        form = $("#sample_form");
        url = base + "referral/get_clinic_physicians";
        data = form.serialize();
        $.post({
            url: url,
            data: data,
            success: function (response) {
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    options = "";
                    for (i = 0; i < data.length; i++) {
                        options += "<option value='" + data[i].id + "'>" + data[i].physician_name + "</option>";
                    }
                    $("#clinic_physician_container").find("select#physicians").html(options);
                    get_physician_weekdays();
                }
            },
            error: function () {
                error("Internal server error");
            }
        });
    }

    function get_weekday_timing() {
        $("form#form_physician_timing").find("#id").val($("select#physicians").val());
        data = $("form#form_physician_timing").serialize();
        url = base + "schedule_settings/get_weekday_timing";
        $.post({
            url: url,
            data: data,
            success: function (response) {
                if (IsJsonString(response)) {
                    response = JSON.parse(response);
                    if (response.result === "success") {
                        data = response.data;
                        root = $("form#form_physician_timing");
                        day_timing = data.daytime[0];
                        root.find("#start_time").val(day_timing.start_time.substr(0, 5));
                        root.find("#end_time").val(day_timing.end_time.substr(0, 5));

                        weekly_blocks = data.weekly_blocks;
                        $("#weekday_additional_blocks").html("");

                        for (i = 0; i < weekly_blocks.length; i++) {
                            add_weekday_blocked_time_fields(weekly_blocks[i].start_time.substr(0, 5),
                                    weekly_blocks[i].end_time.substr(0, 5));
                        }
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

    function get_physician_weekdays() {
        $("form#sample_form").find("#id").val($("select#physicians").val());
        data = $("form#sample_form").serialize();
        url = base + "schedule_settings/get_physician_weekdays";
        $.post({
            url: url,
            data: data,
            success: function (response) {
                if (IsJsonString(response)) {
                    response = JSON.parse(response);
                    if (response.result === "success") {
                        data = response.data[0];
                        root = $("form#form_physician_weekdays");
                        root.find("#id").val(data.id);
                        root.find("#mon").prop("checked", (data.mon === "yes"));
                        root.find("#tue").prop("checked", (data.tue === "yes"));
                        root.find("#wed").prop("checked", (data.wed === "yes"));
                        root.find("#thu").prop("checked", (data.thu === "yes"));
                        root.find("#fri").prop("checked", (data.fri === "yes"));
                        root.find("#sat").prop("checked", (data.sat === "yes"));
                        root.find("#sun").prop("checked", (data.sun === "yes"));

                        //form physician timing
                        let options = "";
                        options += (data.mon === "yes") ? "<option value='mon'>Monday</option>" : "";
                        options += (data.tue === "yes") ? "<option value='tue'>Tuesday</option>" : "";
                        options += (data.wed === "yes") ? "<option value='wed'>Wednesday</option>" : "";
                        options += (data.thu === "yes") ? "<option value='thu'>Thursday</option>" : "";
                        options += (data.fri === "yes") ? "<option value='fri'>Friday</option>" : "";
                        options += (data.sat === "yes") ? "<option value='sat'>Saturday</option>" : "";
                        options += (data.sun === "yes") ? "<option value='sun'>Sunday</option>" : "";

                        form = $("#form_physician_timing");
                        form.find("#weekday").html(options);
                        form.find("#id").val(data.id);

                        if (global_data.calendar_retrieve && global_data.calendar_retrieve.weekday) {
                            tmp_weekday = global_data.calendar_retrieve.weekday;
                            if ($("#weekday").find('option[value="' + tmp_weekday + '"]').length === 1) {
                                form.find("#weekday").val(tmp_weekday);
                            }
                        }
                        global_data.calendar_retrieve = null;

                        get_weekday_timing();
                        set_availability_calendar();

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

    function add_weekday_blocked_time_fields(start_time = null, end_time = null) {

        html = $("#template_additional_weekday_block").html();
        $("#weekday_additional_blocks").append(html);

        $('.form_time_new').datetimepicker({
            weekStart: 1,
            todayBtn: 0,
            autoclose: 1,
            todayHighlight: 1,
            startView: 1,
            minView: 0,
            maxView: 1,
            forceParse: 0
        });
        console.log("start time = " + start_time);
        if (start_time) {
            $("#weekday_additional_blocks").find(".form_time_new .start_time:last").val(start_time);
            $("#weekday_additional_blocks").find(".form_time_new .end_time:last").val(end_time);
    }
    }

    function add_blocked_time_fields(start_time = null, end_time = null) {
        html = $("#template_additional_blocks").html();
        $("#additional_blocks").append(html);
        $('.form_time_new').datetimepicker({
            weekStart: 1,
            todayBtn: 0,
            autoclose: 1,
            todayHighlight: 1,
            startView: 1,
            minView: 0,
            maxView: 1,
            forceParse: 0
        });

        if (start_time) {
            $("#additional_blocks").find(".form_time_new .start_time:last").val(start_time);
            $("#additional_blocks").find(".form_time_new .end_time:last").val(end_time);
        }
        global_data.blocked_index += 1;
    }
</script> 