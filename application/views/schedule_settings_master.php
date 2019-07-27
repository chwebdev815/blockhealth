
<script src="https://cdn.alloyui.com/3.0.1/aui/aui-min.js"></script>	

<style type="text/css">     
    table.table-condensed th.prev, table.table-condensed th.next, table.table-condensed th.switch {     
        visibility: hidden !important;      
    }       
</style>

<div class="db-content-inside clearfix">
    <div class="col-md-12">
        <div class="row pmainsec" style="padding-left: 0px;">
            <div class="col-md-2">
                <h2 id="clinic_physician_container">
                    <label class="maroom-label">Select Provider</label>
                    <select id="physicians" placeholder="Clinic Physician" name="physicians" class="form-control">
                    </select> 
                    <!-- <small id="scheduled_patients">1 Scheduled patients</small> -->
                </h2>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row pmainsec">
            <div class="col-md-6">
                <div id="checklist_div">
                    <form id="form_physician_weekdays">
                        <label class="maroom-label">Select Available Days</label>
                        <input type="hidden" name="id" id="id">
                        <div style="display:inline-block" class="checkbox">
                            <label>
                                <input type="checkbox" name="mon" id="mon" class="checked">
                                <span class="cr"><i class="cr-icon fa fa-check"></i></span>M
                            </label>
                        </div>

                        <div style="display:inline-block" class="checkbox">
                            <label>
                                <input type="checkbox" name="tue" id="tue" class="checked">
                                <span class="cr"><i class="cr-icon fa fa-check"></i></span>Tu
                            </label>
                        </div>

                        <div style="display:inline-block" class="checkbox">
                            <label>
                                <input type="checkbox" name="wed" id="wed" class="checked">
                                <span class="cr"><i class="cr-icon fa fa-check"></i></span>W
                            </label>
                        </div>

                        <div style="display:inline-block" class="checkbox">
                            <label>
                                <input type="checkbox" name="thu" id="thu" class="checked">
                                <span class="cr"><i class="cr-icon fa fa-check"></i></span>Th
                            </label>
                        </div>

                        <div style="display:inline-block" class="checkbox">
                            <label>
                                <input type="checkbox" name="fri" id="fri" class="checked">
                                <span class="cr"><i class="cr-icon fa fa-check"></i></span>F
                            </label>
                        </div>

                        <div style="display:inline-block" class="checkbox">
                            <label>
                                <input type="checkbox" name="sat" id="sat" class="checked">
                                <span class="cr"><i class="cr-icon fa fa-check"></i></span>Sa
                            </label>
                        </div>

                        <div style="display:inline-block" class="checkbox">
                            <label>
                                <input type="checkbox" name="sun" id="sun" class="checked">
                                <span class="cr"><i class="cr-icon fa fa-check"></i></span>Su
                            </label>
                        </div>
                        &nbsp; &nbsp;
                        <button type="button" class="btn btn-theme" id="btn_confirm_update_weekdays">
                            Update
                        </button>
                    </form>
                </div>
            </div>
        </div>


        <div class="row pmainsec">
            <div class="col-md-12 mob-no-padding">
                <form id="form_physician_timing">
                    <div class="col-md-12 pl0">
                        <input type="hidden" name="id" id="id"/>

                        <div class="col-sm-6 col-md-2 col-xs-12 pl0 pdinput">
                            <!-- <label for="weekday">Select Weekday</label> -->
                            <label class="maroom-label">Set Schedule</label>
                            <select id="weekday" name="weekday">
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-3 col-xs-12 pt12 pdinput">
                            <label for="">Start Time</label>
                            <div class="input-group date form_time col-md-12" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                                <input class="form-control" type="text" id="start_time" name="start_time" readonly="">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                            <input type="hidden" id="dtp_input3" value="">
                        </div>
                        <div class="col-sm-6 col-md-3 col-xs-12 pt12">
                            <label for="">End Time</label>
                            <div class="input-group date form_time col-md-12" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                                <input class="form-control" type="text" id="end_time" name="end_time" readonly="">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                            <input type="hidden" id="dtp_input3" value="">
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 pt34">
                            <button type="button" class="btn btn-theme btn-med-width" id="btn_confirm_apply_time">
                                Apply
                            </button> &nbsp;
                            <button type="button" class="btn btn-theme btn-alt-theme btn-med-width" id="btn_confirm_apply_time_all">
                                Apply All
                            </button>
                        </div>
                    </div>
                    <div id="weekday_additional_blocks">
                    </div>

                    <div id="blocktimesec"></div>       
                    <div class="col-md-12">     
                        <div class="col-md-2"></div>        
                        <div class="col-md-3">      
                            <p class="blocked-time-ttl" id="blocked-time-ttl">
                                <a href="javascript:void(0)" id="btn_add_weekday_block">+ Blocked Time</a>
                            </p>       
                        </div>      
                        <div class="col-md-3"></div>        
                        <div class="col-md-4"></div>        
                    </div>
                </form>
                <div class="hidden" id="template_additional_weekday_block">
                    <div class="col-md-12 ptrowblock" id="blocktm">
                        <div class="col-sm-6 col-md-2 col-xs-12 pl0 pdinput text-right">
                            <p class="bold-txt blk-div">Block 
                                <label class="lbl_weekday_block_counter"></label>
                            </p>
                        </div>
                        <div class="col-sm-6 col-md-3 col-xs-12 pdinput sec-rowm">
                            <div class="input-group date form_time_new col-md-12" data-date="" 
                                 data-date-format="hh:ii" data-link-field="dtp_input" 
                                 data-link-format="hh:ii" id="st_time">                
                                <input class="form-control start_time" type="text" 
                                       name="block_start_time[]" readonly="">                    
                                <span class="input-group-addon">                        
                                    <span class="glyphicon glyphicon-time"></span>                    
                                </span>            
                            </div>          
                        </div>
                        <div class="col-sm-6 col-md-3 col-xs-12 pdinput">
                            <div class="input-group date form_time_new col-md-12" data-date="" 
                                 data-date-format="hh:ii" data-link-field="dtp_input" 
                                 data-link-format="hh:ii" id="en_time">                    
                                <input class="form-control end_time" type="text" 
                                       name="block_end_time[]" readonly="">                        
                                <span class="input-group-addon">                            
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>                        
                            </div>           
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                            <p class="blocked-time-close">
                                <a href="javascript:void(0)" class="btn_remove_weekday_block">X</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="myScheduler"></div>
        <div id="availability_calendar">
        </div>
    </div>
