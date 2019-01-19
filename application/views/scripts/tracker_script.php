<style>
    form .form-bottom .input-error {
        border-color: #d03e3e;
        color: #d03e3e;
    }
    .col-lg-12 {
        padding-right: 30px;
    }
    .col-sm-6 {
        padding-right: 30px;
    }
    #modal_view_missing_item .modal-header {
        color: #fff;
        font-size: 14px;
        background: #08b5a2;
    }

    #modal_view_missing_item .modal-body {
        padding: 0px 15px;
        background: #e8f8f7!important;
    }
</style>

<script>
    signup_done = "<?php echo $this->session->userdata("signup_done"); ?>";
    Dropzone.autoDiscover = false;


<?php if ($this->session->userdata("signup_done") == "no") { ?>
        //if signup remaining display popup 
        $("#modal_signup").modal("show");

<?php } ?>

    function operate_messages() {
<?php if ($this->session->flashdata("success") != null) { ?>
            $("#view_upload").click();
            $("#success_upload").show();
            $("#error_upload").hide();
<?php
}
if ($this->session->flashdata("error") != null) {
    ?>
            $("#view_upload").click();
            $("#success_upload").hide();
            $("#error_upload").show();
<?php } ?>
    }

    $(document).ready(function () {
        load_tracker(global_data.record_id);
        // get clinic list, 
        // login name, login id, patient name based on referral code

        $("#back").on("click", function () {
            $(this).parents('fieldset').fadeOut(400, function () {
                $(this).prev().fadeIn();
            });
        });

        $("#btn_add_more_files").on("click", function () {
            template = $(".upload_template").html();
            $("#uploads_container").append(template);
        });

        $("#uploads_container").on("click", ".remove_field", function () {
            parent = $(this).closest(".added_upload_field").remove();
        });

        $("#btn_upload_missing_items").on("click", function () {
            $("#form_upload_missing_items").submit();
        });

        form_signup = $("#form_signup");
        form_signup.find("#next").on("click", function () {
            parent_fieldset = $(this).parents('fieldset');
            next_step = true;
            parent_fieldset.find('.required').each(function () {
                if ($(this).val() == "") {
                    next_step = false;
                    $(this).addClass('input-error');
                } else {
                    $(this).removeClass('input-error');
                }
            });
            if (next_step) {
                if (form_signup.find("#pass").val() != form_signup.find("#confirm_pass").val()) {
                    form_signup.find("#pass").addClass('input-error');
                    error("Password field should match with confirm password.");
                    next_step = false;
                    return;
                }
            }
            if (next_step) {
                parent_fieldset.find('.valid_email').each(function () {
                    if (validate_email($(this).val())) {
                        $(this).removeClass('input-error');
                    } else {
                        $(this).addClass('input-error');
                        next_step = false;
                    }
                });
            }
            if (next_step) {
                parent_fieldset.fadeOut(400, function () {
                    $(this).next().fadeIn();
                });
            }
        });

        form_signup.find("#submit").on("click", function () {
            data = form_signup.serialize();
            url = base + "tracker/signup";

            $.post({
                url: url,
                data: data
            }).done(function (response) {
                data = JSON.parse(response);
                if (data === true) {
                    $(".modal").modal("hide");
                    location.reload();
                } else {
                    error(data);
                }
            });
        });


        $("#view_upload").on("click", function () {
            get_uploaded_items();
            $("#modal_upload_missing_items").modal("show");
        });




        $("#modal_upload_missing_items").on("click", ".btn_view_missing_pdf", function () {
            $("#modal_view_missing_item").modal("show");
            PDFObject.embed(base + "uploads/health_records/" + $(this).data("path") + ".pdf", "#pdf_view_div");
        });

        $("#modal_upload_missing_items").on("click", ".btn_remove_missing_pdf", function () {
            global_data.form_sample.find("#id").val($(this).data("id"));
            url = base + "tracker/remove_missing_item";
            data = global_data.form_sample.serialize();

            $.post({
                url: url,
                data: data,
                success: function (response) {
                    response = JSON.parse(response);
                    if (response.result == "success") {
                        get_uploaded_items();
                        success("Missing item uploaded record removed");
                    } else {
                        error("Failed to remove uploaded file");
                    }
                }
            });
        });

    });

    $("#form_signup").find("#btn_show_agreement").on("click", function () {
        $("fieldset.fs_form").hide();
        $("fieldset.fs_agreement").show();
        $(this).closest(".modal-dialog").removeClass("modal-md").addClass("modal-lg");
    });

    $("#form_signup").find("#btn_hide_agreement").on("click", function () {
        $("fieldset.fs_form").show();
        $("fieldset.fs_agreement").hide();
        $(this).closest(".modal-dialog").removeClass("modal-lg").addClass("modal-md");
    });



    function get_uploaded_items() {
        $("#form_upload_missing_items")[0].reset();
        $("#sample_form").find("#id").val(global_data.record_id);

        url = base + "tracker/get_upload_missing_item_info";
        data = $("#sample_form").serialize();

        $.post({
            url: url,
            data: data,
            success: function (response) {
                response = JSON.parse(response);
                if (response.result == "success") {
                    if (response.info.length > 0) {
                        html = "";
                        for (i = 0; i < response.info.length; i++) {
                            html += "<tr>" +
                                    "<td>" + (i + 1) + "</td>" +
                                    "<td>" + response.info[i].description + "</td>" +
                                    "<td>" + response.info[i].record_date + "</td>" +
                                    "<td><button class='btn btn-sm btn-info btn_view_missing_pdf' data-path='" + response.info[i].record_file + "'>View</button>\n\
                            <button class='btn btn-sm btn-danger btn_remove_missing_pdf' data-id='" + response.info[i].id + "'>Remove</button></td>" +
                                    "</tr>";

                        }
                        $("#table_missing_items").find("tbody").html(html);
                        $("#previously_uploaded_container").show();
                    } else {
                        $("#table_missing_items").find("tbody").html("");
                        $("#previously_uploaded_container").hide();
                    }

                } else {
                    error(data);
                }
            }
        });
    }

    function prepareUpload(event) {
        global_data.files = event.target.files;
    }

    function load_tracker(referral_id) {
        global_data.form_sample.find("#id").val(referral_id);
        data = global_data.form_sample.serialize();
        url = base + "tracker/load_tracker";

        $.post({
            url: url,
            data: data
        }).done(function (response) {
            data = JSON.parse(response);
            $("#tracker").html(data.content);
            if (data.stage2 == "") {
                $("#stage2, #stage3, #stage4").remove();
            } else if (data.stage3 == "") {
                $("#stage3, #stage4").remove();
            } else if (data.stage4 == "") {
                $("#stage4").remove();
            }
            set_clinic("#li_" + data.clinic_id);

            $("#modal_signup").find("#clinic_name_span").html(data.clinic_name);

            if (data.missing_items != null) {
                items = data.missing_items;
                root = $("#missing_upload_container");
                root.show();
                items_html = "";
                for (i = 0; i < items.length; i++) {
                    items_html += "<h3 style='margin-bottom: 0;'>" + (i + 1) + ". " + items[i].item_name + "</h3>";
                }
                root.find("#items_listing").html(items_html);


                setTimeout(function () {
                    operate_messages();
                }, 1000);

            }
        });

    }



    function set_clinic(li) {
        console.log("setting " + li + " as active");
        if ($(li)) {
            $(li).addClass("active");
        } else {
            setTimeout(function () {
                set_clinic(li);
            }, 200);
        }
    }

    function validate_email(email) {
        if (email === "")
            return true;
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

</script>


<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/tracker/reset.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/tracker/animate.css">
<!-- CSS Animation -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/tracker/awsmIcomoon.css">
<!--Icon font -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/tracker/awsm-timeline.css">
<!-- Resource style -->
<script src="<?php echo base_url(); ?>assets/js/tracker/modernizr.js"></script>
<script src="<?php echo base_url(); ?>assets/js/tracker/awsm-timeline.min.js"></script>
<style>
    #new::before{
        border-width: 10px 11px 10px 0px;
        border-right-color: #d8d8d8;
        left: -11px;
        top: 10px;
        color: #d8d8d8;
    }
    .awsm-timeline-style-8 .awsm-timeline-img {
        padding: 0;
        margin-left: -21px;
        width: 42px;
        height: 42px;
        color: #fff;
        text-align: center;
        font-size: 24px;
    }
    .awsm-timeline-style-8 .awsm-timeline-img {
        padding: 0;
        margin-left: -21px;
        width: 42px;
        height: 42px;
        color: #fff;
        text-align: center;
        font-size: 24px;
    }
    .awsm-timeline-style-8 .awsm-timeline-img span {
        display: inline-block;
        margin: auto;
        padding: 6px;
        min-height: 16px;
        min-width: 16px;
        background: #0BAC98;
        -webkit-border-radius: 50%;
        border-radius: 50%;
        vertical-align: top;
        border: 3px solid #fff;
        box-shadow: 0 0 5px 0 rgba(0, 0, 0, 0.5);
        -webkit-box-shadow: 0 0 5px 0 rgba(0, 0, 0, 0.5);
        vertical-align: middle;
    }
    .awsm-timeline .awsm-timeline-content-inner::before {
        color: #d8d8d8 !important;
        border-right-color: #d8d8d8;
    }
    .awsm-timeline .awsm-timeline-block:last-child .awsm-timeline-content .awsm-timeline-content-inner::before{color: #0BAC98 !important;
                                                                                                               border-right-color: #0BAC98;
    }
</style>
