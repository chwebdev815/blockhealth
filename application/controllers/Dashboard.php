<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function index() {
    	if (referring_physician_login()) {
            //redirect to first clinic
            $fax = $this->session->userdata("fax_number");
            $this->db->select("md5(c_usr.id) as id, c_usr.clinic_institution_name");
            $this->db->from("clinic_user_info c_usr, referral_physician_info dr, referral_patient_info pat, clinic_referrals c_ref, efax_info efax");
            $this->db->where(array(
                "dr.fax" => $fax,
                "dr.active" => 1,
                "pat.active" => 1,
                "c_ref.active" => 1,
                "efax.active" => 1,
                "c_usr.active" => 1
            ));
            $this->db->where("pat.id", "dr.patient_id", false);
            $this->db->where("c_ref.id", "pat.referral_id", false);
            $this->db->where("efax.id", "c_ref.efax_id", false);
            $this->db->where("c_usr.id", "efax.`to`", false);

            $this->db->group_by("c_usr.id");
            $clinics = $this->db->get()->result();
            //log_message("error", $this->db->last_query());
            //log_message("error", "clinics result = " . json_encode($clinics));
            redirect("clinic/referrals/" . $clinics[0]->id);
            exit;

            $data['page_content'] = $this->load->view('dashboard_master', NULL, TRUE);
            $data['page_title'] = "Dashboard";
            $data['jquery'] = $this->load->view('scripts/dashboard_script', NULL, TRUE);
            $this->load->view('rp_main', $data);
        }
    	else {
            redirect("/");
        }
    }
    
    public function get_dash_info() {
        if (referring_physician_login() ||  $this->session->userdata("signup_done")) {
            $this->load->model("rp_model");
            $response = $this->rp_model->get_dash_info_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }
}
