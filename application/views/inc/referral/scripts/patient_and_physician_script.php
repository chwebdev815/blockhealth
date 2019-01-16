<script>
    $(document).ready(function() {
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
</script>