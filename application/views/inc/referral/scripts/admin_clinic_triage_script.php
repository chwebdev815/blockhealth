<script>

    //for admin and clinical triage box

    $(document).ready(function () {
        get_location_and_custom();
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
                    if (data === true) {
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

        $("#btn_confirm_assign_physician").on("click", function () {
            form = $("#sample_form");
            form.find("#id").val(global_data.referral_id);
            form.find("#target").val($("select#assigned_physician").val());
            data = form.serialize();
            url = base + "referral/assign_physician";
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                get_referral_details();
                global_data.table_patient_visits.ajax.reload();
            });
        });

        $("#modal_confirm_assign_physician").on("hide.bs.modal", function () {
            get_referral_details();
        });



        $("select#assigned_physician").on("change", function () {
            form = $("#sample_form");
            form.find("#id").val(global_data.referral_id);
            data = form.serialize();
            url = base + "referral/is_patient_scheduled";
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    if (data.result === "success") {
                        if (data.is_patient_scheduled === true) {
                            $("#modal_confirm_assign_physician").modal("show");
                        } else {
                            form = $("#sample_form");
                            form.find("#id").val(global_data.referral_id);
                            form.find("#target").val($("select#assigned_physician").val());
                            data = form.serialize();
                            url = base + "referral/assign_physician";
                            $.post({
                                url: url,
                                data: data
                            }).done(function (response) {
                                get_referral_details();
                                global_data.table_patient_visits.ajax.reload();
                            });
                        }
                    } else {
                        error(data.message);
                    }
                } else {
                    error("Internal server error");
                }
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
        
        $("select#patient_location").on("change", function() {
            form = $("#sample_form");
            form.find("#id").val(global_data.referral_id);
            form.find("#param").val($(this).val());
            data = form.serialize();
            url = base + "referral/set_patient_location";
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                get_referral_details();
            });
        });
        
        $("select#custom").on("change", function() {
            form = $("#sample_form");
            form.find("#id").val(global_data.referral_id);
            form.find("#param").val($(this).val());
            data = form.serialize();
            url = base + "referral/set_custom";
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                get_referral_details();
            });
        });

    });

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
                    options = "<option selected disabled>Select Patient Location</option>";
                    data.locations.forEach(function (value, index) {
                        options += "<option value='" + value.id + "'>" + value.name + "</option>";
                    });
                    $("#collapseFive").find("#patient_location").html(options);

                    //custom
                    options = "<option selected disabled>Custom Fields</option>";
                    data.customs.forEach(function (value, index) {
                        options += "<option value='" + value.id + "'>" + value.name + "</option>";
                    });
                    $("#collapseFive").find("#custom").html(options);
                } else {
                    error("Error getting locations and customs");
                }
            } else {
                error("Error getting locations and customs");
            }
        });
    }
    
</script>

