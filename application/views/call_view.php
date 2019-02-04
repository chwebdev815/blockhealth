<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Demo | BlockHealth</title>
        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/font-awesome.min.css">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="no-nav">
        <div class="lax lax-full lax-center">
            <div class="lax-content text-center center-block bg-white" style="max-width: 600px">
                <div style="padding: 20px; padding-bottom: 40px">
                    <form name ="userinput" class="text-center border border-light p-5" id="zipsForm" action="http://35.203.47.37/call_view/call" method="POST">
                        <p class="h4 mb-4">Add Key</p>
                        <div class="form-group">
                            <input type="text" name="patient_name" class="form-control mb-4" placeholder="Name" >
                        </div>
                        <div class="form-group">
                            <input type="text" name="visit_name" class="form-control mb-4" placeholder="Patient Visit Name" >
                        </div>
                        <div class="form-group">
                            <input type="text" name="clinic_name" class="form-control mb-4" placeholder="Clinic Name" >
                        </div>
                        <div class="form-group">
                            <input type="text" name="visit_date" class="form-control mb-4" placeholder="Appointment Date" >
                        </div>
                        <div class="form-group">
                            <input type="text" name="visit_time" class="form-control mb-4" placeholder="Appointment time" >
                        </div>
                        <div class="form-group">
                            <input type="text" name="phone_number" class="form-control mb-4" placeholder="number" >
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-info btn-block" type="submit" id="addKey" name="submit"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/custom.js"></script>
    </body>
</html>
