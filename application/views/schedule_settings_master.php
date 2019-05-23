<style>
    .popup_content {
        margin: 10px;
        padding: 0 10px;
        max-width: 100%;
        background: white;
        -webkit-box-shadow: 0 5px 15px rgba(0,0,0,.5);
        box-shadow: 0 5px 15px rgba(0,0,0,.5);
    }

    .popup_wrapper{

        top: 10%!important;
    }

    .popup_background{
        z-index: 0!important;
    }

</style>
<script src="https://cdn.alloyui.com/3.0.1/aui/aui-min.js"></script>	
<!-- <button class="popup2_open">Click me 2!</button>
    
    <button class="popup3_open">Click me 2!</button> -->


<section id="popup2">
    <img id="image_for_preview" width="" height="500" src="" />
</section>


<!--   
<div class="preview-image-container">
<img id="hover-img-preview" class="imggy-preview">
</div> 
-->
<div class="db-content-inside clearfix">

    <div class="col-md-12">
        <div id="myScheduler">

            <div class="x_title" style="padding-left: 0px;">
                <h2 id="clinic_physician_container">
                    <?php echo form_open("", array('class' => 'email', 'id' => 'form_physician_timing')) ?>
                    <select id="physicians" placeholder="Clinic Physician" name="physicians" class="form-control">
                    </select> 
                    <small id="scheduled_patients">1 Scheduled patients</small>
                </h2>
                <div class="clearfix"></div>
            </div>

            <div class="row">
                <div class="col-md-6">

                    <div class="form-group">
                        <div class="col-sm-6 col-xs-12" style="padding-left: 0px;">
                            <label for="">Start Time</label>
                            <div class="input-group date form_time col-md-12" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                                <input class="form-control" size="16" type="text" name="visit_time" readonly="">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                            </div>
                            <input type="hidden" id="dtp_input3" value="">
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <label for="">End Time</label>
                            <div class="input-group date form_time col-md-12" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                                <input class="form-control" size="16" type="text" name="visit_time" readonly="">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                            </div>
                            <input type="hidden" id="dtp_input3" value="">
                        </div>
                    </div>
                </div>

                <div class="col-md-6" style="margin-top: 28px;">

                    <div id="checklist_div">

                        <div style="display:inline-block" class="checkbox"><label><input type="checkbox" value="" name="" class="checked" checked=""><span class="cr"><i class="cr-icon fa fa-check"></i></span>M</label></div>

                        <div style="display:inline-block" class="checkbox"><label><input type="checkbox" value="" name="" class="checked"><span class="cr"><i class="cr-icon fa fa-check"></i></span>Tu</label></div>

                        <div style="display:inline-block" class="checkbox"><label><input type="checkbox" value="" name="" class="checked"><span class="cr"><i class="cr-icon fa fa-check"></i></span>W</label></div>

                        <div style="display:inline-block" class="checkbox"><label><input type="checkbox" value="" name="" class="checked"><span class="cr"><i class="cr-icon fa fa-check"></i></span>Th</label></div>

                        <div style="display:inline-block" class="checkbox"><label><input type="checkbox" value="" name="" class="checked"><span class="cr"><i class="cr-icon fa fa-check"></i></span>F</label></div>

                        <div style="display:inline-block" class="checkbox"><label><input type="checkbox" value="" name="" class="checked"><span class="cr"><i class="cr-icon fa fa-check"></i></span>Sa</label></div>

                        <div style="display:inline-block" class="checkbox"><label><input type="checkbox" value="" name="" class="checked"><span class="cr"><i class="cr-icon fa fa-check"></i></span>Su</label></div>	

                    </div>


                </div>

            </div>

            <p>&nbsp;</p>

        </div>
    </div>



    <script>
        YUI().use(
                'aui-scheduler',
                function (Y) {
                    var events = [
                        {
                            content: 'AllDay',
                            endDate: new Date(2013, 1, 5, 23, 59),
                            startDate: new Date(2013, 1, 5, 0)
                        },
                        {
                            color: '#8D8',
                            content: 'Colorful',
                            endDate: new Date(2013, 1, 6, 6),
                            startDate: new Date(2013, 1, 6, 2)
                        },
                        {
                            content: 'MultipleDays',
                            endDate: new Date(2013, 1, 8),
                            startDate: new Date(2013, 1, 4)
                        },
                        {
                            content: 'Disabled',
                            disabled: true,
                            endDate: new Date(2013, 1, 8, 5),
                            startDate: new Date(2013, 1, 8, 1)
                        },
                        {
                            content: 'Meeting',
                            endDate: new Date(2013, 1, 7, 7),
                            meeting: true,
                            startDate: new Date(2013, 1, 7, 3)
                        },
                        {
                            color: '#88D',
                            content: 'Overlap',
                            endDate: new Date(2013, 1, 5, 4),
                            startDate: new Date(2013, 1, 5, 1)
                        },
                        {
                            content: 'Reminder',
                            endDate: new Date(2013, 1, 4, 4),
                            reminder: true,
                            startDate: new Date(2013, 1, 4, 0)
                        }
                    ];

                    var agendaView = new Y.SchedulerAgendaView();
                    var monthView = new Y.SchedulerMonthView();
                    var weekView = new Y.SchedulerWeekView();
                    var dayView = new Y.SchedulerDayView();
                    var eventRecorder = new Y.SchedulerEventRecorder();


                    new Y.Scheduler(
                            {
                                activeView: monthView,
                                boundingBox: '#myScheduler',
                                date: new Date(2013, 1, 4),
                                eventRecorder: eventRecorder,
                                items: events,
                                render: true,
                                views: [monthView, weekView, dayView, agendaView]
                            }
                    );
                }
        );
    </script>



