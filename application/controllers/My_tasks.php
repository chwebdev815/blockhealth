<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class My_tasks extends CI_Controller {
    public function index() {
        if (clinic_login()) {
            $data['page_content'] = $this->load->view('my_tasks_master', NULL, TRUE);
            $data['page_title'] = "Fax Triage";
            $data['jquery'] = $this->load->view('scripts/my_tasks_script', NULL, TRUE);
            $this->load->view('main', $data);
        } else {
            redirect("/");
        }
    }
    public function ssp_my_tasks() {
        if (clinic_login()) {
            $this->load->model("my_tasks_model");
            $response = $this->my_tasks_model->ssp_my_tasks_model();
            echo $response;
        } else {
            echo false;
        }
    }
    
    public function update_task() {
        if (clinic_login()) {
            $this->load->model("my_tasks_model");
            $response = $this->my_tasks_model->update_task_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }
    
    public function task_completed() {
        if (clinic_login()) {
            $this->load->model("my_tasks_model");
            $response = $this->my_tasks_model->task_completed_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }
    
    public function fetch_task_details() {
        if (clinic_login()) {
            $this->load->model("my_tasks_model");
            $response = $this->my_tasks_model->fetch_task_details_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }
    
}
