<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $page_title; ?></title>
        <!-- ===========page title here=========== -->
        <link rel="shortcut icon" type="image/png" href="<?php echo base_url(); ?>assets/img/favicon-32x32.png"/>
        <!-- <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/dataTables.bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.css">
        <!-- <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/jquery-ui.css"> -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/dropzone.css">
        <!-- <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/croppie.css"> -->
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/simple-sidebar.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css">

        <style>
            .dataTables_length {
                float: left;
            }

            table tr {
                cursor: pointer;
            }


            .fc-event-dot {
                display: inline-block;
                width: 10px;
                height: 10px;
                border-radius: 5px;
            }
        </style>


        <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/jquery.dataTables.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/dataTables.bootstrap.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/jquery-ui.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/jquery.validate.min.js"></script>
    </head>
    <body class="bg-white">
        <div class="db-sidebar bg-theme-light sf">
            <div class="db-sidebar-hero bg-theme">
                <a href="<?php echo base_url(); ?>">
                    <img src="<?php echo base_url(); ?>assets/img/logo_white.png" class="vector-logo">
                </a>
                <button class="btn btn-link pull-right db-sidebar-toggle hidden-xs">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>
            </div>
            <ul class="db-sidebar-items list-unstyled" id="main_menu_list">
                <li class="top-items-wrap">
                    <div class="gear-switcher-container">
                        <input class="gear-switcher" type="submit" value="&#xf013" id="submitButton">
                        <input class="slide-workflow hidden-xs" type="submit" value="" id="submitButton">
                    </div>
                    <div class="clinic-greeting hidden-xs">Workflow</div>
                </li>
                <li id="li_workflow" data-toggle="tooltip" data-placement="right" title="Workflow Summary">
                    <a class="clearfix" href="<?php echo base_url(); ?>workflow_dash"><span>Workflow Dashboard</span><i class="numb-notifies">&nbsp;</i></a>
                </li>
                <li id="li_inbox" data-toggle="tooltip" data-placement="right" title="Fax Inbox">
                    <a class="clearfix" href="<?php echo base_url(); ?>inbox"><span>Fax Inbox</span><i class="numb-notifies" id="count_inbox">&nbsp;</i></a>
                </li>
                <li id="li_my_tasks" data-toggle="tooltip" data-placement="right" title="Fax Triage">
                    <a class="clearfix" href="<?php echo base_url(); ?>my_tasks"><span>Fax Triage</span><i class="numb-notifies" id="count_my_tasks">&nbsp;</i></a>
                </li>
                <li id="li_physician_triage" data-toggle="tooltip" data-placement="right" title="Referral Triage">
                    <a class="clearfix" href="<?php echo base_url(); ?>physician_triage"><span>Referral Triage</span><i class="numb-notifies" id="count_physician">&nbsp;</i></a>
                </li>
                <li id="li_accepted" data-toggle="tooltip" data-placement="right" title="Booking">
                    <a class="clearfix" href="<?php echo base_url(); ?>accepted"><span>Booking</span><i class="numb-notifies" id="count_accepted"></i>&nbsp;</a>
                </li>
                <li id="li_scheduled" data-toggle="tooltip" data-placement="right" title="Scheduled">
                    <a class="clearfix" href="<?php echo base_url(); ?>scheduled"><span>Scheduled</span><i class="numb-notifies" id="count_scheduled_stopped">&nbsp;</i></a>
                </li>
            </ul>
            <ul class="db-sidebar-items list-unstyled" id="second_menu_list" style="display:none">
                <li class="top-items-wrap">
                    <div class="gear-switcher-container">
                        <input class="gear-switcher" type="submit" value="&#xf013" id="submitButton">
                        <input class="slide-dashboard hidden-xs" type="submit" value="" id="submitButton">
                    </div>
                    <div class="clinic-greeting hidden-xs">Manage</div>
                </li>
                <li id="li_analytics_dashboard" data-toggle="tooltip" data-placement="right" title="Analytics Dashboard">
                    <a class="clearfix" href="<?php echo base_url(); ?>analytics"><span>Analytics Dashboard</span><img src="<?php echo base_url(); ?>assets/img/dashboard.png" /></a>
                </li>
                <li id="li_schedule_settings" data-toggle="tooltip" data-placement="right" title="" data-original-title="Schedule Settings">
                    <a class="clearfix" href="<?php echo base_url(); ?>schedule_settings"><span>Schedule Settings</span><img src="<?php echo base_url(); ?>assets/img/dashboard.png"></a>
                </li>				
                <li id="li_all_patient_records" data-toggle="tooltip" data-placement="right" title="All Patient Records">
                    <a class="clearfix" href="<?php echo base_url(); ?>completed"><span>All Patient Records</span><i class="fa fa-user" aria-hidden="true"></i></a>
                </li>
                <li id="li_completed_tasks" data-toggle="tooltip" data-placement="right" title="Completed Tasks">
                    <a class="clearfix" href="<?php echo base_url(); ?>completed_tasks"><span>Completed Tasks</span>
                        <i class="fa fa-check" aria-hidden="true"></i>
                        <!-- <i class="numb-notifies" id="count_completed_tasks">&nbsp;</i> -->
                    </a>
                </li>
                <li id="li_manage_physician" data-toggle="tooltip" data-placement="right" title="Manage Physicians">
                    <a class="clearfix" href="<?php echo base_url(); ?>manage_physician"><span>Manage Physicians</span><img src="<?php echo base_url(); ?>assets/img/physicians.png" /></a>
                </li>
                <li id="li_admin_settings" data-toggle="tooltip" data-placement="right" title="Admin settings">
                    <a class="clearfix" href="<?php echo base_url(); ?>admin_settings"><span>Admin settings</span><img src="<?php echo base_url(); ?>assets/img/admin-settings.png" /></a>
                </li>
            </ul>
        </div>
        <div class="db-nav bg-white mobile-title">
            <div class="db-nav-title">
                <button class="btn btn-link pull-right db-sidebar-toggle hidden-sm hidden-md hidden-lg">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>
                <h1 id="page_header">
                    <?php echo $page_title; ?> <!-- ===========page title here=========== -->
                    <?php
                    if ($this->uri->segment(1) === "accepted" && $this->uri->segment(2) !== "referral_details") {
                        ?>
                        &nbsp; &nbsp; 
                        <button id="btn_view_add_new_patient" type="button" class="btn btn-theme pull-right bttn-circle btn-theme btn-alt-theme">
                            <i class="fa fa-plus-circle"></i>&nbsp;&nbsp;
                            <span>New Patient</span>
                        </button>
                        <?php
                    }
                    ?>
                    <small class="nav-patient-name" id="sub_header"></small>
                </h1>
            </div>
            <div class="db-nav-elements hidden-xs">
                <div class="db-nav-elements-right">
                    <div class="db-nav-search">
                        <i class="fa fa-search"></i>
                        <input type="text" placeholder="Search..." class="db-nav-search-input" id="txt_patient_search"/>
                    </div>
                    <div class="btn-group">
                        <button type="button" id="notification_trigger" class="btn btn-link db-nav-notification-trigger" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="More Options"><i class="fa fa-bell-o fa-2x"></i></button>
                        <div class="dropdown-menu dropdown-menu-right notification-container">
                            <div class="notification-header">Notifications</div>
                            <div class="notification-content">
                                <ul class="notification-list list-unstyled" id="notifications">
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-link db-nav-more-trigger" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="More Options"><i class="fa fa-angle-down fa-2x"></i></button>
                        <ul class="dropdown-menu dropdown-menu-right topnav-dropdown">
                            <li><a href="#"><i class="fa fa-user-md"></i><?php echo $this->session->userdata("physician_name"); ?></a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="<?php echo base_url(); ?>login/logout"><i class="fa fa-power-off"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <form class="hidden" id="sample_form">
                <input type="hidden" name="id" id="id"/>
                <input type="hidden" name="target" id="target"/>
                <input type="hidden" name="param" id="param"/>
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            </form>
        </div>
        <div class="db-content bg-white">
            <!-- ===========load content here=========== -->
            <?php echo $page_content; ?>
        </div>
        <!-- =========Load Modals here=========== -->

        <div id="modal_success" class="modal fade" role="dialog" style="z-index: 2056 !important;">
            <div class="modal-dialog modal-md">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Operation Successful</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="modal_error" class="modal fade" role="dialog" style="z-index: 2056 !important;">
            <div class="modal-dialog modal-md">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Operation Failed</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-theme btn-alt-theme" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <!--Modal for request missing items-->
        <div id="modal_missing_items" class="modal fade" role="dialog" style="z-index: 2056 !important;">
            <div class="modal-dialog modal-md">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Request Missing Items</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info content"> </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-theme btn-success" id="btn_request_missing_items" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Loading...">Send Request Fax</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="<?php echo base_url(); ?>assets/js/common.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/dropzone.js"></script> 
        <script src="<?php echo base_url(); ?>assets/js/croppie.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/PDFObject-master/pdfobject.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/printjs.js"></script>

        <script src="<?php echo base_url(); ?>assets/js/bootstrap-datetimepicker.min.js" charset="UTF-8"></script>
        <script src="<?php echo base_url(); ?>assets/js/date-timepicker.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/dobPicker.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/custom.min.js"></script>


        <script>

            global_data = {};

            base = "<?php echo base_url(); ?>";
            global_data.referral_id = "<?php echo $this->uri->segment(3); ?>";
            global_data.clinic_id = "<?php echo md5($this->session->userdata("user_id")); ?>";

            tableActionTO = null;
            myDropzone = null;

            function IsJsonString(str) {
                try {
                    JSON.parse(str);
                } catch (e) {
                    return false;
                }
                return true;
            }


            function get_dom_plan() {
                return '<"wrapper"tlp>';
            }

            function get_records_dom_plan() {
                return '<"wrapper"tp>';
            }

            function success(msg, header = "Operation Successfull") {
                $("#modal_success").find(".alert-success").html(msg);
                $("#modal_success").find(".modal-title").html(header);
                view("modal_success");
            }
            function error(msg, header = "Operation Failed") {
                $("#modal_error").find(".alert-danger").html(msg);
                $("#modal_error").find(".modal-title").html(header);
                view("modal_error");
            }
            function view(modal_id) {
                setTimeout(function () {
                    $("#" + modal_id).modal("show");
                }, 500);
            }

            function IsJsonString(str) {
                try {
                    JSON.parse(str);
                } catch (e) {
                    return false;
                }
                return true;
            }

            function get_latest_dashboard_counts() {
                $.post({
                    url: base + "referral/fetch_dashboard_counts",
                    data: $("#sample_form").serialize()
                }).done(function (response) {
                    if (IsJsonString(response)) {
                        data = JSON.parse(response);
                        data = data[0];

                        list = $("#main_menu_list");
                        list.find("#count_inbox").html(data.count_inbox);
                        list.find("#count_admin").html(data.count_admin);
                        list.find("#count_physician").html(data.count_physician);
                        list.find("#count_accepted").html(data.count_accepted);
                        list.find("#count_scheduled").html(data.count_scheduled);
                        list.find("#count_my_tasks").html(data.count_my_tasks);
                        // $("#second_menu_list").find("#count_completed_tasks").html(data.count_completed_tasks);
                    }
                });
            }


            function set_visit_status(status, dot) {
                console.log(status, dot);
                if (dot == "red") {
                    return '<span class="fc-event-dot" style="background-color:#f74444"></span>  ' + status;
                } else if (dot == "green") {
                    return '<span class="fc-event-dot" style="background-color:#88b794"></span>  ' + status;
                } else if (dot == "yellow") {
                    return '<span class="fc-event-dot" style="background-color:#9da1c3"></span>  ' + status;
                } else if (dot == "blue") {
                    return '<span class="fc-event-dot" style="background-color:#e7e92a"></span>  ' + status;
                } else {
                    console.log("empty color");
                    console.log(status, dot);
                    return status;
                }
            }


            Dropzone.autoDiscover = false;

            $(document).ready(function () {
                base = "<?php echo base_url(); ?>";

                //fetch dashboard counts
                get_latest_dashboard_counts();

                $(".gear-switcher-container").on("click", function () {
                    $("#main_menu_list, #second_menu_list").toggle();
                });

                $("#txt_patient_search").autocomplete({
                    source: base + "referral/search_patient",
                    minLength: 2,
                    select: function (event, ui) {
                        event.preventDefault();
                        location.href = base + (ui.item.value) + "/referral_details/" + ui.item.id;
                    }
                });


                $("#btn_view_request_missing_items").on("click", function () {
                    $("#btn_view_request_missing_items").button("loading");
                    form = $("#sample_form");
                    form.find("#id").val(global_data.referral_id);
                    url = base + "referral/missing_items_details";
                    data = form.serialize();
                    $.post({
                        url: url,
                        data: data
                    }).done(function (response) {
                        $("#btn_view_request_missing_items").button("reset");
                        if (IsJsonString(response)) {
                            data = JSON.parse(response);
                            if (data.hasOwnProperty("result")) {
                                if (data.result === "success") {
                                    $("#modal_missing_items").find(".content").html(data.data);
                                    view("modal_missing_items");
                                } else {
                                    error("Unexpected Error Occured");
                                }
                            } else {
                                error("Something went wrong");
                            }
                        } else
                            error("Unexpected Error Occured");
                    });
                });

                $("#btn_request_missing_items").on("click", function () {
                    form = $("#sample_form");
                    form.find("#id").val(global_data.referral_id);
                    url = base + "referral/request_missing_items";
                    data = form.serialize();
                    $("#btn_request_missing_items").button('loading');
                    $.post({
                        url: url,
                        data: data
                    }).done(function (response) {
                        if (IsJsonString(response)) {
                            data = JSON.parse(response);
                            if (data === true) {
                                $(".modal").modal("hide");
                                success("Missing item request has been sent");
                            } else {
                                $(".modal").modal("hide");
                                success(response);
                            }
                        } else {
                            error("Unexpected Error Occured");
                        }
                    }).complete(function () {
                        $("#btn_request_missing_items").button('reset');
                    });
                });


            });

        </script>
        <script>
            //track js

            $(document).ready(function () {
                $('#pinBoot').pinterest_grid({no_columns: 4, padding_x: 10, padding_y: 10, margin_bottom: 50, single_column_breakpoint: 700});
            });

            (function ($, window, document, undefined) {
                var pluginName = 'pinterest_grid', defaults = {padding_x: 10, padding_y: 10, no_columns: 3, margin_bottom: 50, single_column_breakpoint: 700}, columns, $article, article_width;
                function Plugin(element, options) {
                    this.element = element;
                    this.options = $.extend({}, defaults, options);
                    this._defaults = defaults;
                    this._name = pluginName;
                    this.init();
                }
                Plugin.prototype.init = function () {
                    var self = this, resize_finish;
                    $(window).resize(function () {
                        clearTimeout(resize_finish);
                        resize_finish = setTimeout(function () {
                            self.make_layout_change(self);
                        }, 11);
                    });
                    self.make_layout_change(self);
                    setTimeout(function () {
                        $(window).resize();
                    }, 500);
                };
                Plugin.prototype.calculate = function (single_column_mode) {
                    var self = this, tallest = 0, row = 0, $container = $(this.element), container_width = $container.width();
                    $article = $(this.element).children();
                    if (single_column_mode === true) {
                        article_width = $container.width() - self.options.padding_x;
                    } else {
                        article_width = ($container.width() - self.options.padding_x * self.options.no_columns) / self.options.no_columns;
                    }
                    $article.each(function () {
                        $(this).css('width', article_width);
                    });
                    columns = self.options.no_columns;
                    $article.each(function (index) {
                        var current_column, left_out = 0, top = 0, $this = $(this), prevAll = $this.prevAll(), tallest = 0;
                        if (single_column_mode === false) {
                            current_column = (index % columns);
                        } else {
                            current_column = 0;
                        }
                        for (var t = 0; t < columns; t++) {
                            $this.removeClass('c' + t);
                        }
                        if (index % columns === 0) {
                            row++;
                        }
                        $this.addClass('c' + current_column);
                        $this.addClass('r' + row);
                        prevAll.each(function (index) {
                            if ($(this).hasClass('c' + current_column)) {
                                top += $(this).outerHeight() + self.options.padding_y;
                            }
                        });
                        if (single_column_mode === true) {
                            left_out = 0;
                        } else {
                            left_out = (index % columns) * (article_width + self.options.padding_x);
                        }
                        $this.css({'left': left_out, 'top': top});
                    });
                    this.tallest($container);
                    $(window).resize();
                };
                Plugin.prototype.tallest = function (_container) {
                    var column_heights = [], largest = 0;
                    for (var z = 0; z < columns; z++) {
                        var temp_height = 0;
                        _container.find('.c' + z).each(function () {
                            temp_height += $(this).outerHeight();
                        });
                        column_heights[z] = temp_height;
                    }
                    largest = Math.max.apply(Math, column_heights);
                    _container.css('height', largest + (this.options.padding_y + this.options.margin_bottom));
                };
                Plugin.prototype.make_layout_change = function (_self) {
                    if ($(window).width() < _self.options.single_column_breakpoint) {
                        _self.calculate(true);
                    } else {
                        _self.calculate(false);
                    }
                };
                $.fn[pluginName] = function (options) {
                    return this.each(function () {
                        if (!$.data(this, 'plugin_' + pluginName)) {
                            $.data(this, 'plugin_' + pluginName, new Plugin(this, options));
                        }
                    });
                }
            })(jQuery, window, document);</script>              

        <!-- ===========add scripts for respective views here=========== -->
        <?php echo $jquery; ?>
    </body>
</html>