<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_dashboard_rp extends CI_Controller {

    public function index() {
        if (admin_login()) {
            $data['page_content'] = $this->load->view('tracker/dashboard_rp_master', NULL, TRUE);
            $data['page_title'] = "Referring Physician Dashboard";
            $data['jquery'] = $this->load->view('tracker/scripts/dashboard_rp_script', NULL, TRUE);
            $this->load->view('tracker/template', $data);
        } else {
            redirect("/admin_login");
        }
    }
    
    public function get_page_info() {
        if (admin_login()) {
            $this->load->model("admin_dashboard_model");
            $response = $this->admin_dashboard_model->get_page_info_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }
    
    public function get_graph_data() {
        if (admin_login()) {
            $this->load->model("admin_dashboard_model");
            $response = $this->admin_dashboard_model->get_graph_data_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }
    

    public function ssp_rp_statistics() {
        if (admin_login()) {
            $this->load->model("admin_dashboard_rp_model");
            $response = $this->admin_dashboard_rp_model->ssp_rp_statistics_model();
            echo $response;
        } else {
            echo false;
        }
    }

}
