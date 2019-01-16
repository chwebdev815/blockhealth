<script>
    global_data.form_sample = $("#sample_form");
    
    //table_clinic_referrals
    $(document).ready(function () {

        //  *** Clinic Referrals Datatable
        global_data.table_clinic_referrals_title = "Clinic Referrals";
        global_data.table_clinic_referrals = $("#table_clinic_referrals").DataTable({
            "order": false, //[[ 2, "desc" ]],
            "processing": true,
            "serverSide": true,
            "autoWidth": false,
            "language": {
                "emptyTable": "There are no " + global_data.table_clinic_referrals_title,
                "info": "Showing _START_ to _END_ of _TOTAL_ " + global_data.table_clinic_referrals_title,
                "infoEmpty": "No results found",
                "infoFiltered": "(filtered from _MAX_ total " + global_data.table_clinic_referrals_title + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Show _MENU_ ",
                "loadingRecords": "Loading " + global_data.table_clinic_referrals_title,
                "processing": "Processing " + global_data.table_clinic_referrals_title,
                "search": "",
                "zeroRecords": "No matching " + global_data.table_clinic_referrals_title + " found"
            },
            "ajax": "<?php echo base_url(); ?>clinic/ssp_clinic_referrals/" + global_data.record_id,
            "rowCallback": function (row, data, index) {
                $('td:eq(3)', row).html(set_id_row(data[3], row, "clinic_referral_row"));
            },
            "dom": get_dom_plan(),
            // "drawCallback": set_patients_table,
            "columnDefs": [
                {"width": "25%", "targets": 0},
                {"width": "60%", "targets": 1},
                {"width": "20%", "targets": 2}
            ]
        });
        $("#table_clinic_referrals").wrap('<div class="table-responsive"></div>');
        // $("#btn_view_add_patient").appendTo("#patients_table_wrapper .button-placeholder");
        $("#table_clinic_referrals_wrapper").find(".dataTables_filter input").before('<i class="fa fa-search datatable-search"></i>');
        $("#table_clinic_referrals_wrapper").find(".dataTables_filter input").attr('placeholder', 'Search');
        //  *** Clinic Referrals Datatable Over
    });

    $("table").on("click", ".clinic_referral_row", function () {
        location.href = base + "tracker/referral/" + $(this).data("id");
    });

    function set_id_row(id, row, row_class) {
        $(row).attr("data-id", id);
        $(row).addClass('db-table-link-row');
        $(row).addClass(row_class);
    }

</script>