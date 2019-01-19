
<div class="preview-image-container">
    <img id="hover-img-preview"> 
</div>

<div class="db-content-inside">
    <div id="table-action" class="btn-group" role="group" data-patient-id="0">
        <button type="button" id="table-hover-view-trigger" class="btn btn-default"><i class="fa fa-eye"></i></button>
        <button type="button" id="table-hover-edit-trigger" class="btn btn-default"><i class="fa fa-edit"></i></button>
        <a href="javascript:void(0)" download id="table-hover-delete-trigger" class="btn btn-default"><i class="fa fa-download"></i></a>
    </div>
    <table class="table table-hover db-table" id="table_my_tasks">
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Record Type</th>
                <th>Details</th>
                <th>Days</th>
                <th>Pages</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
    </table>
</div>


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
                                            <th></th>
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

<div class="modal fade" id="eFax-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-md-6 marg-top-10px">
                    <button type="button" class="btn btn-link db-nav-more-trigger back-link" data-toggle="modal" data-target="#add-referral-modal" data-dismiss="modal">
                        <i class="fa fa-angle-left fa-2x"></i>
                    </button>
                    <span id="file_info"></span>
                </div>
                <div class="col-md-6">
                    <span class="eFax-bar-actions col-md-offset-3 marg-top-10px">
                        <a href="javascript:void(0)" id="btn_view_print_referral">Print </a> &nbsp;
                        <a href="javascript:void(0)" id="btn_view_delete_referral">Delete </a> &nbsp;
                        <a href="javascript:void(0)" id="btn_download_referral" download>Download </a>
                        <a href="javascript:void(0)" id="btn_view_save_referral" class="btn-toggle-referral">Save </a>
                    </span>
<!--                    <button  data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Loading..." class="btn btn-theme btn-alt-theme btn-toggle-referral pull-right">
                        New Referral
                    </button>-->
                </div>
            </div>
            <div class="modal-body">

                <div id="wrap-container" class="toggled fadeIn">

                    <!-- Sidebar -->
                    <div id="sidebar-wrapper">
                        <input type="hidden" name="id" id="id" />
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <fieldset>
                            <form id="form_patient_save" class="form-horizontal patients-details-form" autocomplete="off">
                                <div class="form-bottom">
                                    <div class="form-group row  left-padd-20px right-padd-5px">
                                        <h4 class="modal-title">Select Patient</h4>
                                        <br/>
                                        <div class="pull-left">
                                            <button id="btnStartPatientCrop" type="button" class="btn btn-theme">
                                                <span class="fa fa-crop fa-2"></span>
                                            </button>
                                            <!--<button id="btn_search_patient" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Loading..." type="button" class="btn btn-theme btn-alt-theme"><i class="fa" aria-hidden="true"><img src="assets/img/magic-wand.png" width="16"></i>&nbsp;&nbsp;Search</button>-->
                                            <button id="btn_extract_patient" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Loading..." type="button" class="btn btn-theme btn-alt-theme btn-autofil"><i class="fa" aria-hidden="true"><img src="assets/img/magic-wand.png" width="16"></i>&nbsp;&nbsp;Auto Fill</button>
                                        </div>
                                    </div>
                                    <div class="form-group left-padd-20px right-padd-5px">
                                        <div class="alert alert-danger" id="patient_error" style="display: none;"></div>
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
                                                    <option>Referral</option>
                                                    <option>Consult or Imaging Report</option>
                                                    <option>Lab Test</option>
                                                    <option>Prescriptions</option>
                                                    <option>Notes</option>
                                                    <option>Other</option>
                                                </select>
                                            </div>

                                            <div class="col-lg-12 cl-t-listing wrapper_div">
                                                <label for="new-patient-ohip">
                                                    <strong>Enter Details/Notes</strong>
                                                </label>
                                                <div>
                                                    <textarea name="description" style="height: 100px" class="form-control" placeholder="Enter Details/Notes" id="description"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" id="btn_save_task" class="btn btn-theme">Save</button>
                                        <div class="form-group row">
                                            <div class="col-lg-12 cl-t-listing wrapper_div">
                                                <div id="patient_success_display" class="success-icon pull-right">
                                                    <span class="fa fa-check"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </fieldset>
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