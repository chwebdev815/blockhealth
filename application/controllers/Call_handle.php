<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Call_handle extends CI_Controller {


    public function index() {
    	echo date('Y-m-d H:i:s');
    }

    public function call_hanlde() {

    }

    public function call_response() {
        log_message("error", "Call Response");
        log_message("error", "printin post " . json_encode($this->input->post()));
    }
    // public function fetch_notifications() {
    // 	if(clinic_login()) {
    //            $this->load->model("store_model");
    //            $response = $this->store_model->fetch_notifications_model();
    //        }
    //        else {
    //            $response = "Session Expired";
    //        }
    //        echo json_encode($response);
    // }
}



