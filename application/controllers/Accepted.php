<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Accepted extends CI_Controller {

    public function index() {
        if (clinic_login()) {
            $data['page_content'] = $this->load->view('accepted_master', NULL, TRUE);
            $data['page_title'] = "Accepted";
            $data['jquery'] = $this->load->view('scripts/accepted_script', NULL, TRUE);
            $this->load->view('main', $data);
        } else {
            redirect("/");
        }
    }

    public function ssp_accepted() {
        if (clinic_login()) {
            $this->load->model("accepted_model");
            $response = $this->accepted_model->ssp_accepted_model();
            echo $response;
        } else {
            echo false;
        }
    }

    // *********************************************************************    
// Accepted Patient Details    
    // *********************************************************************  
    public function referral_details() {
        if (clinic_login()) {
            //check valid state    
            $this->load->model("referral_model");
            $response = $this->referral_model->check_valid_referral_state_model("Accepted");
            if ($response == true) {
                $data['page_content'] = $this->load->view('accepted_referral_details', NULL, TRUE);
                $data['page_title'] = "Accepted Patient";
                $data['jquery'] = $this->load->view('scripts/accepted_referral_details_script', NULL, TRUE);
                $this->load->view('main', $data);
            } else {
                //set error message (remaining)       
                redirect("accepted");
            }
        } else {
            redirect("/");
        }
    }

    public function get_referral_dash_info() {
        if (clinic_login()) {
            $this->load->model("accepted_model");
            $response = $this->accepted_model->get_referral_dash_info_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }

    public function new_referral() {
        if (clinic_login()) {
            $this->load->model("accepted_model");
            $response = $this->accepted_model->new_referral_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }

}
