<script>    function get_clinic_physicians() {        form = $("#sample_form");        url = base + "referral/get_clinic_physicians";        data = form.serialize();        $.post({            url: url,            data: data        }).done(function (response) {            if (IsJsonString(response)) {                data = JSON.parse(response);                options = "<option value='unassign'>Unassigned</option>";                for (i = 0; i < data.length; i++) {                    options += "<option value='" + data[i].id + "'>" + data[i].physician_name + "</option>";                }                $("#assigned_physician").html(options);                get_referral_details();            }        });    }    function get_referral_details() {        form = $("#sample_form");        form.find("#id").val(global_data.referral_id);        url = base + "accepted/get_referral_dash_info";        data = form.serialize();        $.post({            url: url,            data: data        }).done(function (response) {            if (IsJsonString(response)) {                response = JSON.parse(response);                data = response.dash_info[0];                $("#page_header").html(data.pat_fname + ' ' + data.pat_lname +                        " (" + (data.pat_age) + " year old " + data.pat_gender + ")");                $("#assign_info").html(data.assigned_physician_name + " ( " + data.priority + " Priority )");                root = $(".referral_header");                root.find("#pat_cell_phone").html(data.pat_cell_phone);                root.find("#pat_home_phone").html(data.pat_home_phone);                root.find("#pat_work_phone").html(data.pat_work_phone);                root.find("#pat_email").html(data.pat_email_id);                root.find("#pat_ohip").html(data.pat_ohip);                root.find("#pat_address").html(data.pat_address);                root.find("#dr_name").html('Dr. ' + data.dr_fname + ' ' + data.dr_lname);                root.find("#dr_fax").html(data.dr_fax);                root.find("#dr_phone").html(data.dr_phone_number);                root.find("#dr_billing_num").html(data.dr_billing_num);                root.find("#dr_address").html(data.dr_address);                root = $("#edit-physician-modal");                root.find("#dr_fname").val(data.dr_fname);                root.find("#dr_lname").val(data.dr_lname);                root.find("#dr_phone_number").val(data.dr_phone_number);                root.find("#dr_fax").val(data.dr_fax);                root.find("#dr_email_id").val(data.dr_email_id);                root.find("#dr_address").val(data.dr_address);                root.find("#dr_billing_num").val(data.dr_billing_num);                root = $("#edit-patient-modal");                root.find("#pat_fname").val(data.pat_fname);                root.find("#pat_lname").val(data.pat_lname);                try {                    root.find("#dobday").val(data.pat_dob.substr(8, 2));                    root.find("#dobmonth").val(data.pat_dob.substr(5, 2));                    root.find("#dobyear").val(data.pat_dob.substr(0, 4));                } catch (e) {                    console.log(e);                }                root.find("#pat_cell_phone").val(data.pat_cell_phone);                root.find("#pat_home_phone").val(data.pat_home_phone);                root.find("#pat_work_phone").val(data.pat_work_phone);                root.find("#pat_email_id").val(data.pat_email_id);                root.find("#pat_ohip").val(data.pat_ohip);                root.find("#pat_address").val(data.pat_address);                if (data.priority != 'empty' && data.priority != 'Not Set') {                    //set Priority Label                    $(".priority_label").find("span.not-specified").removeClass("urgent-tag");                    $(".priority_label").find("span.not-specified").removeClass("sub-urgent-tag");                    $(".priority_label").find("span.not-specified").removeClass("routine-tag");                    $("select#priority").val(data.priority);                    if (data.priority == "not_specified") {                        $(".priority_label").find("span.not-specified").html("");                    } else if (data.priority == "urgent") {                        data.priorityLabel = "Urgent";                        $(".priority_label").find("span.not-specified").html("Urgent").addClass("urgent-tag");                    } else if (data.priority == "sub_urgent") {                        data.priorityLabel = "Sub Urgent";                        $(".priority_label").find("span.not-specified").html("Sub Urgent").addClass("sub-urgent-tag");                    } else if (data.priority == "routine") {                        data.priorityLabel = "Routine";                        $(".priority_label").find("span.not-specified").html("Routine").addClass("routine-tag");                    }                }                if (data.assigned_physician != 'empty')                    $("#assigned_physician").val(data.assigned_physician);                $("#request").val(data.diagnosis_type);                // if (data.assigned_physician_name != 'empty')                //     $("#assigned_physician_info").html(' - Assigned to Dr. ' + data.assigned_physician_name                //             + ' ( ' + data.priority + ' )');                //set clinic triage dash                if (data.referral_reason != null && data.referral_reason != "") {                    $(".referral_reason_1").html(data.referral_reason);                    $(".referral_reason_2").html(data.referral_reason);                } else {                    //remove if no reason for referral specified                    $(".referral_reason_2").closest("section").remove();                }                clinic_triage_div = "";                triage_info = response.triage_info;                diseases = triage_info.diseases;                if (diseases.length > 0) {                    clinic_triage_div += '<article class="white-panel">';                    clinic_triage_div += '<h4>Disease or Syndrome</h4><ul>';                    for (i = 0; i < diseases.length; i++) {                        clinic_triage_div += '<li>' + diseases[i].disease + '</li>';                    }                    clinic_triage_div += '</ul></article>';                }                drugs = triage_info.drugs;                if (drugs.length > 0) {                    clinic_triage_div += '<article class="white-panel">';                    clinic_triage_div += '<h4>Medications</h4><ul>';                    for (i = 0; i < drugs.length; i++) {                        clinic_triage_div += '<li>' + drugs[i].drug + '</li>';                    }                    clinic_triage_div += '</ul></article>';                }                tests = triage_info.tests;                if (tests.length > 0) {                    clinic_triage_div += '<article class="white-panel">';                    clinic_triage_div += '<h4>Lab or Test Results</h4><ul>';                    for (i = 0; i < tests.length; i++) {                        clinic_triage_div += '<li>' + tests[i].test + '</li>';                    }                    clinic_triage_div += '</ul></article>';                }                symptoms = triage_info.symptoms;                if (symptoms.length > 0) {                    clinic_triage_div += '<article class="white-panel">';                    clinic_triage_div += '<h4>Sign or Symptoms</h4><ul>';                    for (i = 0; i < symptoms.length; i++) {                        clinic_triage_div += '<li>' + symptoms[i].symptom + '</li>';                    }                    clinic_triage_div += '</ul></article>';                }                devices = triage_info.devices;                if (devices.length > 0) {                    clinic_triage_div += '<article class="white-panel">';                    clinic_triage_div += '<h4>Procedures or Devices</h4><ul>';                    for (i = 0; i < devices.length; i++) {                        clinic_triage_div += '<li>' + devices[i].device + '</li>';                    }                    clinic_triage_div += '</ul></article>';                }                $(".clinic_triage_dash").html(clinic_triage_div);                if (data.assigned_physician_name != 'empty')                    $("#assigned_physician_info").html('Accepted');//                    $("#assigned_physician_info").html('Accepted - Dr. ' + data.assigned_physician_name//                            + ' ( ' + data.priorityLabel + ' )');                global_data.sms_notification_allowed = ((data.pat_cell_phone != null && data.pat_cell_phone != "") || (data.pat_work_phone != null && data.pat_work_phone != "") || (data.pat_home_phone != null && data.pat_home_phone != "")) ? true : false;                global_data.email_notification_allowed = (data.pat_email_id != null && data.pat_email_id != "") ? true : false;                checklist_info = response.checklist_info;                $("#items_total").html(checklist_info.length);                html = "";                checklist_info.forEach(function (value, index) {                    html += '<div class="checkbox">' +                            '<label>' +                            '<input type="checkbox" value="' + value.id + '" name="selected_checklist[]" class="checked" ' + ((value.attached == "true") ? "checked" : "") + '>' +                            '<span class="cr"><i class="cr-icon fa fa-check"></i></span>' +                            value.checklist_name +                            '</label>' +                            '</div>';                });                $("#checklist_div").html(html);                $("#items_checked").html($("#checklist_div").find(":checked").length);            }        });    }    $(document).ready(function () {        $("#li_accepted").addClass("active");        get_clinic_physicians();        $.dobPicker({            daySelector: '#dobday', /* Required */            monthSelector: '#dobmonth', /* Required */            yearSelector: '#dobyear', /* Required */            dayDefault: 'Day', /* Optional */            monthDefault: 'Month', /* Optional */            yearDefault: 'Year', /* Optional */            minimumAge: 0, /* Optional */            maximumAge: 150 /* Optional */        });        $("#checklist_div").on("change", '.checked', function () {            form = $("#sample_form");            form.find("#target").val(this.value);            form.find("#param").val(this.checked);            url = base + "referral/update_checklist_item";            data = form.serialize();            $.post({                url: url,                data: data            }).done(function (response) {                if (response != "true")                    error(JSON.parse(response));                $("#items_checked").html($("#checklist_div").find(":checked").length);            });        });//        $("#btn_view_add_patient_visit").on("click", function () {//            view("modal_add_patient_visit");////            console.log("accepted ");//            $("#modal_add_patient_visit").find("#form_add_patient_visit")[0].reset();//        });        $("#form_add_patient_visit").on("change", "input[name='cell_phone_voice']", function () {            if ($(this).prop("checked") && !global_data.sms_notification_allowed) {                $(this).prop("checked", false);                error("Please add phone number for patient first.");            }        });        $("#form_add_patient_visit").on("change", "input[name='cell_phone']", function () {            if ($(this).prop("checked") && !global_data.sms_notification_allowed) {                $(this).prop("checked", false);                error("Please add phone number for patient first.");            }        });        $("#form_add_patient_visit").on("change", "input[name='email']", function () {            if ($(this).prop("checked") && !global_data.email_notification_allowed) {                $(this).prop("checked", false);                error("Please add email-id for patient first.");            }        });        $("#btn_save_patient").on("click", function () {            //new-patient-form            form = $("#new-patient-form");            form.find("#id").val(global_data.referral_id);            url = base + "referral/update_patient";            data = form.serialize();            $.post({                url: url,                data: data            }).done(function (response) {                if (IsJsonString(response)) {                    data = JSON.parse(response);                    if (data == true) {                        $(".modal").modal("hide");//                        success("Patient Information Successfully Updated");                        $("#new-patient-form")[0].reset();                        get_referral_details();                    } else {                        error(data);                    }                } else {                    error("Unexpected Error Occured");                }            });        });        $("#btn_save_physician").on("click", function () {            //new-physician-form            form = $("#new-physician-form");            form.find("#id").val(global_data.referral_id);            url = base + "referral/update_physician";            data = form.serialize();            $.post({                url: url,                data: data            }).done(function (response) {                if (IsJsonString(response)) {                    data = JSON.parse(response);                    if (data == true) {                        $(".modal").modal("hide");//                        success("Physician Information Successfully Updated");                        $("#new-physician-form")[0].reset();                        get_referral_details();                    } else {                        error(data);                    }                } else {                    error("Unexpected Error Occured");                }            });        });        $("#btn_assign_physician").on("click", function () {            form = $("#assign_physician_form");            form.find("#id").val(global_data.referral_id);            url = base + "referral/assign_physician";            data = form.serialize();            $.post({                url: url,                data: data            }).done(function (response) {                if (IsJsonString(response)) {                    data = JSON.parse(response);                    if (data == true) {                        $(".modal").modal("hide");                        success("Physician Successfully Assigned");                        get_referral_details();                        $("#assign_physician_form")[0].reset();                    } else {                        error(data);                    }                } else                    error("Unexpected Error Occured");            });        });        $("#btn_cancel").on("click", function () {            form = $("#sample_form");            form.find("#id").val(global_data.referral_id);            url = base + "referral/cancel_referral";            data = form.serialize();            $.post({                url: url,                data: data            }).done(function (response) {                if (IsJsonString(response)) {                    data = JSON.parse(response);                    if (data == true) {                        success("Referral Successfully Cancelled");                        get_latest_dashboard_counts();                    } else {                        error(data);                    }                } else                    error("Unexpected Error Occured");            });        });                $("#btn_view_add_patient_visit2").on("click", function () {            $("#btn_view_add_patient_visit").click();        });        $("#btn_confirm_assign_physician").on("click", function () {            form = $("#sample_form");            form.find("#id").val(global_data.referral_id);            form.find("#target").val($("select#assigned_physician").val());            data = form.serialize();            url = base + "referral/assign_physician";            $.post({                url: url,                data: data            }).done(function (response) {                get_referral_details();                global_data.table_patient_visits.ajax.reload();            });        });        $("#modal_confirm_assign_physician").on("hide.bs.modal", function () {            get_referral_details();        });        $("select#assigned_physician").on("change", function () {            form = $("#sample_form");            form.find("#id").val(global_data.referral_id);            data = form.serialize();            url = base + "referral/is_patient_scheduled";            $.post({                url: url,                data: data            }).done(function (response) {                if (IsJsonString(response)) {                    data = JSON.parse(response);                    if (data.result === "success") {                        if (data.is_patient_scheduled === true) {                            $("#modal_confirm_assign_physician").modal("show");                        } else {                            form = $("#sample_form");                            form.find("#id").val(global_data.referral_id);                            form.find("#target").val($("select#assigned_physician").val());                            data = form.serialize();                            url = base + "referral/assign_physician";                            $.post({                                url: url,                                data: data                            }).done(function (response) {                                get_referral_details();                                global_data.table_patient_visits.ajax.reload();                            });                        }                    } else {                        error(data.message);                    }                } else {                    error("Internal server error");                }            });        });        $("select#priority").on("change", function () {            form = $("#sample_form");            form.find("#id").val(global_data.referral_id);            form.find("#target").val($(this).val());            data = form.serialize();            url = base + "referral/set_priority";            $.post({                url: url,                data: data            }).done(function (response) {                get_referral_details();            });        });        //*******************************************        // script for records        //*******************************************        function set_id_row(id, row, row_class) {            $(row).attr("data-id", id);            $(row).addClass('db-table-link-row');            $(row).addClass(row_class);        }        $("#btn_view_add_health_record").on("click", function () {            view("modal_add_health_record");            $("#form_health_record")[0].reset();        });        $("#btn_view_add_admin_note").on("click", function () {            view("modal_add_admin_note");            $("#form_add_admin_note")[0].reset();        });        $("#btn_view_add_patient_visit").on("click", function () {            $("#modal_add_patient_visit").find("#form_add_patient_visit")[0].reset();            $("#btn_view_add_patient_visit").button("loading");            form = $("#sample_form");            form.find("#id").val(global_data.referral_id);            data = form.serialize();            url = base + "referral/get_visit_allocation_for_manual_visit";            $.post({                url: url,                data: data            }).done(function (response) {                $("#btn_view_add_patient_visit").button("reset");                if (IsJsonString(response)) {                    response = JSON.parse(response);                    if (response.result === "success") {                        $(".modal").modal("hide");                        let root = $("#modal_add_patient_visit");                        $(root).find("span#visit_slot_1").text(response.data.allocations.slot1);                        $(root).find("span#visit_slot_2").text(response.data.allocations.slot2);                        $(root).find("span#visit_slot_3").text(response.data.allocations.slot3);                        $(root).find("#record_id").val(response.data.record_id);                        view("modal_add_patient_visit");                    } else {                        error(response.message);                    }                } else {                    error("Unexpected Error Occured");                }            });        });        $('#modal_add_health_record').on('hidden.bs.modal', function () {            if (myDropzone != undefined) {                myDropzone.removeAllFiles();            }        });        $('#modal_add_health_record').on('shown.bs.modal', function () {            //Simple Dropzonejs             myDropzone = new Dropzone("#dropzone_health_record", {                maxFilesize: 100,                url: base + 'referral/add_health_record',                addRemoveLinks: true,                autoProcessQueue: false,                uploadMultiple: true,                paramName: 'asdqwe',                parallelUploads: 100,                maxFiles: 1,                acceptedFiles: "application/pdf",                init: function () {                    var myDropzone = this;                    $("#btn_save_health_record").on("click", function (e) {                        $("#btn_save_health_record").button("loading");                        e.preventDefault();                        e.stopPropagation();                        var form = $('#form_health_record');                        if (form.valid() == true) {                            if (myDropzone.getQueuedFiles().length > 0) {                                myDropzone.processQueue();                            } else {                                var blob = new Blob();                                blob.upload = {'chunked': myDropzone.defaultOptions.chunking};                                myDropzone.uploadFile(blob);                            }                        }                    });                    this.on("sendingmultiple", function (files, xhr, formData) {                        // Gets triggered when the form is actually being sent.                        // Hide the success button or the complete form.                        $('#form_health_record').find("#id").val(global_data.referral_id);                        formData.append('<?php echo $this->security->get_csrf_token_name(); ?>'                                , '<?php echo $this->security->get_csrf_hash(); ?>');                        //add other form data                        $("#form_health_record").find("select, textarea, input[type!='file']").each(function (index, value) {                            name = $(value).attr("name");                            val = ($(value).val() != null) ? $(value).val() : "";                            formData.append(name, val);                        });                    });                    this.on("successmultiple", function (files, response) {                        // Gets triggered when the files have successfully been sent.                        // Redirect user or notify of success.                        $("#btn_save_health_record").button("reset");                        myDropzone.removeAllFiles();                        if (IsJsonString(response)) {                            data = JSON.parse(response);                            if (data == true) {                                $(".modal").modal("hide");                                success("Health record has been successfully saved", "Health record saved");                                global_data.table_health_records.ajax.reload();                            } else {                                error(data);                            }                        } else                            error("Unexpected Error Occured");                    });                }            });        });        $("#add_admin_note").on("click", function () {            form = $("#form_add_admin_note");            form.find("#id").val(global_data.referral_id);            data = form.serialize();            url = base + "referral/add_admin_note";            $.post({                url: url,                data: data            }).done(function (response) {                if (IsJsonString(response)) {                    data = JSON.parse(response);                    if (data == true) {                        $(".modal").modal("hide");                        // success("Admin Note Successfully Created");                        global_data.table_admin_notes.ajax.reload();                    } else {                        error(data);                    }                } else {                    error("Unexpected Error Occured");                }            });        });        $("#btn_add_patient_visit").on("click", function () {            form = $("#form_add_patient_visit");            form.find("#id").val(global_data.referral_id);            data = form.serialize();            url = base + "referral/add_patient_visit";            $("#btn_add_patient_visit").button("loading");            $.post({                url: url,                data: data            }).done(function (response) {                $("#btn_add_patient_visit").button("reset");                if (IsJsonString(response)) {                    data = JSON.parse(response);                    if (data == true) {                        $(".modal").modal("hide");                        success("Patient visit has been successfully added", "Visit added");                        global_data.table_patient_visits.ajax.reload();                        get_latest_dashboard_counts();                        get_referral_details();                        $("#form_add_patient_visit")[0].reset();                    } else {                        error(data);                    }                } else {                    error("Unexpected Error Occured");                }            });        });        $("#btn_update_patient_visit").on("click", function () {            $("#btn_update_patient_visit").button("loading");            form = $("#form_update_patient_visit");            form.find("#id").val(global_data.referral_id);            data = form.serialize();            url = base + "referral/update_patient_visit";            $.post({                url: url,                data: data            }).done(function (response) {                $("#btn_update_patient_visit").button("reset");                if (IsJsonString(response)) {                    data = JSON.parse(response);                    if (data == true) {                        $(".modal").modal("hide");                        success("Patient visit successfully updated");                        global_data.table_patient_visits.ajax.reload();                    } else {                        error(data);                    }                } else {                    error("Unexpected Error Occured");                }            });        });        $("#btn_cancel_visit").on("click", function () {            $("#btn_cancel_visit").button("loading");            form = $("#form_update_patient_visit");            form.find("#id").val(global_data.referral_id);            data = form.serialize();            url = base + "referral/cancel_patient_visit";            $.post({                url: url,                data: data            }).done(function (response) {                $("#btn_cancel_visit").button("reset");                if (IsJsonString(response)) {                    data = JSON.parse(response);                    if (data == true) {                        $(".modal").modal("hide");                        success("Patient Visit Successfully Cancelled");                        global_data.table_patient_visits.ajax.reload();                    } else {                        error(data);                    }                } else {                    error("Unexpected Error Occured");                }            });        });        $("table").on("click", ".health_records_row", function () {            form = $("#sample_form");            form.find("#id").val(global_data.referral_id);            form.find("#target").val($(this).data("id"));            url = base + "referral/get_health_record_info";            data = form.serialize();            $.post({                url: url,                data: data            }).done(function (response) {                if (IsJsonString(response)) {                    data = JSON.parse(response);                    data = data[0];                    root = $("#modal_view_health_record");                    root.find("#record_type").html(data.record_type);                    root.find("#description").html(data.description);                    if (data.record_file != "") {                        global_data.uploaded_file_path = base + "uploads/clinics/" +                                global_data.clinic_id + "/" + global_data.referral_id + "/" +                                data.record_file + ".pdf";                        PDFObject.embed(base + "uploads/clinics/" +                                global_data.clinic_id + "/" + global_data.referral_id + "/" +                                data.record_file + ".pdf", "#pdf_view_div");                    } else {                        root.find("#btn_view_uploaded_doc").hide();                    }                    view("modal_view_health_record");                } else {                    error("Unexpected Error Occured");                }            });        });        $("#btn_print_uploaded_pdf").on("click", function () {            printJS(global_data.uploaded_file_path);        });        $("table").on("click", ".admin_notes_row", function () {            form = $("#sample_form");            form.find("#id").val(global_data.referral_id);            form.find("#target").val($(this).data("id"));            url = base + "referral/get_admin_notes_info";            data = form.serialize();            $.post({                url: url,                data: data            }).done(function (response) {                if (IsJsonString(response)) {                    data = JSON.parse(response);                    data = data[0];                    root = $("#modal_view_admin_note");                    root.find("#note_type").val(data.note_type);                    root.find("#description").val(data.description);                    view("modal_view_admin_note");                } else {                    error("Unexpected Error Occured");                }            });        });        $("table").on("click", ".patient_visits_row", function () {            $("#form_update_patient_visit")[0].reset();            global_data.patient_visit_id = $(this).data("id");            form = $("#sample_form");            form.find("#id").val(global_data.referral_id);            data = form.serialize();            url = base + "referral/get_visit_allocation_for_manual_visit";            $.post({                url: url,                data: data            }).done(function (response) {                if (IsJsonString(response)) {                    response = JSON.parse(response);                    if (response.result === "success") {                        $(".modal").modal("hide");                        let root = $("#modal_view_patient_visit");                        $(root).find("span#visit_slot_1").text(response.data.allocations.slot1);                        $(root).find("span#visit_slot_2").text(response.data.allocations.slot2);                        $(root).find("span#visit_slot_3").text(response.data.allocations.slot3);                        $(root).find("#record_id").val(response.data.record_id);                        $(root).find("#target").val(global_data.patient_visit_id);                        view("modal_view_patient_visit");                    }                } else {                    error("Unexpected Error Occured");                }            });        });        //  *** Health Records Datatable        global_data.table_health_records_title = "Health Records";        global_data.table_health_records = $("#table_health_records").DataTable({            "order": false, //[[ 2, "desc" ]],            "processing": true,            "serverSide": true,            "autoWidth": false,            "language": {                "emptyTable": "There are no " + global_data.table_health_records_title,                "info": "Showing _START_ to _END_ of _TOTAL_ " + global_data.table_health_records_title,                "infoEmpty": "No results found",                "infoFiltered": "(filtered from _MAX_ total " + global_data.table_health_records_title + ")",                "infoPostFix": "",                "thousands": ",",                "lengthMenu": "Show _MENU_ ",                "loadingRecords": "Loading " + global_data.table_health_records_title,                "processing": "Processing " + global_data.table_health_records_title,                "search": "",                "zeroRecords": "No matching " + global_data.table_health_records_title + " found"            },            "ajax": "<?php echo base_url(); ?>referral/ssp_health_records/" + global_data.referral_id,            "rowCallback": function (row, data, index) {                $('td:eq(3)', row).html(                        set_id_row(data[3], row, "health_records_row")                        );            },            "dom": get_records_dom_plan(),            // "drawCallback": set_patients_table,            "columnDefs": [                {"width": "25%", "targets": 0},                {"width": "50%", "targets": 1},                {"width": "25%", "targets": 2}            ]        });        $("#table_health_records").wrap('<div class="table-responsive"></div>');        // $("#btn_view_add_patient").appendTo("#patients_table_wrapper .button-placeholder");        $("#table_health_records_wrapper").find(".dataTables_filter input").before('<i class="fa fa-search datatable-search"></i>');        $("#table_health_records_wrapper").find(".dataTables_filter input").attr('placeholder', 'Search');        //  *** Health Records Datatable Over        //  *** Admin Notes Datatable        global_data.table_admin_notes_title = "Admin Notes";        global_data.table_admin_notes = $("#table_admin_notes").DataTable({            "processing": true,            "serverSide": true,            "autoWidth": false,            "language": {                "emptyTable": "There are no " + global_data.table_admin_notes_title,                "info": "Showing _START_ to _END_ of _TOTAL_ " + global_data.table_admin_notes_title,                "infoEmpty": "No results found",                "infoFiltered": "(filtered from _MAX_ total " + global_data.table_admin_notes_title + ")",                "infoPostFix": "",                "thousands": ",",                "lengthMenu": "Show _MENU_ ",                "loadingRecords": "Loading " + global_data.table_admin_notes_title,                "processing": "Processing " + global_data.table_admin_notes_title,                "search": "",                "zeroRecords": "No matching " + global_data.table_admin_notes_title + " found"            },            "ajax": "<?php echo base_url(); ?>referral/ssp_admin_notes/" + global_data.referral_id,            "rowCallback": function (row, data, index) {                $('td:eq(3)', row).html(                        set_id_row(data[3], row, "admin_notes_row")                        );            },            "dom": get_records_dom_plan(),            // "drawCallback": set_patients_table,            "columnDefs": [                {"width": "25%", "targets": 0},                {"width": "50%", "targets": 1},                {"width": "25%", "targets": 2}            ]        });        $("#table_admin_notes").wrap('<div class="table-responsive"></div>');        // $("#btn_view_add_patient").appendTo("#patients_table_wrapper .button-placeholder");        $("#table_admin_notes_wrapper").find(".dataTables_filter input").before('<i class="fa fa-search datatable-search"></i>');        $("#table_admin_notes_wrapper").find(".dataTables_filter input").attr('placeholder', 'Search');        //  *** Admin Notes Datatable Over        //  *** Patient Visits Datatable        global_data.table_patient_visits_title = "Patient Visits";        global_data.table_patient_visits = $("#table_patient_visits").DataTable({            "processing": true,            "serverSide": true,            "autoWidth": false,            "language": {                "emptyTable": "There are no " + global_data.table_patient_visits_title,                "info": "Showing _START_ to _END_ of _TOTAL_ " + global_data.table_patient_visits_title,                "infoEmpty": "No results found",                "infoFiltered": "(filtered from _MAX_ total " + global_data.table_patient_visits_title + ")",                "infoPostFix": "",                "thousands": ",",                "lengthMenu": "Show _MENU_ ",                "loadingRecords": "Loading " + global_data.table_patient_visits_title,                "processing": "Processing " + global_data.table_patient_visits_title,                "search": "",                "zeroRecords": "No matching " + global_data.table_patient_visits_title + " found"            },            "ajax": "<?php echo base_url(); ?>referral/ssp_patient_visits/" + global_data.referral_id,            "rowCallback": function (row, data, index) {                $('td:eq(4)', row).html(set_id_row(data[5], row, "patient_visits_row"));                $('td:eq(2)', row).html(set_visit_status(data[2], data[4]));            },            "dom": get_records_dom_plan(),            // "drawCallback": set_patients_table,            "columnDefs": [                {"width": "25%", "targets": 0},                {"width": "25%", "targets": 1},                {"width": "25%", "targets": 2},                {"width": "25%", "targets": 3}            ]        });        $("#table_patient_visits").wrap('<div class="table-responsive"></div>');        // $("#btn_view_add_patient").appendTo("#patients_table_wrapper .button-placeholder");        $("#table_patient_visits_wrapper").find(".dataTables_filter input").before('<i class="fa fa-search datatable-search"></i>');        $("#table_patient_visits_wrapper").find(".dataTables_filter input").attr('placeholder', 'Search');        //  *** Patient Visits Datatable Over    });</script>