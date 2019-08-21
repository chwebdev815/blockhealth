<style>
    form .form-bottom .input-error {
        border-color: #d03e3e;
        color: #d03e3e;
    }
    form.patients-details-form fieldset {
        display: none;
    }
    .pdfobject { border: 1px solid #666; }
    .pdfobject-container {
        height: 400px;
    }
</style>

<!-- record tables -->
<div class="row">
    <div class="col-md-12">
        <!-- Nav tabs -->
        <div class="card tabbable full-width-tabs">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a class="tab-logo" href="#home" aria-controls="home" role="tab" data-toggle="tab"><span ></span><span>Clinical Notes, <br/>Forms &amp; Results</span></a></li>
                <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-ul"></i><span>Admin Notes &amp;<br/> Timeline</span></a></li>
                <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab"><i class="fa fa-calendar"></i><span>Patient Visits <br/>&amp; Reminders</span></a></li>
                <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab"><i class="fa fa-envelope"></i><span>Physician<br/> Messaging</span></a></li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home"><p class="color-green"><i class="fa fa-exclamation-circle"></i>&nbsp;Upload any health records, physicial notes, test results, and forms or save them directly from your fax inbox</p>
                    <div class="patient-details-section">
                        <button class="btn btn-theme" id="btn_view_add_health_record"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp;<span>Add Record</span></button>
                        <div class="table-responsive">
                            <table class="table table-hover db-table" id="table_health_records">
                                <thead>
                                    <tr>
                                        <th>Record Type</th>
                                        <th>Details</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="profile"><p class="color-green"><i class="fa fa-exclamation-circle"></i>&nbsp;Log all admin phone calls, notes e-mails or meetings with the patient or referring physicians office</p>
                    <div class="patient-details-section">
                        <button class="btn btn-theme" id="btn_view_add_admin_note"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp;<span>Activity</span></button>
                        <div class="table-responsive">
                            <table class="table table-hover db-table" id="table_admin_notes">
                                <thead>
                                    <tr>
                                        <th>Activity or Note</th>
                                        <th>Details</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="messages"><p class="color-green"><i class="fa fa-exclamation-circle"></i>&nbsp;Activate SMS and e-mail patient reminders by adding all scheduled visits, appointments or procedures</p>
                    <div class="patient-details-section">
                        <button class="btn btn-theme" id="btn_view_add_patient_visit"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp;<span>Patient visit</span></button>
                        <div class="table-responsive">
                            <table class="table table-hover db-table" id="table_patient_visits">
                                <thead>
                                    <tr>
                                        <th>Patient visit</th>
                                        <th>Date and Time</th>
                                        <th>Status</th>
                                        <th>Visit Confirmed</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="settings"><p class="color-green"><i class="fa fa-exclamation-circle"></i>&nbsp;Secure physician-to-physician direct messaging is coming soon</p>
                </div>
            </div>
        </div>
    </div>
</div>


<!--modals for records started--> 

<!-- add health record modal -->
<div class="modal fade" id="modal_add_health_record" tabindex="-1" role="dialog" aria-labelledby="add-record-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> Add Health Record</h4>
            </div>
            <div class="modal-body">
                <form id="form_health_record">
                    <input type="hidden" name="id" id="id"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for="new-patient-birthdate">
                                Select Record
                            </label>
                        </div>
                        <div class="col-lg-12">
                            <select id="signup-speciality" class="div-toggle" data-target=".my-info-1" placeholder="Speciality" name="record_type" class="form-control">
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
                    </div>
                    <div class="my-info-1">
                        <div class="file-upload hide">
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
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <label for="recordFileUpload">Upload Record</label>
                                </div>
                                <div class="col-lg-12">
                                    <div class="dropzone" id="dropzone_health_record">
                                        <div class="fallback">
                                            <input id="asdqwe" name="asdqwe[]" type="file"/>
                                        </div>
                                    </div>
                                    <p class="help-block" id="dropzoneHelp"></p>
                                    <p class="help-block">Only PDF files are allowed.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="btn_save_health_record" class="btn btn-theme">
                    <i class="fa fa-plus-circle"></i>&nbsp;&nbsp;Add Health Record
                </button>
                <button type="button" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- View health record modal -->
<div class="modal fade" id="modal_view_health_record" tabindex="-1" role="dialog" aria-labelledby="add-record-label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> <span id='record_type'></span> ( <span id='description'></span> ) </h4>
            </div>
            <div class="modal-body">
                <div id="wrap-container" class="">
                    <!-- Page Content -->
                    <div id="page-content-wrapper">
                        <div class="container-fluid">
                            <div id="pdf_view_div"></div>
                        </div>
                    </div>
                    <!-- /#page-content-wrapper -->
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Add Admin Notes Modal -->
<div class="modal fade" id="modal_add_admin_note" tabindex="-1" role="dialog" aria-labelledby="add-record-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Admin Note</h4>
            </div>
            <div class="modal-body">
                <form id="form_add_admin_note">
                    <input type="hidden" name="id" id="id"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for="new-patient-birthdate">
                                Note Type
                            </label>
                        </div>
                        <div class="col-lg-12">
                            <select name="note_type" placeholder="Speciality" class="form-control">
                                <option disabled selected>Select Note Type</option>
                                <option>Phone call</option>
                                <option>E-mail</option>
                                <option>Meeting</option>
                                <option>Notes</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for="new-patient-ohip">
                                Description
                            </label>
                        </div>
                        <div class="col-lg-12">
                            <textarea name="description" style="height: 100px" class="form-control" placeholder="Enter a description for the note"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="add_admin_note" type="button" class="btn btn-theme pull-left"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp;<span>Activity</span></button>
                <button type="button" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- View Admin Notes Modal -->
<div class="modal fade" id="modal_view_admin_note" tabindex="-1" role="dialog" aria-labelledby="add-record-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">View Admin Note</h4>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label for="new-patient-birthdate">
                            Note Type
                        </label>
                    </div>
                    <div class="col-lg-12">
                        <input type="text" disabled id="note_type" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-12">
                        <label for="new-patient-ohip">
                            Description
                        </label>
                    </div>
                    <div class="col-lg-12">
                        <textarea disabled id="description" style="height: 100px" class="form-control"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Patient visit Modal -->
<div class="modal fade" id="modal_add_patient_visit" tabindex="-1" role="dialog" aria-labelledby="add-record-label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <div class="">
                    <span class="color-green cc" id="physician_name">Dr. Criss Vascod<span>                    
                </div>
                <!-- <h4 class="modal-title" id="myModalLabel">Add Patient Visit</h4> -->
                <!-- <button id="btn_back_to_month_view" class="btn btn-info" style="display: none">Back</button> -->
            </div>
            <div class="modal-body">
<!--                <p class="color-green"><i class="fa fa-exclamation-circle"></i>&nbsp;Cell / Home / Work phone is required to activate patient visit notifications</p>-->
                <form id="form_add_patient_visit">
                    <input type="hidden" name="id" id="id"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="">
                        <input type="hidden" class="form-control" name="visit_name" placeholder="Enter Patient Visit">
                    </div>

                    <div class="form-group row">
                        <div class="col-lg-4 col-sm-4 col-xs-12">
                            <div class="radio">
                                <label>
                                    <input name="visit_slot" type="radio" value="1">
                                    <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                                    <span id="visit_slot_1"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-4 col-xs-12">
                            <div class="radio">
                                <label>
                                    <input name="visit_slot" type="radio" value="2">
                                    <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                                    <span id="visit_slot_2"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-4 col-xs-12">
                            <div class="radio">
                                <label>
                                    <input name="visit_slot" type="radio" value="3">
                                    <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                                    <span id="visit_slot_3"></span>
                                </label>
                            </div>
                            <input type="hidden" name="record_id" id="record_id" value=""/>
                        </div>
                    </div>

                    <div class="tab-sec text-right">
                        <input type="hidden" id="viewpatient_id" value=""> 
                        <ul class="nav nav-pills">
                            <li><a href="javascript:void(0)" class="btn btn-default btn-normal" id="tab_week">Week</a></li>
                            <li><a href="javascript:void(0)" class="btn btn-default btn-normal active" id="tab_month">Month</a></li>
                        </ul>
                    </div>
                    
                    <div id="selection_calendar_monthmode"></div>
                    <div id="selection_calendar_weekmode"></div>
                    <input type="hidden" name="selected_starttime" id="selected_starttime">
                    <input type="hidden" name="selected_endtime" id="selected_endtime">
                </form>
            </div>
            <div class="modal-footer">
                <button id="btn_add_patient_visit" type="button" class="btn btn-theme pull-left">
                    <i class="fa fa-plus-circle" 
                       data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing...">
                    </i>&nbsp;&nbsp;
                    <span>Patient Visit</span>
                </button>
                <span id="selected_datetime" class="btn-alt-theme"></span>
                <button type="button" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- View Patient visit Modal -->
<div class="modal fade" id="modal_view_patient_visit" tabindex="-1" role="dialog" aria-labelledby="add-record-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">View Patient Visit</h4>
            </div>
            <div class="modal-body">
                <p class="color-green"><i class="fa fa-exclamation-circle"></i>&nbsp;Cell / Home / Work phone is required to activate patient visit notifications</p>
                <form id="form_update_patient_visit">
                    <input type="hidden" name="id" id="id"/>
                    <input type="hidden" name="target" id="target"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="form-group row">
                        <input type="hidden" class="form-control" name="visit_name" placeholder="Enter Patient Visit">
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6 col-xs-12">
                            <label for="">Date</label>
                            <div class="input-group date form_date col-md-12" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
                                <input class="form-control" size="16" type="text"  name="visit_date" readonly>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                            <input type="hidden" id="dtp_input2" value="" />
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <label for="">Time</label>
                            <div class="input-group date form_time col-md-12" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                                <input class="form-control" size="16" type="text"  name="visit_time" readonly>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                            </div>
                            <input type="hidden" id="dtp_input3" value="" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-12 col-xs-12">
                            <div class="radio">
                                <label>
                                    <input name="visit_slot" type="radio" value="1">
                                    <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                                    <span id="visit_slot_1"></span>
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input name="visit_slot" type="radio" value="2">
                                    <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                                    <span id="visit_slot_2"></span>
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input name="visit_slot" type="radio" value="3">
                                    <span class="cr"><i class="cr-icon fa fa-check"></i></span>
                                    <span id="visit_slot_3"></span>
                                </label>
                            </div>
                            <input type="hidden" name="record_id" id="record_id" value=""/>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="btn_update_patient_visit" type="button" class="btn btn-theme pull-left" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing..."><i class="fa fa-plus-circle"></i>&nbsp;&nbsp;<span>Update Patient Visit</span></button>
                <button id="btn_cancel_visit" type="button" class="btn btn-theme btn-alt-theme">Cancel Visit</button>
            </div>
        </div>
    </div>
</div>
<!-- record open modal -->
<!-- Add Notes Modal without button-->
<div class="modal fade" id="modal_view_admin_note" tabindex="-1" role="dialog" aria-labelledby="add-record-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Admin Note</h4>
            </div>
            <div class="modal-body">
                
                <?php echo form_open("", "id='new-referral-form' class='' autocomplete='off'"); ?>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for="new-patient-birthdate">
                                Note Type
                            </label>
                        </div>
                        <div class="col-lg-12">
                            <select id="signup-speciality" placeholder="Speciality" name="signup-speciality" class="form-control">
                                <option disabled selected>Select Note Type</option>
                                <option>Phone call</option>
                                <option>E-mail</option>
                                <option>Meeting</option>
                                <option>Notes</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for="new-patient-ohip">
                                Description
                            </label>
                        </div>
                        <div class="col-lg-12">
                            <textarea style="height: 100px" class="form-control" placeholder="Vivamus id tortor massa. Phasellus eget molestie diam. Ut bibendum nulla at dictum vehicula. In bibendum vulputate ultricies. Cras ultricies mauris in fringilla tincidunt." id="referral-note"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!--modal for confirm changing assigned provider, as it will remove visits if changed-->
<div id="modal_confirm_assign_physician" class="modal fade" role="dialog" style="z-index: 2056 !important;">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirm Assigned Provider</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    Changing the assigned provider will clear any scheduled visits. Please confirm to continue
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_confirm_assign_physician" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>