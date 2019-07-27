<div class="col-md-5 col-sm-5 col-xs-12">
    <div class="row form-group">
        <div class="col-md-3 col-sm-3 col-xs-12">
            <div class="at-patient-details">
                <p>Status</p>
            </div>
        </div>
        <div class="col-md-9 col-sm-9 col-xs-12 admin-triage-txt">
            <p id="assigned_physician_info">Referral Triage</p>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-3 col-sm-3 col-xs-12">
            <div class="at-patient-details">
                <p>Location</p>
            </div>
        </div>
        <div class="col-md-9 col-sm-9 col-xs-12 admin-triage-txt">
            <select id="patient_location" name="patient_location" class="form-control small_select">
            </select>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-3 col-sm-3 col-xs-12">
            <div class="at-patient-details">
                <p>Assigned</p>
            </div>
        </div>
        <div class="col-md-9 col-sm-9 col-xs-12 admin-triage-txt">
            <select id="assigned_physician" name="assigned_physician" class="form-control small_select">
                <option>Not yet assigned</option>
            </select>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-3 col-sm-3 col-xs-12">
            <div class="at-patient-details">
                <p>Next visit</p>
            </div>
        </div>
        <div class="col-md-9 col-sm-9 col-xs-12 admin-triage-txt">
            <select id="next_visit" name="next_visit" class="form-control small_select">
            </select>
        </div>
    </div>
    <div class="row form-group">
        <div class="col-md-3 col-sm-3 col-xs-12">
            <div class="at-patient-details">
                <p>Custom</p>
            </div>
        </div>
        <div class="col-md-9 col-sm-9 col-xs-12 admin-triage-txt">
            <select id="custom" name="custom" class="form-control small_select">
            </select>
        </div>
    </div>
</div>

<div class="col-md-4 col-sm-4 col-xs-12">
    <div class="row ">
        <div class="col-md-3 col-sm-3 col-xs-12">
            <div class="at-patient-details">
                <p class="hidden-lg hidden-md hidden-sm">&nbsp;</p>
                <p>Checklist</p>
            </div>
        </div>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <div class="details-labels at-patient-details admin-triage-txt">
                <p> <span id="items_checked"></span> / <span id="items_total"></span> Items received </p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-sm-3 col-xs-12 hidden-xs">&nbsp;</div>
        <div class="col-md-9 col-sm-9 col-xs-12">
            <div id="checklist_div"> </div>
        </div>
    </div>
</div>