<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Call_center extends CI_Controller {

    public function index() {
        if (clinic_login()) {
            $data['page_content'] = $this->load->view('call_center_master', NULL, TRUE);
            $data['page_title'] = "Call Center";
            $data['jquery'] = $this->load->view('scripts/call_center_script', NULL, TRUE);
            $this->load->view('main', $data);
        } else {
            redirect("/");
        }
    }
    
    public function ssp_call_center() {
        if (clinic_login()) {
            $this->load->model("call_center_model");
            $response = $this->call_center_model->ssp_call_center_model();
            echo $response;
        } else {
            echo false;
        }
    }

}
