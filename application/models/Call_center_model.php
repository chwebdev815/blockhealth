<?php

class Call_center_model extends CI_Model {

    public function ssp_call_center_model() {
//        if ($this->session->userdata("user_id") === 9) {
//            //telnyxfirst
//            $table = "view_call_center";
//
//            $primaryKey = "id";
//            $columns = array(
//                array('db' => 'patient_name', 'dt' => 0),
//                array('db' => 'phone_number', 'dt' => 1),
//                array('db' => 'reason', 'dt' => 2),
//                array('db' => 'recording', 'dt' => 3),
//                array('db' => 'status', 'dt' => 4),
//                array('db' => 'id', 'dt' => 5)
//            );
//
//            $sql_details = array(
//                'user' => $this->db->username,
//                'pass' => $this->db->password,
//                'db' => $this->db->database,
//                'host' => $this->db->hostname
//            );
//            $where = "";
//            if (clinic_admin_login()) {
//                $where = "clinic_id =" . $this->session->userdata("user_id");
//            } else if (clinic_physician_login()) {
//                $where = "clinic_id =" . $this->session->userdata("user_id");
//            }
//
//            require('ssp.class.php');
//            return json_encode(
//                    SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where)
//            );
//        }
//        if ($this->session->userdata("user_id") === 10) {
        //well health
        $table = "call_center_well_health";

        $primaryKey = "id";
        $columns = array(
            array('db' => 'patient_name', 'dt' => 0),
            array('db' => 'phone_number', 'dt' => 1, 'formatter' => function( $data ) {
                    return format_us_number($data);
                }),
            array('db' => 'caller', 'dt' => 2),
            array('db' => 'recording', 'dt' => 3),
            array('db' => 'status', 'dt' => 4),
            array('db' => 'id', 'dt' => 5)
        );

        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db' => $this->db->database,
            'host' => $this->db->hostname
        );
        $where = "";
        if (clinic_admin_login()) {
            $where = "clinic_id =" . $this->session->userdata("user_id");
        } else if (clinic_physician_login()) {
            $where = "clinic_id =" . $this->session->userdata("user_id");
        }

        require('ssp.class.php');
        return json_encode(
                SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where)
        );
    }

    public function task_completed_model() {
        //log_message("error", "updating task");
        $this->form_validation->set_rules('id', 'Log ID', 'required');
        if ($this->form_validation->run()) {
            $this->db->trans_start();
            $data = $this->input->post();

            $this->db->where(array(
                "md5(id)" => $data["id"]
            ));
            $updated = $this->db->update("telnyx_incoming", array(
                "active" => 0
            ));
            $this->db->trans_complete();
            //log_message("error", "remove tasks => " . $this->db->last_query());
            if ($updated) {
                return array(
                    "result" => "success"
                );
            } else {
                return array(
                    "result" => "error",
                    "msg" => "Failed to remove log"
                );
            }
        } else {
            return array(
                "result" => "error",
                "msg" => validation_errors()
            );
        }
    }

}
