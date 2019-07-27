<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Schedule_settings extends CI_Controller {

    public function index() {
        if (clinic_login()) {
            $data['page_content'] = $this->load->view('schedule_settings_master', NULL, TRUE);
            $data['page_title'] = "Schedule Settings";
            $data['jquery'] = $this->load->view('scripts/schedule_settings_script', NULL, TRUE);
            $this->load->view('main', $data);
        } else {
            redirect("/");
        }
    }

    public function get_physician_weekdays() {
        if (clinic_login()) {
            $this->load->model("schedule_settings_model");
            $response = $this->schedule_settings_model->get_physician_weekdays_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function update_physician_weekdays() {
        if (clinic_login()) {
            $this->load->model("schedule_settings_model");
            $response = $this->schedule_settings_model->update_physician_weekdays_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function get_weekday_timing() {
        if (clinic_login()) {
            $this->load->model("schedule_settings_model");
            $response = $this->schedule_settings_model->get_weekday_timing_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function update_weekday_timing() {
        if (clinic_login()) {
            $this->load->model("schedule_settings_model");
            $response = $this->schedule_settings_model->update_weekday_timing_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function update_weekday_timing_all() {
        if (clinic_login()) {
            $this->load->model("schedule_settings_model");
            $response = $this->schedule_settings_model->update_weekday_timing_all_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function get_all_weekday_timing() {
        if (clinic_login()) {
            $this->load->model("schedule_settings_model");
            $response = $this->schedule_settings_model->get_all_weekday_timing_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }    
    
    public function set_day_specific_blocking() {
        if (clinic_login()) {
            $this->load->model("schedule_settings_model");
            $response = $this->schedule_settings_model->set_day_specific_blocking_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    } 
    
    public function get_physician_blocks() {
        if (clinic_login()) {
            $this->load->model("schedule_settings_model");
            $response = $this->schedule_settings_model->get_physician_blocks_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }
    
    public function get_all_blocks() {
        if (clinic_login()) {
            $this->load->model("schedule_settings_model");
            $response = $this->schedule_settings_model->get_all_blocks_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }
    
}
