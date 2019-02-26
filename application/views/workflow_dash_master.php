
<div class="db-content-inside si-dash">
    <div class="row left-right-padd-40px">
        <div class="top_tiles">
            <a href="<?php echo base_url(); ?>inbox">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="tile-stats clearfix">
                        <div class="icon">
                            <p>
                                <img src="assets/img/icon-new-fax.png" />
                            </p>
                            <p>New Faxes</p>
                        </div>
                        <div class="count" id="inbox_count"></div>
                    </div>
                </div>
            </a>
            <a href="<?php echo base_url(); ?>my_tasks">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="tile-stats clearfix">
                        <div class="icon">
                            <p>
                                <img src="assets/img/icon-admin-triage.png" />
                            </p>
                            <p>In Fax Triage</p></div>
                        <div class="count" id="tasks_count"></div>
                    </div>
                </div>
            </a>
            <a href="<?php echo base_url(); ?>physician_triage">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="tile-stats clearfix">
                        <div class="icon">
                            <p>
                                <i class="fa fa-stethoscope"></i>
                            </p>
                            <p>In Referral Triage</p></div>
                        <div class="count" id="physician_triage_count"></div>
                    </div>
                </div>
            </a>
            <a href="<?php echo base_url(); ?>accepted">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="tile-stats clearfix">
                        <div class="icon">
                            <p>
                                <i class="fa fa-calendar-o"></i>
                            </p>
                            <p>To Schedule</p></div>
                        <div class="count" id="accepted_count"></div>
                    </div>
                </div>
            </a>
        </div>
    </div>
    <div id="workflow_dash_container">
        <div id="sample_tab" class="hidden">
            <div class="row marg-top-10px left-right-padd-40px">
                <div class="col-md-12">
                    <div class="x_panel" style="height: auto;">
                        <div class="x_title">
                            <h2>
                                <drname></drname> 
                                <small><visitcount></visitcount> Scheduled patients</small></h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li dr_id="_dr_id">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-down"></i>
                                    </a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content" style="display:none;">
                            <div class="my_calender" style="margin: 0 auto;">  
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>