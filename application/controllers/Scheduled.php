<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Scheduled extends CI_Controller {

    public function index() {
        if (clinic_login()) {
            $data['page_content'] = $this->load->view('scheduled_master', NULL, TRUE);
            $data['page_title'] = "Scheduled";
            $data['jquery'] = $this->load->view('scripts/scheduled_script', NULL, TRUE);
            $this->load->view('main', $data);
        } else {
            redirect("/");
        }
    }

    public function ssp_scheduled() {
        if (clinic_login()) {
            $this->load->model("scheduled_model");
            $response = $this->scheduled_model->ssp_scheduled_model();
            echo $response;
        } else {
            echo false;
        }
    }

    // *********************************************************************
    // Scheduled Patient Details 
    // *********************************************************************
    public function referral_details() {
        if (clinic_login()) {
            //check valid state
            $this->load->model("referral_model");
            $response = $this->referral_model->check_valid_referral_state_model("Scheduled");
            if ($response == true) {
                $data['page_content'] = $this->load->view('scheduled_referral_details', NULL, TRUE);
                $data['page_title'] = "Scheduled";
                $data['jquery'] = $this->load->view('scripts/scheduled_referral_details_script', NULL, TRUE);
                $this->load->view('main', $data);
            } else {
                //set error message (remaining)
                redirect("scheduled");
            }
        } else {
            redirect("/");
        }
    }

    public function get_referral_dash_info() {
        if (clinic_login()) {
            $this->load->model("scheduled_model");
            $response = $this->scheduled_model->get_referral_dash_info_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }

    public function send_report() {
        if (clinic_login()) {
            $this->load->model("scheduled_model");
            $response = $this->scheduled_model->send_report_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }

}
