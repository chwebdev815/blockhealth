    <div class="db-content-gutter no-left-right-padd no-bottom-padd">
        <div class="row referral_header">
            <div class="col-sm-6">
                <div class="well well-lg patient-details-well clearfix">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="fancy-collapse-panel">
                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="headingThree">
                                        <h3 class="panel-title">                      
                                            <a aria-expanded="true" role="button" data-toggle="collapse" href="#collapseThree" aria-expanded="true" aria-controls="collapseThree">                      
                                                Patient Contact Details                      
                                            </a>                    
                                        </h3>
                                    </div>
                                    <div id="collapseThree" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingThree">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-3 col-sm-3 col-xs-12 hidden-sm hidden-xs"> <span class="fa fa-user pull-left"></span> </div>
                                                <div class="col-md-9 col-sm-9 col-xs-12">
                                                    <div class="panel-body details-labels">
                                                        <p>Cell Phone: <span id="pat_cell_phone"></span></p>
                                                        <p>Email: <span id="pat_email"></span></p>
                                                        <p>OHIP #: <span id="pat_ohip"></span></p>
                                                        <p>Address: <span id="pat_address"></span></p>
                                                        <p><a class="update" data-toggle="modal" data-target="#edit-patient-modal" href="#">Update</a></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="well well-lg patient-details-well clearfix">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="fancy-collapse-panel">
                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="headingThree">
                                        <h3 class="panel-title">                      
                                            <a aria-expanded="true" role="button" data-toggle="collapse" href="#collapseFour" aria-expanded="true" aria-controls="collapseFour">                      
                                                Referring Physician Details                      
                                            </a>                    
                                        </h3>
                                    </div>
                                    <div id="collapseFour" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingFour">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <div class="panel-body details-labels">
                                                        <p><span id="dr_name"></span></p>
                                                        <p>Fax: <span id="dr_fax"></span></p>
                                                        <p>Office Phone #: <span id="dr_phone"></span></p>
                                                        <p>Billing #: <span id="dr_billing_num"></span></p>
                                                        <p>Address: <span id="dr_address"></span></p>
                                                        <p><a class="update" data-toggle="modal" data-target="#edit-physician-modal" href="#">Update</a></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- modals -->
    <!-- Edit Patient Contact Details Modal -->
<div class="modal fade" id="edit-patient-modal" tabindex="-1" role="dialog" aria-labelledby="add-patient-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Edit Patient Details</h4>
            </div>
            <div class="modal-body">
                <form id="new-patient-form">
                    <input type="hidden" name="id" id="id" />
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for="new-patient-name">Patient Name *</label>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <input type="text" class="form-control" name="pat_fname" id="pat_fname" placeholder="First Name"> </div>
                        <div class="col-sm-6 col-xs-12">
                            <input type="text" class="form-control" name="pat_lname" id="pat_lname" placeholder="Last Name"> </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for=""> Patient Email </label>
                        </div>
                        <div class="col-lg-12">
                            <input type="email" class="form-control" name="pat_email_id" id="pat_email_id" placeholder="Email"> </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for=""> Patient Address </label>
                        </div>
                        <div class="col-lg-12">
                            <input type="text" class="form-control" name="pat_address" id="pat_address" placeholder="Address"> </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for="new-patient-birthdate"> Date of Birth * </label>
                        </div>
                        <div>
                            <div class="col-sm-4 col-xs-12">
                                <select id="dobday" name="dobday"></select>
                            </div>
                            <div class="col-sm-4 col-xs-12 no-left-right-padd">
                                <select id="dobmonth" name="dobmonth"></select>
                            </div>
                            <div class="col-sm-4 col-xs-12">
                                <select id="dobyear" name="dobyear"></select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for="new-patient-ohip"> OHIP # </label>
                        </div>
                        <div class="col-lg-12">
                            <input type="text" class="form-control" name="pat_ohip" id="pat_ohip" placeholder="1234-123-123-AB"> </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for=""> Cell Phone </label>
                        </div>
                        <div class="col-lg-12">
                            <input type="text" class="form-control" name="pat_cell_phone" id="pat_cell_phone" placeholder="Mobile Number"> </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_save_patient" class="btn btn-theme pull-left">Save Patient</button>
                <button type="button" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Edit Physician Contact Details Modal -->
<div class="modal fade" id="edit-physician-modal" tabindex="-1" role="dialog" aria-labelledby="add-patient-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Edit Referring Physician Details</h4>
            </div>
            <div class="modal-body">
                <form id="new-physician-form">
                    <input type="hidden" name="id" id="id" />
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for="new-patient-name">Full Name *</label>
                        </div>
                        <div class="col-sm-6 col-xs-12">
                            <input type="text" class="form-control" name="dr_fname" id="dr_fname" placeholder="First Name"> </div>
                        <div class="col-sm-6 col-xs-12">
                            <input type="text" class="form-control" name="dr_lname" id="dr_lname" placeholder="Last Name"> </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for=""> Phone Number </label>
                        </div>
                        <div class="col-lg-12">
                            <input type="text" class="form-control" name="dr_phone_number" id="dr_phone_number" placeholder="Phone Number"> </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for=""> Fax Number </label>
                        </div>
                        <div class="col-lg-12">
                            <input type="text" class="form-control" name="dr_fax" id="dr_fax" placeholder="Fax Number"> </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for=""> Email </label>
                        </div>
                        <div class="col-lg-12">
                            <input type="email" class="form-control" name="dr_email_id" id="dr_email_id" placeholder="Email"> </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for=""> Address </label>
                        </div>
                        <div class="col-lg-12">
                            <input type="text" class="form-control" name="dr_address" id="dr_address" placeholder="Address"> </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for="new-patient-ohip"> Billing Number </label>
                        </div>
                        <div class="col-lg-12">
                            <input type="text" class="form-control" name="dr_billing_num" id="dr_billing_num" placeholder="Billing Number"> </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_save_physician" class="btn btn-theme pull-left">Save Physician</button>
                <button type="button" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>