</div>


<!--<div class="modal fade" id="modal_preview" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">            
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <img id="hover-img-preview" width="300" height="300">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>-->


<div id="modal_delete_referral" class="modal fade" role="dialog" style="z-index: 2056 !important;">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirm Delete</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    Are you sure you would like to delete from inbox?
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_delete_referral" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Delete</button>
            </div>
        </div>
    </div>
</div>
<div id="modal_confirm_assign_referral" class="modal fade" role="dialog" style="z-index: 2056 !important;">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirm Assign Referral</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    Are you sure to Assign this Referral to for Selected Patient?
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_confirm_referral" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Confirm</button>
                <button type="button" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="eFax-modal" tabindex="-1" role="dialog" aria-labelledby="delete-patient-label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-md-4 marg-top-10px">
                    <button type="button" class="btn btn-link db-nav-more-trigger back-link" data-toggle="modal" data-target="#add-referral-modal" data-dismiss="modal">
                        <i class="fa fa-angle-left fa-2x"></i>
                    </button>
                    <span id="file_info"></span>
                </div>
                <div class="col-md-8 text-right">
                    <span class="eFax-bar-actions marg-top-10px">
                        <a href="javascript:void(0)" id="btn_view_print_referral">Print </a> &nbsp;
                        <a href="javascript:void(0)" id="btn_view_delete_referral">Delete </a> &nbsp;
                        <a href="javascript:void(0)" id="btn_download_referral" download>Download </a>
                        <a href="javascript:void(0)" class="" id="btn_view_save_referral">Save </a>
                    </span>
                    <button  data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Loading..." class="btn btn-theme btn-alt-theme btn-toggle-referral pull-right">
                        New Referral
                    </button>

                </div>
            </div>
            <div class="modal-body">
                <div id="wrap-container" class="">

                    <div id="save-patient-wrapper">

                        <input type="hidden" name="id" id="id" />
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <fieldset>
                            <form id="form_patient_save" class="form-horizontal patients-details-form" autocomplete="off">

                                <div class="form-bottom">

                                    <div class="form-group row left-padd-20px right-padd-5px">
                                        <div class="">
                                            <h4 class="modal-title" id="myModalLabel">Select Patient</h4>
                                            <br/>
                                        </div>
                                        <div class="pull-left">
                                            <button id="btnStartPatientCrop" type="button" class="btn btn-theme">
                                                <span class="fa fa-crop fa-2"></span>
                                            </button>
                                            <button id="btn_extract_patient" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Loading..." type="button" class="btn btn-theme btn-alt-theme btn-autofil">
                                                <i class="fa" aria-hidden="true"><img src="assets/img/magic-wand.png" width="16"></i>&nbsp;&nbsp;Auto Fill
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-group left-padd-20px right-padd-5px">

                                        <div class="form-group row">
                                            <div class="col-lg-12 col-sm-12 col-xs-12">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
                                                    <input id="pat_search_by_name" type="text" class="form-control" name="pat_search_by_name" placeholder="Enter a patient name to start search">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-12">
                                                <h4 class="modal-title" id="task_h4">Task Details</h4>
                                            </div>
                                            <div class="alert alert-danger" id="patient_error" style="display: none;"></div>
                                            <div class="alert alert-success" id="patient_success" style="display: none;"></div>
                                            <input type="hidden" id="id" name="id" />
                                            <input type="hidden" id="pat_ohip" name="pat_ohip" />
                                            <input type="hidden" id="pat_dob_day" name="pat_dob_day" />
                                            <input type="hidden" id="pat_dob_month" name="pat_dob_month" />
                                            <input type="hidden" id="pat_dob_year" name="pat_dob_year" />
                                            <input type="hidden" id="pat_lname" name="pat_lname" />

                                            <div class="col-lg-12">
                                                <label for="assign_physician">
                                                    <strong>Assign Physician</strong>
                                                </label>
                                                <select id="assign_physician" placeholder="Assign Physician" name="assign_physician" class="form-control">
                                                </select>
                                            </div>
                                            <div class="col-lg-12">
                                                <label for="new-patient-ohip">
                                                    <strong>Select Record Type</strong>
                                                </label>
                                                <select id="record_type" placeholder="Record Type" name="record_type" class="form-control">
                                                    <option data-show=".file-upload" selected>Referral letter</option>
                                                    <option data-show=".file-upload">Consult note</option>
                                                    <option data-show=".file-upload">Imaging note</option>
                                                    <option data-show=".file-upload">Admin note</option>
                                                    <option data-show=".file-upload">Lab test</option>
                                                    <option data-show=".file-upload">Prescription</option>
                                                    <option data-show=".file-upload">Insurance note</option>
                                                    <option data-show=".file-upload">Record release</option>
                                                    <option data-show=".file-upload">Intake form</option>
                                                    <option data-show=".file-upload">Other</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-12 cl-t-listing wrapper_div">
                                                <label for="assign_physician">
                                                    <strong>Enter Details/Notes</strong>
                                                </label>
                                                <div>
                                                    <textarea name="description" style="height: 100px" class="form-control" placeholder="Enter Details/Notes" id="description"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-12 cl-t-listing wrapper_div">
                                                <button type="button" id="btn_save_task" class="btn btn-theme">Save</button>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-12 cl-t-listing wrapper_div">
                                                <div id="patient_success_display" class="success-icon pull-right menu">
                                                    <span class="fa fa-check tick-icon"></span>
                                                </div>
                                            </div>
                                        </div> 

                                    </div>
                                </div>
                            </form>
                        </fieldset>
                    </div>

                    <!-- Sidebar -->
                    <div id="sidebar-wrapper">
                        <form id="signupForm" class="form-horizontal patients-details-form" method="post" action="" enctype="multipart/form-data" autocomplete="off">
                            <input type="hidden" name="id" id="id" />
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            <fieldset>
                                <div class="form-bottom">
                                    <div class="form-group row left-padd-20px right-padd-5px">
                                        <div class="pull-left">
                                            <button id="btnStartCrop" type="button" class="btn btn-theme">
                                                <span class="fa fa-crop fa-2"></span>
                                            </button>
                                            <button id="btnAutoFill" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Loading..." type="button" class="btn btn-theme signup-next btn-autofil"><i class="fa" aria-hidden="true"><img src="assets/img/magic-wand.png" width="16"></i>&nbsp;&nbsp;Auto Fill</button>
                                        </div>
                                        <div class="pull-right">
                                            <button type="button" class="btn btn-theme signup-next btn-next">Next</button>
                                        </div>
                                    </div>
                                    <div class="form-group left-padd-20px right-padd-5px">
                                        <h4 class="modal-title" id="myModalLabel">Add Patient Details</h4>
                                        <div class="form-group row">
                                            <div class="col-lg-12">
                                                <label for="new-patient-name">Full Name *</label>
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <input type="text" class="form-control required" name="pat_fname" id="new-patient-firstname" placeholder="First Name" autocomplete="off">
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <input type="text" class="form-control required" name="pat_lname" id="new-patient-lastname" placeholder="Last Name" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-12">
                                                <label for="new-patient-birthdate">
                                                    Date of Birth *
                                                </label>
                                            </div>
                                            <div class="col-sm-4 col-xs-12">
                                                <select name="pat_dob_day" id="pat_dob_day" class="required"></select>
                                            </div>
                                            <div class="col-sm-4 col-xs-12 no-left-right-padd">
                                                <select name="pat_dob_month" id="pat_dob_month" class="required"></select>
                                            </div>
                                            <div class="col-sm-4 col-xs-12">                    
                                                <select name="pat_dob_year" id="pat_dob_year" class="required"></select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6 col-xs-12">
                                                <label for="new-patient-ohip">
                                                    OHIP #
                                                </label>
                                                <input type="text" class="" name="pat_ohip" id="new-patient-ohip" placeholder="1234-123-123-AB" autocomplete="off">
                                            </div>


                                            <div class="col-sm-6 col-xs-12">
                                                <label for="new-patient-ohip">
                                                    Sex
                                                </label>
                                                <select name="pat_gender" id="pat_gender" class="required">
                                                    <option value="male" selected>Male</option>
                                                    <option value="female">Female</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-12">
                                                <label for="">
                                                    Cell Phone
                                                </label>
                                            </div>
                                            <div class="col-lg-12 no-left-right-padd">
                                                <input type="text" class="form-control" name="pat_cell_phone" id="patient-cell-phone" placeholder="Mobile Number" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-12">
                                                <label for="">
                                                    Email
                                                </label>
                                            </div>
                                            <div class="col-lg-12">
                                                <input type="email" class="form-control valid_email" name="pat_email" id="patient-email-id" placeholder="Email" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-12">
                                                <label for="">
                                                    Address
                                                </label>
                                            </div>
                                            <div class="col-lg-12">
                                                <input type="text" class="form-control geo_complete" name="pat_address" id="pat_geocomplete" placeholder="Address" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="form-bottom">
                                    <div class="form-group row  left-padd-20px right-padd-5px">
                                        <div class="pull-left">
                                            <button type="button" class="icon-prev btn-previous" style="font-size: 13px;"><i class="fa fa-angle-left fa-2x"></i></button>
                                            <button id="btnStartCrop2" type="button" class="btn btn-theme">
                                                <span class="fa fa-crop fa-2" style="font-size: 12px;"></span>
                                            </button>
                                            <button id="btn_extract_physician" style="display: none" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Loading..." type="button" class="btn btn-theme signup-next btn-autofil"><i class="fa" aria-hidden="true"><img src="assets/img/magic-wand.png" width="16"></i>&nbsp;&nbsp;Auto Fill</button>
                                            <button id="btn_find_physician_match" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Loading..." type="button" class="btn btn-theme btn-autofil"><i class="fa" aria-hidden="true"><img src="assets/img/magic-wand.png" width="16"></i>&nbsp;&nbsp;Find Match</button>
                                        </div>
                                        <div class="pull-right">
                                            <button type="button" class="btn btn-theme signup-next btn-next pull-right check_physician" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Loading...">Next</button>
                                        </div>
                                    </div>
                                    <div class="form-group left-padd-20px right-padd-5px">
                                        <h4 class="modal-title" id="myModalLabel">Add Physician Details</h4>
                                        <div class="alert alert-danger" id="physician_error" style="display: none;"></div>
                                        <div class="alert alert-success" id="physician_success" style="display: none;"></div>
                                        <div class="form-group row">
                                            <div class="col-lg-12">
                                                <label for="new-patient-name">Full Name *</label>
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <input type="text" class="form-control required" name="dr_fname" id="dr_fname" placeholder="First Name">
                                            </div>
                                            <div class="col-sm-6 col-xs-12">
                                                <input type="text" class="form-control required" name="dr_lname" id="dr_lname" placeholder="Last Name">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-12">
                                                <label for="">
                                                    Phone Number
                                                </label>
                                            </div>
                                            <div class="col-lg-12">
                                                <input type="number" class="form-control" name="dr_phone_number" id="dr_phone_number" placeholder="Phone Number">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-12">
                                                <label for="">
                                                    Fax Number
                                                </label>
                                            </div>
                                            <div class="col-lg-12">
                                                <input type="number" class="form-control" name="dr_fax" id="dr_fax" placeholder="Fax Number">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-12">
                                                <label for="">
                                                    Email
                                                </label>
                                            </div>
                                            <div class="col-lg-12">
                                                <input type="email" class="form-control valid_email" name="dr_email" id="dr_email" placeholder="Email">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-12">
                                                <label for="">
                                                    Address
                                                </label>
                                            </div>
                                            <div class="col-lg-12">
                                                <input type="text" class="form-control geo_complete" name="dr_address" id="dr_geocomplete" placeholder="Address">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-12">
                                                <label for="new-patient-ohip">
                                                    Billing Number
                                                </label>
                                            </div>
                                            <div class="col-lg-12">
                                                <input type="text" class="form-control" name="dr_billing_num" id="dr_billing_num" placeholder="Billing Number">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="form-group row">
                                    <div class="col-md-2 col-sm-2 col-xs-12">
                                        <button type="button" class="icon-prev btn-previous"><i class="fa fa-angle-left fa-2x"></i></button>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <button id="btnAutofillTriage" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Loading..." type="button" class="btn btn-theme signup-next btn-autofil"><i class="fa" aria-hidden="true"><img src="assets/img/magic-wand.png" width="16" /></i>&nbsp;&nbsp;Auto Fill</button>
                                    </div>                                        
                                    <div class="col-md-4 col-sm-5 col-xs-12">
                                        <button type="button" class="btn btn-theme signup-next btn-next pull-right">Next</button>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <h4 style="display: block;" class="modal-title" id="myModalLabel">Clinical Triage</h4>
                                        <label for="new-patient-ohip">
                                            Select Priority
                                        </label>
                                    </div>
                                    <div class="col-lg-12">
                                        <select id="priority" placeholder="Priority" name="priority" class="form-control">
                                            <option disabled selected>Select Priority</option>
                                            <option value="urgent">Urgent (less than 1 week)</option>
                                            <option value="sub_urgent">Sub-urgent (less than 2 weeks)</option>
                                            <option value="routine">Routine (next available date)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-12 cl-t-listing wrapper_div">
                                        <ul>
                                            <li><strong>Reason for Referral</strong></li>
                                        </ul>

                                        <div>
                                            <div class="input_fields_wrap edit_reasons"></div>
                                            <button type="button" id="btn_add_reason" class="add_field_button"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;Entry</button>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <div class="col-lg-12 cl-t-listing wrapper_div">
                                        <ul>
                                            <li><strong>Disease or Syndrome</strong></li>
                                        </ul>

                                        <div>
                                            <div class="input_fields_wrap edit_diseases"></div>
                                            <button type="button" id="btn_add_diseases" class="add_field_button"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;Entry</button>
                                        </div>

                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-12 cl-t-listing wrapper_div">
                                        <ul>
                                            <li><strong>Sign or Symptoms</strong></li>
                                        </ul>
                                        <div>
                                            <div class="input_fields_wrap edit_symptoms"></div>
                                            <button type="button" id="btn_add_symptoms" class="add_field_button"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;Entry</button>
                                        </div>                                          
                                    </div>                                  
                                </div>  


                                <div class="form-group row">
                                    <div class="col-lg-12 cl-t-listing wrapper_div">
                                        <ul>
                                            <li><strong>Procedures and devices</strong></li>
                                        </ul>
                                        <div>
                                            <div class="input_fields_wrap edit_devices"></div>
                                            <button type="button" id="btn_add_devices" class="add_field_button"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;Entry</button>
                                        </div>              

                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-lg-12 cl-t-listing wrapper_div">
                                        <ul>
                                            <li><strong>Medications</strong></li>
                                        </ul>
                                        <div>
                                            <div class="input_fields_wrap edit_medications"></div>
                                            <button type="button" id="btn_add_medications" class="add_field_button"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;Entry</button>
                                        </div>              

                                    </div>
                                </div>        


                                <div class="form-group row">
                                    <div class="col-lg-12 cl-t-listing wrapper_div">
                                        <ul>
                                            <li>
                                                <strong>Lab or Test Results</strong>
                                                <a href="javascript:void(0)" id="btn_labtest_autofill" style="color:red">Auto Fill</a>
                                            </li>
                                        </ul>
                                        <div>
                                            <div class="input_fields_wrap edit_tests"></div>
                                            <button type="button" id="btn_add_tests" class="add_field_button"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;Entry</button>
                                        </div>
                                    </div>
                                </div>

                            </fieldset>
                            <fieldset>
                                <div class="form-group row">
                                    <div class="col-sm-6 col-xs-12">
                                        <button type="button" class="icon-prev btn-previous"><i class="fa fa-angle-left fa-2x"></i></button>
                                    </div>
                                    <div class="col-sm-6 col-xs-12">
                                        <button type="button" id="btn_add_referral" class="btn btn-theme pull-right" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing...">Add Referral</button>
                                    </div>
                                </div>
                                <h4 class="modal-title" id="myModalLabel">Referral Checklist</h4>
                                <div class="form-group">
                                    <div class="col-lg-12 wrapper_div">
                                        <div id="referral_checklist">
                                        </div>
                                        <div>
                                            <div class="input_fields_wrap edit_documents"></div>
                                            <button type="button" id="btn_add_documents" class="add_field_button"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;Add Checklist Item</button>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                    <!-- /#sidebar-wrapper -->
                    <!-- Page Content -->
                    <div id="page-content-wrapper">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <img src="" id="overlay_image" class="ov_image_close" style="display: none; " />
                        <div class="container-fluid image-viewer-height">
                            <div class="editor">
                                <div class="canvas">
                                    <img id="cropboard" src="#" alt="Picture">
                                </div>
                                <img id="_blob" src="#" alt="test" class="hidden">
                                <div class="cropheader">
                                    <label  id ="currentPage" class="pageInf"></label>
                                    <label  id ="clippedData" class="pageInf"></label>
                                    <div class="btn-group" role="group" style="float:right;">
                                        <button id="btnPrevPage" type="button" class="btn btn-secondary" title="Prev Page"><span class="glyphicon glyphicon-menu-left"></span></button>
                                        <button id="btnNextPage" type="button" class="btn btn-secondary" title="Next Page"><span class="glyphicon glyphicon-menu-right"></span></button>
                                    </div>
                                </div>
                                <div class="toolbar">
                                    <button class="toolbar__button" data-action="move" title="Move"><span class="fa fa-arrows"></span></button>
                                    <button class="toolbar__button" data-action="crop" title="Crop"><span class="fa fa-crop"></span></button>
                                    <button class="toolbar__button" data-action="zoom-in" title="Zoom In"><span class="fa fa-search-plus"></span></button>
                                    <button class="toolbar__button" data-action="zoom-out" title="Zoom Out"><span class="fa fa-search-minus"></span></button>
                                    <button class="toolbar__button" data-action="rotate-left" title="Rotate Left"><span class="fa fa-rotate-left"></span></button>
                                    <button class="toolbar__button" data-action="rotate-right" title="Rotate Right"><span class="fa fa-rotate-right"></span></button>
                                    <button class="toolbar__button" data-action="flip-horizontal" title="Flip Horizontal"><span class="fa fa-arrows-h"></span></button>
                                    <button class="toolbar__button" data-action="flip-vertical" title="Flip Vertical"><span class="fa fa-arrows-v"></span></button>
                                </div>
                            </div>
                            <div id="result">             
                            </div>
                            <!--                            <div id="example1"></div>-->
                        </div>
                    </div>
                    <!-- /#page-content-wrapper -->
                </div>
            </div>
            <div class="modal-footer">  </div>
        </div>
    </div>
