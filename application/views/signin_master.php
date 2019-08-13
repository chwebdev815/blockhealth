<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sign In | BlockHealth</title>
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/font-awesome.min.css">
        <link rel="shortcut icon" type="image/png" href="<?php echo base_url(); ?>assets/img/favicon-32x32.png"/>
<!--        <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>-->
    </head>
    <body class="no-nav">
        <div class="lax lax-full lax-center">     <img src="<?php echo base_url(); ?>assets/img/IMG_1899.PNG" class="img-responsive" style="margin: 0 auto; width: 30%;">
            <div class="lax-content center-block">
                <h3 class="text-theme" style="color: white; text-transform: none;">Sign In to web app</h3>

                <style>
                    .panel-login>.panel-heading{
                        float: none;
                        display: inline-block;
                        width: 100%;
                    }
                    .panel-login>.panel-heading a{
                        text-decoration: none;
                        color: #666;
                        font-weight: bold;
                        font-size: 15px;
                        -webkit-transition: all 0.1s linear;
                        -moz-transition: all 0.1s linear;
                        transition: all 0.1s linear;
                        display:inline-block;
                        width:100%;
                        text-align:center;
                        padding:10px 10px;
                        background:#dedede;
                    }
                    .panel-login>.panel-heading a.active{
                        color: #ffffff;
                        background:#03b6a2;
                        box-shadow: 0px 5px 10px rgba(0,0,0,0.15);
                    }

                    .panel-login input[type="text"],.panel-login input[type="email"],.panel-login input[type="password"] {
                        height: 45px;
                        border: 1px solid #ddd;
                        -webkit-transition: all 0.1s linear;
                        -moz-transition: all 0.1s linear;
                        transition: all 0.1s linear;
                    }
                    .panel-login input:hover,
                    .panel-login input:focus {
                        outline:none;
                        -webkit-box-shadow: none;
                        -moz-box-shadow: none;
                        box-shadow: none;
                        border-color: #ccc;
                    }
                </style>

                <div id="signup-container" class="sf">

                    <div class="panel panel-login">
                        <div class="panel-heading">
                            <div class="row">
<!--                                <div class="col-xs-6">
                                    <a href="#" class="active" id="login-form-link">Speciality Clinic</a>
                                </div>-->
<!--                                <div class="col-xs-6">
                                    <a href="#" id="register-form-link">Referring Physician</a>
                                </div>-->
                            </div>
                            <hr>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form id="speciality-clinic" class="form-horizontal" method="post" action="<?php echo base_url(); ?>login/verify_login" role="form" style="display: block;">
                                        <input type="hidden" value="c" name="login_type">

                                        <div id="signup-first">
                                            <div class="form-group">
                                                <div class="col-xs-12">                                    
                                                    <input type="email" required class="form-control" 
                                                           id="signup-email" placeholder="Email" 
                                                           name="signup-email"/> 
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-xs-12">                                    
                                                    <input type="password" class="form-control" id="signup-pw" placeholder="Password" name="signup-pw">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-xs-12">                                 
                                                    <button type="submit" class="btn btn-theme btn-spacious btn-full" id="signup-submit" value="signup-submit" name="signup-submit">Sign In</button> 
<!--                                                    <button type="submit" class="btn btn-theme btn-spacious btn-full" id="signup-submit" value="signup-submit" name="signup-submit">Speciality Clinic Sign In</button>                                -->
                                                </div>
                                            </div>
                                            <div class="error-container">                    
                                                <?php
                                                if (isset($validation_errors)) {
                                                    echo "<div class='alert alert-danger'>" . $validation_errors . "</div>";
                                                }
                                                ?>                
                                            </div>
                                        </div>
                                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">         
                                    </form>


                                    <form id="family-practice" class="form-horizontal" method="post" action="<?php echo base_url(); ?>login/verify_login" role="form" style="display: none;">
                                        <div id="signup-first">
                                            <input type="hidden" value="rp" name="login_type">
                                            <div class="form-group">
                                                <div class="col-xs-12">                                    
                                                    <input type="email" required class="form-control" id="signup-email" placeholder="Email" name="signup-email">                                
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-xs-12">                                    
                                                    <input type="password" class="form-control" id="signup-pw" placeholder="Password" name="signup-pw">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-xs-12">                                    
                                                    <button type="submit" class="btn btn-theme btn-spacious btn-full" id="signup-submit" value="signup-submit" name="signup-submit">
                                                        Family Practice Sign In
                                                    </button>                                
                                                </div>
                                            </div>
                                            <div class="error-container">                    
                                                <?php
                                                if (isset($validation_errors)) {
                                                    echo "<div class='alert alert-danger'>" . $validation_errors . "</div>";
                                                }
                                                ?>                
                                            </div>
                                        </div>
                                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">                    
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<!--            <a href="<?php echo base_url(); ?>referral_login">
                <h4 style="text-align:center;color: white;">
                    Enter Referral Tracking Code
                </h4>
            </a>-->
        </div>


        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->        
        <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>        
        <!-- Include all compiled plugins (below), or include individual files as needed -->        
        <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>        
        <script src="<?php echo base_url(); ?>assets/js/jquery.validate.min.js"></script>    
        <script src="<?php echo base_url(); ?>assets/js/common.js"></script>    
        <script src="<?php echo base_url(); ?>assets/js/custom.js"></script>      
        <script>
            $(function () {

                $('#login-form-link').click(function (e) {
                    $("#speciality-clinic").delay(100).fadeIn(100);
                    $("#family-practice").fadeOut(100);
                    $('#register-form-link').removeClass('active');
                    $(this).addClass('active');
                    e.preventDefault();
                });
                $('#register-form-link').click(function (e) {
                    $("#family-practice").delay(100).fadeIn(100);
                    $("#speciality-clinic").fadeOut(100);
                    $('#login-form-link').removeClass('active');
                    $(this).addClass('active');
                    e.preventDefault();
                });

            });
            
            //validation for signup form            
            $('#loginForm').validate({
                rules: {
                    "signup-email": {
                        required: true,
                        email: true,
                    },
                    "signup-pw": {
                        required: true,
                    },
                },
                errorPlacement: function (error, element) {
                    element.parents('.form-group').addClass('has-error');
                    element.after(error);
                },
                success: function (label, element) {
                    label.parents('.form-group').removeClass('has-error');
                    label.remove();
                },
            });
        </script>    
    </body>
</html>