<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Check Status | BlockHealth</title>
        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css">
        <!-- Custom Stylesheet -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.css">
        <style type="text/css">
            form .form-bottom .input-error {
                border-color: #d03e3e;
                color: #d03e3e;
            }
            form.registration-form fieldset {
                display: none;
            }
        </style>
    </head>
    <body class="no-nav">
        <div class="lax lax-full lax-center">
            <div class="error-container"></div>
            <div id="signup-container" class="sf">
                <img src="<?php echo base_url(); ?>assets/img/logo.png" class="img-responsive logo-image">
                <form id="form_verify_referral" class="form-horizontal registration-form check-status">
                    <fieldset>
                        <div class="form-bottom">
                            <div class="form-group">
                                <h3 id="signup-title" class="text-theme">Enter your reference code<br/> to check referral status</h3>
                                <hr/>
                                <input value="" type="text" class="form-control" id="referral_code" placeholder="Referral Code" name="referral_code">
                            </div>
                            <div class="form-group">
                                <button id="btn_check_status" type="button" class="btn btn-theme btn-full">Next</button>
                            </div>
                        </div>
                    </fieldset>

                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                </form>
            </div>
        </div>

        <div id="modal_success" class="modal fade" role="dialog" style="z-index: 2056 !important;">
            <div class="modal-dialog modal-md">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Operation Successful</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success"> </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="modal_error" class="modal fade" role="dialog" style="z-index: 2056 !important;">
            <div class="modal-dialog modal-md">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Operation Failed</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger"> </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/common.js"></script>
        <script type="text/javascript">
            global_data = {};
            base = "<?php echo base_url(); ?>";

            function IsJsonString(str) {
                try {
                    JSON.parse(str);
                } catch (e) {
                    return false;
                }
                return true;
            }

            function success(msg) {
                $("#modal_success").find(".alert-success").html(msg);
                view("modal_success");
            }

            function error(msg) {
                $("#modal_error").find(".alert-danger").html(msg);
                view("modal_error");
            }

            function view(modal_id) {
                setTimeout(function () {
                    $("#" + modal_id).modal("show");
                }, 1000);
            }

            $('.registration-form fieldset:first-child').fadeIn('slow');

            $("#btn_check_status").on("click", function () {
                form_verify_referral_submit();
            });


            $("#form_verify_referral").submit(function (event) {
                event.preventDefault();
                form_verify_referral_submit();
            });

            function form_verify_referral_submit() {
                //patch for demo
                if ($("#form_verify_referral").find("#referral_code").val() === "000000") {
                    location.href = "http://dev.blockhealth.co/adi-dev/demo/rp/dashboard";
                }

                //patch ends here

                url = "<?php echo base_url(); ?>" + "login/verify_referral_code";
                data = $("#form_verify_referral").serialize();

                $.post({
                    url: url,
                    data: data
                }).done(function (response) {
                    data = JSON.parse(response);
                    if (data.response == "success") {
                        location.href = "<?php echo base_url(); ?>tracker/referral/" + data.ref_code;
                    } else {
                        error(data.desc);
                    }
                });
            }
        </script>

    </body>
    <script>'undefined' === typeof _trfq || (window._trfq = []);
        'undefined' === typeof _trfd && (window._trfd = []), _trfd.push({'tccl.baseHost': 'secureserver.net'}), _trfd.push({'ap': 'cpbh'}, {'server': 'a2plvcpnl32357'}) // Monitoring performance to make your website faster. If you want to opt-out, please contact web hosting support.</script>

</html>