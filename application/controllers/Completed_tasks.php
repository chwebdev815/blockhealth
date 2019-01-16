<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Completed_tasks extends CI_Controller {
    public function index() {
        if (clinic_login()) {
            $data['page_content'] = $this->load->view('completed_tasks_master', NULL, TRUE);
            $data['page_title'] = "Completed Tasks";
            $data['jquery'] = $this->load->view('scripts/completed_tasks_script', NULL, TRUE);
            $this->load->view('main', $data);
        } else {
            redirect("/");
        }
    }
    public function ssp_completed_tasks() {
        if (clinic_login()) {
            $this->load->model("completed_tasks_model");
            $response = $this->completed_tasks_model->ssp_completed_tasks_model();
            echo $response;
        } else {
            echo false;
        }
    }
}