</div>
<!--<div class="modal-footer">  </div> -->
<!-- Save eFax Modal Modal //-->
<!--<div class="modal fade" id="add_health_record" tabindex="-1" role="dialog" aria-labelledby="add-record-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> Add Health Record</h4>
            </div>
            <div class="modal-body">
                <form id="form_health_record">
                    <input type="hidden" name="id" id="id" />
                    <input type="hidden" name="target" id="target" />
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for="new-patient-birthdate">
                                Select Record
                            </label>
                        </div>
                        <div class="col-lg-12">
                            <select name="record_type" id="signup-speciality" class="div-toggle" data-target=".my-info-1" placeholder="Speciality" name="signup-speciality" class="form-control">
                                <option value="Referral Form" data-show=".file-upload">Referral Form</option>
                                <option value="Clinical Notes" data-show=".file-upload">Clinical Notes</option>
                                <option value="Lab Results" data-show=".file-upload">Lab Results</option>
                                <option value="Diagnosis and Reason for Referral" data-show=".reason-referral">Diagnosis and Reason for Referral</option>
                                <option value="Surgical Notes" data-show=".file-upload">Surgical Notes</option>
                                <option value="Prescriptions &amp; medications" data-show=".file-upload">Prescriptions &amp; medications</option>
                            </select>
                        </div>
                    </div>
                    <div class="my-info-1_">
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <label for="new-patient-ohip">
                                    Description
                                </label>
                            </div>
                            <div class="col-lg-12">
                                <textarea name="description" style="height: 100px" class="form-control" placeholder="Enter Record Details" id="referral-note"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a id="btn_save_health_record" href="#" class="btn btn-theme">
                    <i class="fa fa-plus-circle"></i>&nbsp;&nbsp;Add Health Record
                </a>
                <button type="button" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>-->
<div class="modal fade" id="save-efax-modal" tabindex="-1" role="dialog" aria-labelledby="add-referral-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-md-5 col-sm-5 col-xs-12">
                    <h4 class="modal-title marg-top-10px" id="myModalLabel">Select Existing Patient</h4>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12">
                </div>
                <div class="col-md-1 col-sm-1 col-xs-1">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            </div>
            <div class="modal-body">
                <form id="new-referral-form">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-hover db-table" id="table_clinic_patients">
                                    <thead>
                                        <tr>
                                            <th>Patient Name</th>
                                            <th>Date of Birth</th>
                                            <th>OHIP</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Add eFax Medical Record Modal -->

<script src="https://cdn.jsdelivr.net/gh/vast-engineering/jquery-popup-overlay@2/jquery.popupoverlay.min.js"></script>


<script>
        $('#popup2, #popup3').popup({
            pagecontainer: '#page',
            type: 'tooltip',
            background: true,
            color: '#fff',
            escape: true,
            horizontal: 'left',
            vertical: 'middle'

        });
</script>	