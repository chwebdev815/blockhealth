<script>

    function set_id_row(id, row, row_class) {
        $(row).attr("data-id", id);
        $(row).addClass('db-table-link-row');
        $(row).addClass(row_class);
    }

    $(document).ready(function () {

        //*******************************************
        // script for records
        //*******************************************
        $("#btn_view_add_health_record").on("click", function () {
            view("modal_add_health_record");
            $("#form_health_record")[0].reset();
        });
        $("#btn_view_add_admin_note").on("click", function () {
            view("modal_add_admin_note");
            $("#form_add_admin_note")[0].reset();
        });
        $("#btn_view_add_patient_visit").on("click", function () {
            view("modal_add_patient_visit");
            $("#modal_add_patient_visit").find("#form_add_patient_visit")[0].reset();
            $("#form_add_patient_visit").find("input[name='cell_phone_voice']").prop("checked", global_data.sms_notification_allowed);
            $("#form_add_patient_visit").find("input[name='cell_phone']").prop("checked", global_data.sms_notification_allowed);
            $("#form_add_patient_visit").find("input[name='email']").prop("checked", global_data.email_notification_allowed);
        });


        $('#modal_add_health_record').on('hidden.bs.modal', function () {
            if (myDropzone != undefined) {
                myDropzone.removeAllFiles();
            }
        });

        $('#modal_add_health_record').on('shown.bs.modal', function () {
            //Simple Dropzonejs 
            myDropzone = new Dropzone("#dropzone_health_record", {
                maxFilesize: 100,
                url: base + 'referral/add_health_record',
                addRemoveLinks: true,
                autoProcessQueue: false,
                uploadMultiple: true,
                paramName: 'asdqwe',
                parallelUploads: 100,
                maxFiles: 1,
                acceptedFiles: "application/pdf",
                init: function () {
                    var myDropzone = this;
                    $("#btn_save_health_record").on("click", function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $("#btn_save_health_record").button("loading");
                        var form = $('#form_health_record');
                        if (form.valid() == true) {
                            if (myDropzone.getQueuedFiles().length > 0) {
                                myDropzone.processQueue();
                            } else {
                                var blob = new Blob();
                                blob.upload = {'chunked': myDropzone.defaultOptions.chunking};
                                myDropzone.uploadFile(blob);
                            }
                        }
                    });
                    this.on("sendingmultiple", function (files, xhr, formData) {
                        // Gets triggered when the form is actually being sent.
                        // Hide the success button or the complete form.
                        $('#form_health_record').find("#id").val(global_data.referral_id);
                        formData.append('<?php echo $this->security->get_csrf_token_name(); ?>'
                                , '<?php echo $this->security->get_csrf_hash(); ?>');
                        //add other form data
                        $("#form_health_record").find("select, textarea, input[type!='file']").each(function (index, value) {
                            name = $(value).attr("name");
                            val = ($(value).val() != null) ? $(value).val() : "";
                            formData.append(name, val);
                        });
                    });
                    this.on("successmultiple", function (files, response) {
                        // Gets triggered when the files have successfully been sent.
                        // Redirect user or notify of success.
                        $("#btn_save_health_record").button("loading");
                        myDropzone.removeAllFiles();
                        if (IsJsonString(response)) {
                            data = JSON.parse(response);
                            if (data == true) {
                                $(".modal").modal("hide");
                                success("Health record has been successfully saved", "Health record saved");
                                global_data.table_health_records.ajax.reload();
                            } else {
                                error(data);
                            }
                        } else
                            error("Unexpected Error Occured");
                    });
                }
            });
        });
        $("#add_admin_note").on("click", function () {
            form = $("#form_add_admin_note");
            form.find("#id").val(global_data.referral_id);
            data = form.serialize();
            url = base + "referral/add_admin_note";
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    if (data == true) {
                        $(".modal").modal("hide");
//                        success("Admin Note Successfully Created");
                        global_data.table_admin_notes.ajax.reload();
                    } else {
                        error(data);
                    }
                } else {
                    error("Unexpected Error Occured");
                }
            });
        });
        $("#btn_add_patient_visit").on("click", function () {
            $("#btn_add_patient_visit").button("loading");
            form = $("#form_add_patient_visit");
            form.find("#id").val(global_data.referral_id);
            data = form.serialize();
            url = base + "referral/add_patient_visit";
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                $("#btn_add_patient_visit").button("reset");
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    if (data == true) {
                        $(".modal").modal("hide");
                        success("Patient visit has been successfully added", "Visit Added");
                        global_data.table_patient_visits.ajax.reload();
                        get_latest_dashboard_counts();
                    } else {
                        error(data);
                    }
                } else {
                    error("Unexpected Error Occured");
                }
            });
        });

        $("#btn_update_patient_visit").on("click", function () {
            $("#btn_update_patient_visit").button("loading");
            form = $("#form_update_patient_visit");
            form.find("#id").val(global_data.referral_id);
            data = form.serialize();
            url = base + "referral/update_patient_visit";
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                $("#btn_update_patient_visit").button("reset");
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    if (data == true) {
                        $(".modal").modal("hide");
                        success("Patient Visit Successfully Updated");
                        global_data.table_patient_visits.ajax.reload();
                    } else {
                        error(data);
                    }
                } else {
                    error("Unexpected Error Occured");
                }
            });
        });
        $("#btn_cancel_visit").on("click", function () {
            $("#btn_cancel_visit").button("loading");
            form = $("#form_update_patient_visit");
            form.find("#id").val(global_data.referral_id);
            data = form.serialize();
            url = base + "referral/cancel_patient_visit";
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                $("#btn_cancel_visit").button("reset");
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    if (data == true) {
                        $(".modal").modal("hide");
                        success("Patient Visit Successfully Cancelled");
                        global_data.table_patient_visits.ajax.reload();
                    } else {
                        error(data);
                    }
                } else {
                    error("Unexpected Error Occured");
                }
            });
        });
        $("table").on("click", ".health_records_row", function () {
            form = $("#sample_form");
            form.find("#id").val(global_data.referral_id);
            form.find("#target").val($(this).data("id"));
            url = base + "referral/get_health_record_info";
            data = form.serialize();
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    data = data[0];
                    root = $("#modal_view_health_record");
                    root.find("#record_type").html(data.record_type);
                    root.find("#description").html(data.description);
                    if (data.record_file != "") {
//                        root.find("#btn_view_uploaded_doc").attr("data-id", data.record_file);
//                        root.find("#btn_view_uploaded_doc").show();
                        global_data.uploaded_file_path = base + "uploads/health_records/" + $(this).data("id") + ".pdf";
                        PDFObject.embed(base + "uploads/health_records/" + data.record_file + ".pdf", "#pdf_view_div");
//                        global_data.uploaded_file_path = base + "uploads/health_records/" + data.record_file + ".pdf";
                    } else {
                        root.find("#btn_view_uploaded_doc").hide();
                    }
                    view("modal_view_health_record");
                } else {
                    error("Unexpected Error Occured");
                }
            });
        });
        $("#btn_view_uploaded_doc").on("click", function () {

            view("modal_document_viewer");
        });
        $("#btn_print_uploaded_pdf").on("click", function () {
            printJS(global_data.uploaded_file_path);
        });
        $("table").on("click", ".admin_notes_row", function () {
            form = $("#sample_form");
            form.find("#id").val(global_data.referral_id);
            form.find("#target").val($(this).data("id"));
            url = base + "referral/get_admin_notes_info";
            data = form.serialize();
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    data = data[0];
                    root = $("#modal_view_admin_note");
                    root.find("#note_type").val(data.note_type);
                    root.find("#description").val(data.description);
                    view("modal_view_admin_note");
                } else {
                    error("Unexpected Error Occured");
                }
            });
        });
        $("table").on("click", ".patient_visits_row", function () {
            form = $("#form_update_patient_visit");
            form.find("#id").val(global_data.referral_id);
            form.find("#target").val($(this).data("id"));
            url = base + "referral/get_patient_visit_info";
            data = form.serialize();
            $.post({
                url: url,
                data: data
            }).done(function (response) {
                if (IsJsonString(response)) {
                    data = JSON.parse(response);
                    data = data[0];
                    root = $("#modal_view_patient_visit");
                    root.find("#visit_name").val(data.visit_name);
                    root.find("#visit_date").val(data.visit_date);
                    root.find('.edit_form_date').datetimepicker("update", new Date(data.visit_date));
                    root.find("#visit_time").val(data.visit_time);
                    root.find('.edit_form_time').datetimepicker("update", new Date("01-01-1111 " + data.visit_time));
                    root.find("#cell_phone").prop("checked", (data.notify_sms == "1") ? true : false);
                    root.find("#email").prop("checked", (data.notify_email == "1") ? true : false);
                    view("modal_view_patient_visit");
                } else {
                    error("Unexpected Error Occured");
                }
            });
        });
        //  *** Health Records Datatable
        global_data.table_health_records_title = "Health Records";
        global_data.table_health_records = $("#table_health_records").DataTable({
            "order": false, //[[ 2, "desc" ]],
            "processing": true,
            "serverSide": true,
            "autoWidth": false,
            "language": {
                "emptyTable": "There are no " + global_data.table_health_records_title,
                "info": "Showing _START_ to _END_ of _TOTAL_ " + global_data.table_health_records_title,
                "infoEmpty": "No results found",
                "infoFiltered": "(filtered from _MAX_ total " + global_data.table_health_records_title + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Show _MENU_ ",
                "loadingRecords": "Loading " + global_data.table_health_records_title,
                "processing": "Processing " + global_data.table_health_records_title,
                "search": "",
                "zeroRecords": "No matching " + global_data.table_health_records_title + " found"
            },
            "ajax": "<?php echo base_url(); ?>referral/ssp_health_records/" + global_data.referral_id,
            "rowCallback": function (row, data, index) {
                $('td:eq(3)', row).html(
                        set_id_row(data[3], row, "health_records_row")
                        );
            },
            "dom": get_records_dom_plan(),
            // "drawCallback": set_patients_table,
            "columnDefs": [
                {"width": "20%", "targets": 0},
                {"width": "55%", "targets": 1},
                {"width": "25%", "targets": 2}
            ]
        });
        $("#table_health_records").wrap('<div class="table-responsive"></div>');
        // $("#btn_view_add_patient").appendTo("#patients_table_wrapper .button-placeholder");
        $("#table_health_records_wrapper").find(".dataTables_filter input").before('<i class="fa fa-search datatable-search"></i>');
        $("#table_health_records_wrapper").find(".dataTables_filter input").attr('placeholder', 'Search');
        //  *** Health Records Datatable Over
        //  *** Admin Notes Datatable
        global_data.table_admin_notes_title = "Admin Notes";
        global_data.table_admin_notes = $("#table_admin_notes").DataTable({
            "processing": true,
            "serverSide": true,
            "autoWidth": false,
            "language": {
                "emptyTable": "There are no " + global_data.table_admin_notes_title,
                "info": "Showing _START_ to _END_ of _TOTAL_ " + global_data.table_admin_notes_title,
                "infoEmpty": "No results found",
                "infoFiltered": "(filtered from _MAX_ total " + global_data.table_admin_notes_title + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Show _MENU_ ",
                "loadingRecords": "Loading " + global_data.table_admin_notes_title,
                "processing": "Processing " + global_data.table_admin_notes_title,
                "search": "",
                "zeroRecords": "No matching " + global_data.table_admin_notes_title + " found"
            },
            "ajax": "<?php echo base_url(); ?>referral/ssp_admin_notes/" + global_data.referral_id,
            "rowCallback": function (row, data, index) {
                $('td:eq(3)', row).html(
                        set_id_row(data[3], row, "admin_notes_row")
                        );
            },
            "dom": get_records_dom_plan(),
            // "drawCallback": set_patients_table,
            "columnDefs": [
                {"width": "25%", "targets": 0},
                {"width": "50%", "targets": 1},
                {"width": "25%", "targets": 2}
            ]
        });
        $("#table_admin_notes").wrap('<div class="table-responsive"></div>');
        // $("#btn_view_add_patient").appendTo("#patients_table_wrapper .button-placeholder");
        $("#table_admin_notes_wrapper").find(".dataTables_filter input").before('<i class="fa fa-search datatable-search"></i>');
        $("#table_admin_notes_wrapper").find(".dataTables_filter input").attr('placeholder', 'Search');
        //  *** Admin Notes Datatable Over
        //  *** Patient Visits Datatable
        global_data.table_patient_visits_title = "Patient Visits";
        global_data.table_patient_visits = $("#table_patient_visits").DataTable({
            "processing": true,
            "serverSide": true,
            "autoWidth": false,
            "language": {
                "emptyTable": "There are no " + global_data.table_patient_visits_title,
                "info": "Showing _START_ to _END_ of _TOTAL_ " + global_data.table_patient_visits_title,
                "infoEmpty": "No results found",
                "infoFiltered": "(filtered from _MAX_ total " + global_data.table_patient_visits_title + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Show _MENU_ ",
                "loadingRecords": "Loading " + global_data.table_patient_visits_title,
                "processing": "Processing " + global_data.table_patient_visits_title,
                "search": "",
                "zeroRecords": "No matching " + global_data.table_patient_visits_title + " found"
            },
            "ajax": "<?php echo base_url(); ?>referral/ssp_patient_visits/" + global_data.referral_id,
            "rowCallback": function (row, data, index) {
                $('td:eq(4)', row).html(
                        set_id_row(data[4], row, "patient_visits_row")
                        );
            },
            "dom": get_records_dom_plan(),
            // "drawCallback": set_patients_table,
            "columnDefs": [
                {"width": "25%", "targets": 0},
                {"width": "25%", "targets": 1},
                {"width": "25%", "targets": 2},
                {"width": "25%", "targets": 3}
            ]
        });
        $("#table_patient_visits").wrap('<div class="table-responsive"></div>');
        // $("#btn_view_add_patient").appendTo("#patients_table_wrapper .button-placeholder");
        $("#table_patient_visits_wrapper").find(".dataTables_filter input").before('<i class="fa fa-search datatable-search"></i>');
        $("#table_patient_visits_wrapper").find(".dataTables_filter input").attr('placeholder', 'Search');
        //  *** Patient Visits Datatable Over
    });
</script>