</div>


<div id="modal_confirm_update_physician_weekday" class="modal fade" role="dialog" style="z-index: 2056 !important;">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirm Schedule Change</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    Confirming this change will immediately update the booking schedule
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_update_physician_weekday" 
                        class="btn btn-theme btn-alt-theme"
                        data-dismiss="modal">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>


<div id="modal_set_day_blocks" class="modal fade" role="dialog" style="z-index: 2056 !important;">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Blocked Time (<span id="blocking_date_title"></span>)</h4>
            </div>
            <div class="modal-body">
                <form id="form_day_specific_blocking">
                    <input type="hidden" name="blocking_date" id="blocking_date"/>
                    <input type="hidden" name="id" id="id"/>
                    <input type="hidden" name="type" id="type"/>
                    <div class="row">
                        <div class="col-lg-5 pt12">
                            <label for="">Start Time</label>
                            <div class="input-group date form_time col-md-12" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                                <input id="day_start_time" class="form-control start_time" type="text" name="day_start_time" readonly="">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                            <input type="hidden" id="dtp_input3" value="">
                        </div>
                        <div class="col-lg-5 pt12">
                            <label for="">End Time</label>
                            <div class="input-group date form_time col-md-12" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                                <input id="day_end_time" class="form-control end_time" type="text" name="day_end_time" readonly="">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                            <input type="hidden" id="dtp_input3" value="">
                        </div>
                    </div>
                    <div id="additional_blocks"></div>
                    <div class="row">
                        <div class="col-lg-12">
                            <br/>
                            <a id="btn_add_blocked_time_fields" class="update">+ Blocked Time</a>
                        </div>
                    </div>
                </form>

                <div class="hidden" id="template_additional_blocks">
                    <div class="row">
                        <div class="col-lg-5 pt12">
                            <div class="input-group date form_time_new col-md-12" data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                                <input class="form-control start_time" type="text" 
                                       name="start_time[]" readonly="true" >
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                            <input type="hidden" id="dtp_input3" value="">
                        </div>
                        <div class="col-lg-5 pt12">
                            <div class="input-group date form_time_new col-md-12" data-date="" 
                                 data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                                <input class="form-control end_time" type="text" 
                                       name="end_time[]"  readonly="true">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                            <input type="hidden" id="dtp_input3" value="">
                        </div>
                        <div class="col-lg-2">
                            <span class="fa fa-times remove_timeslot"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_apply_blocked_time_for_day" 
                        class="btn btn-theme pull-left">
                    Apply
                </button>
                <button type="button" id="btn_block_day" 
                        class="btn btn-theme btn-alt-theme">
                    Block Day
                </button>
            </div>
        </div>
    </div>
</div>


<div id="modal_confirm_update_weekday_timing" class="modal fade" role="dialog" style="z-index: 2056 !important;">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirm Schedule Change</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    Confirming this change will immediately update the booking schedule
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_update_weekday_timing" 
                        class="btn btn-theme btn-alt-theme"
                        data-dismiss="modal">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>



<div id="modal_confirm_update_weekday_timing_all" class="modal fade" role="dialog" style="z-index: 2056 !important;">
    <div class="modal-dialog modal-md">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirm Schedule Change</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    Confirming this change will immediately update the booking schedule
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btn_update_weekday_timing_all" 
                        class="btn btn-theme btn-alt-theme"
                        data-dismiss="modal">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>
