<script>
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
                get_referral_details();
            }
        });
    }
    function get_referral_details() {
        form = $("#sample_form");
        form.find("#id").val(global_data.referral_id);
        url = base + "admin_triage/get_referral_dash_info";
        data = form.serialize();
        $.post({
            url: url,
            data: data
        }).done(function (response) {
            if (IsJsonString(response)) {
                response = JSON.parse(response);
                data = response.dash_info[0];
                $("#page_header").html(data.pat_fname + ' ' + data.pat_lname + data.pat_dob2);
                root = $(".referral_header");
                //set status miss
                $("#miss_status").html(data.miss_status);

                root.find("#pat_cell_phone").html(data.pat_cell_phone);
                root.find("#pat_home_phone").html(data.pat_home_phone);
                root.find("#pat_email").html(data.pat_email_id);
                root.find("#pat_ohip").html(data.pat_ohip);
                root.find("#pat_address").html(data.pat_address);
                root.find("#dr_name").html('Dr. ' + data.dr_fname + ' ' + data.dr_lname);
                root.find("#dr_fax").html(data.dr_fax);
                root.find("#dr_phone").html(data.dr_phone_number);
                root.find("#dr_billing_num").html(data.dr_billing_num);
                root.find("#dr_address").html(data.dr_address);
                root = $("#edit-physician-modal");
                root.find("#dr_fname").val(data.dr_fname);
                root.find("#dr_lname").val(data.dr_lname);
                root.find("#dr_phone_number").val(data.dr_phone_number);
                root.find("#dr_fax").val(data.dr_fax);
                root.find("#dr_email_id").val(data.dr_email_id);
                root.find("#dr_address").val(data.dr_address);
                root.find("#dr_billing_num").val(data.dr_billing_num);
                root = $("#edit-patient-modal");
                root.find("#pat_fname").val(data.pat_fname);
                root.find("#pat_lname").val(data.pat_lname);
                try {
                    root.find("#dobday").val(data.pat_dob.substr(8, 2));
                    root.find("#dobmonth").val(data.pat_dob.substr(5, 2));
                    root.find("#dobyear").val(data.pat_dob.substr(0, 4));
                } catch (e) {
                    console.log(e);
                }
                root.find("#pat_cell_phone").val(data.pat_cell_phone);
                root.find("#pat_home_phone").val(data.pat_home_phone);
                root.find("#pat_email_id").val(data.pat_email_id);
                root.find("#pat_ohip").val(data.pat_ohip);
                root.find("#pat_address").val(data.pat_address);
                //set Priority Label
                $(".priority_label").find("span.not-specified").removeClass("urgent-tag");
                $(".priority_label").find("span.not-specified").removeClass("sub-urgent-tag");
                $(".priority_label").find("span.not-specified").removeClass("routine-tag");

                if (data.priority == "")
                    $("select#priority").val("not_specified");
                else
                    $("select#priority").val(data.priority);

                if (data.priority == "not_specified") {
                    $(".priority_label").find("span.not-specified").html("");
                } else if (data.priority == "urgent") {
                    $(".priority_label").find("span.not-specified").html("Urgent").addClass("urgent-tag");
                } else if (data.priority == "sub_urgent") {
                    $(".priority_label").find("span.not-specified").html("Sub Urgent").addClass("sub-urgent-tag");
                } else if (data.priority == "routine") {
                    $(".priority_label").find("span.not-specified").html("Routine").addClass("routine-tag");
                }

                if (data.assigned_physician != 'empty' && data.assigned_physician != 'Not Assigned')
                    $("#assigned_physician").val(data.assigned_physician);
                $("#request").val(data.diagnosis_type);

                //set clinic triage dash
                if (data.referral_reason != null && data.referral_reason != "") {
                    $(".referral_reason_1").html(data.referral_reason);
                    $(".referral_reason_2").html(data.referral_reason);
                } else {
                    //remove if no reason for referral specified
                    $(".referral_reason_2").closest("section").remove();
                }
                clinic_triage_div = "";
                triage_info = response.triage_info;
                diseases = triage_info.diseases;
                if (diseases.length > 0) {
                    clinic_triage_div += '<article class="white-panel">';
                    clinic_triage_div += '<h4>Disease or Syndrome</h4><ul>';
                    for (i = 0; i < diseases.length; i++) {
                        clinic_triage_div += '<li>' + diseases[i].disease + '</li>';
                    }
                    clinic_triage_div += '</ul></article>';
                }

                drugs = triage_info.drugs;
                if (drugs.length > 0) {
                    clinic_triage_div += '<article class="white-panel">';
                    clinic_triage_div += '<h4>Medications</h4><ul>';
                    for (i = 0; i < drugs.length; i++) {
                        clinic_triage_div += '<li>' + drugs[i].drug + '</li>';
                    }
                    clinic_triage_div += '</ul></article>';
                }

                tests = triage_info.tests;
                if (tests.length > 0) {
                    clinic_triage_div += '<article class="white-panel">';
                    clinic_triage_div += '<h4>Lab or Test Results</h4><ul>';
                    for (i = 0; i < tests.length; i++) {
                        clinic_triage_div += '<li>' + tests[i].test + '</li>';
                    }
                    clinic_triage_div += '</ul></article>';
                }

                symptoms = triage_info.symptoms;
                if (symptoms.length > 0) {
                    clinic_triage_div += '<article class="white-panel">';
                    clinic_triage_div += '<h4>Sign or Symptoms</h4><ul>';
                    for (i = 0; i < symptoms.length; i++) {
                        clinic_triage_div += '<li>' + symptoms[i].symptom + '</li>';
                    }
                    clinic_triage_div += '</ul></article>';
                }

                devices = triage_info.devices;
                if (devices.length > 0) {
                    clinic_triage_div += '<article class="white-panel">';
                    clinic_triage_div += '<h4>Procedures or Devices</h4><ul>';
                    for (i = 0; i < devices.length; i++) {
                        clinic_triage_div += '<li>' + devices[i].device + '</li>';
                    }
                    clinic_triage_div += '</ul></article>';
                }

                $(".clinic_triage_dash").html(clinic_triage_div);


                global_data.sms_notification_allowed = (data.pat_cell_phone != null && data.pat_cell_phone != "") ? true : false;
                global_data.email_notification_allowed = (data.pat_email_id != null && data.pat_email_id != "") ? true : false;
                checklist_info = response.checklist_info;
                $("#items_total").html(checklist_info.length);
                html = "";
                checklist_info.forEach(function (value, index) {
                    html += '<div class="checkbox">' +
                            '<label>' +
                            '<input type="checkbox" value="' + value.id + '" name="selected_checklist[]" class="checked" ' + ((value.attached == "true") ? "checked" : "") + '>' +
                            '<span class="cr"><i class="cr-icon fa fa-check"></i></span>' +
                            value.checklist_name +
                            '</label>' +
                            '</div>';
                });
                $("#checklist_div").html(html);
                $("#items_checked").html($("#checklist_div").find(":checked").length);
            }
        });
    }
    $(document).ready(function () {
        $("#li_admin_triage").addClass("active");
        get_clinic_physicians();

        $("#checklist_div").on("change", '.checked', function () {
            form = $("#sample_form");
            form.find("#target").val(this.value);
            form.find("#param").val(this.checked);
            url = base + "referral/update_checklist_item";
            data = form.serialize();
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                if (response != "true")
                    error(JSON.parse(response));
                $("#items_checked").html($("#checklist_div").find(":checked").length);
            });
        });

        $("#form_add_patient_visit").on("change", "input[name='cell_phone_voice']", function () {
            if ($(this).prop("checked") && !global_data.sms_notification_allowed) {
                $(this).prop("checked", false);
                error("Please add phone number for patient first.");
            }
        });
        $("#form_add_patient_visit").on("change", "input[name='cell_phone']", function () {
            if ($(this).prop("checked") && !global_data.sms_notification_allowed) {
                $(this).prop("checked", false);
                error("Please add phone number for patient first.");
            }
        });
        $("#form_add_patient_visit").on("change", "input[name='email']", function () {
            if ($(this).prop("checked") && !global_data.email_notification_allowed) {
                $(this).prop("checked", false);
                error("Please add email-id for patient first.");
            }
        });

        $("#btn_assign_physician").on("click", function () {
            form = $("#assign_physician_form");
            form.find("#id").val(global_data.referral_id);
            url = base + "referral/assign_physician";
            data = form.serialize();
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    if (data == true) {
                        $(".modal").modal("hide");
                        success("Physician Successfully Assigned");
                        get_referral_details();
                        $("#assign_physician_form")[0].reset();
                    } else {
                        error(data);
                    }
                } else
                    error("Unexpected Error Occured");
            });
        });


        $("#btn_cancel").on("click", function () {
            $("#btn_cancel").button("loading");
            form = $("#sample_form");
            form.find("#id").val(global_data.referral_id);
            url = base + "referral/cancel_referral";
            data = form.serialize();
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                $("#btn_cancel").button("reset");
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    if (data == true) {
                        success("Referral Successfully Cancelled");
                        get_latest_dashboard_counts();
                    } else {
                        error(data);
                    }
                } else
                    error("Unexpected Error Occured");
            });
        });
        $("#btn_accept").on("click", function () {
            $("#btn_accept").button("loading");

            form = $("#sample_form");
            form.find("#id").val(global_data.referral_id);
            url = base + "referral/accept_admin_referral";
            data = form.serialize();
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                $("#btn_accept").button("reset");
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    if (data == true) {
                        location.href = base + "referral_triage/referral_details/" + global_data.referral_id;
                    } else {
                        error(data);
                    }
                }
            });
        });

        $("select#assigned_physician").on("change", function () {
            form = $("#sample_form");
            form.find("#id").val(global_data.referral_id);
            form.find("#target").val($(this).val());
            data = form.serialize();
            url = base + "referral/assign_physician";
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                get_referral_details();
            });
        });

        $("select#priority").on("change", function () {
            form = $("#sample_form");
            form.find("#id").val(global_data.referral_id);
            form.find("#target").val($(this).val());
            data = form.serialize();
            url = base + "referral/set_priority";
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                get_referral_details();
            });
        });


    });
</script> 