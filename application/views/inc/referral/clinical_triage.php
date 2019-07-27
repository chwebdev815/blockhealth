<div class="db-content-gutter no-left-right-padd no-bottom-padd">
    <div class="row">
        <div class="">
            <div class="well well-lg patient-details-well clearfix">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="fancy-collapse-panel">
                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                            <div class="panel panel-default">
                                <div class="panel-heading priority_label" role="tab" id="headingThree">
                                    <h3 class="panel-title">                            
                                        <a aria-expanded="true" role="button" data-toggle="collapse" href="#collapseSix" aria-expanded="true" aria-controls="collapseSix">
                                            Clinical Triage &nbsp; 
                                            <span class="not-specified">Urgent</span>                            
                                        </a>                          
                                    </h3>
                                </div>

                                <div class="row form-group" style="margin-top: 15px;">
                                    <div class="col-md-3 col-sm-3 col-xs-12">
                                        <div class="at-patient-details">
                                            <p>Reason</p>
                                        </div>
                                    </div>
                                    <div class="col-md-9 col-sm-9 col-xs-12 admin-triage-txt">
                                        <p style="color: #3cc6b7;" class="referral_reason_1"></p>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-2 col-sm-3 col-xs-12">
                                        <div class="at-patient-details">
                                            <p>Priority</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-9 col-xs-12 admin-triage-txt">
                                        <select id="priority" placeholder="Priority" name="priority" class="form-control">=
                                            <option value="urgent">Urgent (less than 1 week)</option>
                                            <option value="sub_urgent">Sub-urgent (less than 2 weeks)</option>
                                            <option value="routine" selected>Routine (next available date)</option>
                                        </select>
                                    </div>
                                </div>

                                <div id="collapseSix" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingThree">
                                    <div class="panel-body">
                                        <p>&nbsp;</p>
                                        <section style="height: 136px;width: 99.1%;">
                                            <article class="white-panel" style="position: relative;">
                                                <h4>Reason for Referral</h4>
                                                <ul>
                                                    <li class="referral_reason_2"></li>
                                                </ul>
                                            </article>
                                        </section>
                                        <section id="pinBoot" class="clinic_triage_dash">
                                        </section>
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
