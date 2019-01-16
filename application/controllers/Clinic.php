<?php

class Clinic extends CI_Controller {

    public function referrals() {
        if (referring_physician_login()) {
            $data['page_content'] = $this->load->view('clinic_master', NULL, TRUE);
            $data['page_title'] = "Clinic";
            $data['jquery'] = $this->load->view('scripts/clinic_script', NULL, TRUE);
            $this->load->view('rp_main', $data);
        } else if($this->session->userdata("signup_done") == "no") {
            redirect("tracker/referral/" . $this->session->userdata("referral_code"));
        } else {
            redirect("/");
        }
    }

    public function ssp_clinic_referrals() {
        if (referring_physician_login()) {
            $this->load->model("clinic_model");
            $response = $this->clinic_model->ssp_clinic_referrals_model();
            echo $response;
        } else {
            echo false;
        }
    }
    
    
   
    public function get_dash_info() {
        if (referring_physician_login()) {
            $this->load->model("rp_model");
            $response = $this->rp_model->get_dash_info_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

}
