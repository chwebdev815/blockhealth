<script>
    $(document).ready(function () {
        $("#second_menu_list").find("#li_schedule_settings").addClass("active");
        $("#submitButton").click();
        get_clinic_physicians();
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
                options = "";
                for (i = 0; i < data.length; i++) {
                    options += "<option value='" + data[i].id + "'>" + data[i].physician_name + "</option>";
                }
                $("#clinic_physician_container").find("select#physicians").html(options);
                
                get_physician_timings();
            }
        });
    }
    
//    function get_physician_timings() {
//        data = $("form#form_physician_timing").serialize();
//        url = base + "referral/get_physician_timings";
//    }
</script> 