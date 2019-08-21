
<!-- to be removed -->
<style>
    embed {
        height: 500px !important;
    }       
</style>
<!-- to be removed end -->
<div class="db-content-inside">
    <table class="table table-hover db-table" id="table_accepted">
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Reason for Referral</th>
                <th>Assigned</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
    </table>
</div>
<!-- Add Patient visit Modal -->
<div class="modal fade" id="modal_add_patient_visit" tabindex="-1" role="dialog" aria-labelledby="add-record-label">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <div class=""><span class="color-green" id="physician_name"></span> <span class="timeslot-sp">60 minute time slots</span>
                    <!-- <div class="text-right backbtn-sec">
                        <button id="btn_back_to_month_view" class="btn btn-info" style="display: none">
                            Back
                        </button>
                    </div> -->
                </div>
                <!-- <h4 class="modal-title" id="myModalLabel">Add Patient Visit</h4> -->
                
            </div>
            <div class="modal-body">
<!--                <p class="color-green"><i class="fa fa-exclamation-circle"></i>&nbsp;Cell / Home / Work phone is required to activate patient visit notifications</p>-->
                
                <?php echo form_open("", "id='form_add_patient_visit' class=''"); ?>
                    <input type="hidden" name="id" id="id"/>
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

<div id="modal_add_new_patient" class="modal fade" role="dialog" style="z-index: 2056 !important;">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content model_bg">
            <div class="modal-header model_title">
                <div class="col-md-12">
                    <h4 class="modal-title" id="myModalLabel">Add Patient Details</h4>
                </div>                
            </div>
            <div class="modal-body">
                <?php echo form_open("", "id='form_add_patient' class='form-horizontal patients-details-form2'"); ?>
                    <fieldset>
                        <div class="form-bottom">
                            <div class="form-group left-padd-20px right-padd-20px">
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="new-patient-name">Full Name *</label>
                                    </div>
                                    <div class="col-sm-6 col-xs-12">
                                        <input type="text" class="form-control model_input required" name="pat_fname" id="new-patient-firstname" placeholder="First Name" autocomplete="off">
                                    </div>
                                    <div class="col-sm-6 col-xs-12">
                                        <input type="text" class="form-control required model_input" name="pat_lname" id="new-patient-lastname" placeholder="Last Name" autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="new-patient-birthdate">
                                            Date of Birth *
                                        </label>
                                    </div>
                                    <div class="col-sm-4 col-xs-12">
                                        <select name="pat_dob_day" id="pat_dob_day" class="required model_input"></select>
                                    </div>
                                    <div class="col-sm-4 col-xs-12 no-left-right-padd">
                                        <select name="pat_dob_month" id="pat_dob_month" class="required model_input"></select>
                                    </div>
                                    <div class="col-sm-4 col-xs-12">                    
                                        <select name="pat_dob_year" id="pat_dob_year" class="required model_input"></select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-6 col-xs-12">
                                        <label for="new-patient-ohip">
                                            OHIP #
                                        </label>
                                        <input type="text" class="form-control model_input" name="pat_ohip" id="new-patient-ohip" placeholder="1234-123-123-AB" autocomplete="off">
                                    </div>


                                    <div class="col-sm-6 col-xs-12">
                                        <label for="new-patient-ohip">
                                            MRN #
                                        </label>
                                        <input type="text" class="form-control model_input" name="pat_mrn" id="new-pat-mrn" placeholder="1234-123">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="">
                                            Phone
                                        </label>
                                    </div>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control model_input" name="pat_home_phone" id="patient-home-phone" placeholder="Home Phone">
                                    </div>
                                    <div class="col-lg-4" style="padding-left: 0px; padding-right: 0px;">
                                        <input type="text" class="form-control model_input" name="pat_cell_phone" id="patient-cell-phone" placeholder="Cell Phone">
                                    </div>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control model_input" name="pat_work_phone" id="patient-work-phone" placeholder="Work Phone">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="new-patient-ohip">
                                            Assign Physician
                                        </label>
                                    </div>
                                    <div class="col-lg-12">
                                        <select id="assigned_physician" placeholder="Assign Physician" name="assigned_physician" class="form-control model_input">
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="new-patient-ohip">
                                            Select Priority
                                        </label>
                                    </div>
                                    <div class="col-lg-12">
                                        <select id="priority" placeholder="Priority" name="priority" class="form-control model_input">
                                            <option value="urgent">Urgent (less than 1 week)</option>
                                            <option value="sub_urgent">Sub-urgent (less than 2 weeks)</option>
                                            <option value="routine" selected>Routine (next available date)</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="new-patient-ohip">
                                            Patient Location
                                        </label>
                                    </div>
                                    <div class="col-lg-12">
                                        <select id="patient_location" name="patient_location" 
                                                class="form-control model_input">
                                            <option selected disabled>Select Patient Location</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="custom">
                                            Custom
                                        </label>
                                    </div>
                                    <div class="col-lg-12">
                                        <select id="custom" name="custom" 
                                                class="form-control model_input">
                                            <option selected disabled>Select Custom</option>
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
                                            <button type="button" id="btn_add_reason" class="add_field_button"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp; Entry</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer model_footer_btn">
                <button id="btn_add_new_patient" type="button" class="btn btn-theme bttn-circle btn-theme btn-alt-theme">
                    <i class="fa fa-plus-circle"></i>&nbsp;&nbsp;
                    <span>New Patient</span>
                </button>
                <!--<button type="button" id="btn_confirm_referral" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Confirm</button>
                <button type="button" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Close</button>-->
            </div>
        </div>
    </div>
</div>