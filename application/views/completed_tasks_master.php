<div class="db-content-inside">
    <table class="table table-hover db-table" id="table_completed_tasks">
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Record Type</th>
                <th>Details</th>
                <th>Pages</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
    </table>
</div>


<div class="modal fade" id="eFax-modal" tabindex="-1" role="dialog" aria-labelledby="delete-patient-label">
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
                    </span>
<!--                    <button  data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Loading..." class="btn btn-theme btn-alt-theme btn-toggle-referral pull-right">
                        New Referral
                    </button>-->
                </div>
            </div>
            <div class="modal-body">

                <div id="wrap-container">


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