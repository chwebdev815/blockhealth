<script>    function set_row4(id, row) {//    debugger        $(row).find("td:not(:last-child)").each(function (index, td) {            $(td).addClass("db-table-link-row");            $(td).attr("data-id", id);            $(td).attr("data-href", base + "accepted/referral_details/" + id);        });        // $(row).addClass("db-table-link-row");        // $(row).addClass("accepted_row");        return '<a class="btn btn-theme bttn-circle view_add_patient" data-id="' + id + '" ' +                'data-toggle="modal" data-target="#modal_add_patient_visit"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp;Patient Visit</a>';    }    function set_missing_status(status, dot) {        if (dot == "red") {            return '<span class="fc-event-dot" style="background-color:#f74444"></span>  ' + status;        } else if (dot == "green") {            return '<span class="fc-event-dot" style="background-color:#88b794"></span>  ' + status;        } else if (dot == "yellow") {            return '<span class="fc-event-dot" style="background-color:#9da1c3"></span>  ' + status;        } else {            return status;        }    }    $(document).ready(function () {        $("#li_accepted").addClass("active");        $("table").on("click", ".db-table-link-row", function () {            location.href = $(this).data("href");        });        // $('table#table_accepted').on("click", ".accepted_row", function() {        //     let id = $(this).data('id');        //     location.href = base + "accepted/referral_details/" + id;        // });        $("table").on("click", ".view_add_patient", function () {            global_data.referral_id = $(this).data("id");        });        $("#btn_add_patient_visit").on("click", function () {            form = $("#form_add_patient_visit");            form.find("#id").val(global_data.referral_id);            data = form.serialize();            url = base + "referral/add_patient_visit";            $.post({                url: url,                data: data            }).done(function (response) {                if (IsJsonString(response)) {                    data = JSON.parse(response);                    if (data == true) {                        $(".modal").modal("hide");                        success("Patient Visit Successfully Created");                        global_data.table_accepted.ajax.reload();                        get_latest_dashboard_counts();                    } else {                        error(data);                    }                } else {                    error("Unexpected Error Occured");                }            });        });        //  *** Accepted Datatable        global_data.table_accepted_title = "Accepted Referral";        global_data.table_accepted = $("#table_accepted").DataTable({            "processing": true,            "serverSide": true,            "autoWidth": false,            "language": {                "emptyTable": "There are no patients to be scheduled",                "info": "Showing _START_ to _END_ of _TOTAL_ " + global_data.table_accepted_title,                "infoEmpty": "No results found",                "infoFiltered": "(filtered from _MAX_ total " + global_data.table_accepted_title + ")",                "infoPostFix": "",                "thousands": ",",                "lengthMenu": "Show _MENU_ ",                "loadingRecords": "Loading " + global_data.table_accepted_title,                "processing": "Processing " + global_data.table_accepted_title,                "search": "",                "zeroRecords": "No matching " + global_data.table_accepted_title + " found"            },            "ajax": "<?php echo base_url(); ?>accepted/ssp_accepted",            "rowCallback": function (row, data, index) {                $('td:eq(4)', row).html(set_row4(data[5], row));                $('td:eq(3)', row).html(set_missing_status(data[3], data[4]));            },            "dom": get_dom_plan(),            // "drawCallback": set_patients_table,            "columnDefs": [                {"width": "20%", "targets": 0},                {"width": "20%", "targets": 1},                {"width": "20%", "targets": 2},                {"width": "20%", "targets": 3},                {"width": "20%", "targets": 4}            ]        });        $("#table_accepted").wrap('<div class="table-responsive"></div>');        // $("#btn_view_add_patient").appendTo("#patients_table_wrapper .button-placeholder");        $(".dataTables_filter input").before('<i class="fa fa-search datatable-search"></i>');        $(".dataTables_filter input").attr('placeholder', 'Search');        //  *** Accepted Datatable Over    });</script> 