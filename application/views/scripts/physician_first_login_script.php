<script>
    $(document).ready(function () {
        $("#btn_update_password").on("click", function () {
            form = $("#form_update_password");
            url = base + "login/store_physician_creds";
            data = form.serialize();
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    if (data == true) {
                        success("Password Stored Successfully. You can login now with this credentials");
                        setTimeout(function () {
                            location.href = base;
                        }, 1000);
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