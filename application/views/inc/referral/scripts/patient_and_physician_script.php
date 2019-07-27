<script>
    $(document).ready(function () {
        get_clinic_physicians();
        $.dobPicker({
            daySelector: '#dobday', /* Required */
            monthSelector: '#dobmonth', /* Required */
            yearSelector: '#dobyear', /* Required */
            dayDefault: 'Day', /* Optional */
            monthDefault: 'Month', /* Optional */
            yearDefault: 'Year', /* Optional */
            minimumAge: 0, /* Optional */
            maximumAge: 150 /* Optional */
        });
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


        $("#btn_save_patient").on("click", function () {
            //new-patient-form
            form = $("#new-patient-form");
            form.find("#id").val(global_data.referral_id);
            url = base + "referral/update_patient";
            data = form.serialize();
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    if (data == true) {
                        $(".modal").modal("hide");
//                        success("Patient Information Successfully Updated");
                        $("#new-patient-form")[0].reset();
                        get_referral_details();
                    } else {
                        error(data);
                    }
                } else {
                    error("Unexpected Error Occured");
                }
            });
        });
        $("#btn_save_physician").on("click", function () {
            //new-physician-form
            form = $("#new-physician-form");
            form.find("#id").val(global_data.referral_id);
            url = base + "referral/update_physician";
            data = form.serialize();
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    if (data == true) {
                        $(".modal").modal("hide");
//                        success("Physician Information Successfully Updated");
                        $("#new-physician-form")[0].reset();
                        get_referral_details();
                    } else {
                        error(data);
                    }
                } else {
                    error("Unexpected Error Occured");
                }
            });
        });


    });
</script>