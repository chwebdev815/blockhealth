<?php

if (!defined('BASEPATH'))
    exit("Access Denied!");

class Efax extends CI_Controller {

    public function index() {
//        $data_object = array(
//            "m1" => "hola",
//            "m2" => "gracias"
//        );
//        save_json($this->session->userdata("user_id"), $data_object);
        
        $metadata_path = get_metadata_path("1679091c5a880faf6fb5e6087eb1b2dc");
        if(!file_exists($metadata_path)) {
            echo "Path $metadata_path does not exist";
        }
        else {
            $jsondata = file_get_contents($metadata_path);
            echo $jsondata;
        }
    }

    public function send_referral_efax() {
        if (clinic_login()) {
            $this->load->model("efax_model");
            $response = $this->efax_model->send_referral_efax_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function test() {
        $visit_date = "2019-03-30";
        $visit_time = "09:00:00";
        $visit_confirmed = "Awaiting Confirmation";
        $current_date = new DateTime(date("Y-m-d H:i:s"));
        $current_date->add(new DateInterval('P2D'));
        log_message("error", "compare <br/>" . $current_date->format("Y-m-d H:i:s") . "<br/>" .
                $visit_date . " " . $visit_time . "<br/>");
        if ($current_date->format("Y-m-d H:i:s") < ($visit_date . " " . $visit_time)) {
            $visit_confirmed = "N/A";
        }
    }

    public function call_handle() {
        // echo "call handle" . json_encode($this->input->post());
        log_message("error", "from call handeling => " . json_encode($this->input->post()));

        $data = $this->input->post();
        $From = $data["to"];
        $Body = $data["data"];


        //remove + sign
        $From = substr($From, 2);

        log_message("error", "body is 1 or 2 is => " . $Body);
        $this->db->select("DISTINCT(r_pv.id), r_pv.visit_confirmed");
        $this->db->from("referral_patient_info pat, records_patient_visit r_pv");
        $this->db->where(array(
                    "pat.active" => 1,
                    "r_pv.active" => 1
                ))->group_start()
                ->or_group_start()->where(array(
                    "pat.cell_phone" => $From
                ))->group_end()
                ->or_group_start()->where(array(
                    "pat.work_phone" => $From
                ))->group_end()
                ->or_group_start()->where(array(
                    "pat.home_phone" => $From
                ))->group_end()
                ->group_end();
        $this->db->where("r_pv.patient_id", "pat.id", false);
        $result = $this->db->get()->result();

        log_message("error", "webhook sql = " . $this->db->last_query());

        $change_status = false;

        $this->db->trans_start();
        foreach ($result as $row) {
            log_message("error", "row = " . json_encode($row) . "with body = " . $Body . ", status = " . $row->visit_confirmed);
            if ($Body == "1") {
                //change status to confirm
                $this->db->where(array(
                    "id" => $row->id
                ));
                $this->db->set("visit_confirmed", "Awaiting Confirmation");
                $this->db->update("records_patient_visit");
                $change_status = true;
                log_message("error", "change (1) " . $this->db->last_query());
            }
            if ($Body == "2") {
                //change status to Change required
                $this->db->where(array(
                    "id" => $row->id
                ));
                $this->db->set("visit_confirmed", "Change required");
                $this->db->update("records_patient_visit");
                $change_status = true;
                log_message("error", "change (2) " . $this->db->last_query());
            }
        }
        $this->db->trans_complete();
    }

}
