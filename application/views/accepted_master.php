<!-- to be removed --><style>    embed {        height: 500px !important;    }</style><!-- to be removed end --><div class="db-content-inside">    <table class="table table-hover db-table" id="table_accepted">        <thead>            <tr>                <th>Patient Name</th>                <th>Priority</th>                <th>Reason for Referral</th>                <th>Status</th>            </tr>        </thead>    </table></div><form class="hidden" id="sample_form">    <input type="hidden" name="id" id="id"/>    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>"></form><!-- Add Patient visit Modal --><div class="modal fade" id="modal_add_patient_visit" tabindex="-1" role="dialog" aria-labelledby="add-record-label">    <div class="modal-dialog" role="document">        <div class="modal-content">            <div class="modal-header">                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>                <h4 class="modal-title" id="myModalLabel">Add Patient Visit</h4>            </div>            <div class="modal-body">                <p class="color-green"><i class="fa fa-exclamation-circle"></i>&nbsp;Call phone and/or e-mail is required to activate patient visit notifications</p>                <form id="form_add_patient_visit">                    <input type="hidden" name="id" id="id"/>                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">                    <div class="form-group row">                        <div class="col-lg-12">                            <label for="new-patient-birthdate">                                Visit Name                            </label>                        </div>                        <div class="col-lg-12">                            <input type="text" class="form-control" name="visit_name" placeholder="Enter Patient Visit">                        </div>                    </div>                    <div class="form-group row">                        <div class="col-sm-6 col-xs-12">                            <label for="">Date</label>                            <div class="input-group date form_date col-md-12" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">                                <input class="form-control" size="16" type="text"  name="visit_date" readonly>                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>                            </div>                            <input type="hidden" id="dtp_input2" value="" />                        </div>                        <div class="col-sm-6 col-xs-12">                            <label for="">Time</label>                            <div class="input-group date form_time col-md-12" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">                                <input class="form-control" size="16" type="text"  name="visit_time" readonly>                                <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>                            </div>                            <input type="hidden" id="dtp_input3" value="" />                        </div>                    </div>                    <div class="form-group row">                        <div class="col-sm-4 col-xs-12">                            <div class="checkbox">                                <label>                                    <input name="cell_phone_voice" type="checkbox" value="" checked>                                    <span class="cr"><i class="cr-icon fa fa-check"></i></span>                                    Cell Phone(Voice)                                </label>                            </div>                        </div>                        <div class="col-sm-4 col-xs-12">                            <div class="checkbox">                                <label>                                    <input name="cell_phone" type="checkbox" value="" checked>                                    <span class="cr"><i class="cr-icon fa fa-check"></i></span>                                    Cell Phone(SMS)                                </label>                            </div>                        </div>                        <div class="col-sm-4 col-xs-12">                            <div class="checkbox">                                <label>                                    <input name="email" type="checkbox" value="" checked>                                    <span class="cr"><i class="cr-icon fa fa-check"></i></span>                                    E-mail                                </label>                            </div>                        </div>                    </div>                </form>            </div>            <div class="modal-footer">                <button id="btn_add_patient_visit" type="button" class="btn btn-theme pull-left"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp;<span>Patient Visit</span></button>                <button type="button" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Close</button>            </div>        </div>    </div></div><div id="modal_add_new_patient" class="modal fade" role="dialog" style="z-index: 2056 !important;">    <div class="modal-dialog modal-md">        <!-- Modal content-->        <div class="modal-content model_bg">            <div class="modal-header model_title">                <div class="col-md-12">                    <h4 class="modal-title" id="myModalLabel">Add Patient Details</h4>                </div>                            </div>            <div class="modal-body">                <form id="form_add_patient" class="form-horizontal patients-details-form2">                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">                    <fieldset>                        <div class="form-bottom">                            <div class="form-group left-padd-20px right-padd-20px">                                <!--<h4 class="modal-title" id="myModalLabel">Add Patient Details</h4>-->                                <div class="form-group row">                                    <div class="col-lg-12">                                        <label for="new-patient-name">Full Name *</label>                                    </div>                                    <div class="col-sm-6 col-xs-12">                                        <input type="text" class="form-control model_input required" name="pat_fname" id="new-patient-firstname" placeholder="First Name" autocomplete="off">                                    </div>                                    <div class="col-sm-6 col-xs-12">                                        <input type="text" class="form-control required model_input" name="pat_lname" id="new-patient-lastname" placeholder="Last Name" autocomplete="off">                                    </div>                                </div>                                <div class="form-group row">                                    <div class="col-lg-12">                                        <label for="new-patient-birthdate">                                            Date of Birth *                                        </label>                                    </div>                                    <div class="col-sm-4 col-xs-12">                                        <select name="pat_dob_day" id="pat_dob_day" class="required model_input"></select>                                    </div>                                    <div class="col-sm-4 col-xs-12 no-left-right-padd">                                        <select name="pat_dob_month" id="pat_dob_month" class="required model_input"></select>                                    </div>                                    <div class="col-sm-4 col-xs-12">                                                            <select name="pat_dob_year" id="pat_dob_year" class="required model_input"></select>                                    </div>                                </div>                                <div class="form-group row">                                    <div class="col-sm-6 col-xs-12">                                        <label for="new-patient-ohip">                                            OHIP #                                        </label>                                        <input type="text" class="form-control model_input" name="pat_ohip" id="new-patient-ohip" placeholder="1234-123-123-AB" autocomplete="off">                                    </div>                                    <div class="col-sm-6 col-xs-12">                                        <label for="new-patient-ohip">                                            MRN #                                        </label>                                        <input type="text" class="form-control model_input" name="pat_mrn" id="new-pat-mrn" placeholder="1234-123">                                    </div>                                </div>                                <div class="form-group row">                                    <div class="col-lg-12">                                        <label for="">                                            Phone                                        </label>                                    </div>                                    <div class="col-lg-4">                                        <input type="text" class="form-control model_input" name="pat_home_phone" id="patient-home-phone" placeholder="Home Phone">                                    </div>                                    <div class="col-lg-4" style="padding-left: 0px; padding-right: 0px;">                                        <input type="text" class="form-control model_input" name="pat_cell_phone" id="patient-cell-phone" placeholder="Cell Phone">                                    </div>                                    <div class="col-lg-4">                                        <input type="text" class="form-control model_input" name="pat_work_phone" id="patient-work-phone" placeholder="Work Phone">                                    </div>                                </div>                                <div class="form-group row">                                    <div class="col-lg-12">                                        <label for="new-patient-ohip">                                            Assign Physician                                        </label>                                    </div>                                    <div class="col-lg-12">                                        <select id="assigned_physician" placeholder="Assign Physician" name="assigned_physician" class="form-control model_input">                                        </select>                                    </div>                                </div>                                <div class="form-group row">                                    <div class="col-lg-12">                                        <label for="new-patient-ohip">                                            Select Priority                                        </label>                                    </div>                                    <div class="col-lg-12">                                        <select id="priority" placeholder="Priority" name="priority" class="form-control model_input">                                            <option disabled selected>Select Priority</option>                                            <option value="urgent">Urgent (less than 1 week)</option>                                            <option value="sub_urgent">Sub-urgent (less than 2 weeks)</option>                                            <option value="routine">Routine (next available date)</option>                                        </select>                                    </div>                                </div>                                                                <div class="form-group row">                                    <div class="col-lg-12 cl-t-listing wrapper_div">                                        <ul>                                            <li><strong>Reason for Referral</strong></li>                                        </ul>                                        <div>                                            <div class="input_fields_wrap edit_reasons"></div>                                            <button type="button" id="btn_add_reason" class="add_field_button"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp; Entry</button>                                        </div>                                    </div>                                </div>                            </div>                        </div>                    </fieldset>                </form>            </div>            <div class="modal-footer model_footer_btn">                <button id="btn_add_new_patient" type="button" class="btn btn-theme bttn-circle btn-theme btn-alt-theme">                    <i class="fa fa-plus-circle"></i>&nbsp;&nbsp;                    <span>New Patient</span>                </button>                <!--<button type="button" id="btn_confirm_referral" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Confirm</button>                <button type="button" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Close</button>-->            </div>        </div>    </div></div>