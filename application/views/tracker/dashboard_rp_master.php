
<!-- Icon Cards-->
<div class="row" id="tiles">
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card text-white bg-primary o-hidden h-100">
            <div class="card-body">
                <div class="card-body-icon">
                    <i class="fas fa-fw fa-user"></i>
                </div>
                <div class="mr-5"><span id="tile_new_referral"></span> New Referrals!</div>
            </div>
            <a class="card-footer text-white clearfix small z-1" href="#">
                <span class="float-left">View Details</span>
                <span class="float-right">
                    <i class="fas fa-angle-right"></i>
                </span>
            </a>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card text-white bg-warning o-hidden h-100">
            <div class="card-body">
                <div class="card-body-icon">
                    <i class="fas fa-fw fa-check"></i>
                </div>
                <div class="mr-5"><span id="tile_accepted_referral"></span> Accepted Referrals!</div>
            </div>
            <a class="card-footer text-white clearfix small z-1" href="#">
                <span class="float-left">View Details</span>
                <span class="float-right">
                    <i class="fas fa-angle-right"></i>
                </span>
            </a>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card text-white bg-success o-hidden h-100">
            <div class="card-body">
                <div class="card-body-icon">
                    <i class="fas fa-fw fa-paper-plane"></i>
                </div>
                <div class="mr-5"><span id="tile_faxes_sent"></span> Faxes Sent!</div>
            </div>
            <a class="card-footer text-white clearfix small z-1" href="#">
                <span class="float-left">View Details</span>
                <span class="float-right">
                    <i class="fas fa-angle-right"></i>
                </span>
            </a>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card text-white bg-danger o-hidden h-100">
            <div class="card-body">
                <div class="card-body-icon">
                    <i class="fas fa-fw fa-cloud"></i>
                </div>
                <div class="mr-5"><span id="tile_api_calls"></span> Data points captured!</div>
            </div>
            <a class="card-footer text-white clearfix small z-1" href="#">
                <span class="float-left">View Details</span>
                <span class="float-right">
                    <i class="fas fa-angle-right"></i>
                </span>
            </a>
        </div>
    </div>
</div>

<!-- Area Chart Example-->
<div class="card mb-3">
    <div class="card-header">
        <i class="fas fa-chart-area"></i>
        Last 30 Days</div>
    <div class="card-body">
        <canvas id="chart_last_30_days" width="100%" height="30"></canvas>
    </div>
    <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
</div>

<!-- DataTables Example -->
<div class="card mb-3">
    <div class="card-header">
        <i class="fas fa-table"></i>
        Referring Physicians Statistics</div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="table_rp_statistics" class="table table-bordered table-responsive table-striped" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Physician Name</th>
                        <th>Email ID</th>
                        <th>Phone Number</th>
                        <th>Fax Number</th>
                        <th>Clinic Name</th>
                        <th>Signup Date</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Physician Name</th>
                        <th>Email ID</th>
                        <th>Phone Number</th>
                        <th>Fax Number</th>
                        <th>Clinic Name</th>
                        <th>Signup Date</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
</div>

