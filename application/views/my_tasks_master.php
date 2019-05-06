<style>
    .popup_content {
        margin: 10px;
        padding: 0 10px;
        max-width: 100%;
        background: white;
        -webkit-box-shadow: 0 5px 15px rgba(0,0,0,.5);
        box-shadow: 0 5px 15px rgba(0,0,0,.5);
    }
    .popup_wrapper{
        top: 10%!important;
    }
    .popup_background{
        z-index: 0!important;
    }
</style>

<section id="popup2">
    <img id="image_for_preview" width="" height="500" src="" />
</section>
<!--<div class="preview-image-container">
    <img id="hover-img-preview"> 
</div>-->

<div class="db-content-inside">
    <div id="table-action" class="btn-group" role="group" data-patient-id="0">
        <button type="button" id="" class="btn btn-default popup2_open">
            <i class="fa fa-eye"></i>
        </button>
        <button type="button" id="table-hover-edit-trigger" class="btn btn-default">
            <i class="fa fa-edit"></i>
        </button>
        <a href="javascript:void(0)" download id="table-hover-delete-trigger" class="btn btn-default">
            <i class="fa fa-download"></i>
        </a>
    </div>
    <table class="table table-hover db-table" id="table_my_tasks">
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Record Type</th>
                <th>Notes</th>
                <th>Status</th>
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
                        <fieldset>
                            <form id="form_patient_save" class="form-horizontal patients-details-form" autocomplete="off">
                                <input type="hidden" name="id" id="id" />
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                <div class="form-bottom">

                                    <div class="form-group row left-padd-20px right-padd-5px">
                                        <div class="">
                                            <h4 class="modal-title" id="myModalLabel">Select Patient</h4>
                                            <br/>
                                        </div>
                                        <div class="pull-left">
                                            <button id="btnStartPatientCrop" type="button" class="btn btn-theme">
                                                <span class="fa fa-crop fa-2"></span>
                                            </button>
                                            <button id="btn_extract_patient" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Loading..." type="button" class="btn btn-theme btn-alt-theme btn-autofil">
                                                <i class="fa" aria-hidden="true"><img src="assets/img/magic-wand.png" width="16"></i>&nbsp;&nbsp;Auto Fill
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-group left-padd-20px right-padd-5px">

                                        <div class="form-group col-lg-12">
                                            <div class="form-group row">
                                                <div class="col-md-12">
                                                    <label for="new-patient-name">Full Name *</label>
                                                </div>
                                                <div class="col-sm-6 col-xs-12">
                                                    <input type="text" class="form-control required" name="pat_fname" id="new-patient-firstname" placeholder="First Name" autocomplete="off">
                                                </div>
                                                <div class="col-sm-6 col-xs-12">
                                                    <input type="text" class="form-control required" name="pat_lname" id="new-patient-lastname" placeholder="Last Name" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4 col-xs-12">
                                                    <select name="pat_dob_day" id="pat_dob_day" class=""></select>
                                                </div>
                                                <div class="col-sm-4 col-xs-12 no-left-right-padd">
                                                    <select name="pat_dob_month" id="pat_dob_month" class=""></select>
                                                </div>
                                                <div class="col-sm-4 col-xs-12">                    
                                                    <select name="pat_dob_year" id="pat_dob_year" class=""></select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6 col-xs-12">
                                                    <input style="width: 100%;" type="text" class="" name="pat_ohip" id="new-patient-ohip" placeholder="1234-123-123-AB" autocomplete="off">
                                                </div>


                                                <div class="col-sm-6 col-xs-12">
                                                    <select style="width: 100%;" name="pat_gender" id="pat_gender" class="required">
                                                        <option value="unassigned" selected>Unassigned</option>
                                                        <option value="male">Male</option>
                                                        <option value="female">Female</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-lg-12">
                                                <h4 class="modal-title" id="task_h4">Task Details</h4>
                                            </div>
                                            <div class="alert alert-danger" id="patient_error" style="display: none;"></div>
                                            <div class="alert alert-success" id="patient_success" style="display: none;"></div>

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
                                                    <option data-show=".file-upload">Referral</option>
                                                    <option data-show=".file-upload">Consult or Imaging Report</option>
                                                    <option data-show=".file-upload">Lab Test</option>
                                                    <option data-show=".file-upload">Prescriptions</option>
                                                    <option data-show=".file-upload">Notes</option>
                                                    <option data-show=".file-upload">Other</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-12 cl-t-listing wrapper_div">
                                                <label for="assign_physician">
                                                    <strong>Enter Details/Notes</strong>
                                                </label>
                                                <div>
                                                    <textarea name="description" style="height: 100px" class="form-control" placeholder="Enter Details/Notes" id="description"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-lg-12 cl-t-listing wrapper_div">
                                                <button type="button" id="btn_save_task" class="btn btn-theme">Save</button>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-12 cl-t-listing wrapper_div">
                                                <div id="patient_success_display" class="success-icon pull-right menu">
                                                    <span class="fa fa-check tick-icon"></span>
                                                </div>
                                            </div>
                                        </div> 
                                        <!--<div class="alert alert-success"><p>Patient Match Found</p></div>-->

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

<script src="https://cdn.jsdelivr.net/gh/vast-engineering/jquery-popup-overlay@2/jquery.popupoverlay.min.js"></script>


<script>
    $('#popup2, #popup3').popup({
        pagecontainer: '#page',
        type: 'tooltip',
        background: true,
        color: '#fff',
        escape: true,
        horizontal: 'left',
        vertical: 'middle'

    });
</script>	