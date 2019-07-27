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

        <script>
            window['_fs_debug'] = false;
            window['_fs_host'] = 'fullstory.com';
            window['_fs_org'] = '9P1E2';
            window['_fs_namespace'] = 'FS';
            (function (m, n, e, t, l, o, g, y) {
                if (e in m) {
                    if (m.console && m.console.log) {
                        m.console.log('FullStory namespace conflict. Please set window["_fs_namespace"].');
                    }
                    return;
                }
                g = m[e] = function (a, b) {
                    g.q ? g.q.push([a, b]) : g._api(a, b);
                };
                g.q = [];
                o = n.createElement(t);
                o.async = 1;
                o.src = 'https://' + _fs_host + '/s/fs.js';
                y = n.getElementsByTagName(t)[0];
                y.parentNode.insertBefore(o, y);
                g.identify = function (i, v) {
                    g(l, {uid: i});
                    if (v)
                        g(l, v)
                };
                g.setUserVars = function (v) {
                    g(l, v)
                };
                g.event = function (i, v) {
                    g('event', {n: i, p: v})
                };
                g.shutdown = function () {
                    g("rec", !1)
                };
                g.restart = function () {
                    g("rec", !0)
                };
                g.consent = function (a) {
                    g("consent", !arguments.length || a)
                };
                g.identifyAccount = function (i, v) {
                    o = 'account';
                    v = v || {};
                    v.acctId = i;
                    g(o, v)
                };
                g.clearUserCookie = function () {};
            })(window, document, window['_fs_namespace'], 'script', 'user');
        </script>
        <style>
            .dataTables_length {
                float: left;
            }
            .db-nav-elements > .db-nav-elements-right {
                width: auto;
            }
        </style>
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
                    <div class="clinic-greeting hidden-xs">Workflow</div>
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
            <form class="hidden" id="main_form">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
            </form>
        </div>
        <div class="db-content bg-white">
            <!-- ===========load content here=========== -->
            <?php echo $page_content; ?>
        </div>
        <form class="hidden" id="sample_form">
            <input type="hidden" name="id" id="id"/>
            <input type="hidden" name="target" id="target"/>
            <input type="hidden" name="param" id="param"/>
            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
        </form>
        <div id="modal_success" class="modal fade" role="dialog" style="z-index: 2056 !important;">
            <div class="modal-dialog modal-md">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Operatrion Successful</h4>
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
                        <h4 class="modal-title">Operatrion Failed</h4>
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
        <script>
        </script>
        <!-- =========Load Modals here=========== -->
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/jquery.dataTables.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/dataTables.bootstrap.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/jquery-ui.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/jquery.validate.min.js"></script>
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
            global_data.record_id = "<?php echo $this->uri->segment(3); ?>";
            global_data.page = "<?php echo $this->uri->segment(1); ?>";
            global_data.form_sample = $("#sample_form");

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
                return '<"wrapper"tlp>';
            }

            function success(msg, header) {
                if (header === void 0) {
                    header = "Operation Successfull";
                }

                $("#modal_success").find(".alert-success").html(msg);
                $("#modal_success").find(".modal-title").html(header);
                view("modal_success");
            }
            function error(msg, header) {
                if (header === void 0) {
                    header = "Operation Failed";
                }

                $("#modal_error").find(".alert-danger").html(msg);
                $("#modal_error").find(".modal-title").html(header);
                view("modal_error");
            }
            function view(modal_id) {
                setTimeout(function () {
                    $("#" + modal_id).modal("show");
                }, 100);
            }

            $(document).ready(function () {
                get_dash_info();
            });


            function get_dash_info() {
                if (typeof global_data.record_id !== 'undefined') {
                    global_data.form_sample.find("#id").val(global_data.record_id);
                    global_data.form_sample.find("#target").val(global_data.page);
                }
                data = global_data.form_sample.serialize();
                url = base + "dashboard/get_dash_info";

                $.post({
                    url: url,
                    data: data
                }).done(function (response) {
                    data = JSON.parse(response);
                    if (data.response == "success") {
                        menu_items = "";
                        for (i = 0; i < data.clinics.length; i++) {
                            menu_items += '<li id="li_' + data.clinics[i].id + '" \n\
                        title="' + data.clinics[i].clinic_institution_name + '" data-toggle="tooltip" \n\
                        data-placement="right">';
                            menu_items += '<a class="clearfix" href="' + base + 'clinic/referrals/' + data.clinics[i].id + '">\n\
                        <span>' + data.clinics[i].clinic_institution_name + '</span>\n\
                        <i class="numb-notifies">&nbsp;</i></a></li>';
                        }
                        $("#main_menu_list").append(menu_items);
                        my_data = data;
                        setTimeout(function () {
                            if (typeof global_data.record_id !== 'undefined') {
                                $("h1#page_header").html(my_data.header);
                                $("#main_menu_list").find("#li_" + my_data.clinic_id).addClass("active");
                            }
                        }, 100);
                    } else {
                        error("Failed to load dashboard info");
                    }
                });
            }
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