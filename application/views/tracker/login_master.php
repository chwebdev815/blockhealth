<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Blockhealth</title>

        <!-- Bootstrap core CSS-->
        <link href="<?php echo tracker_assets(); ?>vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom fonts for this template-->
        <link href="<?php echo tracker_assets(); ?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

        <!-- Custom styles for this template-->
        <link href="<?php echo tracker_assets(); ?>css/sb-admin.css" rel="stylesheet">

    </head>

    <body class="bg-dark">

        <div class="container">
            <div class="card card-login mx-auto mt-5">
                <div class="card-header">Login</div>
                <div class="card-body">
                    <?php echo form_open("admin_login/verify_admin_login", array('id' => 'form_tracker_login', 'autocomplete' => 'off')); ?>
                    <div class="form-group">
                        <div class="form-label-group">
                            <input type="email" id="tracker_email" name="tracker_email" class="form-control" placeholder="Email address" required="required" autofocus="autofocus">
                            <label for="tracker_email">Email address</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-label-group">
                            <input type="password" id="tracker_password" name="tracker_password" class="form-control" placeholder="Password" required="required">
                            <label for="tracker_password">Password</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="remember-me">
                                Remember Password
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Sign in</button>
                    <br/>
                    <div class="error-container">                    
                        <?php
                        if (isset($validation_errors)) {
                            echo "<div class='alert alert-danger'>" . $validation_errors . "</div>";
                        }
                        ?>                
                    </div>
                    </form>
                    <div class="text-center">
                        <a class="d-block small mt-3" href="register.html">Register an Account</a>
                        <a class="d-block small" href="forgot-password.html">Forgot Password?</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Bootstrap core JavaScript-->
        <script src="<?php echo tracker_assets(); ?>vendor/jquery/jquery.min.js"></script>
        <script src="<?php echo tracker_assets(); ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- Core plugin JavaScript-->
        <script src="<?php echo tracker_assets(); ?>vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="<?php echo tracker_assets(); ?>theme/js/lib/form-validation/jquery.validate.min.js"></script>
        <script>
            $(document).ready(function () {
                $("#form_tracker_login").validate({
                    rules: {
                        tracker_email: {
                            required: true,
                            email: true
                        },
                        tracker_password: {
                            required: true
                        }
                    }
                });
            });
        </script>
    </body>
</html>
